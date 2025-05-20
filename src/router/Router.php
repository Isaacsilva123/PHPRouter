<?php

namespace router\router;

class Router
{
    private array $routes;
    private string $atualPath;

    public function addMapping(string $path, string $method, array $controller, array|null $args = null)
    {
        $this->routes[$path] = [
            "method" => $method,
            "controller" => $controller,
            "arguments" => $args
        ];

        $this->atualPath = $path;

        return $this;
    }

    public function addInterceptor(array $interceptor)
    {
        $this->routes[$this->atualPath]["interceptor"] = $interceptor;
    }

    public function exec()
    {
        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        if (!array_key_exists($uri, $this->routes)) {
            throw new \Exception("Rota não encontrada: " . $uri);
        }

        $methodAtual = $_SERVER["REQUEST_METHOD"];

        if ($this->routes[$uri]["method"] != $methodAtual) {
            throw new \Exception("Método não permitido para esta rota: " . $methodAtual);
        }

        if (isset($this->routes[$uri]["interceptor"])) {
            $response = call_user_func([new $this->routes[$uri]["interceptor"][0], $this->routes[$uri]["interceptor"][1]]);
            if ($response == false) {
                return;
            }
        }
        if ($methodAtual == "GET") {
            echo call_user_func_array([new $this->routes[$uri]["controller"][0], $this->routes[$uri]["controller"][1]], $this->routes[$uri]["arguments"] ?? []);
        } else {
            call_user_func_array([new $this->routes[$uri]["controller"][0], $this->routes[$uri]["controller"][1]], $this->routes[$uri]["arguments"] ?? []);
        }
    }
}
