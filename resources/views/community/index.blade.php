@extends('layouts.app')

@section('title', 'Community Hub')
@section('breadcrumb', 'Community')

@push('styles')
    <style>
        .post-card {
            transition: all 0.3s ease;
        }

        .post-card:hover {
            transform: translateY(-2px);
        }

        .vote-btn {
            transition: all 0.2s ease;
        }

        .vote-btn:hover:not(.loading) {
            transform: scale(1.1);
        }

        .vote-btn.loading {
            pointer-events: none;
            opacity: 0.6;
        }

        .vote-btn.active-up {
            color: #22c55e;
        }

        .vote-btn.active-down {
            color: #ef4444;
        }

        .reaction-btn {
            transition: all 0.2s ease;
        }

        .reaction-btn:hover {
            transform: scale(1.2);
        }

        .reaction-btn.active {
            transform: scale(1.1);
        }

        /* Poll Option Styles */
        .poll-option-btn {
            transition: all 0.2s ease;
        }

        .poll-option-btn:hover:not(:disabled) {
            transform: translateX(4px);
        }

        .poll-option-btn:disabled {
            cursor: not-allowed;
        }

        .tag-pill {
            transition: all 0.2s ease;
        }

        .tag-pill:hover {
            transform: scale(1.05);
        }

        .status-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .floating-btn {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* Loading Animations */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(147, 51, 234, 0.2);
            border-top-color: #9333ea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .animate-pulse {
            animation: pulse-opacity 1s ease-in-out infinite;
        }

        @keyframes pulse-opacity {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* Mobile Filter Nav */
        .mobile-filter-nav {
            display: none;
        }

        @media (max-width: 1023px) {
            .mobile-filter-nav {
                display: flex;
            }

            .desktop-sidebar {
                display: none;
            }
        }

        .mobile-filter-panel {
            transform: translateY(100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-filter-panel.active {
            transform: translateY(0);
        }

        .mobile-nav-btn {
            transition: all 0.2s ease;
        }

        .mobile-nav-btn:hover,
        .mobile-nav-btn.active {
            background: linear-gradient(135deg, #9333ea 0%, #ec4899 100%);
            color: white;
        }

        .mobile-overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }
    </style>
@endpush

@section('content')
    <!-- Colorful Glassmorphism Page Background - Purple/Pink Theme for Community -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div
            class="absolute inset-0 bg-gradient-to-br from-purple-100 via-pink-50 to-indigo-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
        </div>
        <div
            class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-purple-300/40 to-pink-400/40 rounded-full blur-3xl -translate-x-1/3 -translate-y-1/3 dark:from-purple-600/10 dark:to-pink-700/10">
        </div>
        <div
            class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-gradient-to-br from-pink-300/40 to-rose-400/40 rounded-full blur-3xl translate-x-1/3 translate-y-1/3 dark:from-pink-600/10 dark:to-rose-700/10">
        </div>
        <div
            class="absolute top-1/2 left-1/2 w-[500px] h-[500px] bg-gradient-to-br from-violet-300/30 to-purple-400/30 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 dark:from-violet-600/10 dark:to-purple-700/10">
        </div>
        <div
            class="absolute top-1/4 right-1/4 w-[400px] h-[400px] bg-gradient-to-br from-fuchsia-300/30 to-pink-400/30 rounded-full blur-3xl dark:from-fuchsia-600/10 dark:to-pink-700/10">
        </div>
        <div
            class="absolute bottom-1/4 left-1/4 w-[400px] h-[400px] bg-gradient-to-br from-indigo-300/30 to-purple-400/30 rounded-full blur-3xl dark:from-indigo-600/10 dark:to-purple-700/10">
        </div>
    </div>

    <div class="min-h-screen">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 p-8 mb-8">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-comments mr-3"></i>Community Hub
                        </h1>
                        <p class="text-white/80 text-lg max-w-xl">
                            Share feedback, ideas, and connect with other TrackFlow users. Your voice shapes our future!
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white/20 backdrop-blur rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-white">{{ number_format($stats['total_posts']) }}</div>
                            <div class="text-white/70 text-sm">Posts</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-white">{{ number_format($stats['total_comments']) }}</div>
                            <div class="text-white/70 text-sm">Comments</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-white">{{ number_format($stats['implemented']) }}</div>
                            <div class="text-white/70 text-sm">Implemented</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-white">{{ number_format($stats['my_posts']) }}</div>
                            <div class="text-white/70 text-sm">My Posts</div>
                        </div>
                    </div>
                </div>

                <!-- User Reputation Badge -->
                <div class="mt-6 inline-flex items-center gap-3 bg-white/20 backdrop-blur rounded-full px-4 py-2">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center">
                        <i
                            class="fas fa-{{ $reputation->level === 'Newbie' ? 'seedling' : ($reputation->level === 'Contributor' ? 'star' : ($reputation->level === 'Top Voice' ? 'fire' : 'crown')) }} text-white text-sm"></i>
                    </div>
                    <div>
                        <span class="text-white font-semibold">{{ $reputation->level }}</span>
                        <span class="text-white/70 ml-2">{{ $reputation->points }} points</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Filter Navigation Bar -->
        <div
            class="mobile-filter-nav lg:hidden sticky top-0 z-40 bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-2 mb-6 flex items-center justify-between gap-2">
            <button onclick="openCreatePostModal()"
                class="mobile-nav-btn flex-1 flex flex-col items-center gap-1 py-2 px-2 rounded-lg text-gray-600 dark:text-gray-400">
                <i class="fas fa-plus-circle text-lg"></i>
                <span class="text-xs">Create</span>
            </button>
            <button onclick="toggleMobilePanel('sortPanel')"
                class="mobile-nav-btn flex-1 flex flex-col items-center gap-1 py-2 px-2 rounded-lg text-gray-600 dark:text-gray-400"
                id="sortBtn">
                <i class="fas fa-sort-amount-down text-lg"></i>
                <span class="text-xs">Sort</span>
            </button>
            <button onclick="toggleMobilePanel('typePanel')"
                class="mobile-nav-btn flex-1 flex flex-col items-center gap-1 py-2 px-2 rounded-lg text-gray-600 dark:text-gray-400"
                id="typeBtn">
                <i class="fas fa-filter text-lg"></i>
                <span class="text-xs">Type</span>
            </button>
            <button onclick="toggleMobilePanel('tagsPanel')"
                class="mobile-nav-btn flex-1 flex flex-col items-center gap-1 py-2 px-2 rounded-lg text-gray-600 dark:text-gray-400"
                id="tagsBtn">
                <i class="fas fa-tags text-lg"></i>
                <span class="text-xs">Tags</span>
            </button>
            <button onclick="toggleMobilePanel('statusPanel')"
                class="mobile-nav-btn flex-1 flex flex-col items-center gap-1 py-2 px-2 rounded-lg text-gray-600 dark:text-gray-400"
                id="statusBtn">
                <i class="fas fa-tasks text-lg"></i>
                <span class="text-xs">Status</span>
            </button>
            <button onclick="toggleMobilePanel('searchPanel')"
                class="mobile-nav-btn flex-1 flex flex-col items-center gap-1 py-2 px-2 rounded-lg text-gray-600 dark:text-gray-400"
                id="searchBtn">
                <i class="fas fa-search text-lg"></i>
                <span class="text-xs">Search</span>
            </button>
        </div>

        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="mobile-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-40"
            onclick="closeMobilePanels()"></div>

        <!-- Mobile Sort Panel -->
        <div id="sortPanel"
            class="mobile-filter-panel fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-t border-white/50 dark:border-gray-700/50 rounded-t-2xl shadow-2xl p-5 max-h-[70vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-sort-amount-down text-purple-500"></i>
                    Sort By
                </h3>
                <button onclick="closeMobilePanels()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-2">
                <a href="{{ route('community.index', array_merge(request()->query(), ['sort' => 'trending'])) }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $sort === 'trending' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-fire text-orange-500"></i>
                    <span>Trending</span>
                </a>
                <a href="{{ route('community.index', array_merge(request()->query(), ['sort' => 'latest'])) }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $sort === 'latest' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-clock text-blue-500"></i>
                    <span>Latest</span>
                </a>
                <a href="{{ route('community.index', array_merge(request()->query(), ['sort' => 'most_voted'])) }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $sort === 'most_voted' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-star text-yellow-500"></i>
                    <span>Most Voted</span>
                </a>
            </div>
        </div>

        <!-- Mobile Type Panel -->
        <div id="typePanel"
            class="mobile-filter-panel fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-t border-white/50 dark:border-gray-700/50 rounded-t-2xl shadow-2xl p-5 max-h-[70vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-filter text-purple-500"></i>
                    Post Type
                </h3>
                <button onclick="closeMobilePanels()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-2">
                <a href="{{ route('community.index', array_merge(request()->except('type'), ['sort' => $sort])) }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ !$type ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-globe text-gray-500"></i>
                    <span>All Types</span>
                </a>
                @foreach($types as $typeKey => $typeInfo)
                    <a href="{{ route('community.index', array_merge(request()->query(), ['type' => $typeKey])) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $type === $typeKey ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        <i class="fas {{ $typeInfo['icon'] }} text-{{ $typeInfo['color'] }}-500"></i>
                        <span>{{ $typeInfo['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Mobile Tags Panel -->
        <div id="tagsPanel"
            class="mobile-filter-panel fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-t border-white/50 dark:border-gray-700/50 rounded-t-2xl shadow-2xl p-5 max-h-[70vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-tags text-purple-500"></i>
                    Tags
                    <span
                        class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 px-2 py-0.5 rounded-full">{{ $tags->count() }}</span>
                </h3>
                <button onclick="closeMobilePanels()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex flex-wrap gap-2" id="mobileTagsContainer">
                <a href="{{ route('community.index', array_merge(request()->except('tag'), ['sort' => $sort])) }}"
                    class="tag-pill px-3 py-2 rounded-full text-sm font-medium transition-colors {{ !$tag ? 'ring-2 ring-offset-2 ring-purple-500 bg-purple-100 text-purple-700' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    All Tags
                </a>
                @foreach($tags as $index => $tagItem)
                    <a href="{{ route('community.index', array_merge(request()->query(), ['tag' => $tagItem->slug])) }}"
                        class="tag-pill px-3 py-2 rounded-full text-sm font-medium transition-colors {{ $tag === $tagItem->slug ? 'ring-2 ring-offset-2 ring-purple-500' : '' }} {{ $index >= 9 ? 'mobile-hidden-tag hidden' : '' }}"
                        style="background-color: {{ $tagItem->color }}20; color: {{ $tagItem->color }};">
                        {{ $tagItem->name }}
                    </a>
                @endforeach
            </div>
            @if($tags->count() > 9)
                <button onclick="toggleMobileTags()" id="mobileTagsToggleBtn"
                    class="mt-3 w-full py-2 text-sm font-medium text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-chevron-down" id="mobileTagsIcon"></i>
                    <span id="mobileTagsText">Show {{ $tags->count() - 9 }} More Tags</span>
                </button>
            @endif
        </div>

        <!-- Mobile Status Panel -->
        <div id="statusPanel"
            class="mobile-filter-panel fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-t border-white/50 dark:border-gray-700/50 rounded-t-2xl shadow-2xl p-5 max-h-[70vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-tasks text-purple-500"></i>
                    Status
                </h3>
                <button onclick="closeMobilePanels()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-2">
                <a href="{{ route('community.index', array_merge(request()->except('status'), ['sort' => $sort])) }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ !$status ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    <i class="fas fa-globe text-gray-500"></i>
                    <span>All Status</span>
                </a>
                @foreach($statuses as $statusKey => $statusInfo)
                    <a href="{{ route('community.index', array_merge(request()->query(), ['status' => $statusKey])) }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $status === $statusKey ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        <i class="fas {{ $statusInfo['icon'] }} text-{{ $statusInfo['color'] }}-500"></i>
                        <span>{{ $statusInfo['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Mobile Search Panel -->
        <div id="searchPanel"
            class="mobile-filter-panel fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-t border-white/50 dark:border-gray-700/50 rounded-t-2xl shadow-2xl p-5 max-h-[70vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-search text-purple-500"></i>
                    Search Posts
                </h3>
                <button onclick="closeMobilePanels()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="{{ route('community.index') }}" method="GET" class="space-y-4">
                <input type="hidden" name="sort" value="{{ $sort }}">
                @if($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                @if($tag)<input type="hidden" name="tag" value="{{ $tag }}">@endif
                <input type="text" name="search" value="{{ $search }}" placeholder="Search posts..."
                    class="w-full px-4 py-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-3 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                @if($search)
                    <a href="{{ route('community.index', array_merge(request()->except('search'))) }}"
                        class="block w-full text-center text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 py-2">
                        Clear Search
                    </a>
                @endif
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar - Filters (Desktop Only) -->
            <div class="desktop-sidebar lg:col-span-1 space-y-6">
                <!-- Create Post Button -->
                <button onclick="openCreatePostModal()"
                    class="w-full floating-btn bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i>
                    Create Post
                </button>

                <!-- Sort Options -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-5 hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-sort-amount-down text-purple-500"></i>
                        Sort By
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('community.index', array_merge(request()->query(), ['sort' => 'trending'])) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $sort === 'trending' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            <i class="fas fa-fire text-orange-500"></i>
                            <span>Trending</span>
                        </a>
                        <a href="{{ route('community.index', array_merge(request()->query(), ['sort' => 'latest'])) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $sort === 'latest' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            <i class="fas fa-clock text-blue-500"></i>
                            <span>Latest</span>
                        </a>
                        <a href="{{ route('community.index', array_merge(request()->query(), ['sort' => 'most_voted'])) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $sort === 'most_voted' ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            <i class="fas fa-star text-yellow-500"></i>
                            <span>Most Voted</span>
                        </a>
                    </div>
                </div>

                <!-- Filter by Type -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all p-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-filter text-purple-500"></i>
                        Post Type
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('community.index', array_merge(request()->except('type'), ['sort' => $sort])) }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors {{ !$type ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            <i class="fas fa-globe text-gray-500"></i>
                            <span>All Types</span>
                        </a>
                        @foreach($types as $typeKey => $typeInfo)
                            <a href="{{ route('community.index', array_merge(request()->query(), ['type' => $typeKey])) }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors {{ $type === $typeKey ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                <i class="fas {{ $typeInfo['icon'] }} text-{{ $typeInfo['color'] }}-500"></i>
                                <span>{{ $typeInfo['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Tags -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-5 hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-tags text-purple-500"></i>
                            Tags
                        </h3>
                        <span
                            class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 px-2 py-0.5 rounded-full">{{ $tags->count() }}</span>
                    </div>
                    <div class="flex flex-wrap gap-2" id="desktopTagsContainer">
                        @foreach($tags as $index => $tagItem)
                            <a href="{{ route('community.index', array_merge(request()->query(), ['tag' => $tagItem->slug])) }}"
                                class="tag-pill px-3 py-1.5 rounded-full text-sm font-medium transition-colors {{ $tag === $tagItem->slug ? 'ring-2 ring-offset-2 ring-purple-500' : '' }} {{ $index >= 9 ? 'desktop-hidden-tag hidden' : '' }}"
                                style="background-color: {{ $tagItem->color }}20; color: {{ $tagItem->color }};">
                                {{ $tagItem->name }}
                            </a>
                        @endforeach
                    </div>
                    @if($tags->count() > 9)
                        <button onclick="toggleDesktopTags()" id="desktopTagsToggleBtn"
                            class="mt-3 w-full py-2 text-sm font-medium text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-chevron-down" id="desktopTagsIcon"></i>
                            <span id="desktopTagsText">Show {{ $tags->count() - 9 }} More</span>
                        </button>
                    @endif
                </div>

                <!-- Status Filter -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-5 hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-tasks text-purple-500"></i>
                        Status
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('community.index', array_merge(request()->except('status'), ['sort' => $sort])) }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors text-sm {{ !$status ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            <span>All Status</span>
                        </a>
                        @foreach($statuses as $statusKey => $statusInfo)
                            <a href="{{ route('community.index', array_merge(request()->query(), ['status' => $statusKey])) }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors text-sm {{ $status === $statusKey ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                <i class="fas {{ $statusInfo['icon'] }} text-{{ $statusInfo['color'] }}-500"></i>
                                <span>{{ $statusInfo['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Content - Posts Feed -->
            <div class="lg:col-span-3">
                <!-- Search Bar -->
                <div class="mb-6">
                    <form action="{{ route('community.index') }}" method="GET" class="relative">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        @if($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                        @if($tag)<input type="hidden" name="tag" value="{{ $tag }}">@endif
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search posts..."
                            class="w-full pl-12 pr-4 py-4 bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl border border-white/50 dark:border-gray-700/50 rounded-xl shadow-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        @if($search)
                            <a href="{{ route('community.index', array_merge(request()->except('search'))) }}"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Posts List -->
                @if($posts->count() > 0)
                    <div class="space-y-4">
                        @foreach($posts as $post)
                            <div
                                class="post-card bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 overflow-hidden hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all">
                                <div class="p-6">
                                    <div class="flex gap-4">
                                        <!-- Vote Column -->
                                        <div class="flex flex-col items-center gap-1">
                                            <button id="vote-up-{{ $post->id }}" onclick="votePost({{ $post->id }}, 'up')"
                                                class="vote-btn w-10 h-10 rounded-lg flex items-center justify-center transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 {{ isset($userVotes[$post->id]) && $userVotes[$post->id] === 'up' ? 'active-up bg-green-50 dark:bg-green-900/20' : 'text-gray-400' }}">
                                                <i class="fas fa-chevron-up text-lg"></i>
                                            </button>
                                            <span id="vote-score-{{ $post->id }}"
                                                class="font-bold text-lg text-gray-900 dark:text-white">{{ $post->vote_score }}</span>
                                            <button id="vote-down-{{ $post->id }}" onclick="votePost({{ $post->id }}, 'down')"
                                                class="vote-btn w-10 h-10 rounded-lg flex items-center justify-center transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 {{ isset($userVotes[$post->id]) && $userVotes[$post->id] === 'down' ? 'active-down bg-red-50 dark:bg-red-900/20' : 'text-gray-400' }}">
                                                <i class="fas fa-chevron-down text-lg"></i>
                                            </button>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <!-- Header -->
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <!-- Type Badge -->
                                                @php $typeInfo = $post->type_info; @endphp
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $typeInfo['color'] }}-100 dark:bg-{{ $typeInfo['color'] }}-900/30 text-{{ $typeInfo['color'] }}-700 dark:text-{{ $typeInfo['color'] }}-400">
                                                    <i class="fas {{ $typeInfo['icon'] }}"></i>
                                                    {{ $typeInfo['label'] }}
                                                </span>

                                                <!-- Status Badge -->
                                                @php $statusInfo = $post->status_info; @endphp
                                                <span
                                                    class="status-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $statusInfo['color'] }}-100 dark:bg-{{ $statusInfo['color'] }}-900/30 text-{{ $statusInfo['color'] }}-700 dark:text-{{ $statusInfo['color'] }}-400">
                                                    <i class="fas {{ $statusInfo['icon'] }}"></i>
                                                    {{ $statusInfo['label'] }}
                                                </span>

                                                @if($post->is_pinned)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                                        <i class="fas fa-thumbtack"></i>
                                                        Pinned
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Title -->
                                            <a href="{{ route('community.show', $post->id) }}" class="block">
                                                <h3
                                                    class="text-lg font-semibold text-gray-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-400 transition-colors mb-2">
                                                    {{ $post->title }}
                                                </h3>
                                            </a>

                                            <!-- Content Preview -->
                                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-3">
                                                {{ Str::limit(strip_tags($post->content), 200) }}
                                            </p>

                                            <!-- Tags -->
                                            @if($post->tags->count() > 0)
                                                <div class="flex flex-wrap gap-2 mb-3">
                                                    @foreach($post->tags as $postTag)
                                                        <span class="px-2 py-1 rounded-full text-xs font-medium"
                                                            style="background-color: {{ $postTag->color }}20; color: {{ $postTag->color }};">
                                                            {{ $postTag->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <!-- Footer -->
                                            <div
                                                class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                                                <!-- Author -->
                                                <div class="flex items-center gap-3">
                                                    @if($post->is_anonymous)
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                            <i class="fas fa-user-secret text-gray-500 dark:text-gray-400"></i>
                                                        </div>
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">Anonymous</span>
                                                    @else
                                                        @if($post->user->profile_picture)
                                                            <img src="{{ $post->user->profile_picture }}" alt="{{ $post->user->name }}"
                                                                class="w-8 h-8 rounded-full object-cover">
                                                        @else
                                                            <div
                                                                class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold text-sm">
                                                                {{ substr($post->user->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <span
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $post->user->name }}</span>
                                                    @endif
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                                                </div>

                                                <!-- Stats -->
                                                <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                                    @if($post->poll)
                                                        <span class="flex items-center gap-1 text-purple-500 dark:text-purple-400"
                                                            title="This post has a poll">
                                                            <i class="fas fa-poll"></i>
                                                            Poll
                                                        </span>
                                                    @endif
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-comment"></i>
                                                        {{ $post->comment_count }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-eye"></i>
                                                        {{ $post->view_count }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Poll Section (if has poll) -->
                                            @if($post->poll)
                                                @php
                                                    $poll = $post->poll;
                                                    $totalVotes = $poll->options->sum('votes_count');
                                                    $isExpired = $poll->isExpired();
                                                    $postUserPollVotes = $userPollVotes[$poll->id] ?? [];
                                                    $hasVoted = !empty($postUserPollVotes);
                                                @endphp
                                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <h4
                                                            class="font-medium text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                                            <i class="fas fa-poll text-purple-500"></i>
                                                            {{ $poll->question }}
                                                        </h4>
                                                        @if($isExpired)
                                                            <span class="text-xs text-red-500 flex items-center gap-1">
                                                                <i class="fas fa-clock"></i> Ended
                                                            </span>
                                                        @elseif($poll->ends_at)
                                                            <span class="text-xs text-gray-500 flex items-center gap-1">
                                                                <i class="fas fa-clock"></i> Ends {{ $poll->ends_at->diffForHumans() }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="space-y-2" id="poll-options-{{ $poll->id }}">
                                                        @foreach($poll->options as $option)
                                                            @php
                                                                $percentage = $totalVotes > 0 ? round(($option->votes_count / $totalVotes) * 100) : 0;
                                                                $isSelected = in_array($option->id, $postUserPollVotes);
                                                            @endphp
                                                            <button type="button" onclick="votePoll({{ $poll->id }}, {{ $option->id }})"
                                                                class="poll-option-btn w-full text-left relative overflow-hidden rounded-lg border transition-all p-3 text-sm {{ $isSelected ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-600' }} {{ $isExpired ? 'cursor-not-allowed opacity-75' : '' }}"
                                                                {{ $isExpired ? 'disabled' : '' }} data-poll-id="{{ $poll->id }}"
                                                                data-option-id="{{ $option->id }}">
                                                                <!-- Progress bar -->
                                                                <div class="absolute inset-0 bg-purple-100 dark:bg-purple-900/30 transition-all duration-500"
                                                                    style="width: {{ $hasVoted || $isExpired ? $percentage : 0 }}%"></div>
                                                                <div class="relative flex items-center justify-between">
                                                                    <span class="flex items-center gap-2">
                                                                        @if($isSelected)
                                                                            <i class="fas fa-check-circle text-purple-500"></i>
                                                                        @else
                                                                            <i class="far fa-circle text-gray-400"></i>
                                                                        @endif
                                                                        <span
                                                                            class="text-gray-700 dark:text-gray-300">{{ $option->option_text }}</span>
                                                                    </span>
                                                                    @if($hasVoted || $isExpired)
                                                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                                                                            {{ $percentage }}% ({{ $option->votes_count }})
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                    <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                                        <span>{{ $totalVotes }} {{ Str::plural('vote', $totalVotes) }}</span>
                                                        @if($poll->multiple_choice)
                                                            <span class="text-purple-500"><i class="fas fa-check-double mr-1"></i>Multiple
                                                                choice</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Action Buttons -->
                                            <div
                                                class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-2">
                                                <!-- Reactions -->
                                                @php
                                                    $reactions = [
                                                        'love' => ['icon' => '❤️', 'label' => 'Love'],
                                                        'useful' => ['icon' => '👍', 'label' => 'Useful'],
                                                        'mindblown' => ['icon' => '🤯', 'label' => 'Mind Blown'],
                                                        'confused' => ['icon' => '😕', 'label' => 'Confused'],
                                                    ];
                                                    $reactionCounts = $post->reactions->groupBy('reaction')->map->count();
                                                    $userReaction = $userReactions[$post->id] ?? null;
                                                @endphp
                                                <div class="flex items-center gap-1">
                                                    @if($post->user_id != session('user_id'))
                                                        {{-- Only show reaction buttons for posts that don't belong to the current user
                                                        --}}
                                                        @foreach($reactions as $key => $reaction)
                                                            @php $count = $reactionCounts[$key] ?? 0; @endphp
                                                            <button onclick="reactToPost({{ $post->id }}, '{{ $key }}')"
                                                                class="reaction-btn inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs transition-all {{ $userReaction === $key ? 'bg-purple-100 dark:bg-purple-900/30 ring-1 ring-purple-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                                                title="{{ $reaction['label'] }}" id="reaction-{{ $post->id }}-{{ $key }}">
                                                                <span>{{ $reaction['icon'] }}</span>
                                                                <span class="reaction-count text-gray-600 dark:text-gray-400"
                                                                    id="reaction-count-{{ $post->id }}-{{ $key }}">{{ $count > 0 ? $count : '' }}</span>
                                                            </button>
                                                        @endforeach
                                                    @else
                                                        {{-- For own posts, show reactions summary (read-only) --}}
                                                        @foreach($reactions as $key => $reaction)
                                                            @php $count = $reactionCounts[$key] ?? 0; @endphp
                                                            <span
                                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs bg-gray-50 dark:bg-gray-800"
                                                                title="{{ $reaction['label'] }}">
                                                                <span>{{ $reaction['icon'] }}</span>
                                                                <span
                                                                    class="text-gray-600 dark:text-gray-400">{{ $count > 0 ? $count : '' }}</span>
                                                            </span>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="flex-1"></div>

                                                <!-- Comment Button -->
                                                <a href="{{ route('community.show', $post->id) }}#comments"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                    <i class="fas fa-comment"></i>
                                                    <span>Comment</span>
                                                </a>

                                                <!-- Share Button -->
                                                <button onclick="sharePost({{ $post->id }}, '{{ addslashes($post->title) }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                    <i class="fas fa-share-alt"></i>
                                                    <span>Share</span>
                                                </button>

                                                <!-- Report Button -->
                                                <button onclick="openReportModal({{ $post->id }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-gray-600 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                                    <i class="fas fa-flag"></i>
                                                    <span>Report</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $posts->withQueryString()->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div
                        class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 p-12 text-center">
                        <div
                            class="w-20 h-20 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-3xl text-purple-500"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No posts yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">Be the first to share your feedback or ideas!</p>
                        <button onclick="openCreatePostModal()"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-xl transition-all">
                            <i class="fas fa-plus-circle"></i>
                            Create First Post
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div id="createPostModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div
            class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border border-white/50 dark:border-gray-700/50 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                        <i class="fas fa-pen-fancy text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Create New Post</h2>
                        <p class="text-sm text-white/80">Share your feedback or ideas</p>
                    </div>
                </div>
                <button onclick="closeCreatePostModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="createPostForm" class="p-6 space-y-5 overflow-y-auto max-h-[calc(90vh-180px)]">
                <!-- Post Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Post Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($types as $typeKey => $typeInfo)
                            @if($typeKey !== 'announcement')
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="type" value="{{ $typeKey }}" class="peer hidden" {{ $typeKey === 'feedback' ? 'checked' : '' }}>
                                    <div
                                        class="flex items-center gap-2 p-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 transition-all">
                                        <i class="fas {{ $typeInfo['icon'] }} text-{{ $typeInfo['color'] }}-500"></i>
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $typeInfo['label'] }}</span>
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="postTitle" required maxlength="255"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white"
                        placeholder="Give your post a descriptive title">
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Content <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" id="postContent" rows="6" required maxlength="10000"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white resize-none"
                        placeholder="Describe your feedback, suggestion, or idea in detail..."></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Markdown formatting supported</p>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tags (optional)
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tagItem)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tagItem->id }}" class="hidden peer">
                                <span
                                    class="inline-block px-3 py-1.5 rounded-full text-sm font-medium border-2 border-transparent peer-checked:border-purple-500 transition-all cursor-pointer"
                                    style="background-color: {{ $tagItem->color }}20; color: {{ $tagItem->color }};">
                                    {{ $tagItem->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Poll Section -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                    <!-- Poll Toggle -->
                    <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50">
                        <input type="checkbox" name="has_poll" id="hasPoll" onchange="togglePollSection()"
                            class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500">
                        <label for="hasPoll"
                            class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            <i class="fas fa-poll text-purple-500"></i>
                            Add a Poll to this post
                        </label>
                    </div>

                    <!-- Poll Creation Fields (Hidden by default) -->
                    <div id="pollSection" class="hidden p-4 space-y-4 border-t border-gray-200 dark:border-gray-700">
                        <!-- Poll Question -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Poll Question <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="poll_question" id="pollQuestion" maxlength="500"
                                class="w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white"
                                placeholder="What would you like to ask?">
                        </div>

                        <!-- Poll Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Options <span class="text-red-500">*</span> <span class="text-gray-500 text-xs">(min 2, max
                                    10)</span>
                            </label>
                            <div id="pollOptionsContainer" class="space-y-2">
                                <div class="poll-option-row flex items-center gap-2">
                                    <span
                                        class="w-6 h-6 flex items-center justify-center bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full text-sm font-medium">1</span>
                                    <input type="text" name="poll_options[]" maxlength="200"
                                        class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white text-sm"
                                        placeholder="Option 1">
                                    <button type="button" onclick="removePollOption(this)"
                                        class="p-2 text-gray-400 hover:text-red-500 transition-colors opacity-50 cursor-not-allowed"
                                        disabled>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <div class="poll-option-row flex items-center gap-2">
                                    <span
                                        class="w-6 h-6 flex items-center justify-center bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full text-sm font-medium">2</span>
                                    <input type="text" name="poll_options[]" maxlength="200"
                                        class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white text-sm"
                                        placeholder="Option 2">
                                    <button type="button" onclick="removePollOption(this)"
                                        class="p-2 text-gray-400 hover:text-red-500 transition-colors opacity-50 cursor-not-allowed"
                                        disabled>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addPollOption()" id="addOptionBtn"
                                class="mt-3 flex items-center gap-2 text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium transition-colors">
                                <i class="fas fa-plus-circle"></i>
                                Add another option
                            </button>
                        </div>

                        <!-- Poll Settings -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 pt-2">
                            <!-- Multiple Choice -->
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="poll_multiple_choice" id="pollMultipleChoice"
                                    class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500">
                                <label for="pollMultipleChoice" class="text-sm text-gray-700 dark:text-gray-300">
                                    Allow multiple selections
                                </label>
                            </div>
                            <!-- End Date (Optional) -->
                            <div class="flex items-center gap-2">
                                <label for="pollEndsAt" class="text-sm text-gray-700 dark:text-gray-300">
                                    End date:
                                </label>
                                <input type="datetime-local" name="poll_ends_at" id="pollEndsAt"
                                    class="px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Anonymous Option -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_anonymous" id="isAnonymous"
                        class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500">
                    <label for="isAnonymous" class="text-sm text-gray-700 dark:text-gray-300">
                        Post anonymously <span class="text-gray-500">(your name won't be shown)</span>
                    </label>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                <button onclick="closeCreatePostModal()"
                    class="px-6 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors font-medium">
                    Cancel
                </button>
                <button onclick="submitPost()" id="submitPostBtn"
                    class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg transition-all font-medium flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Publish Post
                </button>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div
            class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border border-white/50 dark:border-gray-700/50 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                        <i class="fas fa-flag text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white">Report Post</h2>
                </div>
                <button onclick="closeReportModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form id="reportForm" class="p-6 space-y-4">
                <input type="hidden" id="reportPostId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for reporting <span class="text-red-500">*</span>
                    </label>
                    <select id="reportReason" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900 dark:text-white">
                        <option value="">Select a reason...</option>
                        <option value="spam">Spam</option>
                        <option value="inappropriate">Inappropriate content</option>
                        <option value="harassment">Harassment</option>
                        <option value="misinformation">Misinformation</option>
                        <option value="duplicate">Duplicate post</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Additional details (optional)
                    </label>
                    <textarea id="reportDetails" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900 dark:text-white resize-none"
                        placeholder="Provide any additional context..."></textarea>
                </div>
            </form>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                <button onclick="closeReportModal()"
                    class="px-6 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors font-medium">
                    Cancel
                </button>
                <button onclick="submitReport()" id="submitReportBtn"
                    class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white rounded-lg transition-all font-medium flex items-center gap-2">
                    <i class="fas fa-flag"></i>
                    Submit Report
                </button>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div id="shareModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div
            class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border border-white/50 dark:border-gray-700/50 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                        <i class="fas fa-share-alt text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white">Share Post</h2>
                </div>
                <button onclick="closeShareModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" id="sharePostTitle"></p>

                <div class="flex flex-col gap-3">
                    <!-- Copy Link -->
                    <div class="flex items-center gap-2">
                        <input type="text" id="shareLink" readonly
                            class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white text-sm">
                        <button onclick="copyShareLink()"
                            class="px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition-colors"
                            title="Copy link">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>

                    <!-- Social Share Buttons -->
                    <div class="flex items-center justify-center gap-3 pt-4">
                        <button onclick="shareToTwitter()"
                            class="w-12 h-12 rounded-full bg-[#1DA1F2] hover:bg-[#1a8cd8] text-white flex items-center justify-center transition-colors"
                            title="Share on Twitter">
                            <i class="fab fa-twitter text-xl"></i>
                        </button>
                        <button onclick="shareToFacebook()"
                            class="w-12 h-12 rounded-full bg-[#4267B2] hover:bg-[#365899] text-white flex items-center justify-center transition-colors"
                            title="Share on Facebook">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </button>
                        <button onclick="shareToLinkedIn()"
                            class="w-12 h-12 rounded-full bg-[#0077B5] hover:bg-[#006399] text-white flex items-center justify-center transition-colors"
                            title="Share on LinkedIn">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </button>
                        <button onclick="shareToWhatsApp()"
                            class="w-12 h-12 rounded-full bg-[#25D366] hover:bg-[#20bd5a] text-white flex items-center justify-center transition-colors"
                            title="Share on WhatsApp">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner mx-auto mb-4"></div>
            <p id="loadingText" class="text-white font-medium">Loading...</p>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Loading Helpers
        function showLoading(text = 'Loading...') {
            document.getElementById('loadingText').textContent = text;
            document.getElementById('loadingOverlay').classList.add('active');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        // Create Post Modal
        function openCreatePostModal() {
            document.getElementById('createPostModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreatePostModal() {
            document.getElementById('createPostModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('createPostForm').reset();
            // Reset poll section
            document.getElementById('pollSection').classList.add('hidden');
            resetPollOptions();
        }

        // Poll Functions
        function togglePollSection() {
            const pollSection = document.getElementById('pollSection');
            const hasPoll = document.getElementById('hasPoll').checked;

            if (hasPoll) {
                pollSection.classList.remove('hidden');
            } else {
                pollSection.classList.add('hidden');
            }
        }

        function addPollOption() {
            const container = document.getElementById('pollOptionsContainer');
            const optionCount = container.querySelectorAll('.poll-option-row').length;

            if (optionCount >= 10) {
                if (typeof popupError === 'function') {
                    popupError('Maximum 10 options allowed', 'Limit Reached');
                }
                return;
            }

            const newOptionNum = optionCount + 1;
            const newRow = document.createElement('div');
            newRow.className = 'poll-option-row flex items-center gap-2';
            newRow.innerHTML = `
                                            <span class="w-6 h-6 flex items-center justify-center bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full text-sm font-medium">${newOptionNum}</span>
                                            <input type="text" name="poll_options[]" maxlength="200"
                                                class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white text-sm"
                                                placeholder="Option ${newOptionNum}">
                                            <button type="button" onclick="removePollOption(this)" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>                         `;
            container.appendChild(newRow);
            updatePollOptionButtons();

            // Hide add button if max reached
            if (container.querySelectorAll('.poll-option-row').length >= 10) {
                document.getElementById('addOptionBtn').classList.add('hidden');
            }
        }

        function removePollOption(btn) {
            const container = document.getElementById('pollOptionsContainer');
            const optionCount = container.querySelectorAll('.poll-option-row').length;

            if (optionCount <= 2) {
                return;
            }

            btn.closest('.poll-option-row').remove();
            renumberPollOptions();
            updatePollOptionButtons();

            // Show add button if under max
            if (container.querySelectorAll('.poll-option-row').length < 10) {
                document.getElementById('addOptionBtn').classList.remove('hidden');
            }
        }

        function renumberPollOptions() {
            const container = document.getElementById('pollOptionsContainer');
            const rows = container.querySelectorAll('.poll-option-row');
            rows.forEach((row, index) => {
                const num = index + 1;
                row.querySelector('span').textContent = num;
                row.querySelector('input').placeholder = `Option ${num}`;
            });
        }

        function updatePollOptionButtons() {
            const container = document.getElementById('pollOptionsContainer');
            const rows = container.querySelectorAll('.poll-option-row');
            const canDelete = rows.length > 2;

            rows.forEach(row => {
                const deleteBtn = row.querySelector('button');
                if (canDelete) {
                    deleteBtn.disabled = false;
                    deleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    deleteBtn.disabled = true;
                    deleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        }

        function resetPollOptions() {
            const container = document.getElementById('pollOptionsContainer');
            container.innerHTML = `
                                            <div class="poll-option-row flex items-center gap-2">
                                                <span class="w-6 h-6 flex items-center justify-center bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full text-sm font-medium">1</span>
                                                <input type="text" name="poll_options[]" maxlength="200"
                                                    class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white text-sm"
                                                    placeholder="Option 1">
                                                <button type="button" onclick="removePollOption(this)" class="p-2 text-gray-400 hover:text-red-500 transition-colors opacity-50 cursor-not-allowed" disabled>
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                            <div class="poll-option-row flex items-center gap-2">
                                                <span class="w-6 h-6 flex items-center justify-center bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full text-sm font-medium">2</span>
                                                <input type="text" name="poll_options[]" maxlength="200"
                                                    class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white text-sm"
                                                    placeholder="Option 2">
                                                <button type="button" onclick="removePollOption(this)" class="p-2 text-gray-400 hover:text-red-500 transition-colors opacity-50 cursor-not-allowed" disabled>
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        `;
            document.getElementById('addOptionBtn').classList.remove('hidden');
        }

        // Submit Post
        async function submitPost() {
            const form = document.getElementById('createPostForm');
            const btn = document.getElementById('submitPostBtn');
            const originalContent = btn.innerHTML;

            const title = document.getElementById('postTitle').value.trim();
            const content = document.getElementById('postContent').value.trim();
            const type = form.querySelector('input[name="type"]:checked')?.value;
            const tags = Array.from(form.querySelectorAll('input[name="tags[]"]:checked')).map(el => el.value);
            const isAnonymous = document.getElementById('isAnonymous').checked;

            // Poll data
            const hasPoll = document.getElementById('hasPoll').checked;
            let pollData = {};

            if (hasPoll) {
                const pollQuestion = document.getElementById('pollQuestion').value.trim();
                const pollOptions = Array.from(form.querySelectorAll('input[name="poll_options[]"]'))
                    .map(el => el.value.trim())
                    .filter(val => val !== '');
                const pollMultipleChoice = document.getElementById('pollMultipleChoice').checked;
                const pollEndsAt = document.getElementById('pollEndsAt').value || null;

                if (!pollQuestion) {
                    if (typeof popupError === 'function') {
                        popupError('Please enter a poll question', 'Validation Error');
                    } else {
                        alert('Please enter a poll question');
                    }
                    return;
                }

                if (pollOptions.length < 2) {
                    if (typeof popupError === 'function') {
                        popupError('Please add at least 2 poll options', 'Validation Error');
                    } else {
                        alert('Please add at least 2 poll options');
                    }
                    return;
                }

                pollData = {
                    has_poll: true,
                    poll_question: pollQuestion,
                    poll_options: pollOptions,
                    poll_multiple_choice: pollMultipleChoice,
                    poll_ends_at: pollEndsAt
                };
            }

            if (!title || !content || !type) {
                if (typeof popupError === 'function') {
                    popupError('Please fill in all required fields', 'Validation Error');
                } else {
                    alert('Please fill in all required fields');
                }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publishing...';

            try {
                const response = await fetch('{{ route("community.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ title, content, type, tags, is_anonymous: isAnonymous, ...pollData })
                });

                const data = await response.json();

                if (data.success) {
                    if (typeof popupSuccess === 'function') {
                        popupSuccess('Post created successfully!', 'Success');
                    }
                    closeCreatePostModal();
                    showLoading('Refreshing posts...');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Failed to create post');
                }
            } catch (error) {
                if (typeof popupError === 'function') {
                    popupError(error.message, 'Error');
                } else {
                    alert(error.message);
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }

        // Vote on Post
        async function votePost(postId, vote) {
            const voteContainer = document.querySelector(`#post-${postId}-votes, [data-post-id="${postId}"]`)?.closest('.flex');
            const upBtn = document.getElementById(`vote-up-${postId}`);
            const downBtn = document.getElementById(`vote-down-${postId}`);
            const scoreEl = document.getElementById(`vote-score-${postId}`);

            // Add loading state
            if (upBtn) upBtn.classList.add('loading');
            if (downBtn) downBtn.classList.add('loading');
            if (scoreEl) scoreEl.classList.add('animate-pulse');

            try {
                const response = await fetch(`/community/posts/${postId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ vote })
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById(`vote-score-${postId}`).textContent = data.vote_score;
                    // Show loading before refresh
                    showLoading('Updating...');
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to vote');
                }
            } catch (error) {
                console.error('Vote error:', error);
                if (typeof popupError === 'function') {
                    popupError('Failed to submit vote');
                }
            } finally {
                // Remove loading state
                if (upBtn) upBtn.classList.remove('loading');
                if (downBtn) downBtn.classList.remove('loading');
                if (scoreEl) scoreEl.classList.remove('animate-pulse');
            }
        }

        // Close modal on escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCreatePostModal();
                closeMobilePanels();
            }
        });

        // Close modal on backdrop click
        document.getElementById('createPostModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeCreatePostModal();
            }
        });

        // Mobile Filter Panels
        let activePanel = null;
        const panelIds = ['sortPanel', 'typePanel', 'tagsPanel', 'statusPanel', 'searchPanel'];
        const btnIds = ['sortBtn', 'typeBtn', 'tagsBtn', 'statusBtn', 'searchBtn'];

        function toggleMobilePanel(panelId) {
            const panel = document.getElementById(panelId);
            const overlay = document.getElementById('mobileOverlay');
            const btnId = panelId.replace('Panel', 'Btn');
            const btn = document.getElementById(btnId);

            // If clicking the same panel, close it
            if (activePanel === panelId) {
                closeMobilePanels();
                return;
            }

            // Close any open panels first
            panelIds.forEach(id => {
                document.getElementById(id).classList.remove('active');
            });
            btnIds.forEach(id => {
                document.getElementById(id)?.classList.remove('active');
            });

            // Open the requested panel
            panel.classList.add('active');
            overlay.classList.add('active');
            btn?.classList.add('active');
            activePanel = panelId;
            document.body.style.overflow = 'hidden';
        }

        function closeMobilePanels() {
            const overlay = document.getElementById('mobileOverlay');

            panelIds.forEach(id => {
                document.getElementById(id).classList.remove('active');
            });
            btnIds.forEach(id => {
                document.getElementById(id)?.classList.remove('active');
            });

            overlay.classList.remove('active');
            activePanel = null;
            document.body.style.overflow = '';
        }

        // Toggle Desktop Tags
        let desktopTagsExpanded = false;
        function toggleDesktopTags() {
            const hiddenTags = document.querySelectorAll('.desktop-hidden-tag');
            const icon = document.getElementById('desktopTagsIcon');
            const text = document.getElementById('desktopTagsText');

            desktopTagsExpanded = !desktopTagsExpanded;

            hiddenTags.forEach(tag => {
                tag.classList.toggle('hidden', !desktopTagsExpanded);
            });

            icon.classList.toggle('fa-chevron-down', !desktopTagsExpanded);
            icon.classList.toggle('fa-chevron-up', desktopTagsExpanded);
            text.textContent = desktopTagsExpanded ? 'Show Less' : 'Show {{ $tags->count() - 9 }} More';
        }

        // Toggle Mobile Tags
        let mobileTagsExpanded = false;
        function toggleMobileTags() {
            const hiddenTags = document.querySelectorAll('.mobile-hidden-tag');
            const icon = document.getElementById('mobileTagsIcon');
            const text = document.getElementById('mobileTagsText');

            mobileTagsExpanded = !mobileTagsExpanded;

            hiddenTags.forEach(tag => {
                tag.classList.toggle('hidden', !mobileTagsExpanded);
            });

            icon.classList.toggle('fa-chevron-down', !mobileTagsExpanded);
            icon.classList.toggle('fa-chevron-up', mobileTagsExpanded);
            text.textContent = mobileTagsExpanded ? 'Show Less' : 'Show {{ $tags->count() - 9 }} More Tags';
        }

        // React to Post
        async function reactToPost(postId, reaction) {
            const btn = document.getElementById(`reaction-${postId}-${reaction}`);
            if (!btn) return;

            btn.classList.add('animate-pulse');

            try {
                const response = await fetch(`/community/posts/${postId}/react`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reaction })
                });

                const data = await response.json();

                if (data.success) {
                    // Update all reaction buttons for this post
                    const reactions = ['love', 'useful', 'mindblown', 'confused'];
                    reactions.forEach(r => {
                        const rBtn = document.getElementById(`reaction-${postId}-${r}`);
                        const countEl = document.getElementById(`reaction-count-${postId}-${r}`);
                        if (rBtn) {
                            if (r === reaction && data.added) {
                                rBtn.classList.add('bg-purple-100', 'dark:bg-purple-900/30', 'ring-1', 'ring-purple-500');
                            } else if (r === reaction && !data.added) {
                                rBtn.classList.remove('bg-purple-100', 'dark:bg-purple-900/30', 'ring-1', 'ring-purple-500');
                            }
                        }
                        if (countEl && data.reaction_counts) {
                            const count = data.reaction_counts[r] || 0;
                            countEl.textContent = count > 0 ? count : '';
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to react');
                }
            } catch (error) {
                console.error('React error:', error);
                if (typeof popupError === 'function') {
                    popupError('Failed to add reaction');
                }
            } finally {
                btn.classList.remove('animate-pulse');
            }
        }

        // Vote on Poll
        async function votePoll(pollId, optionId) {
            const btn = document.querySelector(`button[data-poll-id="${pollId}"][data-option-id="${optionId}"]`);
            if (!btn || btn.disabled) return;

            // Add loading state to all poll options
            const allBtns = document.querySelectorAll(`button[data-poll-id="${pollId}"]`);
            allBtns.forEach(b => b.classList.add('opacity-50', 'pointer-events-none'));

            try {
                const response = await fetch(`/community/polls/${pollId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ option_id: optionId })
                });

                const data = await response.json();

                if (data.success) {
                    // Reload to update poll results
                    showLoading('Updating poll...');
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to vote');
                }
            } catch (error) {
                console.error('Poll vote error:', error);
                if (typeof popupError === 'function') {
                    popupError(error.message || 'Failed to submit poll vote');
                }
                // Remove loading state
                allBtns.forEach(b => b.classList.remove('opacity-50', 'pointer-events-none'));
            }
        }

        // Report Modal Functions
        let reportPostId = null;

        function openReportModal(postId) {
            reportPostId = postId;
            document.getElementById('reportPostId').value = postId;
            document.getElementById('reportModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('reportForm').reset();
            reportPostId = null;
        }

        async function submitReport() {
            const reason = document.getElementById('reportReason').value;
            const details = document.getElementById('reportDetails').value;
            const btn = document.getElementById('submitReportBtn');

            if (!reason) {
                if (typeof popupError === 'function') {
                    popupError('Please select a reason for reporting', 'Validation Error');
                } else {
                    alert('Please select a reason for reporting');
                }
                return;
            }

            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            try {
                const response = await fetch('{{ route("community.report") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        post_id: reportPostId,
                        reason: reason,
                        description: details
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (typeof popupSuccess === 'function') {
                        popupSuccess('Report submitted successfully. We will review it shortly.', 'Thank You');
                    }
                    closeReportModal();
                } else {
                    throw new Error(data.message || 'Failed to submit report');
                }
            } catch (error) {
                console.error('Report error:', error);
                if (typeof popupError === 'function') {
                    popupError(error.message || 'Failed to submit report');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }

        // Share Modal Functions
        let sharePostId = null;
        let sharePostTitle = '';

        function sharePost(postId, title) {
            sharePostId = postId;
            sharePostTitle = title;
            const shareUrl = `${window.location.origin}/community/post/${postId}`;

            document.getElementById('shareLink').value = shareUrl;
            document.getElementById('sharePostTitle').textContent = `"${title}"`;
            document.getElementById('shareModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeShareModal() {
            document.getElementById('shareModal').classList.add('hidden');
            document.body.style.overflow = '';
            sharePostId = null;
            sharePostTitle = '';
        }

        function copyShareLink() {
            const input = document.getElementById('shareLink');
            input.select();
            document.execCommand('copy');

            if (typeof popupSuccess === 'function') {
                popupSuccess('Link copied to clipboard!', 'Copied');
            }
        }

        function shareToTwitter() {
            const url = document.getElementById('shareLink').value;
            const text = encodeURIComponent(sharePostTitle);
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${encodeURIComponent(url)}`, '_blank', 'width=600,height=400');
        }

        function shareToFacebook() {
            const url = document.getElementById('shareLink').value;
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank', 'width=600,height=400');
        }

        function shareToLinkedIn() {
            const url = document.getElementById('shareLink').value;
            window.open(`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(sharePostTitle)}`, '_blank', 'width=600,height=400');
        }

        function shareToWhatsApp() {
            const url = document.getElementById('shareLink').value;
            const text = encodeURIComponent(`${sharePostTitle} - ${url}`);
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }

        // Close modals on escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeReportModal();
                closeShareModal();
            }
        });

        // Close modals on backdrop click
        document.getElementById('reportModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeReportModal();
            }
        });

        document.getElementById('shareModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeShareModal();
            }
        });
    </script>
@endpush