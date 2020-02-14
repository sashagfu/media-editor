<?php

namespace App\Models;

use App\Helpers\AuthHelper;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Helpers\DBHelper;
use Laravel\Scout\Searchable;
use Ramsey\Uuid\Uuid;

/**
 * App\Models\Post
 *
 * @property      int $id
 * @property      string $content
 * @property      int $author_id
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flag[] $flaggable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Image[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $stars
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Video[] $videos
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Post followingPosts(\App\Models\User $user)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Post performancePosts()
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Post skipFlagged(\App\Models\User $user)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Post whereAuthorId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Post whereContent($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Post whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Post whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Post whereUpdatedAt($value)
 * @mixin         \Eloquent
 * @property      string $slug
 * @property-read mixed $comment_visibility
 * @property-read mixed $comments_count
 * @property-read mixed $created_at_diff
 * @property-read bool $is_performance
 * @property-read mixed $media
 * @property-read mixed $parsed_content
 * @property-read mixed $shareable
 * @property-read mixed $user_reaction
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Post flagged(\App\Models\User $user)
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Post whereSlug($value)
 */

class Post extends BaseModel
{
    use Searchable;

    const MORPH_TYPE = 'post';

    protected static function boot()
    {
        parent::boot();

        if (isset(Auth::user()->id)) {
            static::creating(
                function ($model) {
                    $model->slug = Uuid::uuid1();
                }
            );
        }
    }

    protected $fillable = ['content'];
    protected $appends = [
        'parsedContent',
        'isPerformance',
        'media',
        'createdAtDiff',
        'userReaction',
        'shareable',
        'commentVisibility',
        'commentsCount'
    ];
    protected $touches = ['author', 'comments'];

    //    use Searchable;

    /**
     * The attributres that will be indexable
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['popularity'] = $this->isPerformance ? $this->stars->count() : $this->likes->count();
        return $array;
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'post_media', 'media_id', 'post_id')
            ->wherePivot('media_type', 'video')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'post_media', 'media_id', 'post_id')
            ->wherePivot('media_type', 'image')
            ->withTimestamps();
    }

    public function media()
    {
        $images = $this->images;
        $videos = $this->videos;

        return $images->merge($videos);
    }

    public function getMediaAttribute()
    {
        $images = $this->images;
        $videos = $this->videos;

        return $images->merge($videos);
    }

    public function getCommentVisibilityAttribute()
    {
        return false;
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function author()
    {
        return  $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('parent_id', null);
    }

    /**
     * Post can have many likes
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function likes()
    {
        return $this->morphToMany(User::class, 'likeable', 'likes');
    }

    /**
     * Post can have many stars
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function stars()
    {
        return $this->morphToMany(User::class, 'starable', 'stars');
    }

    public function flaggable()
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }

    /**
     * Return whether post is liked by a user
     *
     * @return bool
     */
    public function isLikedByUser()
    {
        return Auth::user() ?  (bool) $this->likes->contains(Auth::user()->id) : false;
    }

    /**
     * Return whether post is starred by a user
     *
     * @return bool
     */
    public function isStarredByUser()
    {
        return Auth::user() ?  (bool) $this->stars->contains(Auth::user()->id) : false;
    }

    /**
     * Return whether post has performance video
     *
     * @return bool
     */
    public function getIsPerformanceAttribute()
    {
        if ($this->videos->isEmpty()) {
            return false;
        } else {
            return (bool) $this->videos->first()->is_performance;
        }
    }

    public function scopePerformancePosts($query)
    {
        return $query->whereHas(
            'videos',
            function ($q) {
                $q->where('is_performance', true);
            }
        );
    }

    public function scopeSkipFlagged($query, User $user)
    {
        return $query->whereDoesntHave(
            'flaggable',
            function ($q) use ($user) {
                $q->where('flags.author_id', $user->id);
            }
        );
    }

    public function scopeFlagged($query, User $user)
    {
        return $query->whereHas(
            'flaggable',
            function ($q) use ($user) {
                $q->where('flags.author_id', $user->id);
            }
        );
    }

    public function scopeFollowingPosts($query, User $user)
    {
        $author_ids = $user->following->pluck('id');

        return $query->whereIn('author_id', $author_ids);
    }

    public function getParsedContentAttribute($str_limit = null)
    {
        $str_limit = $str_limit ? $str_limit : null;
        $content = preg_replace_callback(
            '/#(\w+)/',
            function ($matches) {
                return '<a class="post-hashtag" href="'.
                route('hashtag_posts', ['hashtag_name' => $matches[1]])
                .'">'.$matches[0].'</a>';
            },
            $this->content
        );

        $content = str_replace("&nbsp;", '', $content);
        $content = preg_replace_callback(
            '/@([^\s]+)/',
            function ($matches) {
                $user = User::whereUsername($matches[1])->first();
                return '<a class="user-mention" href="'.
                route('front.another_profile', ['username' => $user->username])
                .'">'.$user->display_name.'</a>';
            },
            $content
        );

        if ($str_limit) {
            return str_limit($content, $str_limit);
        } else {
            return $content;
        }
    }

    public function getCreatedAtDiffAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getUserReactionAttribute()
    {
        if ($this->isPerformance) {
            return $this->isStarredByUser();
        } else {
            return $this->isLikedByUser();
        }
    }

    public function getShareableAttribute()
    {
        $user = AuthHelper::me();
        return (boolean) $user ? (($user->feed->contains($this->id) || $this->author == $user) ? false : true) : false;
    }
}
