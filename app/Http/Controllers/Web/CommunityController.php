<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityVote;
use App\Models\CommunityReaction;
use App\Models\CommunityTag;
use App\Models\CommunityReport;
use App\Models\CommunityNotification;
use App\Models\CommunityReputation;
use App\Models\CommunityPoll;
use App\Models\CommunityPollOption;
use App\Models\CommunityPollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    /**
     * Display the community hub
     */
    public function index(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $sort = $request->get('sort', 'latest');
        $type = $request->get('type', null);
        $status = $request->get('status', null);
        $tag = $request->get('tag', null);
        $search = $request->get('search', null);

        $query = CommunityPost::with(['user', 'tags', 'reactions', 'poll.options'])
            ->withCount(['comments', 'votes']);

        // Apply filters
        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('slug', $tag);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'latest':
                $query->latest();
                break;
            case 'most_voted':
                $query->mostVoted();
                break;
            case 'trending':
            default:
                $query->trending();
                break;
        }

        $posts = $query->paginate(15);
        $tags = CommunityTag::all();
        $types = CommunityPost::getTypes();
        $statuses = CommunityPost::getStatuses();

        // Get stats
        $stats = [
            'total_posts' => CommunityPost::count(),
            'total_comments' => CommunityComment::count(),
            'implemented' => CommunityPost::where('status', 'implemented')->count(),
            'my_posts' => CommunityPost::where('user_id', $userId)->count(),
        ];

        // Get user reputation
        $reputation = CommunityReputation::getOrCreate($userId);

        // Get user's votes for the current posts
        $userVotes = CommunityVote::where('user_id', $userId)
            ->whereIn('post_id', $posts->pluck('id'))
            ->pluck('vote', 'post_id')
            ->toArray();

        // Get user's reactions for the current posts
        $userReactions = CommunityReaction::where('user_id', $userId)
            ->whereIn('post_id', $posts->pluck('id'))
            ->pluck('reaction', 'post_id')
            ->toArray();

        // Get user's poll votes for posts with polls
        $pollIds = $posts->pluck('poll.id')->filter()->toArray();
        $userPollVotes = [];
        if (!empty($pollIds)) {
            $userPollVotes = CommunityPollVote::where('user_id', $userId)
                ->whereIn('poll_id', $pollIds)
                ->get()
                ->groupBy('poll_id')
                ->map(function ($votes) {
                    return $votes->pluck('option_id')->toArray();
                })
                ->toArray();
        }

        return view('community.index', compact(
            'posts',
            'tags',
            'types',
            'statuses',
            'stats',
            'reputation',
            'sort',
            'type',
            'status',
            'tag',
            'search',
            'userVotes',
            'userReactions',
            'userPollVotes'
        ));
    }

    /**
     * Show a single post
     */
    public function show($id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $post = CommunityPost::with([
            'user',
            'tags',
            'reactions',
            'poll.options',
            'comments' => function ($q) {
                $q->with(['user', 'replies.user'])->whereNull('parent_id')->latest();
            }
        ])->findOrFail($id);

        // Increment view count only if the viewer is not the post owner
        if ($post->user_id != $userId) {
            $post->incrementViewCount();
        }

        // Get user's vote
        $userVote = CommunityVote::where('user_id', $userId)
            ->where('post_id', $id)
            ->value('vote');

        // Get user's reaction (single reaction per user per post)
        $userReaction = CommunityReaction::where('user_id', $userId)
            ->where('post_id', $id)
            ->value('reaction');

        // Get reaction counts
        $reactionCounts = $post->reactions()->select('reaction', DB::raw('count(*) as count'))
            ->groupBy('reaction')
            ->pluck('count', 'reaction')
            ->toArray();

        // Get comments (top-level with replies)
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        // Get similar posts (same type or same tags)
        $similarPosts = CommunityPost::where('id', '!=', $id)
            ->where(function ($q) use ($post) {
                $q->where('type', $post->type)
                    ->orWhereHas('tags', function ($tq) use ($post) {
                        $tq->whereIn('community_tags.id', $post->tags->pluck('id'));
                    });
            })
            ->orderBy('vote_score', 'desc')
            ->limit(5)
            ->get();

        // Get user's poll votes if poll exists
        $userPollVotes = [];
        if ($post->poll) {
            $userPollVotes = CommunityPollVote::where('poll_id', $post->poll->id)
                ->where('user_id', $userId)
                ->pluck('option_id')
                ->toArray();
        }

        return view('community.show', compact('post', 'userVote', 'userReaction', 'reactionCounts', 'comments', 'similarPosts', 'userPollVotes'));
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'type' => 'required|in:feedback,suggestion,opinion,bug,announcement',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:community_tags,id',
            'is_anonymous' => 'nullable|boolean',
            // Poll validation
            'has_poll' => 'nullable|boolean',
            'poll_question' => 'required_if:has_poll,true|nullable|string|max:500',
            'poll_options' => 'required_if:has_poll,true|nullable|array|min:2|max:10',
            'poll_options.*' => 'required|string|max:200',
            'poll_multiple_choice' => 'nullable|boolean',
            'poll_ends_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $post = CommunityPost::create([
                'user_id' => $userId,
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
                'is_anonymous' => $request->is_anonymous ?? false,
                'status' => 'open',
            ]);

            // Attach tags
            if ($request->tags) {
                $post->tags()->attach($request->tags);
            }

            // Create poll if provided
            if ($request->has_poll && $request->poll_question && $request->poll_options) {
                $poll = CommunityPoll::create([
                    'post_id' => $post->id,
                    'question' => $request->poll_question,
                    'multiple_choice' => $request->poll_multiple_choice ?? false,
                    'ends_at' => $request->poll_ends_at ?? null,
                ]);

                // Create poll options
                foreach ($request->poll_options as $optionText) {
                    if (!empty(trim($optionText))) {
                        CommunityPollOption::create([
                            'poll_id' => $poll->id,
                            'option_text' => trim($optionText),
                        ]);
                    }
                }
            }

            // Add reputation points
            $reputation = CommunityReputation::getOrCreate($userId);
            $reputation->addPoints(10);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'post_id' => $post->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vote on a post
     */
    public function vote(Request $request, $postId)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'vote' => 'required|in:up,down',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid vote'
            ], 422);
        }

        try {
            $post = CommunityPost::findOrFail($postId);

            $existingVote = CommunityVote::where('user_id', $userId)
                ->where('post_id', $postId)
                ->first();

            $userVote = null;
            $message = '';

            if ($existingVote) {
                if ($existingVote->vote === $request->vote) {
                    // Remove vote
                    $existingVote->delete();
                    $message = 'Vote removed';
                    $userVote = null;
                } else {
                    // Change vote
                    $existingVote->vote = $request->vote;
                    $existingVote->save();
                    $message = $request->vote === 'up' ? 'Upvoted!' : 'Downvoted!';
                    $userVote = $request->vote;
                }
            } else {
                // Create new vote
                CommunityVote::create([
                    'user_id' => $userId,
                    'post_id' => $postId,
                    'vote' => $request->vote,
                ]);
                $message = $request->vote === 'up' ? 'Upvoted!' : 'Downvoted!';
                $userVote = $request->vote;

                // Add reputation to post author
                if ($request->vote === 'up' && $post->user_id !== $userId) {
                    $reputation = CommunityReputation::getOrCreate($post->user_id);
                    $reputation->addPoints(5);
                }
            }

            // Update post vote score
            $post->updateVoteScore();

            return response()->json([
                'success' => true,
                'vote_score' => $post->vote_score,
                'user_vote' => $userVote,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to vote: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vote on a poll
     */
    public function pollVote(Request $request, $pollId)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'option_id' => 'required|exists:community_poll_options,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid option'
            ], 422);
        }

        try {
            $poll = CommunityPoll::with('options')->findOrFail($pollId);
            $optionId = $request->option_id;

            // Check if poll has expired
            if ($poll->isExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This poll has ended'
                ], 400);
            }

            // Check if option belongs to this poll
            $option = $poll->options->find($optionId);
            if (!$option) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid option for this poll'
                ], 400);
            }

            DB::beginTransaction();

            if ($poll->multiple_choice) {
                // Multiple choice - toggle the vote
                $existingVote = CommunityPollVote::where('poll_id', $pollId)
                    ->where('option_id', $optionId)
                    ->where('user_id', $userId)
                    ->first();

                if ($existingVote) {
                    // Remove the vote
                    $existingVote->delete();
                    $option->updateVotesCount();
                    $message = 'Vote removed';
                } else {
                    // Add vote
                    CommunityPollVote::create([
                        'poll_id' => $pollId,
                        'option_id' => $optionId,
                        'user_id' => $userId,
                    ]);
                    $option->updateVotesCount();
                    $message = 'Vote recorded';
                }
            } else {
                // Single choice - remove previous vote and add new one
                $existingVote = CommunityPollVote::where('poll_id', $pollId)
                    ->where('user_id', $userId)
                    ->first();

                if ($existingVote) {
                    if ($existingVote->option_id == $optionId) {
                        // Clicking same option - remove vote
                        $oldOption = CommunityPollOption::find($existingVote->option_id);
                        $existingVote->delete();
                        $oldOption->updateVotesCount();
                        $message = 'Vote removed';
                    } else {
                        // Change vote to new option
                        $oldOption = CommunityPollOption::find($existingVote->option_id);
                        $existingVote->option_id = $optionId;
                        $existingVote->save();
                        $oldOption->updateVotesCount();
                        $option->updateVotesCount();
                        $message = 'Vote changed';
                    }
                } else {
                    // New vote
                    CommunityPollVote::create([
                        'poll_id' => $pollId,
                        'option_id' => $optionId,
                        'user_id' => $userId,
                    ]);
                    $option->updateVotesCount();
                    $message = 'Vote recorded';
                }
            }

            DB::commit();

            // Refresh poll data
            $poll->refresh();
            $poll->load('options');

            // Get user's current votes
            $userVotes = CommunityPollVote::where('poll_id', $pollId)
                ->where('user_id', $userId)
                ->pluck('option_id')
                ->toArray();

            // Build options data for response
            $optionsData = $poll->options->map(function ($opt) use ($poll) {
                $totalVoters = $poll->unique_voters;
                $percentage = $totalVoters > 0 ? round(($opt->votes_count / $totalVoters) * 100, 1) : 0;
                return [
                    'id' => $opt->id,
                    'votes_count' => $opt->votes_count,
                    'percentage' => $percentage,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => $message,
                'total_voters' => $poll->unique_voters,
                'user_votes' => $userVotes,
                'options' => $optionsData,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to vote: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * React to a post
     */
    public function react(Request $request, $postId)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'reaction' => 'required|in:love,useful,mindblown,confused',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reaction'
            ], 422);
        }

        try {
            // Check if user is trying to react to their own post
            $post = CommunityPost::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            if ($post->user_id == $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot react to your own post'
                ], 403);
            }

            $existingReaction = CommunityReaction::where('user_id', $userId)
                ->where('post_id', $postId)
                ->where('reaction', $request->reaction)
                ->first();

            if ($existingReaction) {
                $existingReaction->delete();
                $added = false;
            } else {
                CommunityReaction::create([
                    'user_id' => $userId,
                    'post_id' => $postId,
                    'reaction' => $request->reaction,
                ]);
                $added = true;
            }

            // Get updated reaction counts
            $reactionCounts = CommunityReaction::where('post_id', $postId)
                ->select('reaction', DB::raw('count(*) as count'))
                ->groupBy('reaction')
                ->pluck('count', 'reaction')
                ->toArray();

            return response()->json([
                'success' => true,
                'added' => $added,
                'reaction_counts' => $reactionCounts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to react: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a comment
     */
    public function comment(Request $request, $postId)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:community_comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $post = CommunityPost::findOrFail($postId);

            $comment = CommunityComment::create([
                'post_id' => $postId,
                'user_id' => $userId,
                'parent_id' => $request->parent_id,
                'content' => $request->content,
            ]);

            // Update post comment count
            $post->updateCommentCount();

            // Add reputation points
            $reputation = CommunityReputation::getOrCreate($userId);
            $reputation->addPoints(3);

            // Notify post author
            if ($post->user_id !== $userId) {
                CommunityNotification::createNotification(
                    $post->user_id,
                    CommunityNotification::TYPE_NEW_COMMENT,
                    [
                        'post_id' => $postId,
                        'post_title' => $post->title,
                        'comment_id' => $comment->id,
                        'commenter_name' => session('user_name'),
                    ]
                );
            }

            // Load user for response
            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'name' => $comment->user->name,
                        'profile_picture' => $comment->user->profile_picture,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Report a post or comment
     */
    public function report(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'post_id' => 'nullable|exists:community_posts,id',
            'comment_id' => 'nullable|exists:community_comments,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$request->post_id && !$request->comment_id) {
            return response()->json([
                'success' => false,
                'message' => 'Must specify a post or comment to report'
            ], 422);
        }

        try {
            CommunityReport::create([
                'reporter_id' => $userId,
                'post_id' => $request->post_id,
                'comment_id' => $request->comment_id,
                'reason' => $request->reason,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully. Our team will review it.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notifications
     */
    public function notifications()
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notifications = CommunityNotification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notification = CommunityNotification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Update post status (Admin/Moderator only)
     */
    public function updateStatus(Request $request, $postId)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Check if user is admin (you may want to implement proper role checking)
        $user = DB::table('users')->where('id', $userId)->first();
        if (!$user || !in_array($user->role ?? 'user', ['admin', 'moderator'])) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,under_review,planned,in_progress,implemented,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status'
            ], 422);
        }

        try {
            $post = CommunityPost::findOrFail($postId);
            $oldStatus = $post->status;
            $post->status = $request->status;
            $post->save();

            // Notify post author of status change
            if ($post->user_id !== $userId) {
                CommunityNotification::createNotification(
                    $post->user_id,
                    CommunityNotification::TYPE_STATUS_CHANGE,
                    [
                        'post_id' => $postId,
                        'post_title' => $post->title,
                        'old_status' => $oldStatus,
                        'new_status' => $request->status,
                    ]
                );
            }

            // Add bonus reputation for implemented suggestions
            if ($request->status === 'implemented' && $oldStatus !== 'implemented') {
                $reputation = CommunityReputation::getOrCreate($post->user_id);
                $reputation->addPoints(50);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a post
     */
    public function destroy($id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $post = CommunityPost::findOrFail($id);

            // Check if user owns the post or is admin
            $user = DB::table('users')->where('id', $userId)->first();
            $isAdmin = $user && in_array($user->role ?? 'user', ['admin', 'moderator']);

            if ($post->user_id !== $userId && !$isAdmin) {
                return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post: ' . $e->getMessage()
            ], 500);
        }
    }
}
