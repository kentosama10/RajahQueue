<?php

class Router {
    protected $controller = 'QueueController';
    protected $method = 'index';
    protected $params = [];
    protected $routes = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Handle controller
        if (file_exists('../app/controllers/' . $url[0] . '.php')) {
            $this->controller = $url[0];
            unset($url[0]);
        }
        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Handle method
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];

        // Call the controller method with parameters
        call_user_func_array([$this->controller, $this->method], $this->params);

        // Add this route in the Router constructor or wherever you define routes
        $this->addRoute('GET', 'DashboardController/display', 'DashboardController@display');
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return ['UserController', 'showLoginForm']; // Default route
    }
    

    // Method to add routes
    public function addRoute($method, $path, $handler) {
        // Logic to store the route
        // This could be an array or any other structure to hold routes
        $this->routes[] = ['method' => $method, 'path' => $path, 'handler' => $handler];
    }

    

}