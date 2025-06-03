<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rajah Queue - Kiosk</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="/RajahQueue/app/assets/images/RTC LOGO 2017 - Vector-02-ORB.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #fad9bc;
        }

        .dashboard-header {
            background-color: #f08221;
            padding: 10px 20px;
            position: relative;
        }

        .dashboard-header .logout-btn {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            background-color: transparent;
            color: white;
            border: none;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .dashboard-header .logout-btn:hover {
            color: #f02127;
        }

        .card {
            border: none;
            border-radius: 10px;
        }

        .card-header {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #f08221;
            border-color: #d0690e;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #CE007C;
            border-color: #0056b3;
        }

        .btn-primary:focus {
            box-shadow: 0 0 0 0.25rem rgba(38, 143, 255, 0.5);
        }

        /* Add transition for showing/hiding dropdowns */
        .dropdown-field {
            transition: max-height 0.3s ease, opacity 0.3s ease;
            max-height: 0;
            /* Initially hidden */
            opacity: 0;
            /* Initially hidden */
            overflow: hidden;
            /* Prevent overflow */
        }

        .dropdown-field.show {
            max-height: 100px;
            /* Adjust based on content */
            opacity: 1;
            /* Fully visible */
        }

        @media print {

            /* Hide everything except the print template */
            body * {
                visibility: hidden;
            }

            #printTemplate,
            #printTemplate * {
                visibility: visible;
            }

            #printTemplate {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <img src="/RajahQueue/app/assets/images/01 RTC Logo.png" alt="RajahQueue Logo" height="50">
            <a href="/RajahQueue/public/user/logout" class="logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Title -->
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Get Your Queue Number Here!</h2>
            <p class="text-muted">Fill out the form below to get started.</p>
        </div>

        <!-- User Form -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <form action="/RajahQueue/public/QueueController/add" method="POST" id="queueForm">
                            <!-- Name Input -->
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Name</label>
                                <input type="text" autocomplete="off" name="customer_name" id="customer_name"
                                    class="form-control" placeholder="Enter your name" required>
                            </div>

                            <!-- Service Type Dropdown -->
                            <div class="mb-3">
                                <label for="service_type" class="form-label">Select Service</label>
                                <select name="service_type" id="service_type" class="form-select" required>
                                    <option value="" disabled selected>-- Select Service --</option>
                                    <!-- <option value="Visa">Visa Only</option> -->
                                    <option value="Payment">Payment Only</option>
                                    <option value="Tours / Cruise">Tours / Cruise Only</option>
                                    <option value="Flights">Flights Only</option>
                                    <!-- <option value="Travel Insurance">Travel Insurance Only</option> -->
                                    <?php if ($_SESSION['role'] !== 'kiosk'): ?>
                                        <option value="Booth">Booth</option>
                                    <?php endif; ?>
                                    <option value="Multiple Services">Multiple Services</option>
                                </select>
                            </div>

                            <!-- Region Dropdown
                            <div class="mb-3 dropdown-field" id="region_field">
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
                            </div> -->

                            <!-- Priority Lane Dropdown
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority Lane</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="No" selected>No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>

                            Priority Type Dropdown
                            <div class="mb-3 dropdown-field" id="priority_type_field">
                                <label for="priority_type" class="form-label">Select Priority Type</label>
                                <select name="priority_type" id="priority_type" class="form-select">
                                    <option value="" disabled selected>-- Select Type --</option>
                                    <option value="PWD">PWD</option>
                                    <option value="Pregnant">Pregnant</option>
                                    <option value="Senior Citizen">Senior Citizen</option>
                                </select>
                            </div> -->

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

    <!-- Hidden Print Template -->
    <div id="printTemplate" style="display: none;">
        <div style="width: 280px; text-align: center; padding: 5px; font-family: 'Century Gothic', CenturyGothic, AppleGothic, sans-serif;">
            <img src="/RajahQueue/app/assets/images/logo-black.png" alt="RTC Logo"
                style="width: 150px; margin-bottom: 0px;"><br>
            <?php if (isset($_SESSION['success_message']) && isset($_SESSION['success_message']['queue_number'])): ?>
                <div style="font-size: 48px; font-weight: bold; margin: 5px 0;">
                    <?= $_SESSION['success_message']['queue_number']; ?>
                </div>
                <div style="font-size: 14px; margin: 0;">
                    <?= date('Y-m-d H:i:s'); ?>
                </div>
                <hr>
                <div style="font-size: 12px; line-height: 1;">
                    Thank you for choosing Rajah Travel!<br>
                    <div style="margin-top: 10px;">
                        <strong>Contact Us:</strong><br>
                        📞 (632) 8894-0886<br>
                        🌐 www.rajahtravel.com<br>
                        📧 webinquiry@rajahtravel.com<br>   
                        📍 3rd Floor 331 Building
                        Sen. Gil Puyat Ave. Makati City, Philippines
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Loading Overlay -->
    <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">Printing your ticket...</div>
        </div>
    </div>

    <!-- Scripts
    <script>
        $(document).ready(function () {
        //     // Toggle region dropdown with animation when "Tour Packages" is selected
        //     $('#service_type').on('change', function () {
        //         if ($(this).val() === 'Tour Packages') {
        //             $('#region_field').addClass('show'); // Add class to show
        //             $('#region').attr('required', 'required');
        //         } else {
        //             $('#region_field').removeClass('show'); // Remove class to hide
        //             $('#region').removeAttr('required').val('');
        //         }
        //     });

        //     // Toggle priority type dropdown with animation when "Yes" is selected
        //     $('#priority').on('change', function () {
        //         if ($(this).val() === 'Yes') {
        //             $('#priority_type_field').addClass('show'); // Add class to show
        //             $('#priority_type').attr('required', 'required');
        //         } else {
        //             $('#priority_type_field').removeClass('show'); // Remove class to hide
        //             $('#priority_type').removeAttr('required').val('');
        //         }
        //     });
        // });
    </script> -->

    <?php if (isset($_SESSION['success_message'])):
        $queueNumber = $_SESSION['success_message']['queue_number'];
        unset($_SESSION['success_message']); // Clear session after showing the message
        ?>
        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0">
                    <!-- Modal Header -->
                    <div class="modal-header text-white" style="background-color: #F08221;">
                        <h5 class="modal-title fw-bold" id="successModalLabel">Queue Added Successfully!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body text-center">
                        <h3 class="fw-bold" style="color: #F08221;">Your Queue Number</h3>
                        <p class="display-4 fw-bold" style="color: #CE007C;"><?= $queueNumber; ?></p>
                        <p class="text-muted">Please wait for your turn. Thank you!</p>
                        <button id="printTicket" class="btn btn-primary mt-3">
                            <i class="bi bi-printer"></i> Print Ticket
                        </button>
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
            });
        </script>
    <?php endif; ?>

    <script>
        document.getElementById('printTicket').addEventListener('click', function () {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = 'block';

            const printContent = document.getElementById('printTemplate').innerHTML;
            const printWindow = window.open('', '_blank', 'width=1,height=1');

            printWindow.document.write(`
                <html>
                <head>
                    <title>Queue Number</title>
                    <style>
                        @media print {
                            @page {
                                margin: 0;
                                size: 80mm 100mm;
                            }
                            body {
                                margin: 0;
                                padding: 10px;
                                font-family: 'Century Gothic', CenturyGothic, AppleGothic, sans-serif;
                                text-align: center;
                            }
                            img {
                                max-width: 150px;
                                margin-bottom: 10px;
                            }
                        }
                    </style>
                </head>
                <body>${printContent}</body>
                </html>
            `);

            printWindow.document.close();
            
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
                loadingOverlay.style.display = 'none';
            }, 500);
        });
    </script>
</body>
<footer><?php include '../app/views/footer.php'; ?></footer>

</html>