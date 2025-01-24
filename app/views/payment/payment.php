<?php include '../app/views/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Dashboard - RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-header {
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .08);
            padding: 0.5rem 0;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
        }

        .dashboard-header h2 {
            font-weight: 600;
            color: #2c3e50;
        }

        .payment-stats {
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            text-align: center;
        }

        .stats-pending {
            border-left: 4px solid #ffc107;
        }

        .stats-completed {
            border-left: 4px solid #28a745;
        }

        .payment-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .table th {
            background-color: #f8f9fa;
        }

        .search-container {
            max-width: 400px;
            margin-bottom: 1rem;
        }

        .refresh-timer {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
        }

        .refresh-button {
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .refresh-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, .08);
        }
    </style>
</head>

<body>
    <div class="dashboard-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Payment Dashboard</h2>
            <div class="d-flex align-items-center gap-3">
                <span class="refresh-timer">
                    Auto-refresh in: <span id="countdown">15</span>s
                </span>
                <button class="btn btn-primary refresh-button" onclick="loadPaymentQueue()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Now
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Search Bar -->
        <div class="search-container">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Search by customer name or queue number" onkeyup="searchPayments()">
            </div>
        </div>

        <!-- Payment Statistics -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="payment-stats stats-pending">
                    <h4 class="mb-2">Pending Payments</h4>
                    <h2 class="mb-0" id="pendingCount">0</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="payment-stats stats-completed">
                    <h4 class="mb-2">Completed Today</h4>
                    <h2 class="mb-0" id="completedCount">0</h2>
                </div>
            </div>
        </div>

        <!-- Payment Queue Table -->
        <div class="payment-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">Queue #</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Service Type</th>
                            <th class="text-center">Served By</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="paymentTableBody">
                        <!-- Payment items will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <button class="btn btn-secondary" id="prevPage" onclick="changePage(currentPage - 1)" disabled>
                    <i class="bi bi-chevron-left"></i> Previous
                </button>
                <button class="btn btn-secondary" id="nextPage" onclick="changePage(currentPage + 1)">
                    Next <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <span id="pageInfo" class="text-muted"></span>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentPage = 1;
        let countdownValue = 15;
        let countdownInterval;

        function startCountdown() {
            clearInterval(countdownInterval);
            countdownValue = 15;
            updateCountdown();
            countdownInterval = setInterval(() => {
                countdownValue--;
                updateCountdown();
                if (countdownValue <= 0) {
                    loadPaymentQueue();
                }
            }, 1000);
        }

        function updateCountdown() {
            document.getElementById('countdown').textContent = countdownValue;
        }

        function loadPaymentQueue() {
            $('#errorMessage').hide();

            $.ajax({
                url: '/RajahQueue/public/PaymentController/getPaymentQueue',
                method: 'GET',
                data: {
                    page: currentPage,
                    search: $('#searchInput').val()
                },
                dataType: 'json',
                success: function (data) {
                    updatePaymentDashboard(data);
                    startCountdown();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching payment queue data:', error);
                    $('#errorMessage').text('Failed to load payment queue. Please try again later.').show();
                },
            });
        }

        function updatePaymentDashboard(data) {
            // Update statistics
            $('#pendingCount').text(data.stats?.pending || 0);
            $('#completedCount').text(data.stats?.completed || 0);

            // Update payment table
            const paymentTableBody = $('#paymentTableBody');
            paymentTableBody.empty();

            if (!data.payments || data.payments.length === 0) {
                paymentTableBody.append(`
                    <tr>
                        <td colspan="5" class="text-center text-muted">No payments pending.</td>
                    </tr>
                `);
            } else {
                data.payments.forEach(item => {
                    paymentTableBody.append(`
                        <tr>
                            <td class="text-center">${item.queue_number}</td>
                            <td class="text-center">${item.customer_name}</td>
                            <td class="text-center">${item.service_type}</td>
                            <td class="text-center">${item.first_name ? `${item.first_name} ${item.last_name}` : 'Not assigned'}</td>
                            <td class="text-center">
                                <span class="badge bg-warning">Pending Payment</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-success btn-sm" onclick="completePayment('${item.queue_number}')">
                                        <i class="bi bi-check-circle"></i> Complete
                                    </button>
                                    <button class="btn btn-danger btn-sm ms-2" onclick="cancelPayment('${item.queue_number}')">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }

            // Update pagination
            updatePagination(data.totalCount || 0);
        }

        function updatePagination(totalCount) {
            const itemsPerPage = 20;
            const totalPages = Math.ceil(totalCount / itemsPerPage);

            $('#pageInfo').text(`Page ${currentPage} of ${totalPages}`);
            $('#prevPage').prop('disabled', currentPage === 1);
            $('#nextPage').prop('disabled', currentPage === totalPages);
        }

        function changePage(newPage) {
            currentPage = newPage;
            loadPaymentQueue();
        }

        function searchPayments() {
            currentPage = 1; // Reset to first page when searching
            loadPaymentQueue();
        }

        function completePayment(queueNumber) {
            if (!confirm('Are you sure you want to mark this payment as completed?')) {
                return;
            }

            $.ajax({
                url: '/RajahQueue/public/PaymentController/completePayment',
                method: 'POST',
                data: { queue_number: queueNumber },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        loadPaymentQueue();
                    } else {
                        alert(response.message || 'Failed to complete payment. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error completing payment:', error);
                    alert('Error completing payment. Please try again.');
                }
            });
        }

        function cancelPayment(queueNumber) {
            if (!confirm('Are you sure you want to cancel this payment?')) {
                return;
            }

            $.ajax({
                url: '/RajahQueue/public/PaymentController/cancelPayment',
                method: 'POST',
                data: { queue_number: queueNumber },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        loadPaymentQueue();
                    } else {
                        alert(response.message || 'Failed to cancel payment. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error canceling payment:', error);
                    alert('Error canceling payment. Please try again.');
                }
            });
        }

        // Initial load
        $(document).ready(function () {
            loadPaymentQueue();
        });
    </script>
</body>
<footer><?php include '../app/views/footer.php'; ?></footer>
</html>
