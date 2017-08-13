<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\FreeContent\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/pages', 'verb' => 'GET'],
	   ['name' => 'page#showPublic', 'url' => '/pages/{id}', 'verb' => 'GET'],
	   ['name' => 'admin#getEntries', 'url' => '/admin', 'verb' => 'GET'],
	   ['name' => 'admin#addEntry', 'url' => '/admin', 'verb' => 'POST'],
	   ['name' => 'admin#updateEntry', 'url' => '/admin/{id}', 'verb' => 'POST'],
	   ['name' => 'admin#deleteEntry', 'url' => '/admin/{id}', 'verb' => 'DELETE'],
    ]
];
