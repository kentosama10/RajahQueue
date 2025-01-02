<?php

require_once 'Router.php'; // Include the Router class

class App {
    protected $router;

    public function __construct() {
        session_start(); // Start the session for role management
        $this->router = new Router(); // Initialize the Router
        // Role-based access control can be handled here if needed
    }
}
