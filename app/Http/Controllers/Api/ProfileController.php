<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use App\Models\User;
use App\Models\Image;

class ProfileController extends Controller
{
    public function saveLocation(Request $request)
    {
        $location = json_encode(
            [
            'lat' => $request->lat,
            'lng' => $request->lng ]
        );

        $user = AuthHelper::me();

        $user->setSetting('location', $location);
    }

    public function getUserByProfile($username)
    {
        return User::whereUsername($username)->firstOrFail();
    }

    public function getUnreadNotifications()
    {
        $user = AuthHelper::me();
        return $user->unreadNotifications()->count();
    }

    public function getCurrentUser()
    {
        return AuthHelper::me();
    }

    public function updateProfile(Request $request)
    {
        $user_id = AuthHelper::myId();
        $user = AuthHelper::me();
        $gp = $request->google_plus;
        $fb = $request->facebook;
        $inst = $request->instagram;
        $li = $request->linkedin;
        $sp = $request->snapchat;

        $this->validate(
            $request,
            [
            'username'     => 'required|max:20|unique:users,username,' . $user_id,
            'display_name' => 'required|max:20|unique:users,display_name,' . $user_id,
            'talent'       => 'required',
            'quote'        => 'required',
            'avatar'       => 'mimes:jpeg,jpg,png|max:2000',
            'google_plus'  => 'url',
            'facebook'     => 'url',
            'instagram'    => 'url',
            'linkedin'     => 'url',
            'snapchat'     => 'url',
            ]
        );

        $socials = [
            'google_plus' => $gp,
            'facebook'    => $fb,
            'instagram'   => $inst,
            'linkedin'    => $li,
            'snapchat'    => $sp,
        ];

        $user->username = $request->username;
        $user->display_name = $request->display_name;
        $user->talent = $request->talent;
        $user->quote = $request->quote;
        $user->setSetting('socials', $socials);

        if ($request->latLng) {
            $user->setSetting('location', $request->latLng);
        }

        $user->save();

        return $user;
    }

    public function updateAvatarProfile(Request $request)
    {
        $this->validate(
            $request,
            [
            'avatar'       => 'required|mimes:jpeg,jpg,png|max:2000',
            ]
        );

        $user = AuthHelper::me();

        $avatar = $request->avatar;
        $image = Image::newUserAvatarFromForm($avatar);
        $user->avatars->add($image);
        return $user;
    }

    public function getUserVideos()
    {
        $user = AuthHelper::me();
        return $user->videos()->where('is_performance', true)->latest()->get();
    }

    public function getMentionUsers()
    {
        return User::where('is_verified', true)->get();
    }
}
