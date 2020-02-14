<?php

namespace App\Http\GraphQL\Mutations;

use App\Helpers\AuthHelper;
use App\Http\GraphQL\Validators\UpdateUserValidator;
use App\Models\Image;
use App\Models\User;
use App\Notifications\NewFollowerNotification;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Nuwave\Lighthouse\Execution\Utils\Subscription;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Validator;

class UserMutator
{
    public function update($root, $args, $context)
    {

        $data = $args['user'];

        $user = User::find($data['id']);

        $user->username = $data['username'];
        $user->display_name = $data['displayName'];
        $user->bio = $data['bio'];

        if (isset($args['socials'])) {
            $socials = [
                'google_plus' => $data['socials']['googlePlus'] ?? null,
                'facebook'    => $data['socials']['facebook'] ?? null,
                'instagram'   => $data['socials']['instagram'] ?? null,
                'linkedin'    => $data['socials']['linkedin'] ?? null,
                'snapchat'    => $data['socials']['snapchat'] ?? null,
            ];
            $user->setSetting('socials', $socials);
        }

        if (isset($data['latLng'])) {
            $user->setSetting('location', $data['latLng']);
        }

        $user->save();

        return $user;
    }

    public function follow($rootValue, array $args)
    {
        $user_id = $args['userId'];

        $follower = Auth::user();

        $user = User::find($user_id);

        if ($user->followers->where('id', $follower->id)->first()) {
            throw new \Exception(
                trans(
                    'users.follower_already_exist',
                    [
                        'user' => $user->display_name,
                    ]
                )
            );
        }

        $user->followers()
             ->attach($follower->id);

        $user->notify(new NewFollowerNotification($user, $follower));

        return $user;
    }

    public function changePassword($root, $args)
    {
        $user = Auth::user();

        $old_password = $args['oldPassword'];
        $new_password = $args['newPassword'];

        if (Hash::check($old_password, $user->password)) {
            $user->password = bcrypt($new_password);
            $user->save();

            return response()->json([
               'message' => 'Password changed successfully',
               'statusCode' => 200,
            ]);
        }

        throw new \Exception('You specified wrong old password');
    }

    public function unfollow($rootValue, array $args)
    {
        $user_id = $args['userId'];

        $follower = Auth::user();

        $user = User::find($user_id);

        if (!$user->followers->where('id', $follower->id)->first()) {
            throw new \Exception(
                trans(
                    'users.follower_does_not_exist',
                    [
                        'user' => $user->display_name,
                    ]
                )
            );
        }

        $user->followers()->detach($follower->id);

        $follower->old_following = $user;

        Subscription::broadcast('followerDeleted', $follower);

        return $user;
    }

    public function createAvatar($root, $args)
    {
        $user = AuthHelper::me();

        $avatar = $args['url'];
        $avatars_dir = $avatars_dir = 'users/' . $user->id . '/avatars/';
        if (!$avatar) {
            Storage::disk('s3')->deleteDirectory($avatars_dir);
            $user->avatars()->delete();
            return $user;
        }
        $image = Image::newUserAvatarFromForm($avatar);
        $user->avatars->add($image);

        return $user;
    }

    public function updateUserSettings($root, $args)
    {
        $types = $args['settings'];

        $user = Auth::user();

        foreach ($types as $type => $value) {
            $user->setSetting($type, $value);
        }

        return $user->settings;
    }
}
