@extends('email-template.layout')

@section('content')
    <p class="greeting">Hello {{ $userName ?? 'User' }} 👋</p>

    @php
        $actionType = $actionType ?? 'expense_added';
        $isExpense = ($transactionType ?? 'expense') === 'expense';
    @endphp

    {{-- Different messages based on action type --}}
    @if($actionType === 'group_created')
        <p class="message">
            Congratulations! You have successfully created the group <strong>{{ $groupName ?? 'Group' }}</strong>. You are now
            the group leader.
        </p>
    @elseif($actionType === 'expense_added')
        <p class="message">
            A new expense has been added to your group <strong>{{ $groupName ?? 'Group' }}</strong>:
        </p>
    @elseif($actionType === 'member_added')
        <p class="message">
            You have been added to the group <strong>{{ $groupName ?? 'Group' }}</strong> by
            {{ $addedBy ?? 'the group leader' }}.
        </p>
    @elseif($actionType === 'new_member_joined')
        <p class="message">
            <strong>{{ $newMemberName ?? 'A new member' }}</strong> has been added to your group
            <strong>{{ $groupName ?? 'Group' }}</strong> by {{ $addedBy ?? 'the group leader' }}.
        </p>
    @elseif($actionType === 'settlement_request')
        <p class="message">
            You have a settlement request in <strong>{{ $groupName ?? 'Group' }}</strong>:
        </p>
    @elseif($actionType === 'settlement_completed')
        <p class="message">
            A settlement has been completed in <strong>{{ $groupName ?? 'Group' }}</strong>:
        </p>
    @elseif($actionType === 'member_removed')
        <p class="message">
            You have been removed from the group <strong>{{ $groupName ?? 'Group' }}</strong>.
        </p>
    @elseif($actionType === 'group_deleted')
        <p class="message">
            The group <strong>{{ $groupName ?? 'Group' }}</strong> has been deleted.
        </p>
    @elseif($actionType === 'balance_reminder')
        <p class="message">
            This is a reminder about your pending balance in <strong>{{ $groupName ?? 'Group' }}</strong>:
        </p>
    @endif

    {{-- Group Created Card --}}
    @if($actionType === 'group_created')
        <div class="transaction-card">
            <div class="transaction-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                👑
            </div>

            <p class="transaction-type">Group Leader</p>

            <table class="data-table">
                @if(isset($groupName))
                    <tr>
                        <td>📋 Group Name</td>
                        <td>{{ $groupName }}</td>
                    </tr>
                @endif
                @if(isset($groupDescription) && $groupDescription)
                    <tr>
                        <td>📝 Description</td>
                        <td>{{ $groupDescription }}</td>
                    </tr>
                @endif
                @if(isset($groupCode))
                    <tr>
                        <td>🔗 Invite Code</td>
                        <td><strong>{{ $groupCode }}</strong></td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="alert-box info">
            <p class="alert-title">💡 Share Your Group</p>
            <p class="alert-message">
                Share the invite code <strong>{{ $groupCode ?? '' }}</strong> with friends and family to let them join your
                group!
            </p>
        </div>
    @endif

    {{-- New Member Joined Card (for existing members) --}}
    @if($actionType === 'new_member_joined')
        <div class="transaction-card">
            <div class="transaction-icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                👤
            </div>

            <p class="transaction-type">New Member</p>

            <table class="data-table">
                <tr>
                    <td>👤 Name</td>
                    <td>{{ $newMemberName ?? 'New Member' }}</td>
                </tr>
                @if(isset($totalMembers))
                    <tr>
                        <td>👥 Total Members</td>
                        <td>{{ $totalMembers }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    {{-- Expense/Transaction Card --}}
    @if(in_array($actionType, ['expense_added', 'settlement_request', 'settlement_completed']))
        <div class="transaction-card">
            <div class="transaction-icon {{ $isExpense ? 'expense' : 'income' }}">
                @if($actionType === 'settlement_completed' || $actionType === 'settlement_request')
                    💸
                @else
                    {{ $isExpense ? '↑' : '↓' }}
                @endif
            </div>

            <p class="transaction-type">
                @if($actionType === 'settlement_completed')
                    Settlement Completed
                @elseif($actionType === 'settlement_request')
                    Settlement Request
                @else
                    {{ ucfirst($transactionType ?? 'Expense') }}
                @endif
            </p>

            <p class="transaction-amount {{ $isExpense ? 'expense' : 'income' }}">
                {{ $currency ?? '₹' }}{{ number_format($amount ?? 0, 2) }}
            </p>

            <table class="data-table">
                @if(isset($description))
                    <tr>
                        <td>📝 Description</td>
                        <td>{{ $description }}</td>
                    </tr>
                @endif
                @if(isset($paidBy))
                    <tr>
                        <td>💳 Paid By</td>
                        <td>{{ $paidBy }}</td>
                    </tr>
                @endif
                @if(isset($yourShare))
                    <tr>
                        <td>👤 Your Share</td>
                        <td style="color: #f87171;">{{ $currency ?? '₹' }}{{ number_format($yourShare, 2) }}</td>
                    </tr>
                @endif
                @if(isset($category))
                    <tr>
                        <td>🏷️ Category</td>
                        <td>{{ $category }}</td>
                    </tr>
                @endif
                @if(isset($transactionDate))
                    <tr>
                        <td>📅 Date</td>
                        <td>{{ $transactionDate }}</td>
                    </tr>
                @endif
                @if(isset($splitAmong))
                    <tr>
                        <td>👥 Split Among</td>
                        <td>{{ $splitAmong }} members</td>
                    </tr>
                @endif
                @if(isset($fromMember) && isset($toMember))
                    <tr>
                        <td>📤 From</td>
                        <td>{{ $fromMember }}</td>
                    </tr>
                    <tr>
                        <td>📥 To</td>
                        <td>{{ $toMember }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    {{-- Member Added Card --}}
    @if($actionType === 'member_added')
        <div class="transaction-card">
            <div class="transaction-icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                👥
            </div>

            <p class="transaction-type">Welcome to {{ $groupName ?? 'the group' }}!</p>

            <table class="data-table">
                @if(isset($groupDescription))
                    <tr>
                        <td>📋 Description</td>
                        <td>{{ $groupDescription }}</td>
                    </tr>
                @endif
                @if(isset($totalMembers))
                    <tr>
                        <td>👥 Total Members</td>
                        <td>{{ $totalMembers }}</td>
                    </tr>
                @endif
                @if(isset($groupLeader))
                    <tr>
                        <td>👑 Group Leader</td>
                        <td>{{ $groupLeader }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    {{-- Balance Reminder Card --}}
    @if($actionType === 'balance_reminder')
        <div class="transaction-card">
            <div class="transaction-icon {{ ($netBalance ?? 0) >= 0 ? 'income' : 'expense' }}">
                💰
            </div>

            <p class="transaction-type">Your Balance</p>

            <p class="transaction-amount {{ ($netBalance ?? 0) >= 0 ? 'income' : 'expense' }}">
                {{ ($netBalance ?? 0) >= 0 ? '+' : '' }}{{ $currency ?? '₹' }}{{ number_format($netBalance ?? 0, 2) }}
            </p>

            <table class="data-table">
                @if(isset($youOwe) && $youOwe > 0)
                    <tr>
                        <td>📤 You Owe</td>
                        <td style="color: #f87171;">{{ $currency ?? '₹' }}{{ number_format($youOwe, 2) }}</td>
                    </tr>
                @endif
                @if(isset($youAreOwed) && $youAreOwed > 0)
                    <tr>
                        <td>📥 You Are Owed</td>
                        <td style="color: #34d399;">{{ $currency ?? '₹' }}{{ number_format($youAreOwed, 2) }}</td>
                    </tr>
                @endif
            </table>
        </div>

        @if(isset($settlements) && count($settlements) > 0)
            <div class="alert-box info">
                <p class="alert-title">📋 Suggested Settlements</p>
                @foreach($settlements as $settlement)
                    <p class="alert-message" style="margin-bottom: 8px;">
                        @if($settlement['type'] === 'pay')
                            Pay <strong>{{ $currency ?? '₹' }}{{ number_format($settlement['amount'], 2) }}</strong> to
                            {{ $settlement['to'] }}
                        @else
                            Receive <strong>{{ $currency ?? '₹' }}{{ number_format($settlement['amount'], 2) }}</strong> from
                            {{ $settlement['from'] }}
                        @endif
                    </p>
                @endforeach
            </div>
        @endif
    @endif

    {{-- Action Buttons --}}
    @if(!in_array($actionType, ['member_removed', 'group_deleted']))
        <div class="button-container">
            <a href="{{ config('app.url', 'https://trackflow.app') }}/groups/{{ $groupId ?? '' }}" class="button">
                View Group Details
            </a>
        </div>
    @endif

    {{-- Settlement Action --}}
    @if($actionType === 'balance_reminder' && isset($netBalance) && $netBalance < 0)
        <div class="button-container">
            <a href="{{ config('app.url', 'https://trackflow.app') }}/groups/{{ $groupId ?? '' }}/settle" class="button"
                style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                Settle Up Now
            </a>
        </div>
    @endif

    <p class="message" style="text-align: center; color: #718096; font-size: 13px;">
        You can manage your group expense notifications in your <a
            href="{{ config('app.url', 'https://trackflow.app') }}/settings/notifications"
            style="color: #0d9488;">notification settings</a>.
    </p>
@endsection