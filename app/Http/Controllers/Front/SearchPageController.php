<?php

namespace App\Http\Controllers\Front;

use App\Models\SearchResult;
use Illuminate\Http\Request;
use AlgoliaSearch\Client as Algolia;
use App\Models\Post;
use App\Models\User;
use App\Helpers\AuthHelper;

class SearchPageController extends Controller
{
    const HITSPERPAGE = 30;
    const TRENDING_HITSPERPAGE = 5;
    const POSTS_PAGINATION_COUNT = 10;
    const USERS_PAGINATION_COUNT = 10;

    public function prepareSearch($index_name, $settings)
    {
        $settings = array_merge(['hitsPerPage' => self::HITSPERPAGE], $settings);
        $client = new Algolia(env('ALGOLIA_APP_ID', ''), env('ALGOLIA_SECRET', ''));
        $client->initIndex($index_name)->setSettings($settings);
        return $client->initIndex($index_name);
    }

    public function trendingPosts()
    {
        $search_from =
            $this->prepareSearch('posts', ['ranking' => ['desc(popularity)']]);

        $posts_search =
            $search_from->search('', ['hitsPerPage'=>self::TRENDING_HITSPERPAGE]);

        foreach ($posts_search['hits'] as $post) {
            $trending_posts[] = Post::find($post['id']);
        }

        if (!empty($trending_posts)) {
            return collect($trending_posts);
        }
        return false;
    }

    public function trendingUsers()
    {
        $search_from =
            $this->prepareSearch('users', ['ranking' => ['desc(popularity)']]);

        $users_search =
            $search_from->search('', ['hitsPerPage'=>self::TRENDING_HITSPERPAGE]);

        foreach ($users_search['hits'] as $user) {
            $trending_users[] = User::find($user['id']);
        }

        if (!empty($trending_users)) {
            return collect($trending_users);
        }
        return false;
    }

    public function searchMain(Request $request)
    {
        $errors = trans('search.no_res');
        $search_term = $request->get('q');
        $posts_page = $request->postsPage;
        $users_page = $request->usersPage;

        //trending items

        $trending_posts = $this->trendingPosts();
        $trending_users = $this->trendingUsers();
        $trending_hashtags = SearchResult::all()->sortByDesc('total_search')->take(15);


        // Making sure the user entered a keyword.
        if ($request->has('q')) {
            $posts = Post::search($search_term)
                ->paginate(self::POSTS_PAGINATION_COUNT, 'posts')
                ->appends(['posts' => $posts_page]);
            $users = User::search($search_term)
                ->paginate(self::USERS_PAGINATION_COUNT, 'users')
                ->appends(['users' => $users_page]);

            $results = $posts->merge($users)->isEmpty();

            return  view('front.search-results')->with(
                [
                    'posts' => $posts,
                    'users' => $users,
                    'search_term' => $search_term,
                    'errors' => $results ? $errors : null,
                    'trending_posts' => $trending_posts,
                    'trending_users' => $trending_users,
                    'trending_hashtags' => $trending_hashtags
                ]
            );
        }
        return abort(404);
    }

    public function getSearchResultsPosts(Request $request)
    {
        $search_term = $request->get('query');
        $page_num = (int) $request->posts;

        $posts = Post::search($search_term)
            ->paginate(self::POSTS_PAGINATION_COUNT, 'posts', $page_num)
            ->appends(['posts' => $page_num]);

        return $posts;
    }

    public function getSearchResultsUsers(Request $request)
    {
        $search_term = $request->get('query');
        $page_num = (int) $request->users;

        $users = User::search($search_term)
            ->paginate(self::USERS_PAGINATION_COUNT, 'users', $page_num)
            ->appends(['users' => $page_num]);

        return $users;
    }
}
