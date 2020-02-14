<?php

return [

	/*
	|--------------------------------------------------------------------------
	| File clear settings
	|--------------------------------------------------------------------------
	|
	| paths - defines relative root paths array where directories and files store
	|
	| excluded_paths - defines relative root paths array where directories and files would not be deleted
	|
	| excluded_files - defines relative root paths array with filenames those would not be deleted
	|
	| time_before_remove - defines how many minutes files may be store
	|
	| model - if not null will be deleted with the associated file
	|
	| file_field_name - name of the table field where filename is stored. Work only if model set
	|
	| remove_directory - remove directory if all files had been deleted. Only nested directories would be removed
	|
	*/

	/**
	 * array
	 */
	'paths'              => [
		storage_path('uploads/tmp/'),
	],

	/**
	 * array
	 */
	'excluded_paths'     => [

	],

	/**
	 * array
	 */
	'excluded_files'     => [

	],

	/**
	 * integer
	 */
	'time_before_remove' => 15,

	/**
	 * bool
	 */
	'remove_directories' => true,

	/**
	 * EloquentModel|null
	 */
	'model'              => null,

	/**
	 * string|null
	 */
	'file_field_name'    => null,

	/**
	 * Delete file and instance only if there is no related instance(s)
	 */
	'relation'           => null,

];
