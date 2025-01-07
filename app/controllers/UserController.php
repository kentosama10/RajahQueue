<?php

require_once '../core/Controller.php';

class UserController extends Controller {
    // Update the counter for the logged-in user
    public function updateCounter() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in.']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $counter = $_POST['counter'];

        $userModel = $this->model('User');
        $success = $userModel->updateCounter($userId, $counter);

        echo json_encode(['success' => $success]);
    }

    // Fetch the counter for the logged-in user
    public function getCounter() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['counter' => null]);
            return;
        }

        $userId = $_SESSION['user_id'];

        $userModel = $this->model('User');
        $counter = $userModel->getCounter($userId);

        echo json_encode(['counter' => $counter]);
    }

    // Show the login form
    public function showLoginForm() {
        $this->view('user/login');
    }

    // Handle the login request
    public function login() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $userModel = $this->model('User');
            $user = $userModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 'kiosk') {
                    header('Location: /RajahQueue/public/QueueController/index');
                } else {
                    header('Location: /RajahQueue/public/DashboardController/index');
                }
                exit;
            } else {
                // Handle invalid login
                $this->view('user/login', ['error' => 'Invalid username or password']);
            }
        } else {
            // Handle missing username or password
            $this->view('user/login', ['error' => 'Please enter both username and password']);
        }
    }

    // Handle the logout request
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /RajahQueue/public/UserController/showLoginForm');
        exit();
    }

    // Show the registration form
    public function showRegisterForm() {
        $this->view('user/register');
    }

    // Handle the registration request
    public function register() {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $userModel = $this->model('User');
        $success = $userModel->createUser($username, $password);

        if ($success) {
            header('Location: /RajahQueue/public/UserController/showLoginForm');
            exit;
        } else {
            echo 'Registration failed';
        }
    }
}