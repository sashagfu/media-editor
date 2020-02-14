<?php

namespace App\Models;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use App\Http\Traits\UserSettingsTrait;
use App\Models\Video;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Image;
use App\Models\UserSetting;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;
use Cmgmyr\Messenger\Traits\Messagable;
use App\Models\Thread;

/**
 * App\Models\User
 *
 * @property      int $id
 * @property      string $username
 * @property      string $email
 * @property      string $password
 * @property      string $is_verified
 * @property      string $verification_code
 * @property      int $total_likes
 * @property      int $total_stars
 * @property      string $remember_token
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @property      string $talent
 * @property      string $display_name
 * @property      string $quote
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $avatars
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flag[] $flagable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $followers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $following
 * @property-read mixed $avatar
 * @property-read mixed $unread_messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection
 * |\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Cmgmyr\Messenger\Models\Participant[] $participants
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserSetting[] $settings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Thread[] $threads
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User exceptAdmin()
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereDisplayName($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereEmail($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereIsVerified($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User wherePassword($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereQuote($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereRememberToken($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereTalent($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereTotalLikes($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereTotalStars($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereUpdatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereUsername($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\User whereVerificationCode($value)
 * @mixin         \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $feed
 * @property-read mixed $can_be_followed
 * @property-read mixed $is_following
 * @property-read mixed $total_followers
 * @property-read mixed $total_following
 * @property-read mixed $total_videos
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|
 * \Illuminate\Notifications\DatabaseNotification[] $notifications
 */
class User extends BaseModel
{
    use Notifiable;
    use Searchable;
    use UserSettingsTrait;
    use Messagable;
    use HasApiTokens;

    const MORPH_TYPE = 'user';

    const USER_BALANCE = 'user.balance';

    const USER_PAYPAL_EMAIL = 'user.paypal_email';

    const USER_PAYPAL_VERIFIED = 'user.paypal_verified';

    // Setting constants

    const SETTING_DISPLAY_TOP_SPONSORS = 'privacy.display_top_sponsors';

    const SETTING_DISPLAY_SPONSORSHIP = 'privacy.display_sponsorship';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'verification_code',
        'talent',
        'bio',
        'display_name',
        'quote',
        'settings'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Appends to user model
     *
     * @var array
     */
    protected $appends = [
        'avatar',
        'totalFollowers',
        'totalFollowing',
        'canBeFollowed',
        'isFollowing',
        'totalVideos',
        'balance',
        'paypalEmail',
        'paypalVerified',
        'pendingDonations',
        'sponsors',
        'sponsorship',
        'unreadNotificationsCount',
        'notificationsCount',
    ];

    /**
     * The attributres that will be indexable
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['popularity'] = $this->followers->count();
        return $array;
    }

    // Relations Section

    /**
     * A user can have many videos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function videos()
    {
        return $this->hasMany(Video::class, 'author_id');
    }

    /**
     * A user can have many avatars
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function avatars()
    {
        return $this->morphMany(Image::class, 'imageable')->where('album_id', Image::AVATARS);
    }

    /**
     * A user can have many comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    /**
     * A user can have many posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function flagable()
    {
        return $this->morphMany(Flag::class, 'flagable');
    }

    /**
     * A user can have many followers and can be followed by many users
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_followings', 'user_id', 'follower_id')
            ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_followings', 'follower_id', 'user_id')
            ->withTimestamps();
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'author_id');
    }

    public function feed()
    {
        $user_type = DBHelper::getMapByModel(self::class);

        return $this->belongsToMany(Post::class, 'post_share', 'feed_id')
            ->wherePivot('feed_type', $user_type)
            ->withTimestamps();
    }

    public function savedAssets()
    {
        return $this->belongsToMany(Asset::class, 'saved_assets');
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'author_id');
    }

    public function sponsors()
    {
        return $this->belongsToMany(User::class, 'transactions', 'payee_id', 'payer_id')
            ->where('type', Transaction::DONATION_TYPE)
            ->whereIn('status', Transaction::SUCCESS_STATUSES)
            ->distinct();
    }

    /**
     * A user can have many debit transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function debitTransactions()
    {
        return $this->hasMany(Transaction::class, 'payee_id');
    }

    /**
     * A user can have many credit transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function creditTransactions()
    {
        return $this->hasMany(Transaction::class, 'payer_id');
    }

    /**
     * A user can have many properties
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(UserProperty::class);
    }

    public function autoAccepts()
    {
        return $this->belongsToMany(User::class, 'auto_operations', 'subject_id', 'initiator_id')
            ->where('status', AutoOperation::STATUS_ALWAYS_ACCEPT);
    }

    public function autoDeclines()
    {
        return $this->belongsToMany(User::class, 'auto_operations', 'subject_id', 'initiator_id')
                    ->where('status', AutoOperation::STATUS_ALWAYS_DECLINE);
    }

    // Avatar section

    public function getAvatarAttribute()
    {
        $last_avatar = $this->avatars->last();

        if ($last_avatar) {
            return route('front.get.avatar', ['user_id' => $this->id, 'filename' => $last_avatar->file_name]);
        } else {
            // TODO
            $gender = ($this->id % 2) ? 'men' : 'women';
            return 'https://randomuser.me/api/portraits/' . $gender . '/' . $this->id * 6 . '.jpg';
//            return route('front.default.avatar');
        }
    }

    public function getUnreadMessagesCountAttribute()
    {
        return Thread::forUserWithNewMessages($this->id)->get()->map(
            function ($thread) {
                return $thread->userUnreadMessagesCount($this->id);
            }
        )->sum();
    }

    // Followers section

    public function scopeExceptAdmin($query)
    {
        return $query->where('id', '!=', 1);
    }

    public function getTotalFollowersAttribute()
    {
        return $this->followers()->count();
    }

    public function getTotalFollowingAttribute()
    {
        return $this->following()->count();
    }

    public function getCanBeFollowedAttribute()
    {
        $user_id = AuthHelper::myId();
        return $user_id ? ($this->id !== $user_id || $this->isFollowing) : false ;
    }

    public function getIsFollowingAttribute()
    {
        return (bool) AuthHelper::me() ? AuthHelper::me()->following->contains($this) : false;
    }

    public function getTotalVideosAttribute()
    {
        return $this->videos()->count();
    }

    public function hasSavedVideo($parent_video_id)
    {
        return $this->videos()->where(function ($query) use ($parent_video_id) {
            $query->where('parent_id', $parent_video_id);
        })->count();
    }

    public function getPropertyValue(string $propertyName)
    {
        return $this->properties()->where('name', $propertyName)->first()->value;
    }

    public function setPropertyValue(string $propertyName, $value)
    {
        $property = $this->properties()->where('name', $propertyName)->first();
        if ($property) {
            $property->value = $value;
            $property->save();
        } else {
            $property = new UserProperty();
            $property->user_id = $this->id;
            $property->name = $propertyName;
            $property->value = $value;
            $property->save();
        }
    }

    public function getBalanceAttribute()
    {
        $balance = \Cache::tags(['users', 'transactions'])
                         ->rememberForever('user_balance_' . $this->id, function () {
                             return $this->getTransactionsSum() ?? 0;
                         });
        return $balance;
    }

    private function getTransactionsSum()
    {
        return Transaction::where(function ($query) {
            $query->where('payee_id', $this->id)
                ->orWhere('payer_id', $this->id);
        })
            ->whereNotIn('type', [Transaction::VERIFICATION_TYPE])
            ->whereIn('status', Transaction::SUCCESS_STATUSES)
            ->get()
            ->map(function ($t) {
                return (in_array($t->type, Transaction::DEBIT) && $t->payee_id == $this->id)
                    ? $t->amount
                    : -$t->amount;
            })
            ->sum();
    }

    public function getPaypalEmailAttribute()
    {
        return $this->properties()
                    ->where('name', User::USER_PAYPAL_EMAIL)
                    ->first()
                   ->value ?? 0;
    }

    public function getPaypalVerifiedAttribute()
    {
        return $this->properties()
                    ->where('name', User::USER_PAYPAL_VERIFIED)
                    ->first()
                   ->value ?? 0;
    }

    public function getPendingDonationsAttribute()
    {
        return $this->debitTransactions()
                    ->where('status', Transaction::STATUS_PENDING)
                    ->where('type', Transaction::DONATION_TYPE)
                    ->get();
    }

    public function getDonationsSum($recipient_id, $period = null)
    {
        $query = $this->creditTransactions()
                    ->where('type', Transaction::DONATION_TYPE)
                    ->where('payee_id', $recipient_id);
        if ($period) {
            $query->whereBetween('created_at', $period);
        }

        return $query->get()->sum('amount');
    }

    public function getSponsorsAttribute()
    {
        return  User::whereHas('creditTransactions', function ($query) {
            $query->where('payee_id', $this->id)
                  ->where('status', Transaction::STATUS_ACCEPTED);
        })
            ->get()
            ->sortByDesc(function ($user) {
                return $user->getDonationsSum($this->id);
            })
            ->map(function ($user) {
                $user->donated = $user->getDonationsSum($this->id);
                return $user;
            });
    }

    public function getSponsorshipAttribute()
    {
        return User::whereHas('debitTransactions', function ($query) {
            $query->where('payer_id', $this->id)
                ->where('status', Transaction::STATUS_ACCEPTED);
        })
            ->get()
            ->sortByDesc(function ($user) {
                return $this->getDonationsSum($user->id);
            })
            ->map(function ($user) {
                $user->donated = $this->getDonationsSum($user->id);
                return $user;
            });
    }

    public function getNotificationsCountAttribute()
    {
        return $this->notifications()->count();
    }

    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->whereNull('read_at')->count();
    }
}
