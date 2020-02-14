<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Circle;
use App\Models\Invite;
use Illuminate\Auth\Access\HandlesAuthorization;

class CirclePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the circle.
     *
     * @param  \App\Models\User   $user
     * @param  \App\Models\Circle $circle
     * @return mixed
     */
    public function view(User $user, Circle $circle)
    {
        if ($circle->type == Circle::TYPE_SECRET) {
            return $user->id === (int)$circle->creator_id || $circle->members->contains($user);
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can create circles.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the circle.
     *
     * @param  \App\Models\User   $user
     * @param  \App\Models\Circle $circle
     * @return mixed
     */
    public function updateSettings(User $user, Circle $circle)
    {
        return $user->id === (int)$circle->creator_id;
    }

    /**
     * Determine whether the user can delete the circle.
     *
     * @param  \App\Models\User   $user
     * @param  \App\Models\Circle $circle
     * @return mixed
     */
    public function delete(User $user, Circle $circle)
    {
        return $user->id === (int)$circle->creator_id;
    }

    /**
     * Determine wheter the user can see timeline
     *
     * @param  User   $user
     * @param  Circle $circle
     * @return bool
     */

    public function seeFeed(User $user, Circle $circle)
    {
        if ($circle->type == Circle::TYPE_SECRET || $circle->type == Circle::TYPE_CLOSED) {
            return $user->id === (int)$circle->creator_id || $circle->members->contains($user);
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can add post to timeline
     *
     * @param  User   $user
     * @param  Circle $circle
     * @return bool
     */
    public function addPost(User $user, Circle $circle)
    {
        if ($circle->post_adding_privacy == Circle::POST_ADDING_ADMINS) {
            return $circle->members()->wherePivot('status', Circle::STATUS_ADMIN)->get()->contains($user);
        } elseif ($circle->post_adding_privacy == Circle::POST_ADDING_MEMBERS) {
            return $circle->members->contains($user);
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can join the circle
     *
     * @param  User   $user
     * @param  Circle $circle
     * @return bool
     */
    public function join(User $user, Circle $circle)
    {
        if ($circle->type == Circle::TYPE_SECRET) {
            // TODO: If user has invite to a group
            // Temporarily forbidden for all members
            return false;
        } elseif ($circle->type == Circle::TYPE_CLOSED) {
            return (!$circle->requestingUsers->contains($user) && !$circle->members->contains($user));
        } elseif ($circle->type == Circle::TYPE_OPEN) {
            return !$circle->isMember($user);
        } else {
            return false;
        }
    }

    /**
     * Determine whether user can cancel his Request
     *
     * @param  User   $user
     * @param  Circle $circle
     * @return bool
     */
    public function cancelRequest(User $user, Circle $circle)
    {
        if ($circle->type == Circle::TYPE_CLOSED) {
            return $circle->requestingUsers->contains($user);
        } else {
            return false;
        }
    }

    /**
     * Determines whether the user can see members
     *
     * @param  User   $user
     * @param  Circle $circle
     * @return bool
     */
    public function seeMembers(User $user, Circle $circle)
    {
        $status = $circle->members_block_privacy;

        if ($status == Circle::MEMBERS_BLOCK_VISIBILITY_ALL) {
            return true;
        } elseif ($status == Circle::MEMBERS_BLOCK_VISIBILITY_MEMBERS) {
            return $circle->members->contains($user);
        }
        return false;
    }

    public function beInvited(User $user, Circle $circle, $email = null)
    {
        $email = $email ? $email : $user->email;

        if ($circle->members->contains($user)) {
            return false;
        } elseif ($circle->requestingUsers->contains($user)) {
            return false;
        } elseif (Invite::whereEmail($email)->where('circle_id', $circle->id)->exists()
        ) {
            return false;
        } else {
            return true;
        }
    }
}
