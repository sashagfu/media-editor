<?php

namespace App\Http\Controllers\Api;

use App\Mail\CircleInvitation;
use App\Models\Invite;
use App\Notifications\CircleInviteNotification;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\Circle;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use Session;
use Mail;
use App\Models\Image;

class CircleController extends Controller
{
    /**
     * Handle Membership request to circle
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function handleCircleMembership(Request $request)
    {
        $circle_slug = $request->slug;

        $user = AuthHelper::me();
        $circle = Circle::whereSlug($circle_slug)->firstOrFail();

        if ($user->can('join', $circle)) {
            if ($circle->type == Circle::TYPE_OPEN) {
                $circle->members()->attach($user, ['status' => Circle::STATUS_MEMBER]);
                return [
                    'text' => trans('circles.leave'),
                    'circle_slug' => $circle->slug,
                ];
            } elseif ($circle->type == Circle::TYPE_CLOSED) {
                $circle->requestingUsers()->attach($user, ['status' => Circle::STATUS_PENDING]);
                return [
                    'text' => trans('circles.request-pending'),
                    'circle_slug' => $circle->slug,
                ];
            } else {
                return response()->json(['error' => true], 422);
            }
        } else {
            $circle->members()->detach($user);
            return [
                'text' => trans('circles.request-pending'),
                'circle_slug' => $circle->slug,
            ];
        }
    }

    /**
     * Approve user request to circle
     *
     * @param  Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function approveRequest(Request $request)
    {
        $requester_id = $request->user_id;
        $circle_slug = $request->slug;

        $user = AuthHelper::me();
        $requester = User::find($requester_id);
        $circle = Circle::whereSlug($circle_slug)->firstOrFail();
        $requests = $circle->requestingUsers;

        if ($user->can('updateSettings', $circle)) {
            if ($requests->contains($requester)) {
                $circle->requestingUsers()->updateExistingPivot($requester_id, ['status' => Circle::STATUS_MEMBER]);
                return ['success' => true];
            } else {
                return response()->json(['error' => true], 422);
            }
        }
        return response()->json(['error' => true], 422);
    }

    /**
     * Delete user request to circle
     *
     * @param  Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function cancelRequest(Request $request)
    {
        $requester_id = $request->user_id;
        $circle_slug = $request->slug;

        $user = AuthHelper::me();
        $requester = User::find($requester_id);
        $circle = Circle::whereSlug($circle_slug)->firstOrFail();
        $requests = $circle->requestingUsers;

        if ($user->can('updateSettings', $circle)) {
            if ($requests->contains($requester)) {
                $circle->requestingUsers()->detach($requester_id);
                return ['success' => true];
            } else {
                return response()->json(['error' => true], 422);
            }
        }

        return response()->json(['error' => true], 422);
    }

    public function cancelSelfRequest(Request $request)
    {
        $circle_slug = $request->slug;

        $user = AuthHelper::me();
        $circle = Circle::whereSlug($circle_slug)->firstOrFail();

        if ($user->can('cancelRequest', $circle)) {
            $circle->requestingUsers()->detach($user);
            return ['success' => true];
        }
        return response()->json(['error' => true], 422);
    }

    public function inviteByEmail(Request $request)
    {
        $admin = AuthHelper::me();
        $circle_slug = $request->circle_slug;
        $circle = Circle::whereSlug($circle_slug)->firstOrFail();

        if ($admin->can('updateSettings', $circle)) {
            $emails = $request->emails;
            $this->validate(
                $request,
                [
                'emails' => 'required',
                ]
            );
            foreach ($emails as $email) {
                $user = User::whereEmail($email)->first();
                $date = Carbon::now()->addDays(Invite::EXPIRATION_DAYS);
                if ($user) {
                    if ($user->can('beInvited', $circle)) {
                        $invite = new Invite();
                        $invite->id = Uuid::uuid4()->toString();
                        $invite->email = $user->email;
                        $invite->user_id = $user->id;
                        $invite->circle_id = $circle->id;
                        $invite->active = true;
                        $invite->expiration = $date;
                        $invite->save();

                        $user->notify(new CircleInviteNotification($admin, $circle, $invite));
                    }
                } else {
                    $invite_check = Invite::whereEmail($email)
                    ->where('circle_id', $circle->id)
                    ->exists();

                    if (!$invite_check) {
                        $invite = new Invite();
                        $invite->id = Uuid::uuid4()->toString();
                        $invite->email = $email;
                        $invite->circle_id = $circle->id;
                        $invite->active = true;
                        $invite->expiration = $date;
                        $invite->save();

                        Mail::to($email)->send(new CircleInvitation($circle, $invite));
                    }
                }
            }
            return ['success' => true];
        } else {
            return response()->json(['error' => trans('common.error_reload')], 422);
        }
    }

    public function inviteExisting(Request $request)
    {
        $admin = AuthHelper::me();
        $circle_slug = $request->circle_slug;
        $circle = Circle::whereSlug($circle_slug)->firstOrFail();

        if ($admin->can('updateSettings', $circle)) {
            $users = $request->users;
            $this->validate(
                $request,
                [
                'users' => 'required',
                ]
            );
            foreach ($users as $user) {
                $user = User::find($user['id']);
                $date = Carbon::now()->addDays(Invite::EXPIRATION_DAYS);
                if ($user->can('beInvited', $circle)) {
                    $invite = new Invite();
                    $invite->id = Uuid::uuid4()->toString();
                    $invite->email = $user->email;
                    $invite->user_id = $user->id;
                    $invite->circle_id = $circle->id;
                    $invite->active = true;
                    $invite->expiration = $date;
                    $invite->save();

                    $user->notify(new CircleInviteNotification($admin, $circle, $invite));
                }
            }
            return ['success' => true];
        } else {
            return response()->json(['error' => trans('common.error_reload')], 422);
        }
    }


    /**
     * Remove the invite from database
     *
     * @param  $id
     * @return array|bool
     */
    public function cancelInvite(Request $request)
    {
        $invite_id = $request->invite_id;
        $notification_id = $request->notification_id;
        $invite = Invite::findorFail($invite_id);
        $user = AuthHelper::me();
        $notification = $user->notifications()->where('id', $notification_id)->first();

        if ($user->email != $invite->email) {
            return false;
        } else {
            $invite->delete();
            $notification->delete();
        }
    }



    public function acceptInvite(Request $request)
    {
        $invite_id = $request->invite_id;
        $notification_id = $request->notification_id;
        $invite = Invite::findorFail($invite_id);
        $user = AuthHelper::me();
        $notification = $user->notifications()->where('id', $notification_id)->first();
        $circle = Circle::find($invite->circle_id);

        if ($user->email != $invite->email) {
            return false;
        } else {
            $invite->used = true;
            $notification->delete();
            $invite->save();
            $circle->members()->attach($user, ['status' => Circle::STATUS_MEMBER]);

            return ['redirect_url' => route('circle.single', ['slug' => $circle->slug])];
        }
    }

    public function updateSettings(Request $request)
    {
        $slug = $request->circle_slug;

        $this->validate(
            $request,
            [
            'title'                 => 'required|min:3',
            'description'           => 'required|min:3|max:300',
            'type'                  => 'required',
            'cover'                 => 'mimes:jpeg,jpg,png|max:2000',
            'post_adding_privacy'   => 'required',
            'members_block_privacy' => 'required',
            ]
        );

        $cover = $request->cover;

        $circle = Circle::whereSlug($slug)->firstOrFail();

        $circle->update($request->all());


        if ($cover) {
            $image = Image::newCircleCover($cover, $circle->id);
            $circle->covers->add($image);
        }
        $redirect_url = route('circle.single', ['slug' => $slug]);
        return $redirect_url;
    }
}
