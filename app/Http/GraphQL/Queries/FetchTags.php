<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Tag;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AlgoliaSearch\Client as Algolia;

class FetchTags
{
    const HITSPERPAGE = 30;
    const CONTENT_MAX_SYMBOLS = 100;

    public function resolve($rootValue, array $args)
    {
        if (isset($args['term'])) {
            $term = $args['term'];

            $search_from =
                $this->prepareSearch('tags', [
                    'searchableAttributes' => [
                        'name',
                    ]
                ]);

            //Restrict search attributes to needed ones
            $tags =
                $search_from->search($term, [
                    'restrictSearchableAttributes' => [
                        'name',
                    ]
                ]);

            if (!empty($tags['hits'])) {
                foreach ($tags['hits'] as $tag) {
                    $tag = Tag::find($tag['id']);
                    if ($tag->exists()) {
                        $data[] = $tag;
                    }
                }

                return $data;
            } else {
                return null;
            }
        }
    }

    public function prepareSearch($index_name, $settings)
    {
        $settings = array_merge(['hitsPerPage' => self::HITSPERPAGE], $settings);
        $client = new Algolia(env('ALGOLIA_APP_ID', ''), env('ALGOLIA_SECRET', ''));
        $client->initIndex($index_name)->setSettings($settings);
        return $client->initIndex($index_name);
    }
}
