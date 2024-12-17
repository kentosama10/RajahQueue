<?php
// app/controllers/QueueController.php

require_once '../core/Controller.php';

class QueueController extends Controller {
    public function index() {
        $queueModel = $this->model('Queue');
        $data['queue'] = $queueModel->getAll();
        $this->view('queue/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $queueModel = $this->model('Queue');
    
            // Sanitize inputs
            $customerName = trim($_POST['customer_name']);
            $serviceType = $_POST['service_type'];
            $region = isset($_POST['region']) ? $_POST['region'] : null;
            $priority = $_POST['priority'];
            $priorityType = ($priority === 'Yes') ? $_POST['priority_type'] : null;
    
            // If errors exist, stop execution and show error
            if (!empty($errors)) {
                echo '<div class="alert alert-danger">';
                foreach ($errors as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                return;
            }
    
            // Insert into the database
            $queueModel->add($customerName, $serviceType, $region, $priority, $priorityType);
            header('Location: /RajahQueue/public/');
            exit;
        }
    }
    
    
    
}
