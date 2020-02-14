<?php

namespace App\Http\GraphQL\Queries;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AlgoliaSearch\Client as Algolia;

class FetchUsers
{
    const HITSPERPAGE = 30;
    const CONTENT_MAX_SYMBOLS = 100;

    public function resolve($rootValue, array $args)
    {
        if (isset($args['term'])) {
            $term = $args['term'];

            // Prepare Search objects and define search attributes
            $search_from =
                $this->prepareSearch('users', [
                    'searchableAttributes' => [
                        'username',
                        'display_name',
                        'talent'
                    ]
                ]);

            //Restrict search attributes to needed ones
            $users =
                $search_from->search($term, [
                    'restrictSearchableAttributes' => [
                        'username',
                        'display_name'
                    ]
                ]);

            if (!empty($users['hits'])) {
                foreach ($users['hits'] as $user) {
                    $user = User::find($user['id']);
                    if ($user->exists()) {
                        $data[] = $user;
                    }
                }

                return $data;
            } else {
                return [];
            }
        }

        return User::all();
    }

    public function prepareSearch($index_name, $settings)
    {
        $settings = array_merge(['hitsPerPage' => self::HITSPERPAGE], $settings);
        $client = new Algolia(env('ALGOLIA_APP_ID', ''), env('ALGOLIA_SECRET', ''));
        $client->initIndex($index_name)->setSettings($settings);
        return $client->initIndex($index_name);
    }
}
