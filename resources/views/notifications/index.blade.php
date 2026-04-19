@extends('layouts.app')

@section('content')
    <!-- Colorful Glassmorphism Page Background - Violet/Fuchsia Theme -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-violet-100 via-fuchsia-50 to-pink-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-violet-300/40 to-purple-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-violet-600/10 dark:to-purple-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-fuchsia-300/40 to-pink-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-fuchsia-600/10 dark:to-pink-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-purple-300/30 to-violet-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-purple-600/10 dark:to-violet-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-pink-300/30 to-fuchsia-400/30 rounded-full blur-3xl dark:from-pink-600/10 dark:to-fuchsia-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-violet-300/30 to-indigo-400/30 rounded-full blur-3xl dark:from-violet-600/10 dark:to-indigo-700/10">
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 relative" x-data="notificationsPage()" x-init="init()">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Notifications</h1>
            <p class="text-gray-600 dark:text-gray-400">Stay updated with your financial activities</p>
        </div>

        <!-- Actions Bar -->
        <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-lg shadow-lg border border-white/50 dark:border-gray-700/50-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <!-- Filter Tabs -->
                <div class="flex items-center gap-2">
                    <button @click="setFilter('all')"
                        :class="filter === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        All <span x-text="'(' + totalCount + ')'"></span>
                    </button>
                    <button @click="setFilter('unread')"
                        :class="filter === 'unread' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Unread <span x-text="'(' + unreadCount + ')'"></span>
                    </button>
                    <button @click="setFilter('read')"
                        :class="filter === 'read' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Read
                    </button>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button @click="markAllAsRead()" x-show="unreadCount > 0"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-check-double"></i>
                        Mark All Read
                    </button>
                    <button @click="deleteAllNotifications()" x-show="totalCount > 0"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-trash-alt"></i>
                        Delete All
                    </button>
                    <a href="/notifications/preferences"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            <!-- Loading State -->
            <template x-if="loading">
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-lg shadow-lg border border-white/50 dark:border-gray-700/50-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400">Loading notifications...</p>
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && notifications.length === 0">
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-lg shadow-lg border border-white/50 dark:border-gray-700/50-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <i class="fas fa-bell-slash text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Notifications</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        <span x-show="filter === 'all'">You don't have any notifications yet.</span>
                        <span x-show="filter === 'unread'">You don't have any unread notifications.</span>
                        <span x-show="filter === 'read'">You don't have any read notifications.</span>
                    </p>
                </div>
            </template>

            <!-- Notifications Grid -->
            <template x-if="!loading && notifications.length > 0">
                <div>
                    <template x-for="notification in notifications" :key="notification.id">
                        <div @click="handleNotificationClick(notification)"
                            :class="{ 'border-l-4 border-l-blue-500': !notification.is_read }"
                            class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-lg shadow-lg border border-white/50 dark:border-gray-700/50-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow cursor-pointer mb-4">
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div
                                    :class="'w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 bg-' + notification.color + '-100 dark:bg-' + notification.color + '-900/30'">
                                    <i
                                        :class="'fas ' + notification.icon + ' text-xl text-' + notification.color + '-600 dark:text-' + notification.color + '-400'"></i>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white"
                                            x-text="notification.title"></h3>
                                        <div class="flex items-center gap-2">
                                            <span
                                                :class="notification.priority === 'high' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : notification.priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400'"
                                                class="px-2 py-1 rounded text-xs font-medium capitalize"
                                                x-text="notification.priority"></span>
                                            <button @click.stop="deleteNotification(notification.id)"
                                                class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 p-2">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <p class="text-gray-600 dark:text-gray-400 mb-3" x-text="notification.message"></p>

                                    <div class="flex items-center gap-4 text-sm">
                                        <span class="text-gray-500 dark:text-gray-500 flex items-center gap-1">
                                            <i class="fas fa-clock"></i>
                                            <span x-text="formatTime(notification.created_at)"></span>
                                        </span>

                                        <span
                                            :class="notification.is_read ? 'text-gray-500' : 'text-blue-600 dark:text-blue-400'"
                                            class="flex items-center gap-1 font-medium">
                                            <i :class="notification.is_read ? 'fa-envelope-open' : 'fa-envelope'"
                                                class="fas"></i>
                                            <span x-text="notification.is_read ? 'Read' : 'Unread'"></span>
                                        </span>

                                        <button x-show="!notification.is_read" @click.stop="markAsRead(notification.id)"
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                            Mark as read
                                        </button>

                                        <a x-show="notification.action_url" :href="notification.action_url" @click.stop
                                            class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium flex items-center gap-1">
                                            View Details
                                            <i class="fas fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <script>
        function notificationsPage() {
            return {
                loading: false,
                notifications: [],
                filter: 'all',
                unreadCount: 0,
                totalCount: 0,

                async init() {
                    await this.fetchNotifications();
                },

                async setFilter(filter) {
                    this.filter = filter;
                    await this.fetchNotifications();
                },

                async fetchNotifications() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/notifications/list?filter=${this.filter}`);
                        const data = await response.json();
                        if (data.success) {
                            this.notifications = data.notifications || [];
                            this.unreadCount = data.unread_count || 0;
                            this.totalCount = data.total_count || 0;
                        }
                    } catch (error) {
                        console.error('Error fetching notifications:', error);
                        showToast({ message: 'Failed to load notifications', type: 'error' });
                    } finally {
                        this.loading = false;
                    }
                },

                async handleNotificationClick(notification) {
                    // Mark as read
                    if (!notification.is_read) {
                        await this.markAsRead(notification.id);
                    }

                    // Navigate to action URL if exists
                    if (notification.action_url) {
                        window.location.href = notification.action_url;
                    }
                },

                async markAsRead(notificationId) {
                    try {
                        const response = await fetch(`/notifications/${notificationId}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Update notification in list
                            const notification = this.notifications.find(n => n.id === notificationId);
                            if (notification) {
                                notification.is_read = true;
                            }
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                            showToast({ message: 'Notification marked as read', type: 'success' });
                        }
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                        showToast({ message: 'Failed to mark notification as read', type: 'error' });
                    }
                },

                async markAllAsRead() {
                    try {
                        const response = await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Update all notifications in list
                            this.notifications.forEach(n => n.is_read = true);
                            this.unreadCount = 0;
                            showToast({ message: 'All notifications marked as read', type: 'success' });
                        }
                    } catch (error) {
                        console.error('Error marking all as read:', error);
                        showToast({ message: 'Failed to mark all as read', type: 'error' });
                    }
                },

                async deleteNotification(notificationId) {
                    if (!confirm('Are you sure you want to delete this notification?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/notifications/${notificationId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Remove from list
                            this.notifications = this.notifications.filter(n => n.id !== notificationId);
                            this.totalCount = Math.max(0, this.totalCount - 1);
                            showToast({ message: 'Notification deleted', type: 'success' });
                            await this.fetchNotifications();
                        }
                    } catch (error) {
                        console.error('Error deleting notification:', error);
                        showToast({ message: 'Failed to delete notification', type: 'error' });
                    }
                },

                async deleteAllNotifications() {
                    if (!confirm('Are you sure you want to delete all notifications? This action cannot be undone.')) {
                        return;
                    }

                    try {
                        const response = await fetch('/notifications/delete-all', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Clear all notifications
                            this.notifications = [];
                            this.unreadCount = 0;
                            this.totalCount = 0;
                            showToast({ message: 'All notifications deleted', type: 'success' });
                        }
                    } catch (error) {
                        console.error('Error deleting all notifications:', error);
                        showToast({ message: 'Failed to delete notifications', type: 'error' });
                    }
                },

                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    const now = new Date();
                    const diff = Math.floor((now - date) / 1000); // seconds

                    if (diff < 60) return 'Just now';
                    if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
                    if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
                    if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';

                    return date.toLocaleDateString('en-US', {
                        weekday: 'short',
                        month: 'short',
                        day: 'numeric',
                        year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined,
                        hour: 'numeric',
                        minute: '2-digit'
                    });
                }
            }
        }
    </script>
@endsection
