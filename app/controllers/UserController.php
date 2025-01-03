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
}