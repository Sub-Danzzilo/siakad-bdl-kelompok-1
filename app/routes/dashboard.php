<?php

declare(strict_types=1);

use App\Controllers\DashboardController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/', [DashboardController::class, 'index']);
};
