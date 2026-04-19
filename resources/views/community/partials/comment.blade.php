@php
    $maxDepth = 3;
@endphp

<div class="comment-item p-5 {{ $depth > 0 ? 'ml-8 border-l-2 border-purple-200 dark:border-purple-800' : '' }}"
    id="comment-{{ $comment->id }}">
    <div class="flex gap-4">
        <!-- Vote Column -->
        <div class="flex flex-col items-center gap-1">
            <button onclick="voteComment({{ $comment->id }}, 'up')"
                class="vote-btn w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                <i class="fas fa-chevron-up"></i>
            </button>
            <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $comment->vote_score }}</span>
            <button onclick="voteComment({{ $comment->id }}, 'down')"
                class="vote-btn w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-2">
                @if($comment->is_anonymous)
                    <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                        <i class="fas fa-user-secret text-gray-500 dark:text-gray-400 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Anonymous</span>
                @else
                    @if($comment->user->profile_picture)
                        <img src="{{ $comment->user->profile_picture }}" alt="{{ $comment->user->name }}"
                            class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-semibold text-xs">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                    @endif
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                @endif
                <span
                    class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
            </div>

            <!-- Comment Content -->
            <div class="text-gray-700 dark:text-gray-300 text-sm mb-3 whitespace-pre-wrap">{{ $comment->content }}</div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                @if($depth < $maxDepth)
                    <button
                        onclick="openReplyModal({{ $comment->id }}, '{{ addslashes(Str::limit($comment->content, 100)) }}')"
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors flex items-center gap-1">
                        <i class="fas fa-reply"></i>
                        Reply
                    </button>
                @endif
                @if($comment->user_id !== auth()->id())
                    <button onclick="reportComment({{ $comment->id }})"
                        class="text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 transition-colors flex items-center gap-1">
                        <i class="fas fa-flag"></i>
                        Report
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Nested Replies -->
    @if($comment->replies && $comment->replies->count() > 0 && $depth < $maxDepth)
        <div class="mt-4 space-y-4">
            @foreach($comment->replies as $reply)
                @include('community.partials.comment', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>

<script>
    async function voteComment(commentId, vote) {
        try {
            const response = await fetch(`/community/posts/{{ $comment->post_id }}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    vote,
                    votable_type: 'comment',
                    votable_id: commentId
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Vote error:', error);
        }
    }

    function reportComment(commentId) {
        document.getElementById('reportModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Store comment info for report submission
        window.reportingComment = commentId;
    }
</script>