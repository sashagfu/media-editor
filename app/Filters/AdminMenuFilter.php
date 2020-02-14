<?php

namespace App\Filters;

use App\Models\Flag;
use App\Models\Post;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Comment;
use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class AdminMenuFilter implements FilterInterface
{

    /**
     * @param array   $item
     * @param Builder $builder
     *
     * @return array
     */
    public function transform($item, Builder $builder)
    {
        if (isset($item['url']) && $item['url'] == 'admin/users') {
            $item['label'] = User::where('created_at', '>=', Carbon::today())->count();
        }

        if (isset($item['url']) && $item['url'] == 'admin/flags') {
            $item['label'] = Flag::whereIsVerified(false)->count();
        }

        if (isset($item['url']) && $item['url'] == 'admin/posts') {
            $item['label'] = Post::where('created_at', '>=', Carbon::today())->count();
        }

        if (isset($item['url']) && $item['url'] == 'admin/videos') {
            $item['label'] = Video::where('created_at', '>=', Carbon::today())->count();
        }

        if (isset($item['url']) && $item['url'] == 'admin/comments') {
            $item['label'] = Comment::where('created_at', '>=', Carbon::today())->count();
        }

        return $item;
    }
}
