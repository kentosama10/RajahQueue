<?php include '../app/views/header.php'; ?>
<!DOCTYPE html>
<html lang="en"></html>

</html>

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

    /* Updated styles for the dashboard header */
    .dashboard-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        padding: 0.5rem 1rem;
        margin-bottom: 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .dashboard-header h2 {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
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

    .stats-cancelled {
        border-left: 4px solid #d3212d;
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
        margin: 0;
    }

    .refresh-timer {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
    }

    .refresh-button {
        width: 10rem;
        padding: 7px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .refresh-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, .08);
    }

    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1rem;
    }

    .active-counters-btn {
        white-space: nowrap;
    }
</style>
</head>

<body>
    <div class="dashboard-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Payment Dashboard</h2>
            <div class="d-flex align-items-center gap-3">
                <span class="refresh-timer">
                    Auto-refresh in: <span id="countdown">10</span>s
                </span>
                <button class="btn btn-primary refresh-button" onclick="loadPaymentQueue()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Now
                </button>
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <div class="header-actions d-flex justify-content-between align-items-center">
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

            <!-- Counter Selection and Show Active Counters -->
            <div class="d-flex align-items-center gap-3">
                <select id="cashierCounterSelect" class="form-select counter-select" onchange="updateCashierCounter()">
                    <option value="" disabled selected>Choose Counter</option>
                    <option value="release">Release Counter</option>
                    <option value="1">Counter 1</option>
                    <option value="2">Counter 2</option>
                    <option value="3">Counter 3</option>
                    <option value="4">Counter 4</option>
                    <option value="5">Counter 5</option>
                    <option value="6">Counter 6</option>
                    <option value="7">Counter 7</option>
                    <option value="8">Counter 8</option>
                    <option value="9">Counter 9</option>
                    <option value="10">Counter 10</option>
                    <option value="11">Counter 11</option>
                    <option value="12">Counter 12</option>
                    <option value="13">Counter 13</option>
                    <option value="14">Counter 14</option>
                    <option value="15">Counter 15</option>
                    <option value="16">Counter 16</option>
                    <option value="17">Counter 17</option>
                    <option value="18">Counter 18</option>
                    <option value="19">Counter 19</option>
                    <option value="20">Counter 20</option>
                </select>
                <button id="toggleActiveCounters" class="btn btn-primary active-counters-btn" onclick="toggleActiveCounters()">
                    Show Active Counters
                </button>
            </div>
        </div>

        <!-- Active Counters List -->
        <ul id="activeCountersList" class="list-group mt-2" 
            style="display: none; transition: max-height 0.5s ease-out; overflow: hidden;">
        </ul>
    </div>

    <div class="container">
        <!-- Payment Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="payment-stats stats-pending">
                    <h4 class="mb-2">Pending Payments</h4>
                    <h2 class="mb-0" id="pendingCount">0</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="payment-stats stats-completed">
                    <h4 class="mb-2">Completed Today</h4>
                    <h2 class="mb-0" id="completedCount">0</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="payment-stats stats-cancelled">
                    <h4 class="mb-2">Cancelled Payments</h4>
                    <h2 class="mb-0" id="cancelledCount">0</h2>
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
        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const userRole = '<?php echo $_SESSION['role']; ?>';
        let currentPage = 1;
        let countdownValue = 10;
        let countdownInterval;

        function startCountdown() {
            clearInterval(countdownInterval);
            countdownValue = 10;
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

        function showLoadingSpinner() {
            document.getElementById('loadingSpinner').style.display = 'block';
        }

        function hideLoadingSpinner() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }

        function loadPaymentQueue() {
            showLoadingSpinner(); // Show spinner
            $('#errorMessage').hide();

            $.ajax({
                url: '/RajahQueue/public/payment/getPaymentQueue',
                method: 'GET',
                data: {
                    page: currentPage,
                    search: $('#searchInput').val()
                },
                dataType: 'json',
                success: function (data) {
                    updatePaymentDashboard(data);
                    startCountdown();
                    hideLoadingSpinner(); // Hide spinner after success
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching payment queue data:', error);
                    $('#errorMessage').text('Failed to load payment queue. Please try again later.').show();
                    hideLoadingSpinner(); // Hide spinner after error
                },
            });
        }

        function updatePaymentDashboard(data) {
            // Update statistics
            $('#pendingCount').text(data.stats?.pending || 0);
            $('#completedCount').text(data.stats?.completed || 0);
            $('#cancelledCount').text(data.stats?.cancelled || 0);

            // Sort payments to ensure 'serving' status is at the top, followed by 'pending', 'completed', and 'cancelled'
            const sortedPayments = data.payments.sort((a, b) => {
                const statusOrder = ['serving', 'pending', 'completed', 'cancelled'];
                return statusOrder.indexOf(a.payment_status.toLowerCase()) - statusOrder.indexOf(b.payment_status.toLowerCase());
            });

            // Update payment table
            const paymentTableBody = $('#paymentTableBody');
            paymentTableBody.empty();

            if (!sortedPayments || sortedPayments.length === 0) {
                paymentTableBody.append(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">No payments pending.</td>
                    </tr>
                `);
            } else {
                sortedPayments.forEach(item => {
                    paymentTableBody.append(`
                        <tr>
                            <td class="text-center">${item.queue_number}</td>
                            <td class="text-center">${item.customer_name}</td>
                            <td class="text-center">${item.service_type}</td>
                            <td class="text-center">${item.first_name ? `${item.first_name} ${item.last_name}` : '—'}</td>
                            <td class="text-center">
                                <span class="badge ${getStatusBadgeClass(item.payment_status)}">${item.payment_status}</span>
                            </td>
                            <td class="text-center">
                                ${getActionButtons(item)}
                            </td>
                        </tr>
                    `);
                });
            }

            // Update pagination
            updatePagination(data.totalCount || 0);
        }

        function getStatusBadgeClass(status) {
            switch (status?.toLowerCase()) {
                case 'pending':
                    return 'bg-warning';
                case 'serving':
                    return 'bg-primary';
                case 'completed':
                    return 'bg-success';
                case 'cancelled':
                    return 'bg-danger';
                default:
                    return 'bg-secondary';
            }
        }

        function getActionButtons(item) {
            switch (item.payment_status.toLowerCase()) {
                case 'pending':
                    return `
                        <button class="btn btn-sm btn-primary" onclick="updatePaymentStatus('${item.queue_number}', 'Serving')">
                            <i class="bi bi-play-fill"></i> Start
                        </button>`;
                case 'serving':
                    // Always use the selected counter if item.counter_number is missing
                    let counterNum = item.counter_number;
                    if (!counterNum) {
                        const select = document.getElementById("cashierCounterSelect");
                        counterNum = select && select.value && select.value !== "release" ? select.value : '';
                    }
                    return `
                        <button class="btn btn-sm btn-success" onclick="completePayment('${item.queue_number}')">
                            <i class="bi bi-check-circle"></i> Complete
                        </button>
                        <button class="btn btn-sm btn-danger ms-2" onclick="cancelPayment('${item.queue_number}')">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button class="btn btn-sm btn-primary ms-2" onclick="triggerAnnouncement('${item.queue_number}', '${counterNum}')">
                            <i class="bi bi-megaphone"></i> 
                        </button>`;
                default:
                    return '';
            }
        }

        function triggerAnnouncement(queueNumber, counterNumber) {
            if (!queueNumber || !counterNumber) {
                alert('Counter number is missing for this queue.');
                return;
            }
            $.ajax({
                url: '/RajahQueue/public/dashboard/announceManual',
                method: 'POST',
                data: {
                    queue_number: queueNumber,
                    counter_number: counterNumber
                },
                success: function(response) {
                    alert('Announcement triggered. Please wait for the audio to play.');
                },
                error: function() {
                    alert('Failed to trigger announcement.');
                }
            });
        }

        function updatePaymentStatus(queueNumber, newStatus) {
            const selectedCounter = document.getElementById("cashierCounterSelect").value;
            
            if (!selectedCounter || selectedCounter === "release") {
                alert('Please select a counter first before serving customers.');
                return;
            }

            $.ajax({
                url: '/RajahQueue/public/payment/updatePaymentStatus',
                method: 'POST',
                data: {
                    queue_number: queueNumber,
                    payment_status: newStatus,
                    counter_number: selectedCounter
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        loadPaymentQueue();
                    } else {
                        alert(response.message || 'Failed to update payment status. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error updating payment status:', error);
                    alert('Error updating payment status. Please try again.');
                }
            });
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
            const receiptNumber = prompt('Please enter the receipt number:');
            if (receiptNumber === null) {
                alert('Receipt number is required to complete the payment.');
                return;
            }

            showLoadingSpinner(); // Show spinner

            $.ajax({
                url: '/RajahQueue/public/payment/completePayment',
                method: 'POST',
                data: { queue_number: queueNumber, receipt_number: receiptNumber },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        loadPaymentQueue();
                    } else {
                        alert(response.message || 'Failed to complete payment. Please try again.');
                    }
                    hideLoadingSpinner(); // Hide spinner after success
                },
                error: function (xhr, status, error) {
                    alert('Error completing payment. Please try again.');
                    hideLoadingSpinner(); // Hide spinner after error
                }
            });
        }

        function cancelPayment(queueNumber) {
            if (!confirm('Are you sure you want to cancel this payment?')) {
                return;
            }

            showLoadingSpinner(); // Show spinner

            $.ajax({
                url: '/RajahQueue/public/payment/cancelPayment',
                method: 'POST',
                data: { queue_number: queueNumber },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        loadPaymentQueue();
                    } else {
                        alert(response.message || 'Failed to cancel payment. Please try again.');
                    }
                    hideLoadingSpinner(); // Hide spinner after success
                },
                error: function (xhr, status, error) {
                    console.error('Error canceling payment:', error);
                    alert('Error canceling payment. Please try again.');
                    hideLoadingSpinner(); // Hide spinner after error
                }
            });
        }

        function updateCashierCounter() {
            const selectedCounter = document.getElementById("cashierCounterSelect").value;

            if (!selectedCounter) {
                alert("Please select a cashier counter first.");
                return;
            }

            const isReleasingCounter = selectedCounter === "release";

            // First check counter availability before updating
            if (!isReleasingCounter) {
                $.ajax({
                    url: "/RajahQueue/public/user/checkCounterAvailability",
                    method: "POST",
                    data: { counter_number: selectedCounter },
                    success: function (response) {
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            if (!data.available) {
                                alert("This counter is already assigned to another user.");
                                refreshCashierCounterSelect(); // Reset the dropdown
                                return;
                            }
                            // If available, proceed with counter update
                            proceedWithCounterUpdate(selectedCounter, isReleasingCounter);
                        } catch (e) {
                            console.error("Error checking counter availability:", e);
                            alert("An error occurred while checking counter availability.");
                            refreshCashierCounterSelect();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error checking counter availability:", error);
                        alert("An error occurred while checking counter availability.");
                        refreshCashierCounterSelect();
                    }
                });
            } else {
                // If releasing counter, proceed directly
                proceedWithCounterUpdate(selectedCounter, isReleasingCounter);
            }
        }

        function proceedWithCounterUpdate(selectedCounter, isReleasingCounter) {
            $.ajax({
                url: "/RajahQueue/public/user/updateCounter",
                method: "POST",
                data: {
                    counter_number: isReleasingCounter ? null : selectedCounter
                },
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;

                        if (data.success) {
                            if (isReleasingCounter) {
                                alert("Counter successfully released!");
                                document.getElementById("cashierCounterSelect").value = ""; // Reset to default
                            } else {
                                alert(`You are now assigned to Counter ${selectedCounter}.`);
                            }
                            loadPaymentQueue(); // Refresh the payment queue to update counter information
                        } else {
                            alert(data.message || "Failed to update the counter. Please try again.");
                            refreshCashierCounterSelect();
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e);
                        alert("An error occurred while processing the response.");
                        refreshCashierCounterSelect();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error updating counter:", error);
                    alert("An error occurred while updating the counter. Please try again.");
                    refreshCashierCounterSelect();
                }
            });
        }

        function refreshCashierCounterSelect() {
            $.ajax({
                url: "/RajahQueue/public/user/getCounter",
                method: "GET",
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        const select = document.getElementById("cashierCounterSelect");

                        if (data.counter_number) {
                            select.value = data.counter_number;
                        } else {
                            select.value = ""; // Reset to default if no counter is assigned
                        }
                    } catch (e) {
                        console.error("Error refreshing counter select:", e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching counter info:", error);
                }
            });
        }

        // Add this function to fetch and display active counters
        function fetchActiveCounters() {
            $.ajax({
                url: '/RajahQueue/public/dashboard/getActiveCounters',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    const activeCountersList = $('#activeCountersList');
                    activeCountersList.empty();
                    // Sort counters by counter_number
                    response.activeCounters.sort((a, b) => a.counter_number - b.counter_number);
                    response.activeCounters.forEach(counter => {
                        activeCountersList.append(`<li>Counter ${counter.counter_number}: ${counter.first_name}</li>`);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching active counters:', error);
                }
            });
        }

        // Update the document ready function
        $(document).ready(function () {
            refreshCashierCounterSelect();
            loadPaymentQueue();
            fetchActiveCounters();
            setInterval(fetchActiveCounters, 15000); // Refresh every 15 seconds

            // Add event listener for counter select changes
            document.getElementById("cashierCounterSelect").addEventListener("change", function (e) {
                const selectedValue = e.target.value;
                if (selectedValue && selectedValue !== "release") {
                    // Check if counter is already assigned
                    $.ajax({
                        url: "/RajahQueue/public/user/checkCounterAvailability",
                        method: "POST",
                        data: { counter_number: selectedValue },
                        success: function (response) {
                            try {
                                const data = typeof response === 'string' ? JSON.parse(response) : response;
                                if (!data.available) {
                                    alert("This counter is already assigned to another user.");
                                    refreshCashierCounterSelect(); // Reset the dropdown
                                    return;
                                }
                                // If available, proceed with updateCashierCounter
                                updateCashierCounter();
                            } catch (e) {
                                console.error("Error checking counter availability:", e);
                                alert("An error occurred while checking counter availability.");
                                refreshCashierCounterSelect();
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error checking counter availability:", error);
                            alert("An error occurred while checking counter availability.");
                            refreshCashierCounterSelect();
                        }
                    });
                } else {
                    // If releasing or no counter selected, proceed normally
                    updateCashierCounter();
                }
            });
        });

        // Add the toggle function
        function toggleActiveCounters() {
            var activeCountersList = $('#activeCountersList');
            if (activeCountersList.css('display') === 'none') {
                activeCountersList.css('display', 'block').hide().slideDown();
            } else {
                activeCountersList.slideUp(function () {
                    $(this).css('display', 'none');
                });
            }
        }
    </script>
</body>

</html>