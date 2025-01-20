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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Set JSON header before any output
        header('Content-Type: application/json');

        try {
            // Get the current user's ID from session
            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            if (!$userId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not logged in',
                    'counter_number' => null
                ]);
                exit;
            }

            $userModel = $this->model('User');
            $counterInfo = $userModel->getUserCounter($userId);

            if ($counterInfo) {
                echo json_encode([
                    'success' => true,
                    'counter_number' => $counterInfo['counter_number'],
                    'message' => 'Counter found'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'counter_number' => null,
                    'message' => 'No counter assigned'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error retrieving counter information',
                'counter_number' => null
            ]);
        }
        exit;
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
                $_SESSION['first_name'] = $user['first_name'];

                if ($user['role'] == 'kiosk') {
                    header('Location: /RajahQueue/public/QueueController/index');
                } else if ($user['role'] == 'user') {
                    header('Location: /RajahQueue/public/DashboardController/index');
                } else if ($user['role'] == 'cashier') {
                    header('Location: /RajahQueue/public/PaymentController/index');
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
        // Check if the required fields are set
        if (isset($_POST['first_name'], $_POST['last_name'], $_POST['username'], $_POST['password'])) {
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            $userModel = $this->model('User');
            $success = $userModel->createUser($firstName, $lastName, $username, $password);

            if ($success) {
                header('Location: /RajahQueue/public/UserController/showLoginForm');
                exit;
            } else {
                echo 'Registration failed';
            }
        } else {
            echo 'Please fill in all fields.';
        }
    }

    public function checkCounterAvailability() {
        // Set JSON header before any output
        header('Content-Type: application/json');
        
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Default response
            $response = [
                'success' => false,
                'available' => false,
                'message' => ''
            ];

            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                $response['message'] = 'User not logged in';
                echo json_encode($response);
                exit;
            }

            // Check if it's a POST request with counter_number
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['counter_number'])) {
                $response['message'] = 'Invalid request or missing counter number';
                echo json_encode($response);
                exit;
            }

            $counter_number = filter_var($_POST['counter_number'], FILTER_VALIDATE_INT);
            if ($counter_number === false) {
                $response['message'] = 'Invalid counter number format';
                echo json_encode($response);
                exit;
            }

            $userModel = $this->model('User');
            $activeUser = $userModel->getActiveUserForCounter($counter_number);

            // Counter is available if:
            // 1. No active user is assigned to it, or
            // 2. It's assigned to the current user
            $isAvailable = !$activeUser || 
                (isset($_SESSION['user_id']) && $activeUser['id'] == $_SESSION['user_id']);

            $response = [
                'success' => true,
                'available' => $isAvailable,
                'message' => $isAvailable ? 
                    'Counter is available' : 
                    'Counter is already assigned to another user'
            ];

            echo json_encode($response);
            exit;

        } catch (Exception $e) {
            error_log("Error in checkCounterAvailability: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'available' => false,
                'message' => 'An error occurred while checking counter availability'
            ]);
            exit;
        }
    }
}