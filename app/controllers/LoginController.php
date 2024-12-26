<?php

require_once '../core/Controller.php';

class LoginController extends Controller {
    public function index() {
        // Redirect to dashboard if already logged in
        if (isset($_SESSION['role'])) {
            switch ($_SESSION['role']) {
                case 'kiosk':
                    header('Location: /RajahQueue/public/QueueController/index');
                    exit;
                case 'staff':
                    header('Location: /RajahQueue/public/QueueController/dashboard');
                    exit;
                case 'admin':
                    header('Location: /RajahQueue/public/QueueController/dashboard'); // Adjust as needed for admin
                    exit;
            }
        }
        
        // If not logged in, show the login form
        $this->view('login/index');
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $userModel = $this->model('User');
            $user = $userModel->getUserByUsername($username);

            if ($user && md5($password) === $user['password']) { // Use password_verify for secure hashing
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'kiosk':
                        header('Location: /RajahQueue/public/queue/index');
                        break;
                    case 'staff':
                        header('Location: /RajahQueue/public/queue/dashboard');
                        break;
                }
                exit;
            } else {
                $_SESSION['error'] = 'Invalid username or password';
                header('Location: /RajahQueue/public/LoginController/index');
                exit;
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /RajahQueue/public/LoginController/index');
        exit;
    }
}
