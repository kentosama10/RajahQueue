<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Dashboard - RajahQueue</title>
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
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .queue-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            margin-bottom: 1rem;
        }
        .queue-stats {
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .stats-waiting { background-color: #fff3cd; }
        .stats-serving { background-color: #cfe2ff; }
        .stats-completed { background-color: #d1e7dd; }
        .status-badge {
            padding: 0.5em 1em;
            border-radius: 20px;
            font-weight: 500;
        }
        .status-waiting { background-color: #ffc107; color: #000; }
        .status-serving { background-color: #0d6efd; color: #fff; }
        .status-done { background-color: #198754; color: #fff; }
        .status-skipped { background-color: #dc3545; color: #fff; }
        .table th { 
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .refresh-timer {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .status-no-show {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-recalled {
            background-color: #0dcaf0;
            color: #fff;
        }
        
        /* Add hover effects for buttons */
        .btn-group .btn:hover {
            opacity: 0.9;
        }
        
        .side-panel {
            position: sticky;
            top: 1rem;
        }
        
        .recall-item {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }
        
        .recall-item:hover {
            background-color: #f8f9fa;
        }
        
        .recall-item.no-show {
            border-left-color: #ffc107;
        }
        
        .recall-item.recalled {
            border-left-color: #0dcaf0;
        }
        
        .recall-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .recall-actions {
            display: none;
        }
        
        .recall-item:hover .recall-actions {
            display: block;
        }
        
        .empty-list {
            padding: 1.5rem;
            text-align: center;
            color: #6c757d;
        }
        .input-group {
            max-width: 400px; /* Limit the width of the search bar */
            margin: auto; /* Center the search bar */
        }

        .input-group-text {
            background-color: #f8f9fa; /* Subtle background color */
            border: 1px solid #ced4da; /* Match the input border */
            border-right: none; /* Remove right border for seamless look */
        }

        .form-control {
            border: 1px solid #ced4da; /* Match the input border */
            border-left: none; /* Remove left border for seamless look */
            border-radius: 0 0.25rem 0.25rem 0; /* Adjust border radius */
        }

        .form-control:focus {
            box-shadow: none; /* Remove focus shadow */
            border-color: #ced4da; /* Match the input border */
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Queue Dashboard</h2>
                <div class="d-flex align-items-center">
                    <span class="refresh-timer me-3">
                        Auto-refresh in: <span id="countdown">15</span>s
                    </span>
                    <button class="btn btn-primary" onclick="refreshDashboard()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Queue Content -->
            <div class="col-lg-9">
                <!-- Add Search Bar -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text" id="search-addon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by customer name or queue number" onkeyup="searchQueue()" aria-label="Search" aria-describedby="search-addon" style="border: 1px solid #ced4da; border-radius: 0.25rem;">
                    </div>
                </div>
                <!-- Queue Statistics -->
                <div class="row mb-4">
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

                <!-- Queue Table -->
                <div class="queue-card p-3">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Queue #</th>
                                    <th>Customer Name</th>
                                    <th>Service</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        let countdownValue = 15;
        let countdownInterval;
        let currentPage = 1; // Track the current page
        let totalCount = 0; // Declare totalCount globally

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

        function refreshDashboard() {
            $.ajax({
                url: '/RajahQueue/public/queue/getDashboardData?page=' + currentPage, // Pass the current page
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    totalCount = data.totalCount; // Set totalCount from the response
                    updateDashboard(data);
                    updateRecallPanel(data);
                    updatePagination(totalCount); // Update pagination
                    startCountdown();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                }
            });
        }

        function updateDashboard(data) {
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

            if (data.queue.length === 0) {
                queueTableBody.append(`
                    <tr>
                        <td colspan="6" class="text-center text-muted">No results found.</td>
                    </tr>
                `);
            } else {
                data.queue.forEach(item => {
                    queueTableBody.append(`
                        <tr>
                            <td>${item.queue_number}</td>
                            <td>${item.customer_name}</td>
                            <td>${item.service_type}</td>
                            <td>${item.priority}</td>
                            <td><span class="status-badge status-${item.status.toLowerCase()}">${item.status}</span></td>
                            <td>${getActionButtons(item)}</td>
                        </tr>
                    `);
                });
            }
        }

        function getActionButtons(item) {
            switch(item.status.toLowerCase()) {
                case 'waiting':
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-primary" onclick="updateStatus('${item.queue_number}', 'Serving')">
                                <i class="bi bi-play-fill"></i> Start
                            </button>
                            <button class="btn btn-sm btn-danger ms-1" onclick="updateStatus('${item.queue_number}', 'Skipped')">
                                <i class="bi bi-skip-forward-fill"></i> Skip
                            </button>
                        </div>`;
                case 'serving':
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-success" onclick="updateStatus('${item.queue_number}', 'Done')">
                                <i class="bi bi-check-lg"></i> Complete
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
            switch(status?.toLowerCase()) {
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
            let confirmMessage = '';
            switch(newStatus.toLowerCase()) {
                case 'no show':
                    confirmMessage = 'Mark this customer as No Show?';
                    break;
                case 'skipped':
                    confirmMessage = 'Are you sure you want to skip this customer?';
                    break;
                case 'done':
                    confirmMessage = 'Mark this service as completed?';
                    break;
            }

            if (confirmMessage && !confirm(confirmMessage)) {
                return;
            }

            // Add loading state to buttons
            const buttons = $(`button[onclick*="${queueNumber}"]`).prop('disabled', true);
            
            $.ajax({
                url: '/RajahQueue/public/queue/updateStatus',
                method: 'POST',
                data: {
                    queue_number: queueNumber,
                    status: newStatus
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (newStatus.toLowerCase() === 'recalled') {
                            // Show alert for recalled number
                            alert(`Queue number ${queueNumber} has been recalled!`);
                        }
                        refreshDashboard();
                    } else {
                        alert('Failed to update status. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating status:', error);
                    alert('Error updating status. Please try again.');
                },
                complete: function() {
                    // Re-enable buttons
                    buttons.prop('disabled', false);
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
            const itemsPerPage = 10;
            const totalPages = Math.ceil(totalCount / itemsPerPage);
            
            $('#pageInfo').text(`Page ${currentPage} of ${totalPages}`);
            
            // Enable/disable pagination buttons
            $('#prevPage').prop('disabled', currentPage === 1);
            $('#nextPage').prop('disabled', currentPage === totalPages);
        }

        function changePage(newPage) {
            if (newPage < 1 || newPage > Math.ceil(totalCount / 10)) return; // Prevent invalid page numbers
            currentPage = newPage;
            refreshDashboard(); // Refresh the dashboard with the new page
        }

        function searchQueue() {
            const searchTerm = document.getElementById('searchInput').value;
            currentPage = 1; // Reset to the first page on new search
            $.ajax({
                url: '/RajahQueue/public/queue/getDashboardData?page=' + currentPage + '&search=' + encodeURIComponent(searchTerm), // Pass the search term
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    totalCount = data.totalCount; // Set totalCount from the response
                    updateDashboard(data);
                    updateRecallPanel(data);
                    updatePagination(totalCount); // Update pagination
                    startCountdown();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                    console.log('Response Text:', xhr.responseText);
                }
            });
        }

        // Initial load
        $(document).ready(function() {
            refreshDashboard();
        });
    </script>
</body>
</html>
