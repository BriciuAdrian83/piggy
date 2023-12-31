<?php

declare(strict_types=1);

namespace Framework;

use App\Controller\HomeController;


class Router
{
  private array $routes = [];

  public function add(string $method, string $path, array $controller)
  {
    $path = $this->normalizePath($path);

    $this->routes[] = [
      'method' => strtoupper($method),
      'path' => $path,
      'controller' => $controller
    ];
  }

  private function normalizePath(string $path): string
  {
    $path = trim($path, '/');

    $path = "/{$path}/";

    $pattern = '#[/]{2,}#';
    $replacingChar = '/';
    $path = preg_replace($pattern, $replacingChar, $path);

    return $path;
  }

  public function dispatch(string $path, string $method, Container $container = null)
   {
    $path = $this->normalizePath($path);
    $method = strtoupper($method);

    foreach ($this->routes as $route) {
      if (
        !preg_match("#^{$route['path']}$#", $path) ||
        $route['method'] !== $method
      ) {
        continue;
      }

      [$class, $function] = $route['controller'];


      $controllerInstance = $container ?
        $container->resolve($class) :
        new $class;

      $controllerInstance->{$function}();
    }
  }
}
