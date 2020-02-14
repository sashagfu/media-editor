<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use AlgoliaSearch\Client as Algolia;
use App\Models\User;
use App\Models\Post;
use App\Models\Circle;
use App\Models\SearchResult;
use DB;

class SearchController extends Controller
{
    const HITSPERPAGE = 30;
    const CONTENT_MAX_SYMBOLS = 100;

    public function prepareSearch($index_name, $settings)
    {
        $settings = array_merge(['hitsPerPage' => self::HITSPERPAGE], $settings);
        $client = new Algolia(env('ALGOLIA_APP_ID', ''), env('ALGOLIA_SECRET', ''));
        $client->initIndex($index_name)->setSettings($settings);
        return $client->initIndex($index_name);
    }

    public function searchUsers(Request $request)
    {
        // Define error message
        $error = [['label' => trans('search.no_res'), 'value' => 'error']];

        // Define whether request term is added by user
        if ($request->has('q')) {
            $term = $request->get('q');

            // Prepare Search objects and define search attributes
            $search_from =
                $this->prepareSearch('users', ['searchableAttributes' => ['username','display_name','talent']]);

            //Restrict search attributes to needed ones
            $users =
                $search_from->search($term, ['restrictSearchableAttributes' => ['username', 'display_name']]);

            //Preparing data for jQuery-UI
            if (!empty($users['hits'])) {
                foreach ($users['hits'] as $user) {
                    $user = User::whereId($user['id']);
                    if ($user->exists()) {
                        $user = $user->first();
                        $data[] = [
                            'exists'   => $user->id,
                            'type'  => 'user',
                            'value' => $user->display_name,
                            'url'   => route('front.another_profile', ['username' => $user->username]),
                            'icon'  => $user->avatar,
                            'quote' => $user->quote,
                        ];
                    }
                }
                return $data;
            } else {
                return $error;
            }
        }

        return $error;
    }

    public function searchPosts(Request $request)
    {
        // Define error message
        $error = [['label' => trans('search.no_res'), 'value' => 'error']];

        // Define whether request term is added by user
        if ($request->has('q')) {
            $term = $request->get('q');

            // Prepare Search objects and define search attributes
            $search_from =
                $this->prepareSearch('posts', ['searchableAttributes' => ['content']]);

            //Restrict search attributes to needed ones
            $posts =
                $search_from->search(
                    $term,
                    [
                    'typoTolerance' => false,
                    'restrictSearchableAttributes' => 'content'
                    ]
                );

            //Preparing data for jQuery-UI
            if (!empty($posts['hits'])) {
                foreach ($posts['hits'] as $post) {
                    $post = Post::whereId($post['id']);
                    if ($post->exists()) {
                        $post = $post->first();
                        $data[] = [
                            'type' => 'post',
                            'value' => str_limit($post->parsedContent, self::CONTENT_MAX_SYMBOLS),
                            'url' => route('post.single', ['slug' => $post['slug']]),
                            'icon' => $post->author->avatar,
                            'author' => $post->author->username,
                            'time' => $post->created_at->diffForHumans(),
                            'author_profile' => route('front.another_profile', ['username' =>$post->author->username]),
                            'term' => $term
                        ];
                    }
                }
                return $data;
            } else {
                return $error;
            }
        }

        return $error;
    }

    public function searchTalents(Request $request)
    {
        // Define error message
        $error = [['label' => trans('search.no_res'), 'value' => 'error']];

        // Define whether request term is added by user
        if ($request->has('q')) {
            $term = $request->get('q');

            // Prepare Search objects and define search attributes
            $search_from =
                $this->prepareSearch('users', ['searchableAttributes' => ['username','display_name','talent']]);

            //Restrict search attributes to needed ones
            $users =
                $search_from->search($term, ['restrictSearchableAttributes' => ['talent']]);

            //Preparing data for jQuery-UI
            if (!empty($users['hits'])) {
                foreach ($users['hits'] as $user) {
                    $user = User::whereId($user['id']);
                    if ($user->exists()) {
                        $user = $user->first();
                        $data[] = [
                            'exists'   => $user->id,
                            'type'  => 'user',
                            'value' => $user->display_name,
                            'url'   => route('front.another_profile', ['username' => $user->username]),
                            'icon'  => $user->avatar,
                            'quote' => $user->quote,
                        ];
                    }
                }
                return $data;
            } else {
                return $error;
            }
        }

        return $error;
    }

    public function storeSearchRequests(Request $request)
    {
        $this->validate(
            $request,
            [
            'search_term' => 'required',
            ]
        );

        $search_term = $request->search_term;

        $existing_term = SearchResult::whereSearchTerm($search_term)->first();

        if ($existing_term) {
            $table_name = $existing_term->getTable();
            DB::table($table_name)->whereId($existing_term->id)->increment('total_search');
        } else {
            $search = new SearchResult();
            $search->search_term = $search_term;
            $search->total_search = 1;
            $search->save();
        }
    }

    public function mentionUsers(Request $request)
    {
        // Define error message
        $error = [['label' => trans('search.no_res'), 'value' => 'error']];

        // Define whether request term is added by user
        if ($request->has('term')) {
            $term = $request->get('term');

            $users = User::search($term)->get();

            foreach ($users as $user) {
                $data[] = [
                    'value' => $user->display_name,
                    'image' => $user->avatar,
                    'uid' => $user->id,
                ];
            }
            return isset($data) ? $data : [];
        }
        return $error;
    }

    /**
     * Search for users who can be invited to circle
     *
     * @param  Request $request
     * @return array
     */
    public function searchInvitesUser(Request $request)
    {
        // Define error message
        $error = [['label' => trans('search.no_res'), 'value' => 'error']];

        $slug = $request->circle_slug;
        $circle = Circle::whereSlug($slug)->firstOrFail();
        $term = $request->get('q');

        // Exclude users from search
        $invites_ids = $circle->invites->pluck('user_id')->toArray();
        $members_ids = $circle->members()->pluck('user_id')->toArray();
        $except_ids = array_merge($invites_ids, $members_ids);
        $user_ids = User::whereNotIn('id', $except_ids)->pluck('id')->toArray();
        $users = User::search($term)->get()->whereIn('id', $user_ids);

        foreach ($users as $user) {
            $data[] = [
                'value' => $user->display_name,
                'email' => $user->email
            ];
        }
        return isset($data) ? $data : $error;
    }
}
