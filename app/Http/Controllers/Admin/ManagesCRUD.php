<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Redirect;
use View;
use Auth;

/**
 * Trait ManagesCRUD
 *
 * @package App\Http\Admin\Controllers
 */
trait ManagesCRUD
{

    protected $paginateCount = 25;

    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $ModelName   = $this->getModelName();
        $entity_name = $this->getEntityName();
        $searchQuery = $request->input('q');

        // If a search query was provided via $_GET and search method was declared in the controller, construct a query
        // specific for the controller and its corresponding model
        if (!empty($searchQuery) && method_exists($this, 'search')) {
            $query = $this->search($searchQuery, $ModelName);
        } else {
            $query = $ModelName::query();
        }

        // If listing items depends on other tables, eager-load them to avoid the n+1 problem
        if (method_exists($this, 'getIndexRelationshipFields')) {
            $query = $query->with($this->getIndexRelationshipFields());
        }

        $query = $this->withOrder($query);

        // If a class declares pageCount property, use pagination and set item limit per page using the property's value
        if (property_exists($this, 'paginateCount')) {
            // Get records paginated
            $records = $query->paginate($this->paginateCount);
        } else {
            // Get all the records
            $records = $query->get();
        }

        $with = array_merge(
            $this->withIndexView($request),
            [
            str_plural($entity_name) => $records,
            'searchQuery' => $searchQuery
            ]
        );

        // load the view and pass the data
        return View::make('admin.' . str_plural($entity_name) . '.index')
            ->with($with);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $entity_name = $this->getEntityName();

        return View::make('admin.' . str_plural($entity_name) . '.edit')
            ->with($this->getRelationshipFields());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request Request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $ModelName = $this->getModelName();
        $entity_name = $this->getEntityName();

        $this->validate($request, $this->getValidationRules());
        $this->formatInput($request);

        $data = $request->all();

        $this->unsetOrEncryptPassword($data);

        // store
        $entity = $ModelName::create($data);

        $this->syncManyToManyRelations($request, $entity);

        if (property_exists($this, 'useCreatedBy')) {
            $entity->created_by = Auth::id();
        }

        if (property_exists($this, 'useUpdatedBy')) {
            $entity->updated_by = Auth::id();
        }

        // Add information on who created and updated the item
        if (property_exists($this, 'useCreatedBy') || property_exists($this, 'useUpdatedBy')) {
            $entity->save();
        }

        // redirect
        $this->setFlashMessage('A new ' . $entity_name . ' was created.', 'success');

        return Redirect::route(str_plural($entity_name) . '.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id Entity ID
     *
     * @return Response
     */
    public function show($id)
    {
        $ModelName = $this->getModelName();
        $entity_name = $this->getEntityName();

        $record = $ModelName::findOrFail($id);

        return View::make('admin.' . str_plural($entity_name) . '.show')
            ->with($entity_name, $record);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id Entity ID
     *
     * @return Response
     */
    public function edit($id)
    {
        $ModelName = $this->getModelName();
        $entity_name = $this->getEntityName();

        $record = $ModelName::findOrFail($id);

        return View::make('admin.' . str_plural($entity_name) . '.edit')
            ->with($entity_name, $record)
            ->with($this->getRelationshipFields());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int     $id      Entity ID
     *
     * @param Request $request
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $ModelName = $this->getModelName();
        $entity_name = $this->getEntityName();
        $rules = $this->getValidationRules();

        if ($rules) {
            $this->validate($request, $this->getValidationRules());
        } else {
            throw new \BadMethodCallException('Validation rules are not defined');
        }

        $request = $this->filterRequest($request);

        $record = $ModelName::findOrFail($id);

        $data = \Request::all();

        $this->unsetOrEncryptPassword($data);

        $record->update($data);

        // Add information on who updated the item
        if (property_exists($this, 'useUpdatedBy')) {
            $record->updated_by = Auth::id();
            $record->save();
        }

        $this->syncManyToManyRelations($request, $record);

        $this->setFlashMessage('The ' . $entity_name . ' was updated.', 'info');

        // redirect: Update
        if (isset($data['action_mode']) && $data['action_mode'] == 'update') {
            return redirect()->route(str_plural($entity_name) . '.edit', $id);
        }

        // redirect: Update & Close
        return redirect()->route(str_plural($entity_name) . '.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id Entity ID
     *
     * @return Response
     */
    public function destroy($id)
    {
        $ModelName = $this->getModelName();
        $entity_name = $this->getEntityName();

        $ModelName::destroy($id);

        // redirect
        $this->setFlashMessage('The ' . $entity_name . ' was deleted.', 'warning');
        return redirect()->route(str_plural($entity_name) . '.index');
    }

    /**
     * Shows Entity revisions
     *
     * @param int $id Entity ID
     *
     * @return Response
     */
    public function revisions($id)
    {
        $ModelName = $this->getModelName();
        $entity_name = $this->getEntityName();

        $record = $ModelName::findOrFail($id);

        return View::make('admin.' . str_plural($entity_name) . '.revisions')
                   ->with($entity_name, $record);
    }

    /**
     * Defines model validation rules for storing and updating data
     *
     * @return array
     */
    protected function getValidationRules()
    {
        return [];
    }

    /**
     * Defines model relationships
     *
     * @return array
     */
    protected function getRelationshipFields()
    {
        return [];
    }


    /**
     * Format given request input after validation and before saving/updating data.
     * This method is called both upon store and upon update.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Request
     */
    protected function filterRequest(Request $request)
    {
        return $request;
    }

    protected function withIndexView(Request $request)
    {
        return [];
    }

    protected function withOrder($query)
    {
        return $query;
    }

    // PRIVATEES

    /**
     * Returns model name from current controller
     *
     * @param bool $short Defines where to return full or short class name
     *
     * @return string
     * @throws BadMethodCallException
     */
    private function getModelName($short = false)
    {
        $class_path_segments = explode('\\', get_class($this));

        if (!count($class_path_segments)) {
            throw new BadMethodCallException(
                'ManageCRUDOperations Unexpected error!'
            );
        }

        $controller_name = end($class_path_segments);

        if (!strpos($controller_name, 'Controller')) {
            throw new BadMethodCallException(
                'ManageCRUDOperations trait must be used in Controllers only!'
            );
        }

        $app_namespace = app()->getNamespace();

        $short_name = str_replace('Controller', '', $controller_name);
        $full_name = '\\' . $app_namespace . 'Models\\' . $short_name;


        if (!class_exists($full_name)) {
            throw new BadMethodCallException(
                'ManageCRUDOperations: Model ' . $full_name
                . ' Not Found for Controller ' . $controller_name . '!'
            );
        }

        return $short ? $short_name : $full_name;
    }

    /**
     * Returns current model's entity name
     *
     * @return string
     * @throws BadMethodCallException
     */
    private function getEntityName()
    {
        $model_name = $this->getModelName(true);

        if (!is_string($model_name)) {
            throw new BadMethodCallException(
                'ManageCRUDOperations: Cannot transform to Entity name'
            );
        }

        return str_replace(' ', '_', snake_case($model_name));
    }

    /**
     * Saves changes to many-to-many relations defined in getBelongsToManyRelationships method
     *
     * @param Request $request
     * @param $record
     */
    protected function syncManyToManyRelations(Request $request, $record)
    {
        if (method_exists($this, 'getBelongsToManyRelationships')) {
            foreach ($this->getBelongsToManyRelationships() as $relation) {
                $assocData = $request->get($relation);
                if (!is_array($assocData)) {
                    $assocData = [];
                }

                $record->$relation()->sync($assocData);
            }
        }
    }

    /**
     * @param array $data
     */
    protected function unsetOrEncryptPassword(&$data)
    {
        if (isset($data['password'])) {
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = bcrypt($data['password']);
            }
        }
    }
}
