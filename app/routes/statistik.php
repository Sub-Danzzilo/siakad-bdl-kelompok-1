<?php

declare(strict_types=1);

use App\Controllers\StatistikController;
use App\Core\Router;

return static function (Router $router): void {
    $router->get('/statistik', [StatistikController::class, 'index']);
};
