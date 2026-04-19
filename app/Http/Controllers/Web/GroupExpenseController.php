<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupTransaction;
use App\Models\GroupTransactionMember;
use App\Models\SettlementPayment;
use App\Models\Category;
use App\Models\User;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GroupExpenseController extends Controller
{
    protected $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Display the group expense dashboard
     */
    public function index()
    {
        $userId = session('user_id');

        // Get all groups where user is a member
        $groups = Group::whereHas('members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['members', 'transactions'])->get();

        // Get user's currency settings - use cached singleton
        $user = User::find($userId);
        $userSetting = app('user.settings');
        $userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));
        $currencyConfig = config('currency.currencies');
        $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '₹';

        // Get currency service for conversion
        $currencyService = app(\App\Services\CurrencyService::class);

        return view('group-expense.index', compact('groups', 'currencySymbol', 'userCurrency', 'currencyService'));
    }

    /**
     * Show specific group dashboard
     */
    public function showGroup($groupId)
    {
        $userId = session('user_id');

        $group = Group::with(['members.user', 'transactions.paidBy.user', 'transactions.category', 'transactions.members.member.user'])
            ->findOrFail($groupId);

        // Check if user is a member
        $currentMember = $group->members()->where('user_id', $userId)->first();
        if (!$currentMember) {
            return redirect()->route('group-expense.index')
                ->with('error', 'You are not a member of this group');
        }

        // Get user's currency settings - use cached singleton
        $user = User::find($userId);
        $userSetting = app('user.settings');
        $userCurrency = strtoupper(trim($userSetting->display_currency ?? $user->currency ?? config('currency.default', 'INR')));
        $currencyConfig = config('currency.currencies');
        $currencySymbol = $currencyConfig[$userCurrency]['symbol'] ?? '₹';

        // Get currency service for conversion
        $currencyService = app(\App\Services\CurrencyService::class);

        // Calculate summary statistics with currency conversion
        $summary = $this->calculateGroupSummary($group, $currencyService, $userCurrency);

        // Get balance sheet with currency conversion
        $balanceSheet = $this->calculateBalanceSheet($group, $currencyService, $userCurrency);

        // Get categories for dropdown
        $categories = Category::where('user_id', $userId)->get();

        return view('group-expense.dashboard', compact('group', 'currentMember', 'summary', 'balanceSheet', 'categories', 'currencySymbol', 'userCurrency', 'currencyService'));
    }

    /**
     * Create a new group
     */
    public function createGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_name' => 'required|string|max:255',
            'leader_email' => 'nullable|email',
            'leader_phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');

            DB::beginTransaction();

            // Create group with unique code
            $group = Group::create([
                'name' => $request->name,
                'description' => $request->description,
                'group_code' => Group::generateUniqueCode(),
                'created_by' => $userId
            ]);

            // Add creator as leader
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $userId,
                'name' => $request->leader_name,
                'email' => $request->leader_email,
                'phone' => $request->leader_phone,
                'role' => 'leader',
                'status' => 'active',
                'last_active_at' => now()
            ]);

            DB::commit();

            // Send email to group leader
            $this->emailService->sendGroupCreatedEmail($userId, [
                'group_name' => $group->name,
                'group_id' => $group->id,
                'group_description' => $group->description,
                'group_code' => $group->group_code,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'group_id' => $group->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Join group by code
     */
    public function joinByCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_code' => 'required|string|size:8|regex:/^[A-Z0-9]+$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid group code format'
            ], 422);
        }

        try {
            $userId = session('user_id');
            $code = strtoupper($request->group_code);

            // Find group by code
            $group = Group::where('group_code', $code)->first();

            if (!$group) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid group code. Please check and try again.'
                ], 404);
            }

            // Check if user is already a member
            $existingMember = GroupMember::where('group_id', $group->id)
                ->where('user_id', $userId)
                ->first();

            if ($existingMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already a member of this group'
                ], 409);
            }

            // Get user details
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            DB::beginTransaction();

            // Add user as member with profile picture
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $userId,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'picture' => $user->profile_picture ?? null,
                'role' => 'member',
                'status' => 'active',
                'last_active_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Successfully joined group: ' . $group->name,
                'group_id' => $group->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to join group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add member to group
     */
    public function addMember(Request $request, $groupId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'picture' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            $group = Group::findOrFail($groupId);

            // Check if user is leader
            $currentMember = $group->members()->where('user_id', $userId)->first();
            if (!$currentMember || !$currentMember->isLeader()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the leader can add members'
                ], 403);
            }

            // Handle profile picture upload
            $picturePath = null;
            if ($request->hasFile('picture')) {
                $picturePath = $request->file('picture')->store('group-members', 'public');
            }

            // Check if a user account exists with the provided email
            $linkedUserId = null;
            $linkedUser = null;
            if ($request->email) {
                $existingUser = User::where('email', strtolower(trim($request->email)))->first();
                if ($existingUser) {
                    $linkedUserId = $existingUser->id;
                    $linkedUser = $existingUser;
                    // Use the user's profile picture if they have one and no picture was uploaded
                    if (!$picturePath && $existingUser->profile_picture) {
                        $picturePath = $existingUser->profile_picture;
                    }
                }
            }

            // Get existing members before adding new one (for notification)
            $existingMembers = $group->members()->where('status', 'active')->get();
            $groupLeader = $existingMembers->where('role', 'leader')->first();

            // Add member
            $member = GroupMember::create([
                'group_id' => $groupId,
                'user_id' => $linkedUserId,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'picture' => $picturePath,
                'role' => 'member',
                'status' => 'active'
            ]);

            // Total members after adding
            $totalMembers = $existingMembers->count() + 1;

            // Send email to new member (if they have a registered email)
            if ($linkedUser && $linkedUser->email) {
                $this->emailService->sendMemberAddedEmail($linkedUser->email, [
                    'member_name' => $linkedUser->name,
                    'group_name' => $group->name,
                    'group_id' => $group->id,
                    'group_description' => $group->description,
                    'added_by' => $groupLeader ? $groupLeader->display_name : 'the group leader',
                    'total_members' => $totalMembers,
                    'group_leader' => $groupLeader ? $groupLeader->display_name : null,
                ]);
            }

            // Send notification email to all existing members with registered accounts
            foreach ($existingMembers as $existingMember) {
                // Only send to members with linked user accounts
                if ($existingMember->user_id && $existingMember->user) {
                    $this->emailService->sendNewMemberNotificationEmail($existingMember->user->email, [
                        'recipient_name' => $existingMember->user->name,
                        'group_name' => $group->name,
                        'group_id' => $group->id,
                        'new_member_name' => $request->name,
                        'total_members' => $totalMembers,
                        'added_by' => $groupLeader ? $groupLeader->display_name : 'the group leader',
                    ]);
                }
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove member from group
     */
    public function removeMember(Request $request, $groupId, $memberId)
    {
        try {
            $userId = session('user_id');
            $group = Group::findOrFail($groupId);

            // Check if user is leader
            $currentMember = $group->members()->where('user_id', $userId)->first();
            if (!$currentMember || !$currentMember->isLeader()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the leader can remove members'
                ], 403);
            }

            $member = GroupMember::findOrFail($memberId);

            // Cannot remove leader
            if ($member->isLeader()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove the leader. Transfer leadership first.'
                ], 422);
            }

            $member->delete();

            return response()->json([
                'success' => true,
                'message' => 'Member removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Leave group (member leaves voluntarily)
     */
    public function leaveGroup(Request $request, $groupId)
    {
        try {
            $userId = session('user_id');
            $group = Group::findOrFail($groupId);

            // Get current member
            $currentMember = $group->members()->where('user_id', $userId)->first();

            if (!$currentMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }

            // Leader cannot leave, must transfer leadership first
            if ($currentMember->isLeader()) {
                return response()->json([
                    'success' => false,
                    'message' => 'As the leader, you must transfer leadership to another member before leaving the group.'
                ], 422);
            }

            // Check if member has unsettled transactions
            $hasUnsettledTransactions = GroupTransactionMember::where('member_id', $currentMember->id)
                ->whereHas('transaction', function ($query) {
                    $query->where('status', 'unpaid');
                })
                ->exists();

            if ($hasUnsettledTransactions) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have unsettled transactions. Please settle all debts before leaving the group.'
                ], 422);
            }

            // Remove member
            $currentMember->delete();

            return response()->json([
                'success' => true,
                'message' => 'You have successfully left the group'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to leave group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change member role or transfer leadership
     */
    public function changeRole(Request $request, $groupId, $memberId)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:leader,member'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = session('user_id');
            $group = Group::findOrFail($groupId);

            // Check if user is leader
            $currentLeader = $group->members()->where('user_id', $userId)->where('role', 'leader')->first();
            if (!$currentLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the leader can change roles'
                ], 403);
            }

            DB::beginTransaction();

            if ($request->role === 'leader') {
                // Transfer leadership
                // Remove leader role from current leader
                $currentLeader->update(['role' => 'member']);

                // Assign leader role to new member
                $newLeader = GroupMember::findOrFail($memberId);
                $newLeader->update(['role' => 'leader']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Leadership transferred successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to change role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add group transaction
     */
    public function addTransaction(Request $request, $groupId)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:income,expense',
            'paid_by_member_id' => 'required|exists:group_members,id',
            'category_id' => 'nullable|exists:categories,id',
            'total_amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'status' => 'required|in:paid,unpaid',
            'members' => 'required|array',
            'members.*.member_id' => 'required|exists:group_members,id',
            'members.*.contributed_amount' => 'required|numeric|min:0',
            'members.*.participated' => 'required|boolean'
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

            // Create transaction
            $transaction = GroupTransaction::create([
                'group_id' => $groupId,
                'paid_by_member_id' => $request->paid_by_member_id,
                'type' => $request->type,
                'category_id' => $request->category_id,
                'total_amount' => $request->total_amount,
                'description' => $request->description,
                'date' => $request->date,
                'note' => $request->note,
                'status' => $request->status
            ]);

            // Calculate equal share
            $participatingMembers = collect($request->members)->where('participated', true);
            $participantCount = $participatingMembers->count();
            $equalShare = $participantCount > 0 ? $request->total_amount / $participantCount : 0;

            // Add member contributions
            foreach ($request->members as $memberData) {
                GroupTransactionMember::create([
                    'transaction_id' => $transaction->id,
                    'member_id' => $memberData['member_id'],
                    'contributed_amount' => $memberData['contributed_amount'],
                    'final_share_amount' => $memberData['participated'] ? $equalShare : 0,
                    'participated' => $memberData['participated']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction added successfully',
                'transaction_id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit transaction
     */
    public function editTransaction(Request $request, $groupId, $transactionId)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:income,expense',
            'paid_by_member_id' => 'required|exists:group_members,id',
            'category_id' => 'nullable|exists:categories,id',
            'total_amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'status' => 'required|in:paid,unpaid',
            'members' => 'required|array',
            'members.*.member_id' => 'required|exists:group_members,id',
            'members.*.contributed_amount' => 'required|numeric|min:0',
            'members.*.participated' => 'required|boolean'
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

            $transaction = GroupTransaction::findOrFail($transactionId);

            // Update transaction
            $transaction->update([
                'type' => $request->type,
                'paid_by_member_id' => $request->paid_by_member_id,
                'category_id' => $request->category_id,
                'total_amount' => $request->total_amount,
                'description' => $request->description,
                'date' => $request->date,
                'note' => $request->note,
                'status' => $request->status
            ]);

            // Delete existing members and re-add
            $transaction->members()->delete();

            // Calculate equal share among participants
            $participantCount = collect($request->members)->where('participated', true)->count();
            $equalShare = $participantCount > 0 ? $request->total_amount / $participantCount : 0;

            // Add members with contributions
            foreach ($request->members as $memberData) {
                GroupTransactionMember::create([
                    'transaction_id' => $transaction->id,
                    'member_id' => $memberData['member_id'],
                    'contributed_amount' => $memberData['contributed_amount'],
                    'final_share_amount' => $memberData['participated'] ? $equalShare : 0,
                    'participated' => $memberData['participated']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete transaction
     */
    public function deleteTransaction(Request $request, $groupId, $transactionId)
    {
        try {
            DB::beginTransaction();

            $transaction = GroupTransaction::findOrFail($transactionId);

            // Delete all transaction members first (cascade)
            $transaction->members()->delete();

            // Delete the transaction
            $transaction->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle transaction status
     */
    public function toggleTransactionStatus(Request $request, $groupId, $transactionId)
    {
        try {
            $transaction = GroupTransaction::findOrFail($transactionId);
            $transaction->update([
                'status' => $transaction->status === 'paid' ? 'unpaid' : 'paid'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction status updated',
                'status' => $transaction->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate group summary
     */
    private function calculateGroupSummary($group, $currencyService = null, $userCurrency = 'INR')
    {
        $members = $group->members;
        $transactions = $group->transactions;

        $totalMembers = $members->count();

        // Get raw amounts in INR (base currency)
        $totalExpensesRaw = $transactions->where('type', 'expense')->sum('total_amount');
        $totalIncomeRaw = $transactions->where('type', 'income')->sum('total_amount');
        $paidTransactionsRaw = $transactions->where('status', 'paid')->sum('total_amount');
        $unpaidTransactionsRaw = $transactions->where('status', 'unpaid')->sum('total_amount');

        // Convert to user's currency if service is available
        if ($currencyService && $userCurrency !== 'INR') {
            $totalExpenses = $currencyService->convert((float) $totalExpensesRaw, 'INR', $userCurrency);
            $totalIncome = $currencyService->convert((float) $totalIncomeRaw, 'INR', $userCurrency);
            $paidTransactions = $currencyService->convert((float) $paidTransactionsRaw, 'INR', $userCurrency);
            $unpaidTransactions = $currencyService->convert((float) $unpaidTransactionsRaw, 'INR', $userCurrency);
        } else {
            $totalExpenses = $totalExpensesRaw;
            $totalIncome = $totalIncomeRaw;
            $paidTransactions = $paidTransactionsRaw;
            $unpaidTransactions = $unpaidTransactionsRaw;
        }

        $totalBalance = $totalIncome - $totalExpenses;
        $leader = $group->leader();

        return [
            'total_members' => $totalMembers,
            'total_balance' => $totalBalance,
            'total_expenses' => $totalExpenses,
            'total_income' => $totalIncome,
            'total_settled' => $paidTransactions,
            'total_unsettled' => $unpaidTransactions,
            'leader' => $leader
        ];
    }

    /**
     * Calculate balance sheet (who owes whom)
     * 
     * BACKUP OF ORIGINAL LOGIC (can revert later):
     * ============================================
     * $contributedRaw = GroupTransactionMember::where('member_id', $member->id)->sum('contributed_amount');
     * $shouldPayRaw = GroupTransactionMember::where('member_id', $member->id)->where('participated', true)->sum('final_share_amount');
     * $netBalance = $contributed - $shouldPay;
     * ============================================
     * 
     * NEW LOGIC FROM NOTES FILE (Bazar Khata Style):
     * Total Paid = Sum of all transactions where this member paid (paid_by_member_id)
     * Total Share = Sum of final_share_amount where this member participated
     * Net Balance = Total Paid - Total Share
     */
    private function calculateBalanceSheet($group, $currencyService = null, $userCurrency = 'INR')
    {
        $members = $group->members;
        $balances = [];

        // Calculate net balance for each member using NOTES FILE LOGIC
        foreach ($members as $member) {
            // A. Total Paid: কত টাকা পকেট থেকে দিয়েছে (বাজার করেছে)
            // Sum of total_amount from GroupTransaction where paid_by_member_id = this member
            $totalPaidRaw = GroupTransaction::where('group_id', $group->id)
                ->where('paid_by_member_id', $member->id)
                ->sum('total_amount');

            // B. Total Share: কত টাকার জিনিস ভোগ করেছে (খেয়েছে)
            // Sum of final_share_amount where this member participated
            $totalShareRaw = GroupTransactionMember::where('member_id', $member->id)
                ->whereHas('transaction', function ($q) use ($group) {
                    $q->where('group_id', $group->id);
                })
                ->where('participated', true)
                ->sum('final_share_amount');

            // Convert to user's currency if service is available
            if ($currencyService && $userCurrency !== 'INR') {
                $contributed = $currencyService->convert((float) $totalPaidRaw, 'INR', $userCurrency);
                $shouldPay = $currencyService->convert((float) $totalShareRaw, 'INR', $userCurrency);
            } else {
                $contributed = $totalPaidRaw;
                $shouldPay = $totalShareRaw;
            }

            // C. Net Balance: (টাকা দেওয়া - খাওয়া খরচ)
            // Positive (+) = Gets money back, Negative (-) = Owes money
            $netBalance = $contributed - $shouldPay;

            $balances[] = [
                'member' => $member,
                'contributed' => $contributed,
                'should_pay' => $shouldPay,
                'net_balance' => $netBalance
            ];
        }

        // Sort by net balance (যারা পাবে তারা উপরে)
        usort($balances, function ($a, $b) {
            return $b['net_balance'] <=> $a['net_balance'];
        });

        // Calculate settlements
        $settlements = $this->calculateSettlements($balances);

        return [
            'balances' => $balances,
            'settlements' => $settlements
        ];
    }

    /**
     * Calculate optimal settlements
     */
    private function calculateSettlements($balances)
    {
        $settlements = [];
        $creditors = collect($balances)->where('net_balance', '>', 0)->values();
        $debtors = collect($balances)->where('net_balance', '<', 0)->values();

        $i = 0;
        $j = 0;

        while ($i < $creditors->count() && $j < $debtors->count()) {
            $creditor = $creditors[$i];
            $debtor = $debtors[$j];

            $amountToSettle = min(abs($creditor['net_balance']), abs($debtor['net_balance']));

            if ($amountToSettle > 0.01) { // Ignore tiny amounts
                $settlements[] = [
                    'from' => $debtor['member']->display_name,
                    'from_member_id' => $debtor['member']->id,
                    'to' => $creditor['member']->display_name,
                    'to_member_id' => $creditor['member']->id,
                    'amount' => round($amountToSettle, 2)
                ];
            }

            // Update balances
            $creditor['net_balance'] -= $amountToSettle;
            $debtor['net_balance'] += $amountToSettle;

            if (abs($creditor['net_balance']) < 0.01) {
                $i++;
            }
            if (abs($debtor['net_balance']) < 0.01) {
                $j++;
            }
        }

        return $settlements;
    }

    /**
     * Update member activity status
     */
    public function updateMemberActivity(Request $request, $groupId, $memberId)
    {
        try {
            $member = GroupMember::findOrFail($memberId);
            $member->update([
                'last_active_at' => now(),
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Activity updated'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update activity: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete group (only leader can delete)
     */
    public function deleteGroup(Request $request, $groupId)
    {
        try {
            $userId = session('user_id');
            $group = Group::findOrFail($groupId);

            // Check if user is leader
            $currentMember = $group->members()->where('user_id', $userId)->where('role', 'leader')->first();
            if (!$currentMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the leader can delete the group'
                ], 403);
            }

            DB::beginTransaction();

            // Get all transactions for this group
            $transactions = $group->transactions;

            // Delete all transaction members for each transaction
            foreach ($transactions as $transaction) {
                $transaction->members()->delete();
            }

            // Delete all transactions
            $group->transactions()->delete();

            // Delete all group members
            $group->members()->delete();

            // Finally delete the group itself
            $group->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Settle up member balance
     */
    public function settleUp($groupId)
    {
        try {
            $userId = session('user_id');

            // Get the group and member
            $group = Group::findOrFail($groupId);
            $member = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->firstOrFail();

            DB::beginTransaction();

            // Mark member as settled
            $member->update([
                'is_settled' => true,
                'settled_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Your balance has been settled successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to settle balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get member profile details for popup
     */
    public function getMemberProfile($groupId, $memberId)
    {
        try {
            $userId = session('user_id');

            // Get the group
            $group = Group::findOrFail($groupId);

            // Check if the requesting user is a member of this group
            $currentMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->first();

            if (!$currentMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }

            // Get the target member
            $member = GroupMember::with('user')->where('group_id', $groupId)
                ->where('id', $memberId)
                ->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            // Build profile data
            $profileData = [
                'id' => $member->id,
                'display_name' => $member->display_name,
                'email' => $member->email ?? ($member->user ? $member->user->email : null),
                'phone' => $member->phone ?? ($member->user ? $member->user->phone : null),
                'bio' => $member->user ? $member->user->bio : null,
                'profile_picture' => $member->profile_picture ?? ($member->user ? $member->user->profile_picture : null),
                'role' => $member->role,
                'is_trackflow_member' => $member->user_id !== null,
                'is_verified' => $member->user && $member->user->two_factor_enabled,
                'joined_at' => $member->created_at->format('M d, Y'),
            ];

            // Get primary UPI if the member has a linked user account
            if ($member->user_id) {
                $primaryUpi = \App\Models\UserUpi::where('user_id', $member->user_id)
                    ->where('is_primary', true)
                    ->where('is_active', true)
                    ->first();

                if ($primaryUpi) {
                    $profileData['upi'] = [
                        'name' => $primaryUpi->name,
                        'upi_id' => $primaryUpi->upi_id,
                        'qr_code_url' => $primaryUpi->qr_code_path ? asset('storage/' . $primaryUpi->qr_code_path) : null,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'profile' => $profileData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get member profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new settlement payment request
     */
    public function createSettlement(Request $request, $groupId)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $validator = Validator::make($request->all(), [
                'receiver_member_id' => 'required|exists:group_members,id',
                'amount' => 'required|numeric|min:0.01'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            // Verify current user is a member of this group
            $currentMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->first();

            if (!$currentMember) {
                return response()->json(['success' => false, 'message' => 'You are not a member of this group'], 403);
            }

            // Verify receiver is in the same group
            $receiver = GroupMember::where('id', $request->receiver_member_id)
                ->where('group_id', $groupId)
                ->first();

            if (!$receiver) {
                return response()->json(['success' => false, 'message' => 'Receiver not found in this group'], 404);
            }

            // Create settlement payment
            $settlement = SettlementPayment::create([
                'group_id' => $groupId,
                'payer_member_id' => $currentMember->id,
                'receiver_member_id' => $receiver->id,
                'amount' => $request->amount,
                'currency' => 'INR',
                'status' => SettlementPayment::STATUS_PENDING
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Settlement request created',
                'settlement' => $settlement
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create settlement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit payment proof (transaction ID and screenshot)
     */
    public function submitPaymentProof(Request $request, $groupId, $settlementId)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required|string|min:6|max:100',
                'proof_screenshot' => 'required|image|max:5120', // 5MB max - Required for auto-verification
                'upi_id_used' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            // Get settlement
            $settlement = SettlementPayment::where('id', $settlementId)
                ->where('group_id', $groupId)
                ->first();

            if (!$settlement) {
                return response()->json(['success' => false, 'message' => 'Settlement not found'], 404);
            }

            // Verify current user is the payer
            $currentMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->first();

            if (!$currentMember || $currentMember->id !== $settlement->payer_member_id) {
                return response()->json(['success' => false, 'message' => 'Only the payer can submit payment proof'], 403);
            }

            // Check for duplicate transaction ID
            if (SettlementPayment::isDuplicateTransaction($request->transaction_id, $settlementId)) {
                return response()->json(['success' => false, 'message' => 'This transaction ID has already been used'], 422);
            }

            // Handle screenshot upload
            $screenshotPath = null;
            if ($request->hasFile('proof_screenshot')) {
                $screenshotPath = $request->file('proof_screenshot')->store('settlement-proofs/' . $groupId, 'public');
            }

            // Update settlement with auto-verification (mandatory with screenshot)
            $settlement->update([
                'transaction_id' => $request->transaction_id,
                'proof_screenshot' => $screenshotPath,
                'upi_id_used' => $request->upi_id_used,
                'paid_at' => now(),
                'status' => SettlementPayment::STATUS_PAID // Auto-verified immediately
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'settlement' => $settlement->fresh(),
                'auto_verified' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit payment proof: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a payment (Admin/Leader only)
     */
    public function verifyPayment(Request $request, $groupId, $settlementId)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // Verify current user is a leader of this group
            $currentMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->first();

            if (!$currentMember || !$currentMember->isLeader()) {
                return response()->json(['success' => false, 'message' => 'Only group leaders can verify payments'], 403);
            }

            // Get settlement
            $settlement = SettlementPayment::where('id', $settlementId)
                ->where('group_id', $groupId)
                ->whereIn('status', [
                    SettlementPayment::STATUS_VERIFICATION_PENDING,
                    SettlementPayment::STATUS_AUTO_VERIFIED
                ])
                ->first();

            if (!$settlement) {
                return response()->json(['success' => false, 'message' => 'Settlement not found or not awaiting verification'], 404);
            }

            // Update settlement to paid
            $settlement->update([
                'status' => SettlementPayment::STATUS_PAID,
                'verified_by' => $userId,
                'verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'settlement' => $settlement->fresh()->load(['payer', 'receiver'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a payment (Admin/Leader only)
     */
    public function rejectPayment(Request $request, $groupId, $settlementId)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            // Verify current user is a leader of this group
            $currentMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->first();

            if (!$currentMember || !$currentMember->isLeader()) {
                return response()->json(['success' => false, 'message' => 'Only group leaders can reject payments'], 403);
            }

            // Get settlement
            $settlement = SettlementPayment::where('id', $settlementId)
                ->where('group_id', $groupId)
                ->whereIn('status', [
                    SettlementPayment::STATUS_VERIFICATION_PENDING,
                    SettlementPayment::STATUS_AUTO_VERIFIED
                ])
                ->first();

            if (!$settlement) {
                return response()->json(['success' => false, 'message' => 'Settlement not found or not awaiting verification'], 404);
            }

            // Update settlement to rejected
            $settlement->update([
                'status' => SettlementPayment::STATUS_REJECTED,
                'rejection_reason' => $request->reason,
                'verified_by' => $userId,
                'verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected',
                'settlement' => $settlement->fresh()->load(['payer', 'receiver'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all pending settlements for a group (Admin/Leader only)
     */
    public function getPendingSettlements(Request $request, $groupId)
    {
        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // Verify current user is a member of this group
            $currentMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->first();

            if (!$currentMember) {
                return response()->json(['success' => false, 'message' => 'You are not a member of this group'], 403);
            }

            // Get settlements query
            $query = SettlementPayment::where('group_id', $groupId)
                ->with(['payer', 'receiver', 'verifier']);

            // If leader, show all. Otherwise show only user's settlements
            if (!$currentMember->isLeader()) {
                $query->where(function ($q) use ($currentMember) {
                    $q->where('payer_member_id', $currentMember->id)
                        ->orWhere('receiver_member_id', $currentMember->id);
                });
            }

            $settlements = $query->orderBy('created_at', 'desc')->get();

            // Group by status
            $grouped = [
                'pending' => $settlements->where('status', SettlementPayment::STATUS_PENDING)->values(),
                'awaiting_verification' => $settlements->whereIn('status', [
                    SettlementPayment::STATUS_VERIFICATION_PENDING,
                    SettlementPayment::STATUS_AUTO_VERIFIED
                ])->values(),
                'paid' => $settlements->where('status', SettlementPayment::STATUS_PAID)->values(),
                'rejected' => $settlements->where('status', SettlementPayment::STATUS_REJECTED)->values()
            ];

            return response()->json([
                'success' => true,
                'settlements' => $settlements,
                'grouped' => $grouped,
                'is_leader' => $currentMember->isLeader()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get settlements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show public payment request page (No auth required)
     * Token format: base64(upiId|amount|name)
     */
    public function showPaymentRequest($token)
    {
        try {
            // Decode token
            $decoded = base64_decode($token);
            if (!$decoded) {
                abort(404, 'Invalid payment link');
            }

            $parts = explode('|', $decoded);
            if (count($parts) < 3) {
                abort(404, 'Invalid payment link');
            }

            $upiId = $parts[0];
            $amount = floatval($parts[1]);
            $name = $parts[2];

            // Validate data
            if (empty($upiId) || $amount <= 0) {
                abort(404, 'Invalid payment details');
            }

            // Generate UPI deep link
            $upiLink = 'upi://pay?' . http_build_query([
                'pa' => $upiId,
                'pn' => $name,
                'am' => number_format($amount, 2, '.', ''),
                'cu' => 'INR',
                'tn' => 'TrackFlow_Payment'
            ]);

            return view('group-expense.payment-request', [
                'upiId' => $upiId,
                'amount' => $amount,
                'name' => $name,
                'upiLink' => $upiLink
            ]);

        } catch (\Exception $e) {
            abort(404, 'Payment link expired or invalid');
        }
    }
}
