<?php

namespace App\Models;

use App\Helpers\AuthHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cmgmyr\Messenger\Models\Models;

/**
 * App\Models\Thread
 *
 * @property    int $id
 * @property    string $subject
 * @property    int $creator_id
 * @property    \Carbon\Carbon $created_at
 * @property    \Carbon\Carbon $updated_at
 * @property    \Carbon\Carbon $deleted_at
 * @property-read mixed $avatar
 * @property-read string $conversationer
 * @property-read string $latest_message
 * @property-read mixed $is_unread
 * @property-read \Illuminate\Contracts\Translation\Translator|string $latest_author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\Cmgmyr\Messenger\Models\Participant[] $participants
 * @method  static \Illuminate\Database\Query\Builder|\Cmgmyr\Messenger\Models\Thread between($participants)
 * @method  static \Illuminate\Database\Query\Builder|\Cmgmyr\Messenger\Models\Thread forUser($userId)
 * @method  static \Illuminate\Database\Query\Builder|\Cmgmyr\Messenger\Models\Thread forUserWithNewMessages($userId)
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread whereCreatedAt($value)
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread whereCreatorId($value)
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread whereDeletedAt($value)
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread whereId($value)
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread whereSubject($value)
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread whereUpdatedAt($value)
 * @mixin   \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method  static bool|null forceDelete()
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread onlyTrashed()
 * @method  static bool|null restore()
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread withTrashed()
 * @method  static \Illuminate\Database\Query\Builder|\App\Models\Thread withoutTrashed()
 */
class Thread extends \Cmgmyr\Messenger\Models\Thread
{
    const GROUP_CHAT_STRING_LENGTH = 35;
    use SoftDeletes;

    protected $appends = ['avatar', 'conversationer', 'latestMessage', 'latestAuthor', 'isUnread'];

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['subject', 'creator_id', 'opened'];

    /**
     * Return the latest message if exists
     *
     * @return string
     */
    public function getLatestMessageAttribute()
    {
        if ($this->messages->isEmpty()) {
            return '';
        }
        $last_message = $this->messages()->latest()->first();
        if (str_contains($last_message->body, env('APP_URL').'/post/')) {
            return trans('posts.shared_post');
        } else {
            return $this->messages()->latest()->first()->body;
        }
    }

    /**
     * Returns the latest author string if needed
     *
     * @return \Illuminate\Contracts\Translation\Translator|string
     */
    public function getlatestAuthorAttribute()
    {
        if ($this->latestMessage) {
            $last_message = $this->messages()->latest()->first();
            $author = $last_message->user;
            $user = AuthHelper::me();

            if ($author == $this->creator() && $author == $user) {
                return trans('chat.you');
            } else {
                return '';
            }
        } else {
            return '';
        }
    }


    public function getisUnreadAttribute()
    {
        $user_id = AuthHelper::myId();
        if ($this->userUnreadMessagesCount($user_id) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns the avatar url for another user(s) in thread
     *
     * @return string
     */
    public function participantsString($userId = null, $columns = ['display_name'])
    {
        $participantsTable = Models::table('participants');
        $usersTable = Models::table('users');
        $userPrimaryKey = Models::user()->getKeyName();

        $selectString = $this->createSelectString($columns);

        $participantNames = $this->getConnection()->table($usersTable)
            ->join($participantsTable, $usersTable . '.' . $userPrimaryKey, '=', $participantsTable . '.user_id')
            ->where($participantsTable . '.thread_id', $this->id)
            ->select($this->getConnection()->raw($selectString));

        if ($userId !== null) {
            $participantNames->where($usersTable . '.' . $userPrimaryKey, '!=', $userId);
        }

        return $participantNames->implode('name', ', ');
    }

    /**
     * Checks to see if a user is a current participant of the thread.
     *
     * @param $userId
     *
     * @return bool
     */
    public function hasParticipant($userId)
    {
        $participants = $this->participants()->where('user_id', '=', $userId);
        if ($participants->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Generates a select string used in participantsString().
     *
     * @param $columns
     *
     * @return string
     */
    protected function createSelectString($columns)
    {
        $dbDriver = $this->getConnection()->getDriverName();
        $tablePrefix = $this->getConnection()->getTablePrefix();
        $usersTable = Models::table('users');

        switch ($dbDriver) {
            case 'pgsql':
            case 'sqlite':
                $columnString = implode(" || ' ' || " . $tablePrefix . $usersTable . '.', $columns);
                $selectString = '(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
                break;
            case 'sqlsrv':
                $columnString = implode(" + ' ' + " . $tablePrefix . $usersTable . '.', $columns);
                $selectString = '(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
                break;
            default:
                $columnString = implode(", ' ', " . $tablePrefix . $usersTable . '.', $columns);
                $selectString = 'concat(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
        }

        return $selectString;
    }

    /**
     * Returns array of unread messages in thread for given user.
     *
     * @param $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function userUnreadMessages($userId)
    {
        $messages = $this->messages()->get();

        try {
            $participant = $this->getParticipantFromUser($userId);
        } catch (ModelNotFoundException $e) {
            return collect();
        }

        if (!$participant->last_read) {
            return $messages;
        }

        return $messages->filter(
            function ($message) use ($participant) {
                return $message->updated_at->gt($participant->last_read);
            }
        );
    }

    /**
     * Returns count of unread messages in thread for given user.
     *
     * @param $userId
     *
     * @return int
     */
    public function userUnreadMessagesCount($userId)
    {
        return $this->userUnreadMessages($userId)->count();
    }

    public function getAvatarAttribute()
    {
        $participants = $this->participants->filter(
            function ($participant) {
                return $participant->user->id != AuthHelper::myId();
            }
        );

        $participant_count = $participants->count();

        switch ($participant_count) {
            case (1 == $participant_count):
                return $participants->first()->user->avatar;
                break;
            case ($participant_count > 1):
                return route('chat.default-icon');
        }
    }

    /**
     * Returns the conversationer attribute
     *
     * @return string
     */
    public function getConversationerAttribute()
    {
        $participants = $this->participants->filter(
            function ($participant) {
                return $participant->user->id != AuthHelper::myId();
            }
        );

        $participant_count = $participants->count();

        switch ($participant_count) {
            case ($participant_count == 1):
                return $participants->first()->user->display_name;
                break;
            case ($participant_count > 1):
                return str_limit($this->participantsString(), self::GROUP_CHAT_STRING_LENGTH);
            default:
                return $participants->first()->user->display_name;
        }
    }
}
