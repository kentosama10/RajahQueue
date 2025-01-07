<?php
// core/Controller.php

class Controller {
    public function view($view, $data = []) {
        require_once '../app/views/' . $view . '.php';
    }

    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    // Check if the user is logged in
    protected function isAuthenticated() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    // Redirect to login page if not authenticated
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: /RajahQueue/public/UserController/showLoginForm');
            exit();
        }
    }
}
