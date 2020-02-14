<?php

namespace App\Models;

/**
 * App\Models\Message
 *
 * @property      int $id
 * @property      int $thread_id
 * @property      int $user_id
 * @property      string $body
 * @property      string $last_read
 * @property      \Carbon\Carbon $created_at
 * @property      \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Cmgmyr\Messenger\Models\Participant[] $participants
 * @property-read \App\Models\Thread $thread
 * @property-read \App\Models\User $user
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Message whereBody($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Message whereCreatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Message whereId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Message whereLastRead($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Message whereThreadId($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Message whereUpdatedAt($value)
 * @method        static \Illuminate\Database\Query\Builder|\App\Models\Message whereUserId($value)
 * @mixin         \Eloquent
 * @property      string|null $deleted_at
 * @method        static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereDeletedAt($value)
 */

class Message extends \Cmgmyr\Messenger\Models\Message
{
    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['thread', 'user'];

    /**
     * Loads model with user relationship
     *
     * @var array
     */
    protected $with = ['user'];

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = [
        'thread_id',
        'user_id',
        'body',
        'share_data'
    ];

    protected $casts = [
        'share_data' => 'json'
    ];
}
