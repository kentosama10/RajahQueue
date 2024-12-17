<?php
session_start();
if (isset($_SESSION['success_message'])):
    $queueNumber = $_SESSION['success_message']['queue_number'];
    unset($_SESSION['success_message']); // Clear session after showing the message
    ?>
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Queue Added Successfully!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h3 class="fw-bold text-primary">Your Queue Number</h3>
                    <p class="display-4 fw-bold text-success"><?= $queueNumber; ?></p>
                    <p>Please wait for your turn. Thank you!</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                backdrop: 'static', // Prevent closing by clicking outside
                keyboard: false     // Prevent closing with the escape key
            });
            successModal.show();
    </script>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rajah Queue - Kiosk</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts - Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        h1 {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container mt-5">

        <!-- User Form -->
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <!-- Card Interface -->
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white text-center">
                            <h3 class="mb-0">RajahQueue</h3>
                        </div>
                        <div class="card-body">
                            <form action="/RajahQueue/public/QueueController/add" method="POST" id="queueForm">
                                <!-- Name Input -->
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label">Your Name</label>
                                    <input type="text" autocomplete="off" name="customer_name" id="customer_name" class="form-control"
                                        placeholder="Enter your name" required>
                                </div>

                                <!-- Service Type Dropdown -->
                                <div class="mb-3">
                                    <label for="service_type" class="form-label">Select Service</label>
                                    <select name="service_type" id="service_type" class="form-select" required>
                                        <option value="" disabled selected>-- Select Service --</option>
                                        <option value="Visa">Visa</option>
                                        <option value="Tour Packages">Tour Packages</option>
                                        <option value="Flights">Flights</option>
                                        <option value="Travel Insurance">Travel Insurance</option>
                                    </select>
                                </div>

                                <!-- Region Dropdown -->
                                <div class="mb-3" id="region_field" style="display: none;">
                                    <label for="region" class="form-label">Select Region</label>
                                    <select name="region" id="region" class="form-select">
                                        <option value="" disabled selected>-- Select Region --</option>
                                        <option value="Philippines">Philippines</option>
                                        <option value="America">America</option>
                                        <option value="Europe">Europe</option>
                                        <option value="Africa">Africa</option>
                                        <option value="Asia">Asia</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>

                                <!-- Priority Lane Dropdown -->
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority Lane</label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="No" selected>No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>

                                <!-- Priority Type Dropdown -->
                                <div class="mb-3" id="priority_type_field" style="display: none;">
                                    <label for="priority_type" class="form-label">Select Priority Type</label>
                                    <select name="priority_type" id="priority_type" class="form-select">
                                        <option value="" disabled selected>-- Select Type --</option>
                                        <option value="PWD">PWD</option>
                                        <option value="Pregnant">Pregnant</option>
                                        <option value="Senior Citizen">Senior Citizen</option>
                                    </select>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Add to Queue</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Queue Table
        <div class="mt-4 bg-white shadow rounded p-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Service Type</th>
                        <th>Region</th>
                        <th>Priority</th>
                        <th>Priority Type</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php if (isset($data['queue']) && count($data['queue']) > 0): ?>
                        <?php foreach ($data['queue'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['queue_number']); ?></td>
                                <td><?= htmlspecialchars($item['customer_name']); ?></td>
                                <td><?= htmlspecialchars($item['service_type']); ?></td>
                                <td><?= htmlspecialchars($item['region'] ?: '-'); ?></td>
                                <td><?= htmlspecialchars($item['priority']); ?></td>
                                <td><?= htmlspecialchars($item['priority_type'] ?: '-'); ?></td>
                                <td><?= htmlspecialchars($item['status']); ?></td>
                                <td><?= htmlspecialchars($item['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>


        </div>
    </div>
                    -->

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery CDN -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- jQuery Validation Script -->
        <script>


            $(document).ready(function () {
                // Toggle region dropdown when "Tour Packages" is selected
                $('#service_type').on('change', function () {
                    if ($(this).val() === 'Tour Packages') {
                        $('#region_field').show();
                        $('#region').attr('required', 'required');
                    } else {
                        $('#region_field').hide();
                        $('#region').removeAttr('required').val('');
                    }
                });

                // Toggle priority type dropdown when "Yes" is selected
                $('#priority').on('change', function () {
                    if ($(this).val() === 'Yes') {
                        $('#priority_type_field').show();
                        $('#priority_type').attr('required', 'required');
                    } else {
                        $('#priority_type_field').hide();
                        $('#priority_type').removeAttr('required').val('');
                    }
                });

                // Form submission validation
                $('#queueForm').on('submit', function (e) {
                    let isValid = true;

                });
            });
        </script>

        </script>
</body>

</html>