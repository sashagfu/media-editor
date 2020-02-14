<?php

namespace App\Http\Controllers\Api;

use App\Models\Flag;
use App\Models\FlagReason;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Models\Post;

class FlagController extends Controller
{
    public function createNewFlag(Request $request)
    {
        $this->validate(
            $request,
            [
            'reason_id' => 'required',
            'post_id' => 'required',
            'reason_comment' => 'required',
            ]
        );

        $user_id = AuthHelper::myId();
        $post_id = $request->post_id;
        $type = DBHelper::getMapByModel(Post::class);
        $post = Post::find($post_id);
        $reason_id = $request->reason_id;
        $reason = FlagReason::findOrFail($reason_id)->title;
        $reason_comment = $request->reason_comment;

        $existing_flag = Flag::whereFlaggableId($post_id)
            ->whereFlaggableType($type)
            ->whereAuthorId($user_id)
            ->first();

        if ($existing_flag) {
            return response()->json(
                [
                'exist_error' => trans('flags.flag_exists')
                ],
                422
            );
        } else {
            $flag = new Flag();
            $flag->flaggable()->associate($post);
            $flag->description =  $reason . '<br/>' .  $reason_comment;
            $flag->author_id = $user_id;
            $flag->save();

            return trans('flags.flag_added');
        }
    }
}
