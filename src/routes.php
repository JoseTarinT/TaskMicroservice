<?php
use Slim\Routing\RouteCollectorProxy;
use App\TaskController;

/**
 * Catch-all OPTIONS route (CORS preflight)
 */
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->group('/tasks', function (RouteCollectorProxy $group) {
    $controller = new TaskController();

    $group->post('', [$controller, 'create']);           // Create task
    $group->get('', [$controller, 'getAll']);            // List all tasks
    $group->get('/{id}', [$controller, 'getOne']);       // Get one task
    $group->put('/{id}', [$controller, 'update']);       // Update task
    $group->delete('/{id}', [$controller, 'delete']);    // Delete task
    $group->put('/{id}/completed', [$controller, 'markTaskAsCompleted']); // Mark task as completed
});
