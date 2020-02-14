<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

/**
 * App\Models\Comment
 *
 * @property      int $id
 * @property      string $text
 * @property      int $author_id
 * @property      int $post_id
 * @property      int $parent_id
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flag[] $flagable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $likes
 * @property-read \App\Models\Comment $parent
 * @property-read \App\Models\Post $post
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $replies
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Comment whereAuthorId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Comment whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Comment whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Comment whereParentId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Comment wherePostId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Comment whereText($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Comment whereUpdatedAt($value)
 * @mixin         \Eloquent
 * @property-read mixed $created_at_diff
 * @property-read bool $is_liked
 * @property-read mixed $replies_length
 */
class Comment extends BaseModel
{
    const MORPH_TYPE = 'comment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['text', 'project_id', 'author_id', 'parent_id'];

    protected $touches = ['author', 'replies'];

    protected $with = ['author', 'likes'];

    protected $appends = ['isLiked', 'createdAtDiff', 'repliesLength'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Comment is owned by an author
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Comment can have many likes
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function likes()
    {
        return $this->morphToMany(User::class, 'likeable', 'likes');
    }

    /**
     * Comment can have parent comments(replies)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function flagable()
    {
        return $this->morphMany(Flag::class, 'flagable');
    }

    /**
     * Return whether comment is liked by a user
     *
     * @return bool
     */
    public function getIsLikedAttribute()
    {
        return Auth::user() ?  (bool) $this->likes->contains(Auth::user()->id) : false;
    }

    public function getRepliesLengthAttribute()
    {
        return $this->replies()->count();
    }

    public function getCreatedAtDiffAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
