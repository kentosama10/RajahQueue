<?php

require_once '../core/Controller.php';

class UserController extends Controller {
    // Update the counter for the logged-in user
    public function updateCounter() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        error_log("Session data: " . print_r($_SESSION, true));
        error_log("POST data: " . print_r($_POST, true));

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in.']);
            return;
        }
    
        $userId = (int)$_SESSION['user_id'];
        $counter_number = isset($_POST['counter_number']) ? (int)$_POST['counter_number'] : null;
    
        // Add debug logging
        error_log("Attempting to update counter: Counter={$counter_number}, UserID={$userId}");
    
        if ($counter_number !== null && !is_numeric($counter_number)) {
            echo json_encode(['success' => false, 'message' => 'Invalid counter number format']);
            return;
        }
    
        $userModel = $this->model('User');
    
        if (is_null($counter_number)) {
            // Release counter
            $success = $userModel->releaseCounter($userId);
            $message = $success ? 'Counter released successfully.' : 'Failed to release counter.';
        } else {
            // Assign counter
            $success = $userModel->updateCounter($counter_number, $userId);
            $message = $success ? 'Counter updated successfully.' : 'Failed to update counter.';
        }
    
        // Add debug logging
        error_log("Update counter result: Success={$success}, Message={$message}");
    
        echo json_encode(['success' => $success, 'message' => $message]);
    }
    
    // Fetch the counter for the logged-in user
    public function getCounter() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['counter_number' => null]);
            return;
        }
    
        $counter_number = $_GET['counter_number'] ?? null; // Optionally accept a counter ID in the request
        if (!$counter_number || !is_numeric($counter_number)) {
            echo json_encode(['counter_number' => null, 'message' => 'Invalid counter specified.']);
            return;
        }
    
        $userModel = $this->model('User');
    
        // Fetch the active user for the specified counter
        $activeUser = $userModel->getActiveUserForCounter($counter_number);
    
        if ($activeUser) {
            echo json_encode(['counter_number' => $counter_number, 'active_user' => $activeUser]);
        } else {
            echo json_encode(['counter_number' => $counter_number, 'active_user' => null, 'message' => 'No active user for this counter.']);
        }
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