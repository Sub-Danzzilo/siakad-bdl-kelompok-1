<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\ErrorController;

final class Router
{
    /** @var array<string, array<int, array{regex: string, handler: array{0: string, 1: string}}>> */
    private array $routes = [];

    /**
     * @param array{0: string, 1: string} $handler [NamaController::class, 'aksi']
     */
    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * @param array{0: string, 1: string} $handler
     */
    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $base = rtrim((string) Config::get('app.base_url', ''), '/');
        $path = ($base !== '' && str_starts_with($uri, $base)) ? substr($uri, strlen($base)) : $uri;
        $path = '/' . trim($path, '/');

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $path, $matches) === 1) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = $route['handler'];
                (new $class())->$action(...array_values(array_map('urldecode', $params)));

                return;
            }
        }

        (new ErrorController())->notFound();
    }

    /**
     * @param array{0: string, 1: string} $handler
     */
    private function add(string $method, string $path, array $handler): void
    {
        // '/mahasiswa/{nim}/ubah' => regex dengan named group
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[$method][] = ['regex' => $regex, 'handler' => $handler];
    }
}
