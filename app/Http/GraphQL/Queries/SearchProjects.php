<?php

namespace App\Http\GraphQL\Queries;

use AlgoliaSearch\Client as Algolia;
use App\Models\Project;
use App\Models\Tag;

class SearchProjects
{
    const HITSPERPAGE = 30;
    const CONTENT_MAX_SYMBOLS = 100;

    public function resolve($rootValue, $args)
    {
        if (isset($args['term'])) {
            $term = $args['term'];
            $result = collect();

            $tag_names = explode(' ', $term);

            $tags = collect();
            foreach ($tag_names as $tag_name) {
                $tag = Tag::where('name', strtolower($tag_name))->first();

                if ($tag && count($tag->projects)) {
                    $tags->push($tag);
                }
            }

            $result['tags'] = $tags;

            // Prepare Search objects and define search attributes
            $search_from =
                $this->prepareSearch('projects', [
                    'searchableAttributes' => [
                        'title',
                        'description'
                    ]
                ]);

            //Restrict search attributes to needed ones
            $projects =
                $search_from->search($term, [
                    'restrictSearchableAttributes' => [
                        'title',
                        'description'
                    ]
                ]);

            if (!empty($projects['hits'])) {
                foreach ($projects['hits'] as $project) {
                    $project = Project::find($project['id']);
                    $data[] = $project;
                }

                $result['projects'] = $data;
            }

            return $result;
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
