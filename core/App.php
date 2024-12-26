<?php

class App {
    protected $controller = 'QueueController';
    protected $method = 'index';
    protected $params = [];

    
    public function __construct() {
        session_start(); // Start the session for role management
        $url = $this->parseUrl();

        // Handle controller
        if (file_exists('../app/controllers/' . $url[0] . '.php')) {
            $this->controller = $url[0];
            unset($url[0]);
        }
        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Handle method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];

        // Role-based access control
        $this->checkRoleAccess($this->controller, $this->method);

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
    
        // Default to LoginController if no URL is provided
        return ['LoginController', 'index']; // Redirect to LoginController's index method
    }
    
    

    

    private function checkRoleAccess($controller, $method) {
        // Define role-based access rules
        $rolePermissions = [
            'QueueController' => ['kiosk', 'staff', 'admin'],
            'AdminController' => ['admin'],
            'StaffController' => ['staff', 'admin']
        ];

        // Get the controller name as a string
        $controllerName = is_object($controller) ? get_class($controller) : $controller;

        // Get the user's role from the session
        $role = $_SESSION['role'] ?? null;

        // Check if the user has access to the controller
        if (isset($rolePermissions[$controllerName]) && !in_array($role, $rolePermissions[$controllerName])) {
            header('Location: /LoginController/index');
            exit;
        }

        
    }
}
