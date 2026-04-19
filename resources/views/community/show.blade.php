@extends('layouts.app')

@section('title', $post->title . ' - Community')
@section('breadcrumb', 'Community Post')

@push('styles')
    <style>
        .vote-btn {
            transition: all 0.2s ease;
        }

        .vote-btn:hover:not(.loading) {
            transform: scale(1.15);
        }

        .vote-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .vote-btn.active-up {
            color: #22c55e;
            background-color: rgba(34, 197, 94, 0.15);
            transform: scale(1.05);
        }

        .vote-btn.active-up:hover {
            background-color: rgba(34, 197, 94, 0.25);
        }

        .vote-btn.active-down {
            color: #ef4444;
            background-color: rgba(239, 68, 68, 0.15);
            transform: scale(1.05);
        }

        .vote-btn.active-down:hover {
            background-color: rgba(239, 68, 68, 0.25);
        }

        .vote-score {
            transition: all 0.3s ease;
        }

        .vote-score.positive {
            color: #22c55e;
        }

        .vote-score.negative {
            color: #ef4444;
        }

        .reaction-btn {
            transition: all 0.2s ease;
        }

        .reaction-btn:hover:not(.loading) {
            transform: scale(1.2);
        }

        .reaction-btn.loading {
            pointer-events: none;
            opacity: 0.6;
        }

        .reaction-btn.active {
            transform: scale(1.1);
            background-color: rgba(147, 51, 234, 0.1);
        }

        /* Poll Loading States */
        .poll-option button.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .poll-option button.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 1rem;
            width: 1rem;
            height: 1rem;
            border: 2px solid #9333ea;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* Generic Loading Spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes pulse-opacity {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .animate-pulse-fast {
            animation: pulse-opacity 1s ease-in-out infinite;
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

        .comment-thread {
            position: relative;
        }

        .comment-thread::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 50px;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #e5e7eb, transparent);
        }

        .dark .comment-thread::before {
            background: linear-gradient(to bottom, #374151, transparent);
        }

        .nested-comment {
            margin-left: 50px;
        }
    </style>
@endpush

@section('content')
    <!-- Colorful Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-100 via-pink-50 to-indigo-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"></div>
        <div class="absolute top-0 -left-40 w-96 h-96 bg-purple-400/30 dark:bg-purple-900/30 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 -right-40 w-96 h-96 bg-pink-400/30 dark:bg-pink-900/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 w-80 h-80 bg-indigo-400/30 dark:bg-indigo-900/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 right-1/4 w-72 h-72 bg-violet-400/20 dark:bg-violet-900/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/4 w-64 h-64 bg-fuchsia-400/20 dark:bg-fuchsia-900/20 rounded-full blur-3xl"></div>
    </div>

    <div class="min-h-screen">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('community.index') }}"
                class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Community</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Post Card -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-2xl shadow-lg border border-white/50 dark:border-gray-700/50 overflow-hidden">
                    <div class="p-6 md:p-8">
                        <div class="flex gap-6">
                            <!-- Vote Column -->
                            <div class="flex flex-col items-center gap-1 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3">
                                <button onclick="votePost('up')"
                                    class="vote-btn w-12 h-12 rounded-xl flex items-center justify-center transition-all {{ $userVote === 'up' ? 'active-up' : 'text-gray-400 hover:text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20' }}"
                                    title="Upvote">
                                    <i class="fas fa-arrow-up text-xl"></i>
                                </button>
                                <span id="vote-score"
                                    class="vote-score font-bold text-2xl py-1 {{ $post->vote_score > 0 ? 'text-green-600 dark:text-green-400' : ($post->vote_score < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white') }}">{{ $post->vote_score }}</span>
                                <button onclick="votePost('down')"
                                    class="vote-btn w-12 h-12 rounded-xl flex items-center justify-center transition-all {{ $userVote === 'down' ? 'active-down' : 'text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20' }}"
                                    title="Downvote">
                                    <i class="fas fa-arrow-down text-xl"></i>
                                </button>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Badges -->
                                <div class="flex flex-wrap items-center gap-2 mb-4">
                                    @php $typeInfo = $post->type_info; @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-{{ $typeInfo['color'] }}-100 dark:bg-{{ $typeInfo['color'] }}-900/30 text-{{ $typeInfo['color'] }}-700 dark:text-{{ $typeInfo['color'] }}-400">
                                        <i class="fas {{ $typeInfo['icon'] }}"></i>
                                        {{ $typeInfo['label'] }}
                                    </span>

                                    @php $statusInfo = $post->status_info; @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-{{ $statusInfo['color'] }}-100 dark:bg-{{ $statusInfo['color'] }}-900/30 text-{{ $statusInfo['color'] }}-700 dark:text-{{ $statusInfo['color'] }}-400">
                                        <i class="fas {{ $statusInfo['icon'] }}"></i>
                                        {{ $statusInfo['label'] }}
                                    </span>

                                    @if($post->is_pinned)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                            <i class="fas fa-thumbtack"></i>
                                            Pinned
                                        </span>
                                    @endif
                                </div>

                                <!-- Title -->
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ $post->title }}
                                </h1>

                                <!-- Author Info -->
                                <div class="flex items-center gap-4 mb-6">
                                    @if($post->is_anonymous)
                                        <div
                                            class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                            <i class="fas fa-user-secret text-gray-500 dark:text-gray-400 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">Anonymous</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $post->created_at->format('M d, Y \a\t g:i A') }}</div>
                                        </div>
                                    @else
                                        @if($post->user->profile_picture)
                                            <img src="{{ $post->user->profile_picture }}" alt="{{ $post->user->name }}"
                                                class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            <div
                                                class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
                                                {{ substr($post->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $post->user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $post->created_at->format('M d, Y \a\t g:i A') }}</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 mb-6">
                                    {!! nl2br(e($post->content)) !!}
                                </div>

                                <!-- Tags -->
                                @if($post->tags->count() > 0)
                                    <div class="flex flex-wrap gap-2 mb-6">
                                        @foreach($post->tags as $tag)
                                            <span class="px-3 py-1.5 rounded-full text-sm font-medium"
                                                style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Poll Section -->
                                @if($post->poll)
                                    @php
                                        $poll = $post->poll;
                                        $totalVoters = $poll->unique_voters;
                                        $hasVoted = count($userPollVotes) > 0;
                                        $isExpired = $poll->isExpired();
                                    @endphp
                                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-6 mb-6 border border-purple-200 dark:border-purple-800">
                                        <!-- Poll Header -->
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                                    <i class="fas fa-poll-h text-white"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $poll->question }}</h3>
                                                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                        <span>{{ $totalVoters }} {{ Str::plural('vote', $totalVoters) }}</span>
                                                        @if($poll->multiple_choice)
                                                            <span class="text-purple-500 dark:text-purple-400">• Multiple choice</span>
                                                        @endif
                                                        @if($poll->ends_at)
                                                            <span class="{{ $isExpired ? 'text-red-500' : 'text-gray-500' }}">
                                                                • {{ $isExpired ? 'Ended' : 'Ends' }} {{ $poll->ends_at->format('M d, Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($hasVoted)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm font-medium rounded-full">
                                                    <i class="fas fa-check-circle"></i> Voted
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Poll Options -->
                                        <div id="poll-options-container" class="space-y-3">
                                            @foreach($poll->options as $option)
                                                @php
                                                    $percentage = $totalVoters > 0 ? round(($option->votes_count / $totalVoters) * 100, 1) : 0;
                                                    $isSelected = in_array($option->id, $userPollVotes);
                                                @endphp
                                                <div class="poll-option relative" data-option-id="{{ $option->id }}">
                                                    <button type="button"
                                                        onclick="votePoll({{ $poll->id }}, {{ $option->id }})"
                                                        class="w-full text-left relative overflow-hidden rounded-xl border-2 transition-all {{ $isSelected ? 'border-purple-500 bg-white dark:bg-gray-800' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-purple-300 dark:hover:border-purple-600' }} {{ $isExpired ? 'cursor-not-allowed opacity-75' : '' }}"
                                                        {{ $isExpired ? 'disabled' : '' }}>
                                                        <!-- Progress Bar Background -->
                                                        <div class="poll-progress-bar absolute inset-0 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/40 dark:to-pink-900/40 transition-all duration-500"
                                                            style="width: {{ $hasVoted || $isExpired ? $percentage : 0 }}%"></div>
                                                        
                                                        <!-- Content -->
                                                        <div class="relative flex items-center justify-between p-4">
                                                            <div class="flex items-center gap-3">
                                                                @if($poll->multiple_choice)
                                                                    <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all {{ $isSelected ? 'border-purple-500 bg-purple-500' : 'border-gray-300 dark:border-gray-600' }}">
                                                                        @if($isSelected)
                                                                            <i class="fas fa-check text-white text-xs"></i>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all {{ $isSelected ? 'border-purple-500' : 'border-gray-300 dark:border-gray-600' }}">
                                                                        @if($isSelected)
                                                                            <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                <span class="font-medium text-gray-900 dark:text-white">{{ $option->option_text }}</span>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <span class="poll-vote-count text-sm text-gray-500 dark:text-gray-400 {{ $hasVoted || $isExpired ? '' : 'hidden' }}">
                                                                    {{ $option->votes_count }} {{ Str::plural('vote', $option->votes_count) }}
                                                                </span>
                                                                <span class="poll-percentage font-bold text-purple-600 dark:text-purple-400 {{ $hasVoted || $isExpired ? '' : 'hidden' }}">
                                                                    {{ $percentage }}%
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Poll Footer -->
                                        @if(!$isExpired)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-center">
                                                @if($poll->multiple_choice)
                                                    Click to select/deselect options
                                                @else
                                                    Click to vote • Click again to change your vote
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Admin Response -->
                                @if($post->admin_response)
                                    <div
                                        class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-5 mb-6 border-l-4 border-purple-500">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-shield-alt text-white text-sm"></i>
                                            </div>
                                            <span class="font-semibold text-purple-700 dark:text-purple-400">Official
                                                Response</span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $post->admin_response }}</p>
                                        @if($post->admin_responded_at)
                                            <p class="text-xs text-gray-500 mt-2">{{ $post->admin_responded_at->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Reactions -->
                                <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    @if($post->user_id != session('user_id'))
                                        {{-- Only show reaction buttons for posts that don't belong to the current user --}}
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">React:</span>
                                        @php
                                            $reactionTypes = [
                                                'love' => ['emoji' => '❤️', 'label' => 'Love'],
                                                'useful' => ['emoji' => '🔥', 'label' => 'Useful'],
                                                'mindblown' => ['emoji' => '🤯', 'label' => 'Mind Blown'],
                                                'confused' => ['emoji' => '😕', 'label' => 'Confused'],
                                            ];
                                        @endphp
                                        @foreach($reactionTypes as $type => $info)
                                            <button onclick="reactToPost('{{ $type }}')"
                                                class="reaction-btn px-3 py-2 rounded-lg flex items-center gap-2 text-sm transition-all {{ $userReaction === $type ? 'active bg-purple-50 dark:bg-purple-900/30' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                                title="{{ $info['label'] }}">
                                                <span class="text-lg">{{ $info['emoji'] }}</span>
                                                <span id="reaction-count-{{ $type }}"
                                                    class="text-gray-600 dark:text-gray-400">{{ $reactionCounts[$type] ?? 0 }}</span>
                                            </button>
                                        @endforeach
                                    @else
                                        {{-- For own posts, show reactions summary (read-only) --}}
                                        <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">Reactions:</span>
                                        @php
                                            $reactionTypes = [
                                                'love' => ['emoji' => '❤️', 'label' => 'Love'],
                                                'useful' => ['emoji' => '🔥', 'label' => 'Useful'],
                                                'mindblown' => ['emoji' => '🤯', 'label' => 'Mind Blown'],
                                                'confused' => ['emoji' => '😕', 'label' => 'Confused'],
                                            ];
                                        @endphp
                                        @foreach($reactionTypes as $type => $info)
                                            <span class="px-3 py-2 rounded-lg flex items-center gap-2 text-sm bg-gray-50 dark:bg-gray-800"
                                                title="{{ $info['label'] }}">
                                                <span class="text-lg">{{ $info['emoji'] }}</span>
                                                <span class="text-gray-600 dark:text-gray-400">{{ $reactionCounts[$type] ?? 0 }}</span>
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-comment"></i>
                                {{ $post->comment_count }} comments
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-eye"></i>
                                {{ $post->view_count }} views
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <button onclick="sharePost()"
                                class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                <i class="fas fa-share-alt"></i>
                                Share
                            </button>
                            @if($post->user_id !== auth()->id())
                                <button onclick="openReportModal()"
                                    class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                    <i class="fas fa-flag"></i>
                                    Report
                                </button>
                            @endif
                            @if($post->user_id === auth()->id())
                                <button onclick="confirmDeletePost()"
                                    class="px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors flex items-center gap-2 text-sm">
                                    <i class="fas fa-trash-alt"></i>
                                    Delete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div
                    class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-2xl shadow-lg border border-white/50 dark:border-gray-700/50 overflow-hidden">
                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-comments text-purple-500"></i>
                            Comments ({{ $post->comment_count }})
                        </h2>
                    </div>

                    <!-- Add Comment -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <form id="addCommentForm" class="space-y-4">
                            <textarea id="commentContent" rows="4" placeholder="Share your thoughts..."
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white resize-none"></textarea>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="commentAnonymous"
                                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500">
                                    <label for="commentAnonymous" class="text-sm text-gray-600 dark:text-gray-400">Comment
                                        anonymously</label>
                                </div>
                                <button type="button" onclick="submitComment()" id="submitCommentBtn"
                                    class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg transition-all font-medium flex items-center gap-2">
                                    <i class="fas fa-paper-plane"></i>
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div id="commentsList" class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($comments as $comment)
                            @include('community.partials.comment', ['comment' => $comment, 'depth' => 0])
                        @empty
                            <div class="p-8 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-comment-slash text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No comments yet. Be the first to share your
                                    thoughts!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Post Stats -->
                <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all p-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-purple-500"></i>
                        Post Stats
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Upvotes</span>
                            <span class="font-semibold text-green-600">{{ $post->upvote_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Downvotes</span>
                            <span class="font-semibold text-red-600">{{ $post->downvote_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Comments</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $post->comment_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Views</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $post->view_count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                @if($post->type === 'suggestion' || $post->type === 'feedback' || $post->type === 'bug')
                    <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-stream text-purple-500"></i>
                            Status Timeline
                        </h3>
                        @php
                            $statusOrder = ['open', 'under_review', 'planned', 'in_progress', 'implemented'];
                            $currentIndex = array_search($post->status, $statusOrder);
                            $statuses = \App\Models\CommunityPost::getStatuses();
                        @endphp
                        <div class="space-y-4">
                            @foreach($statusOrder as $index => $statusKey)
                                @php $sInfo = $statuses[$statusKey]; @endphp
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center {{ $index <= $currentIndex ? 'bg-' . $sInfo['color'] . '-100 text-' . $sInfo['color'] . '-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-400' }}">
                                        <i class="fas {{ $index <= $currentIndex ? $sInfo['icon'] : 'fa-circle' }} text-sm"></i>
                                    </div>
                                    <span
                                        class="{{ $index <= $currentIndex ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-400' }}">
                                        {{ $sInfo['label'] }}
                                    </span>
                                    @if($index < $currentIndex && $statusKey === $post->status)
                                        <span class="text-xs text-gray-500">Current</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Similar Posts -->
                @if($similarPosts->count() > 0)
                    <div class="bg-white/40 dark:bg-gray-800/50 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 dark:border-gray-700/50 hover:bg-white/50 dark:hover:bg-gray-800/60 transition-all p-5">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-layer-group text-purple-500"></i>
                            Similar Posts
                        </h3>
                        <div class="space-y-3">
                            @foreach($similarPosts as $similar)
                                <a href="{{ route('community.show', $similar->id) }}"
                                    class="block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 mb-1">
                                        {{ $similar->title }}</h4>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-arrow-up"></i>
                                            {{ $similar->vote_score }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-comment"></i>
                                            {{ $similar->comment_count }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl shadow-lg p-5 text-white">
                    <h3 class="font-semibold mb-4 flex items-center gap-2">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('community.index', ['type' => 'suggestion']) }}"
                            class="flex items-center gap-3 px-4 py-3 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                            <i class="fas fa-lightbulb"></i>
                            <span>Browse Suggestions</span>
                        </a>
                        <a href="{{ route('community.index', ['status' => 'implemented']) }}"
                            class="flex items-center gap-3 px-4 py-3 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                            <i class="fas fa-check-circle"></i>
                            <span>View Implemented</span>
                        </a>
                        <a href="{{ route('community.index') }}"
                            class="flex items-center gap-3 px-4 py-3 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                            <i class="fas fa-plus-circle"></i>
                            <span>Create New Post</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border border-white/50 dark:border-gray-700/50 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
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

    <!-- Reply Modal -->
    <div id="replyModal"
        class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border border-white/50 dark:border-gray-700/50 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                        <i class="fas fa-reply text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-white">Reply to Comment</h2>
                </div>
                <button onclick="closeReplyModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form id="replyForm" class="p-6 space-y-4">
                <input type="hidden" id="replyParentId">
                <div id="replyToPreview"
                    class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-sm text-gray-600 dark:text-gray-400 border-l-4 border-purple-500">
                </div>
                <textarea id="replyContent" rows="4" required
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-white resize-none"
                    placeholder="Write your reply..."></textarea>
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="replyAnonymous"
                        class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500">
                    <label for="replyAnonymous" class="text-sm text-gray-600 dark:text-gray-400">Reply anonymously</label>
                </div>
            </form>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                <button onclick="closeReplyModal()"
                    class="px-6 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors font-medium">
                    Cancel
                </button>
                <button onclick="submitReply()" id="submitReplyBtn"
                    class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg transition-all font-medium flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Post Reply
                </button>
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
        const postId = {{ $post->id }};

        // Loading Helpers
        function showLoading(text = 'Loading...') {
            document.getElementById('loadingText').textContent = text;
            document.getElementById('loadingOverlay').classList.add('active');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        // Vote on Post
        async function votePost(vote) {
            const upBtn = document.querySelector('button[onclick="votePost(\'up\')"]');
            const downBtn = document.querySelector('button[onclick="votePost(\'down\')"]');
            const targetBtn = vote === 'up' ? upBtn : downBtn;
            const originalIcon = targetBtn.innerHTML;

            // Add loading state
            upBtn.classList.add('loading');
            downBtn.classList.add('loading');
            targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-xl"></i>';

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
                    const scoreEl = document.getElementById('vote-score');
                    const newScore = data.vote_score;
                    scoreEl.textContent = newScore;
                    
                    // Update score color
                    scoreEl.classList.remove('text-green-600', 'dark:text-green-400', 'text-red-600', 'dark:text-red-400', 'text-gray-900', 'dark:text-white');
                    if (newScore > 0) {
                        scoreEl.classList.add('text-green-600', 'dark:text-green-400');
                    } else if (newScore < 0) {
                        scoreEl.classList.add('text-red-600', 'dark:text-red-400');
                    } else {
                        scoreEl.classList.add('text-gray-900', 'dark:text-white');
                    }
                    
                    // Update button states
                    upBtn.classList.remove('active-up', 'text-gray-400', 'hover:text-green-500', 'hover:bg-green-50', 'dark:hover:bg-green-900/20');
                    downBtn.classList.remove('active-down', 'text-gray-400', 'hover:text-red-500', 'hover:bg-red-50', 'dark:hover:bg-red-900/20');
                    
                    if (data.user_vote === 'up') {
                        upBtn.classList.add('active-up');
                        downBtn.classList.add('text-gray-400', 'hover:text-red-500', 'hover:bg-red-50', 'dark:hover:bg-red-900/20');
                    } else if (data.user_vote === 'down') {
                        downBtn.classList.add('active-down');
                        upBtn.classList.add('text-gray-400', 'hover:text-green-500', 'hover:bg-green-50', 'dark:hover:bg-green-900/20');
                    } else {
                        upBtn.classList.add('text-gray-400', 'hover:text-green-500', 'hover:bg-green-50', 'dark:hover:bg-green-900/20');
                        downBtn.classList.add('text-gray-400', 'hover:text-red-500', 'hover:bg-red-50', 'dark:hover:bg-red-900/20');
                    }
                    
                    // Show feedback
                    if (typeof popupSuccess === 'function') {
                        popupSuccess(data.message);
                    }
                }
            } catch (error) {
                console.error('Vote error:', error);
                if (typeof popupError === 'function') {
                    popupError('Failed to submit vote');
                }
            } finally {
                // Remove loading state
                upBtn.classList.remove('loading');
                downBtn.classList.remove('loading');
                upBtn.innerHTML = '<i class="fas fa-arrow-up text-xl"></i>';
                downBtn.innerHTML = '<i class="fas fa-arrow-down text-xl"></i>';
            }
        }

        // Vote on Poll
        async function votePoll(pollId, optionId) {
            const container = document.getElementById('poll-options-container');
            const optionEl = container.querySelector(`[data-option-id="${optionId}"]`);
            const button = optionEl?.querySelector('button');
            
            // Add loading state to the clicked option
            if (button) {
                button.classList.add('loading');
            }

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
                    // Update UI with new data
                    updatePollUI(data);
                    
                    if (typeof popupSuccess === 'function') {
                        popupSuccess(data.message);
                    }
                } else {
                    throw new Error(data.message || 'Failed to vote');
                }
            } catch (error) {
                console.error('Poll vote error:', error);
                if (typeof popupError === 'function') {
                    popupError(error.message || 'Failed to submit vote');
                }
            } finally {
                // Remove loading state from all poll options
                const allButtons = container.querySelectorAll('.poll-option button');
                allButtons.forEach(btn => btn.classList.remove('loading'));
            }
        }

        function updatePollUI(data) {
            const container = document.getElementById('poll-options-container');
            const userVotes = data.user_votes || [];
            const totalVoters = data.total_voters || 0;

            // Update each option
            data.options.forEach(option => {
                const optionEl = container.querySelector(`[data-option-id="${option.id}"]`);
                if (!optionEl) return;

                const isSelected = userVotes.includes(option.id);
                const button = optionEl.querySelector('button');
                const progressBar = optionEl.querySelector('.poll-progress-bar');
                const voteCount = optionEl.querySelector('.poll-vote-count');
                const percentage = optionEl.querySelector('.poll-percentage');
                const checkIndicator = optionEl.querySelector('.w-5.h-5');

                // Update progress bar
                progressBar.style.width = option.percentage + '%';

                // Update vote count and percentage
                voteCount.textContent = option.votes_count + ' ' + (option.votes_count === 1 ? 'vote' : 'votes');
                voteCount.classList.remove('hidden');
                percentage.textContent = option.percentage + '%';
                percentage.classList.remove('hidden');

                // Update selection state
                if (isSelected) {
                    button.classList.add('border-purple-500');
                    button.classList.remove('border-gray-200', 'dark:border-gray-700', 'hover:border-purple-300', 'dark:hover:border-purple-600');
                    
                    // Update checkbox/radio indicator
                    if (checkIndicator.classList.contains('rounded-full')) {
                        // Radio button
                        checkIndicator.classList.add('border-purple-500');
                        checkIndicator.classList.remove('border-gray-300', 'dark:border-gray-600');
                        checkIndicator.innerHTML = '<div class="w-3 h-3 rounded-full bg-purple-500"></div>';
                    } else {
                        // Checkbox
                        checkIndicator.classList.add('border-purple-500', 'bg-purple-500');
                        checkIndicator.classList.remove('border-gray-300', 'dark:border-gray-600');
                        checkIndicator.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
                    }
                } else {
                    button.classList.remove('border-purple-500');
                    button.classList.add('border-gray-200', 'dark:border-gray-700', 'hover:border-purple-300', 'dark:hover:border-purple-600');
                    
                    // Update checkbox/radio indicator
                    if (checkIndicator.classList.contains('rounded-full')) {
                        // Radio button
                        checkIndicator.classList.remove('border-purple-500');
                        checkIndicator.classList.add('border-gray-300', 'dark:border-gray-600');
                        checkIndicator.innerHTML = '';
                    } else {
                        // Checkbox
                        checkIndicator.classList.remove('border-purple-500', 'bg-purple-500');
                        checkIndicator.classList.add('border-gray-300', 'dark:border-gray-600');
                        checkIndicator.innerHTML = '';
                    }
                }
            });

            // Update voted badge
            const votedBadge = document.querySelector('.fa-check-circle')?.closest('span');
            if (votedBadge) {
                if (userVotes.length > 0) {
                    votedBadge.classList.remove('hidden');
                } else {
                    votedBadge.classList.add('hidden');
                }
            }
        }

        // React to Post
        async function reactToPost(type) {
            const reactionBtns = document.querySelectorAll('.reaction-btn');
            const targetBtn = document.querySelector(`.reaction-btn[onclick="reactToPost('${type}')"]`);
            const originalEmoji = targetBtn?.querySelector('span.text-lg')?.textContent;
            
            // Add loading state to all reaction buttons
            reactionBtns.forEach(btn => btn.classList.add('loading'));
            if (targetBtn) {
                targetBtn.innerHTML = `<i class="fas fa-spinner fa-spin text-lg text-purple-500"></i><span class="text-gray-600 dark:text-gray-400">...</span>`;
            }

            try {
                const response = await fetch(`/community/posts/${postId}/react`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reaction: type })
                });

                const data = await response.json();

                if (data.success) {
                    showLoading('Updating reactions...');
                    window.location.reload();
                }
            } catch (error) {
                console.error('Reaction error:', error);
                if (typeof popupError === 'function') {
                    popupError('Failed to add reaction');
                }
                // Restore buttons on error
                reactionBtns.forEach(btn => btn.classList.remove('loading'));
                if (targetBtn && originalEmoji) {
                    window.location.reload(); // Reload to restore state on error
                }
            }
        }

        // Submit Comment
        async function submitComment() {
            const content = document.getElementById('commentContent').value.trim();
            const isAnonymous = document.getElementById('commentAnonymous').checked;
            const btn = document.getElementById('submitCommentBtn');
            const originalContent = btn.innerHTML;

            if (!content) {
                if (typeof popupError === 'function') {
                    popupError('Please enter a comment', 'Validation Error');
                }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';

            try {
                const response = await fetch(`/community/posts/${postId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content, is_anonymous: isAnonymous })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to post comment');
                }
            } catch (error) {
                if (typeof popupError === 'function') {
                    popupError(error.message, 'Error');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }

        // Reply Modal
        function openReplyModal(commentId, commentContent) {
            document.getElementById('replyParentId').value = commentId;
            document.getElementById('replyToPreview').textContent = commentContent.substring(0, 100) + (commentContent.length > 100 ? '...' : '');
            document.getElementById('replyModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('replyForm').reset();
        }

        async function submitReply() {
            const parentId = document.getElementById('replyParentId').value;
            const content = document.getElementById('replyContent').value.trim();
            const isAnonymous = document.getElementById('replyAnonymous').checked;
            const btn = document.getElementById('submitReplyBtn');
            const originalContent = btn.innerHTML;

            if (!content) {
                if (typeof popupError === 'function') {
                    popupError('Please enter a reply', 'Validation Error');
                }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';

            try {
                const response = await fetch(`/community/posts/${postId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content, parent_id: parentId, is_anonymous: isAnonymous })
                });

                const data = await response.json();

                if (data.success) {
                    if (typeof popupSuccess === 'function') {
                        popupSuccess('Reply posted!', 'Success');
                    }
                    closeReplyModal();
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to post reply');
                }
            } catch (error) {
                if (typeof popupError === 'function') {
                    popupError(error.message, 'Error');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }

        // Report Modal
        function openReportModal() {
            document.getElementById('reportModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('reportForm').reset();
        }

        async function submitReport() {
            const reason = document.getElementById('reportReason').value;
            const details = document.getElementById('reportDetails').value.trim();
            const btn = document.getElementById('submitReportBtn');
            const originalContent = btn.innerHTML;

            if (!reason) {
                if (typeof popupError === 'function') {
                    popupError('Please select a reason', 'Validation Error');
                }
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            try {
                const response = await fetch('/community/report', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        reportable_type: 'post',
                        reportable_id: postId,
                        reason,
                        details
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (typeof popupSuccess === 'function') {
                        popupSuccess('Report submitted. Thank you!', 'Success');
                    }
                    closeReportModal();
                } else {
                    throw new Error(data.message || 'Failed to submit report');
                }
            } catch (error) {
                if (typeof popupError === 'function') {
                    popupError(error.message, 'Error');
                }
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        }

        // Share Post
        function sharePost() {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: '{{ $post->title }}',
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    if (typeof popupSuccess === 'function') {
                        popupSuccess('Link copied to clipboard!', 'Copied');
                    }
                });
            }
        }

        // Delete Post
        function confirmDeletePost() {
            if (typeof popupConfirm === 'function') {
                popupConfirm(
                    'Are you sure you want to delete this post? This action cannot be undone.',
                    'Delete Post',
                    deletePost
                );
            } else if (confirm('Are you sure you want to delete this post?')) {
                deletePost();
            }
        }

        async function deletePost() {
            showLoading('Deleting post...');
            
            try {
                const response = await fetch(`/community/posts/${postId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    if (typeof popupSuccess === 'function') {
                        popupSuccess('Post deleted successfully', 'Success');
                    }
                    setTimeout(() => window.location.href = '{{ route("community.index") }}', 1000);
                } else {
                    throw new Error(data.message || 'Failed to delete post');
                }
            } catch (error) {
                console.error('Delete error:', error);
                hideLoading();
                if (typeof popupError === 'function') {
                    popupError(error.message || 'Failed to delete post');
                }
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeReportModal();
                closeReplyModal();
            }
        });

        // Close modals on backdrop click
        ['reportModal', 'replyModal'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', function (e) {
                if (e.target === this) {
                    if (id === 'reportModal') closeReportModal();
                    if (id === 'replyModal') closeReplyModal();
                }
            });
        });
    </script>
@endpush