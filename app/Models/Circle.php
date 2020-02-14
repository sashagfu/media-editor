<?php

namespace App\Models;

use App\Helpers\AuthHelper;
use App\Helpers\DBHelper;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invite;
use Auth;

/**
 * App\Models\Circle
 *
 * @property      int $id
 * @property      string $title
 * @property      string $slug
 * @property      string $description
 * @property      int $creator_id
 * @property      int $type
 * @property      int $post_adding_privacy
 * @property      int $members_block_privacy
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $covers
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $feed
 * @property-read string $last_cover
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $invites
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $members
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $requestingUsers
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle exceptSecret()
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereCreatorId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereDescription($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereMembersBlockPrivacy($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle wherePostAddingPrivacy($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereSlug($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereTitle($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereType($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Circle whereUpdatedAt($value)
 * @mixin         \Eloquent
 * @property-read mixed $total_members
 * @property-read mixed $total_posts
 */

class Circle extends BaseModel
{
    const MORPH_TYPE = 'circle';

    const TYPE_OPEN = 'open';
    const TYPE_CLOSED = 'closed';
    const TYPE_SECRET = 'secret';
    const STATUS_PENDING = 0;
    const STATUS_ADMIN = 1;
    const STATUS_MEMBER = 2;
    const POST_ADDING_ADMINS = 1;
    const POST_ADDING_MEMBERS = 2;
    const MEMBERS_BLOCK_VISIBILITY_ALL = 1;
    const MEMBERS_BLOCK_VISIBILITY_MEMBERS = 2;

    protected $fillable = ['title', 'description', 'type', 'slug', 'post_adding_privacy', 'members_block_privacy'];

    protected $appends = ['lastCover', 'totalMembers', 'totalPosts'];

    protected static function boot()
    {
        parent::boot();

        if (isset(Auth::user()->id)) {
            static::creating(
                function ($model) {
                    $user_id = AuthHelper::myId();
                    $model->creator()->associate($user_id);
                    $model->slug = time() * time();
                }
            );

            static::created(
                function ($model) {
                    $user_id = AuthHelper::myId();
                    $model->members()->attach($user_id, ['status' => self::STATUS_ADMIN]);
                }
            );
        }
    }

    /**
     * Circle belongs to Creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Circle can have many members
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'circle_members', 'circle_id')
            ->wherePivot('status', '!=', '0')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Circle can have many requesting Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function requestingUsers()
    {
        return $this->belongsToMany(User::class, 'circle_members', 'circle_id')
            ->wherePivot('status', '0')
            ->withTimestamps();
    }

    /**
     * Circle can have many covers
     *
     * @return mixed
     */
    public function covers()
    {
        return $this->morphMany(Image::class, 'imageable')->where('album_id', Image::COVERS);
    }

    /**
     * Circle can have many posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function feed()
    {
        $circle_type = DBHelper::getMapByModel(self::class);

        return $this->belongsToMany(Post::class, 'post_share', 'feed_id')
            ->wherePivot('feed_type', $circle_type)
            ->withTimestamps();
    }

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }



    /**
     * Determines whether user is group creator
     *
     * @return bool
     */
    public function isCreator($user = null)
    {
        $user = $user ? $user : AuthHelper::me();

        return $this->creator->id === $user->id;
    }

    /**
     * Determines whether the user is requesting membership to circle
     *
     * @return bool
     */
    public function isRequesting($user = null)
    {
        $user = $user ? $user : AuthHelper::me();

        if ($this->type == self::TYPE_CLOSED) {
            return $this->requestingUsers->contains($user);
        } else {
            return false;
        }
    }

    /**
     * Determines wheteher the user is member of a circle
     *
     * @param  null $user
     * @return mixed
     */
    public function isMember($user = null)
    {
        $user = $user ? $user : AuthHelper::me();

        return $this->members->contains($user);
    }

    /**
     * Scope to retrieve circles for user
     *
     * @param $query
     */
    public function scopeExceptSecret($query)
    {
        $user_id = AuthHelper::myId();

        return $query->where('type', '!=', 'secret')->orWhereHas(
            'members',
            function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            }
        );
    }

    // Attributes
    /**
     * Return circle last cover
     *
     * @return string
     */
    public function getLastCoverAttribute()
    {
        $last_cover = $this->covers->last();

        if ($last_cover) {
            return route('front.get.circle.cover', ['circle_id' => $this->id, 'filename' => $last_cover->file_name]);
        } else {
            return route('front.default.circle.cover');
        }
    }

    // Total members
    public function getTotalMembersAttribute()
    {
        return $this->members()->count();
    }

    // Total posts
    public function getTotalPostsAttribute()
    {
        return $this->feed()->count();
    }
}
