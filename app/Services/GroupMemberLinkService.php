<?php

namespace App\Services;

use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupMemberLinkService
{
    /**
     * Link a user to any pending group members that match their email
     * This is called when a user registers or logs in
     * 
     * @param User $user The user to link
     * @return array Array of linked group member IDs
     */
    public static function linkUserToGroupMembers(User $user): array
    {
        $linkedMembers = [];

        try {
            // Find all group members with matching email that don't have a user_id yet
            $pendingMembers = GroupMember::where('email', $user->email)
                ->whereNull('user_id')
                ->get();

            if ($pendingMembers->isEmpty()) {
                return $linkedMembers;
            }

            DB::beginTransaction();

            foreach ($pendingMembers as $member) {
                // Update the group member to link to this user
                $member->update([
                    'user_id' => $user->id,
                    'name' => $user->name, // Update name to match user's actual name
                    'picture' => $user->profile_picture ?? $member->picture, // Use user's picture if available
                    'phone' => $user->phone ?? $member->phone, // Use user's phone if available
                    'last_active_at' => now()
                ]);

                $linkedMembers[] = $member->id;

                Log::info("Linked user {$user->id} ({$user->email}) to group member {$member->id} in group {$member->group_id}");
            }

            DB::commit();

            return $linkedMembers;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to link user {$user->id} to group members: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if a user has any pending group memberships waiting to be linked
     * 
     * @param string $email Email to check
     * @return int Number of pending memberships
     */
    public static function countPendingMemberships(string $email): int
    {
        return GroupMember::where('email', $email)
            ->whereNull('user_id')
            ->count();
    }

    /**
     * Get all groups that a user should be linked to based on their email
     * 
     * @param string $email Email to check
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPendingGroups(string $email)
    {
        return GroupMember::where('email', $email)
            ->whereNull('user_id')
            ->with('group')
            ->get();
    }
}
