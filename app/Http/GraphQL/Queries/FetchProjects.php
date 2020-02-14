<?php

namespace App\Http\GraphQL\Queries;

use App\Models\Project;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use AlgoliaSearch\Client as Algolia;

class FetchProjects
{
    const HITSPERPAGE         = 30;
    const CONTENT_MAX_SYMBOLS = 100;

    public function resolve($rootValue, array $args)
    {
        if (isset($args['tag'])) {
            return Project::whereHas('tags', function ($query) use ($args) {
                $query->where('name', $args['tag']['name']);
            })->get();
        }

        if (isset($args['term'])) {
            $term = $args['term'];

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

            $data = [];

            if (!empty($projects['hits'])) {
                foreach ($projects['hits'] as $project) {
                    $project = Project::find($project['id']);
                    if ($project) {
                        $data[] = $project;
                    }
                }

                return $data;
            }
        }

        if (isset($args['userId'])) {
            if ($args['userId'] != Auth::user()->id || isset($args['status'])) {
                $projects = Project::where('author_id', $args['userId'])
                                   ->where('status', Project::STATUS_PUBLISHED)
                                   ->orderBy('pinned', 'desc')
                                   ->get();

                return $projects;
            }
        }
        // If are queried own projects
        if (isset($args['status'])) {
            return Auth::user()->projects->where('status', Project::STATUS_PUBLISHED);
        }

        return Auth::user()
                   ->projects()
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    public function prepareSearch($index_name, $settings)
    {
        $settings = array_merge(['hitsPerPage' => self::HITSPERPAGE], $settings);
        $client   = new Algolia(env('ALGOLIA_APP_ID', ''), env('ALGOLIA_SECRET', ''));
        $client->initIndex($index_name)->setSettings($settings);

        return $client->initIndex($index_name);
    }
}
