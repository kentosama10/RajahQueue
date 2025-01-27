<?php include '../app/views/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Dashboard - RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/RajahQueue/app/assets/css/dashboard.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    

</head>

<body>
    <div class="dashboard-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Queue Dashboard</h2>
            <div class="d-flex align-items-center gap-3">
                <span class="refresh-timer">
                    Auto-refresh in: <span id="countdown">15</span>s
                </span>
                <button class="btn btn-primary refresh-button" onclick="refreshDashboard()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Now
                </button>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        <div class="row">
            <!-- Main Queue Content -->
            <div class="col-lg-9">
                <div class="controls-wrapper">
                    <div class="d-flex align-items-center gap-4">
                        <!-- Search Bar -->
                        <div class="search-container">
                            <div class="input-group">
                                <span class="input-group-text" id="search-addon">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Search by customer name or queue number" onkeyup="searchQueue()"
                                    aria-label="Search" aria-describedby="search-addon">
                            </div>
                        </div>

                        <!-- Counter Dropdown -->
                        <div class="counter-container">
                            <select id="counterSelect" class="counter-select" onchange="updateUserCounter()">
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
                            </select>
                        </div>
                        <!-- Active Counters -->
                        <div class="active-counters mt-2">
                            <h4>
                                <button id="toggleActiveCounters" class="btn btn-primary"
                                    onclick="toggleActiveCounters()">Show Active Counters</button>
                            </h4>
                            <ul id="activeCountersList" class="list-group mt-2"
                                style="display: none; transition: max-height 0.5s ease-out; overflow: hidden;"></ul>
                        </div>



                    </div>
                </div>
                <!-- Queue Statistics -->
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="queue-stats stats-waiting">
                            <h4 class="mb-2">Waiting</h4>
                            <h2 class="mb-0" id="waitingCount">0</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="queue-stats stats-serving">
                            <h4 class="mb-2">Currently Serving</h4>
                            <h2 class="mb-0" id="servingCount">0</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="queue-stats stats-completed">
                            <h4 class="mb-2">Completed Today</h4>
                            <h2 class="mb-0" id="completedCount">0</h2>
                        </div>
                    </div>
                </div>

                <!-- Service Filter -->
                <div class="service-filter-container">
                    <label for="serviceFilter" class="form-label">Filter by Service:</label>
                    <div id="serviceFilter" class="row">
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Visa" id="serviceVisa">
                                <label class="form-check-label" for="serviceVisa">Visa</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Tours / Cruise"
                                    id="serviceToursCruise">
                                <label class="form-check-label" for="serviceToursCruise">Tours / Cruise</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Flights" id="serviceFlights">
                                <label class="form-check-label" for="serviceFlights">Flights</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Travel Insurance"
                                    id="serviceTravelInsurance">
                                <label class="form-check-label" for="serviceTravelInsurance">Travel Insurance</label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Multiple Services"
                                    id="serviceMultipleServices">
                                <label class="form-check-label" for="serviceMultipleServices">Multiple Services</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Queue Table -->
                <div class="queue-card p-3">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Queue #</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Service</th>
                                    <!-- <th class="text-center">Priority</th> -->
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Served By</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="queueTableBody">
                                <!-- Queue items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add this below the table in the dashboard -->
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
            </div>

            <!-- Side Panel for Recalls -->
            <div class="col-lg-3">
                <div class="side-panel">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-bell"></i> Recalls & No Shows
                                <span class="badge bg-dark ms-2" id="recallCount">0</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="recallsList">
                                <!-- Recalls will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Recall History -->
                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history"></i> Recent Activity
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="recallHistory">
                                <!-- History will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingSpinner" class="spinner-border text-primary" role="status"
        style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1050;">
        <span class="visually-hidden">Loading...</span>
    </div>

    <script>
        var currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
        let countdownValue = 15;
        let countdownInterval;
        let currentPage = 1; // Track the current page
        let totalCount = 0; // Declare totalCount globally
        let currentFilter = '';

        function startCountdown() {
            clearInterval(countdownInterval);
            countdownValue = 15;
            updateCountdown();
            countdownInterval = setInterval(() => {
                countdownValue--;
                updateCountdown();
                if (countdownValue <= 0) {
                    refreshDashboard();
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

        function refreshDashboard() {
            showLoadingSpinner(); // Show spinner

            const selectedServices = Array.from(document.querySelectorAll('#serviceFilter .form-check-input:checked')).map(checkbox => checkbox.value);
            currentFilter = selectedServices.join(','); // Save the current filter state as a comma-separated string

            $.ajax({
                url: '/RajahQueue/public/DashboardController/getDashboardData?page=' + currentPage + '&services=' + encodeURIComponent(currentFilter), // Pass the selected services
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    totalCount = data.totalCount; // Set totalCount from the response
                    updateDashboard(data, selectedServices);
                    updateRecallPanel(data);
                    updatePagination(totalCount); // Update pagination
                    startCountdown();
                    hideLoadingSpinner(); // Hide spinner after success
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                    console.log('Response Text:', xhr.responseText);
                    hideLoadingSpinner(); // Hide spinner after error
                },
            });
        }

        function updateDashboard(data, selectedServices = []) {
            // Check if stats exist in the response
            if (data.stats) {
                $('#waitingCount').text(data.stats.waiting);
                $('#servingCount').text(data.stats.serving);
                $('#completedCount').text(data.stats.completed);
            } else {
                // If stats are not available, reset the counts
                $('#waitingCount').text('0');
                $('#servingCount').text('0');
                $('#completedCount').text('0');
            }

            // Update the queue table
            const queueTableBody = $('#queueTableBody');
            queueTableBody.empty(); // Clear existing rows

            let filteredQueue = data.queue;
            if (selectedServices.length > 0) {
                filteredQueue = filteredQueue.filter(item => selectedServices.includes(item.service_type));
            }

            if (filteredQueue.length === 0) {
                queueTableBody.append(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">No results found.</td>
                    </tr>
                `);
            } else {
                filteredQueue.forEach(item => {
                    queueTableBody.append(`
                        <tr>
                            <td class="text-center">${item.queue_number}</td>
                            <td class="text-center">${item.customer_name}</td>
                            <td class="text-center">${item.service_type}${item.region ? ` - ${item.region}` : ''}</td>
                            <td class="text-center">
                                <span class="badge ${getStatusBadgeClass(item.status)}">
                                    ${item.status}
                                </span>
                            </td>
                            <td class="text-center">
                                ${item.status === 'Serving' ?
                            (item.first_name ? `${item.first_name} ${item.last_name}` : 'Not assigned') :
                            ''}
                            </td>
                            <td class="text-center">
                                ${getActionButtons(item)}
                            </td>
                        </tr>
                    `);
                });
            }
        }

        function getActionButtons(item) {
            switch (item.status.toLowerCase()) {
                case 'waiting':
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-primary" onclick="updateStatus('${item.queue_number}', 'Serving')">
                                <i class="bi bi-play-fill"></i> Start
                            </button>
                        </div>`;
                case 'serving':
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-success" onclick="updateStatus('${item.queue_number}', 'Done')">
                                <i class="bi bi-check-lg"></i> Done
                            </button>
                            <button class="btn btn-sm btn-success ms-1" onclick="updateStatus('${item.queue_number}', 'Done + Payment')">
                                <i class="bi bi-cash"></i> Payment
                            </button>
                            <button class="btn btn-sm btn-warning ms-1" onclick="updateStatus('${item.queue_number}', 'No Show')">
                                <i class="bi bi-person-x"></i> No Show
                            </button>
                        </div>`;
                case 'no show':
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-info" onclick="updateStatus('${item.queue_number}', 'Recalled')">
                                <i class="bi bi-bell"></i> Recall
                            </button>
                            <button class="btn btn-sm btn-danger ms-1" onclick="updateStatus('${item.queue_number}', 'Skipped')">
                                <i class="bi bi-x-lg"></i> Skip
                            </button>
                        </div>`;
                default:
                    return '';
            }
        }

        function getStatusBadgeClass(status) {
            switch (status?.toLowerCase()) {
                case 'waiting':
                    return 'bg-warning';
                case 'serving':
                    return 'bg-primary';
                case 'done':
                    return 'bg-success';
                case 'skipped':
                    return 'bg-danger';
                case 'no show':
                    return 'bg-warning text-dark';
                case 'recalled':
                    return 'bg-info';
                default:
                    return 'bg-secondary';
            }
        }

        function updateStatus(queueNumber, newStatus) {
            showLoadingSpinner(); // Show spinner

            const selectedCounter = document.querySelector('#counterSelect').value; // Assuming there's a select element with id 'counterSelect' for counters

            if (!selectedCounter) {
                alert('Please choose a counter first.');
                hideLoadingSpinner(); // Hide spinner if no counter is selected
                return;
            }

            // Confirmation messages for different statuses
            const confirmationMessages = {
                'Done': 'Are you sure you want to mark this queue as Done?',
                'Done + Payment': 'Are you sure you want to mark this queue as Done and proceed to payment?',
                'No Show': 'Are you sure you want to mark this queue as No Show?',
                'Recalled': 'Are you sure you want to recall this queue?',
                'Skipped': 'Are you sure you want to skip this queue?'
            };

            // Show confirmation dialog if the new status requires validation
            if (confirmationMessages[newStatus]) {
                if (!confirm(confirmationMessages[newStatus])) {
                    hideLoadingSpinner(); // Hide spinner if user cancels confirmation
                    return; // Exit if the user cancels the confirmation
                }
            }

            // Check the current status of the queue number before updating
            $.ajax({
                url: '/RajahQueue/public/DashboardController/checkQueueStatus',
                method: 'POST',
                data: { queue_number: queueNumber },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        const servingUserId = parseInt(response.serving_user_id, 10);
                        const currentUserIdInt = parseInt(currentUserId, 10);

                        if (response.current_status === 'Serving' && servingUserId !== null) {
                            if (newStatus === 'Done' && servingUserId !== currentUserIdInt) {
                                alert('Only the user currently serving this customer can complete the status.');
                                hideLoadingSpinner(); // Hide spinner if there's a conflict
                                return;
                            }
                        }

                        // Proceed with updating the status if no conflict
                        $.ajax({
                            url: '/RajahQueue/public/DashboardController/updateStatus',
                            method: 'POST',
                            data: {
                                queue_number: queueNumber,
                                status: newStatus,
                                counter: selectedCounter
                            },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    if (newStatus.toLowerCase() === 'recalled') {
                                        // Show alert for recalled number
                                        alert(`Queue number ${queueNumber} has been recalled!`);
                                    }
                                    refreshDashboard();
                                } else if (response.promptPayment) {
                                    if (confirm(response.message)) {
                                        updatePaymentStatus(queueNumber, 'Pending');
                                    } else {
                                        updatePaymentStatus(queueNumber, 'Not Required');
                                    }
                                } else {
                                    alert(response.message || 'Failed to update status');
                                }
                                hideLoadingSpinner(); // Hide spinner after success
                            },
                            error: function (xhr, status, error) {
                                console.error('Error updating status:', xhr.responseText);
                                alert('Error updating status. Please try again.', xhr.responseText);
                                hideLoadingSpinner(); // Hide spinner after error
                            }
                        });
                    } else {
                        alert(response.message || 'Failed to check queue status');
                        hideLoadingSpinner(); // Hide spinner if check fails
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error checking queue status:', error);
                    alert('Error checking queue status. Please try again.');
                    hideLoadingSpinner(); // Hide spinner after error
                }
            });
        }

        function updatePaymentStatus(queueNumber, paymentStatus) {
            $.ajax({
                url: '/RajahQueue/public/DashboardController/updatePaymentStatus',
                method: 'POST',
                data: {
                    queue_number: queueNumber,
                    payment_status: paymentStatus
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        refreshDashboard();
                    } else {
                        alert('Failed to update payment status. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error updating payment status:', error);
                    alert('Error updating payment status. Please try again.');
                }
            });
        }

        function updateRecallPanel(data) {
            const recallsList = $('#recallsList');
            const recallHistory = $('#recallHistory');

            // Filter no-shows and recalls
            const activeRecalls = data.queue.filter(item =>
                ['No Show', 'Recalled'].includes(item.status)
            );

            // Update recall count
            $('#recallCount').text(activeRecalls.length);

            // Update active recalls list
            if (activeRecalls.length === 0) {
                recallsList.html(`
                    <div class="empty-list">
                        <i class="bi bi-check-circle"></i>
                        <p class="mb-0">No active recalls</p>
                    </div>
                `);
            } else {
                recallsList.empty();
                activeRecalls.forEach(item => {
                    recallsList.append(createRecallItem(item));
                });
            }

            // Update recall history (if available)
            if (data.recallHistory && data.recallHistory.length > 0) {
                recallHistory.empty();
                data.recallHistory.forEach(item => {
                    recallHistory.append(createHistoryItem(item));
                });
            } else {
                recallHistory.html(`
                    <div class="empty-list">
                        <p class="mb-0">No recent activity</p>
                    </div>
                `);
            }
        }

        function createRecallItem(item) {
            const timeDiff = getTimeDifference(item.updated_at);
            return `
                <div class="list-group-item recall-item ${item.status.toLowerCase().replace(' ', '-')}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">${item.queue_number} - ${item.customer_name}</h6>
                            <small class="text-muted">${item.service_type}</small>
                            <div class="recall-time">
                                <i class="bi bi-clock"></i> ${timeDiff}
                            </div>
                        </div>
                        <div class="recall-actions">
                            ${getRecallActionButtons(item)}
                        </div>
                    </div>
                </div>
            `;
        }

        function createHistoryItem(item) {
            return `
                <div class="list-group-item">
                    <small class="text-muted float-end">${formatDateTime(item.updated_at)}</small>
                    <p class="mb-0">
                        <strong>${item.queue_number}</strong> - ${item.action}
                    </p>
                </div>
            `;
        }

        function getRecallActionButtons(item) {
            if (item.status === 'No Show') {
                return `
                    <button class="btn btn-sm btn-info" onclick="updateStatus('${item.queue_number}', 'Recalled')">
                        <i class="bi bi-bell"></i> Recall
                    </button>
                `;
            }
            return `
                <button class="btn btn-sm btn-primary" onclick="updateStatus('${item.queue_number}', 'Serving')">
                    <i class="bi bi-play-fill"></i> Start
                </button>
            `;
        }

        function getTimeDifference(timestamp) {
            const now = new Date();
            const past = new Date(timestamp);
            const diffInMinutes = Math.floor((now - past) / 60000);

            if (diffInMinutes < 1) return 'Just now';
            if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
            const hours = Math.floor(diffInMinutes / 60);
            if (hours < 24) return `${hours}h ago`;
            return `${Math.floor(hours / 24)}d ago`;
        }

        function formatDateTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleString();
        }

        function updatePagination(totalCount) {
            const itemsPerPage = 20;
            const totalPages = Math.ceil(totalCount / itemsPerPage);

            $('#pageInfo').text(`Page ${currentPage} of ${totalPages}`);

            // Enable/disable pagination buttons
            $('#prevPage').prop('disabled', currentPage === 1);
            $('#nextPage').prop('disabled', currentPage === totalPages);
        }

        function changePage(newPage) {
            if (newPage < 1 || newPage > Math.ceil(totalCount / 20)) return; // Prevent invalid page numbers
            currentPage = newPage;
            refreshDashboard(); // Refresh the dashboard with the new page
        }

        function searchQueue() {
            const searchTerm = document.getElementById('searchInput').value;
            const selectedServices = Array.from(document.querySelectorAll('#serviceFilter .form-check-input:checked')).map(checkbox => checkbox.value);
            currentPage = 1; // Reset to the first page on new search

            $.ajax({
                url: '/RajahQueue/public/DashboardController/getDashboardData?page=' + currentPage + '&search=' + encodeURIComponent(searchTerm) + '&services=' + encodeURIComponent(selectedServices.join(',')), // Pass the search term and selected services
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    totalCount = data.totalCount; // Set totalCount from the response
                    updateDashboard(data, selectedServices);
                    updateRecallPanel(data);
                    updatePagination(totalCount); // Update pagination
                    startCountdown();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                    console.log('Response Text:', xhr.responseText);
                }
            });
        }

        function updateUserCounter() {
            const selectedCounter = document.getElementById("counterSelect").value;

            if (!selectedCounter) {
                alert("Please select a counter first.");
                return;
            }

            const isReleasingCounter = selectedCounter === "release";

            // Send the AJAX request to update or release the counter
            $.ajax({
                url: "/RajahQueue/public/UserController/updateCounter",
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
                                document.getElementById("counterSelect").value = ""; // Reset to default
                            } else {
                                alert(`You are now assigned to Counter ${selectedCounter}.`);
                            }
                            refreshDashboard(); // Refresh the dashboard to update counter information
                        } else {
                            alert(data.message || "Failed to update the counter. Please try again.");
                            // Reset the dropdown if there was an error
                            refreshCounterSelect();
                        }
                    } catch (e) {
                        console.error("Error parsing response:", e);
                        alert("An error occurred while processing the response.");
                        refreshCounterSelect();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error updating counter:", error);
                    console.log("Response Text:", xhr.responseText);
                    alert("An error occurred while updating the counter. Please try again.");
                    refreshCounterSelect();
                }
            });
        }

        // Function to refresh the counter select dropdown
        function refreshCounterSelect() {
            $.ajax({
                url: "/RajahQueue/public/UserController/getCounter",
                method: "GET",
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        const select = document.getElementById("counterSelect");

                        // If user has an assigned counter, select it
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

        // Function to fetch active counters
        function fetchActiveCounters() {
            $.ajax({
            url: '/RajahQueue/public/DashboardController/getActiveCounters',
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

        // Add event listener for counter select changes
        document.getElementById("counterSelect").addEventListener("change", function (e) {
            const selectedValue = e.target.value;
            if (selectedValue && selectedValue !== "release") {
                // Check if counter is already assigned
                $.ajax({
                    url: "/RajahQueue/public/UserController/checkCounterAvailability",
                    method: "POST",
                    data: { counter_number: selectedValue },
                    success: function (response) {
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            if (!data.available) {
                                alert("This counter is already assigned to another user.");
                                refreshCounterSelect(); // Reset the dropdown
                                return;
                            }
                            // If available, proceed with updateUserCounter
                            updateUserCounter();
                        } catch (e) {
                            console.error("Error checking counter availability:", e);
                            alert("An error occurred while checking counter availability.");
                            refreshCounterSelect();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error checking counter availability:", error);
                        alert("An error occurred while checking counter availability.");
                        refreshCounterSelect();
                    }
                });
            } else {
                // If releasing or no counter selected, proceed normally
                updateUserCounter();
            }
        });

        // Initial load
        $(document).ready(function () {
            refreshCounterSelect();
            refreshDashboard();
            fetchActiveCounters();
            setInterval(fetchActiveCounters, 15000); // Refresh every 15 seconds
        });

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

        document.getElementById('serviceFilter').addEventListener('change', searchQueue);

        function applyFilter() {
            // Logic to apply the filter based on currentFilter
            if (currentFilter) {
                // Apply the filter logic here
                console.log(`Applying filter: ${currentFilter}`);
            }
        }

        // Call refreshDashboard at regular intervals
        setInterval(refreshDashboard, 15000); // Adjust the interval as needed

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Load selected services from localStorage
            const selectedServices = JSON.parse(localStorage.getItem('selectedServices')) || [];
            selectedServices.forEach(service => {
                document.querySelector(`input[value="${service}"]`).checked = true;
            });

            // Add event listeners to checkboxes
            document.querySelectorAll('#serviceFilter .form-check-input').forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    searchQueue();
                    saveSelectedServices();
                });
            });

            // Initial search with persisted services
            searchQueue();
        });

        function saveSelectedServices() {
            const selectedServices = Array.from(document.querySelectorAll('#serviceFilter .form-check-input:checked')).map(checkbox => checkbox.value);
            localStorage.setItem('selectedServices', JSON.stringify(selectedServices));
        }

        function searchQueue() {
            const searchTerm = document.getElementById('searchInput').value;
            const selectedServices = Array.from(document.querySelectorAll('#serviceFilter .form-check-input:checked')).map(checkbox => checkbox.value);
            currentPage = 1; // Reset to the first page on new search

            $.ajax({
                url: '/RajahQueue/public/DashboardController/getDashboardData?page=' + currentPage + '&search=' + encodeURIComponent(searchTerm) + '&services=' + encodeURIComponent(selectedServices.join(',')), // Pass the search term and selected services
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    totalCount = data.totalCount; // Set totalCount from the response
                    updateDashboard(data, selectedServices);
                    updateRecallPanel(data);
                    updatePagination(totalCount); // Update pagination
                    startCountdown();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                    console.log('Response Text:', xhr.responseText);
                }
            });
        }

        function updateDashboard(data, selectedServices = []) {
            // Check if stats exist in the response
            if (data.stats) {
                $('#waitingCount').text(data.stats.waiting);
                $('#servingCount').text(data.stats.serving);
                $('#completedCount').text(data.stats.completed);
            } else {
                // If stats are not available, reset the counts
                $('#waitingCount').text('0');
                $('#servingCount').text('0');
                $('#completedCount').text('0');
            }

            // Update the queue table
            const queueTableBody = $('#queueTableBody');
            queueTableBody.empty(); // Clear existing rows

            let filteredQueue = data.queue;
            if (selectedServices.length > 0) {
                filteredQueue = filteredQueue.filter(item => selectedServices.includes(item.service_type));
            }

            if (filteredQueue.length === 0) {
                queueTableBody.append(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">No results found.</td>
                    </tr>
                `);
            } else {
                filteredQueue.forEach(item => {
                    queueTableBody.append(`
                        <tr>
                            <td class="text-center">${item.queue_number}</td>
                            <td class="text-center">${item.customer_name}</td>
                            <td class="text-center">${item.service_type}${item.region ? ` - ${item.region}` : ''}</td>
                            <td class="text-center">
                                <span class="badge ${getStatusBadgeClass(item.status)}">
                                    ${item.status}
                                </span>
                            </td>
                            <td class="text-center">
                                ${item.status === 'Serving' ?
                            (item.first_name ? `${item.first_name} ${item.last_name}` : 'Not assigned') :
                            ''}
                            </td>
                            <td class="text-center">
                                ${getActionButtons(item)}
                            </td>
                        </tr>
                    `);
                });
            }
        }
    </script>
</body>
<footer class=" text-center py-3 mt-4"></footer>

</html>