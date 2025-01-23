<?php include '../app/views/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .report-header {
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .08);
            padding: 0.5rem 0;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
        }

        .report-header h2 {
            font-weight: 600;
            color: #2c3e50;
        }

        .report-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Reports</h2>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="report-card">
                    <h4>Total Queue Count</h4>
                    <p><?php echo $data['totalQueueCount']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <h4>Completed Today</h4>
                    <p><?php echo $data['completedToday']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <h4>Waiting Count</h4>
                    <p><?php echo $data['waitingCount']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <h4>Serving Count</h4>
                    <p><?php echo $data['servingCount']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <h4>Skipped Count</h4>
                    <p><?php echo $data['skippedCount']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <h4>No Show Count</h4>
                    <p><?php echo $data['noShowCount']; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <h4>Average Queue Time Spent</h4>
                    <p><?php echo formatTime($data['averageQueueTimeSpent']); ?></p>
                </div>
            </div>
        </div>

        <!-- Custom Reports -->
        <div class="row">
            <!-- Daily Summary Report -->
            <div class="col-md-6">
                <div class="report-card">
                    <h4>Daily Summary Report</h4>
                    <p>Total Queues Today: <?php echo $data['dailySummary']['totalQueues']; ?></p>
                    <p>Completed Queues: <?php echo $data['dailySummary']['completedQueues']; ?></p>
                    <p>Skipped Queues: <?php echo $data['dailySummary']['skippedQueues']; ?></p>
                </div>
            </div>

            <!-- Monthly Summary Report -->
            <div class="col-md-6">
                <div class="report-card">
                    <h4>Monthly Summary Report</h4>
                    <p>Total Queues This Month: <?php echo $data['monthlySummary']['totalQueues']; ?></p>
                    <p>Completed Queues: <?php echo $data['monthlySummary']['completedQueues']; ?></p>
                    <p>Skipped Queues: <?php echo $data['monthlySummary']['skippedQueues']; ?></p>
                </div>
            </div>

            <!-- Service Type Breakdown -->
            <div class="col-md-6">
                <div class="report-card">
                    <h4>Service Type Breakdown</h4>
                    <p>Visa: <?php echo $data['serviceTypeBreakdown']['visa']; ?></p>
                    <p>Tours / Cruise: <?php echo $data['serviceTypeBreakdown']['tourPackages']; ?></p>
                    <p>Travel Insurance: <?php echo $data['serviceTypeBreakdown']['travelInsurance']; ?></p>
                    <p>Flights: <?php echo $data['serviceTypeBreakdown']['flights']; ?></p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="report-card">
                    <h4>Average Time Spent by Service</h4>
                    <p>Visa:
                        <?php echo isset($data['averageTimeSpentByService']['Visa']) ? formatTime($data['averageTimeSpentByService']['Visa']) : 'N/A'; ?>
                    </p>
                    <p>Tours / Cruise:
                        <?php echo isset($data['averageTimeSpentByService']['Tours / Cruise']) ? formatTime($data['averageTimeSpentByService']['Tours / Cruise']) : 'N/A'; ?>
                    </p>
                    <p>Travel Insurance:
                        <?php echo isset($data['averageTimeSpentByService']['Travel Insurance']) ? formatTime($data['averageTimeSpentByService']['Travel Insurance']) : 'N/A'; ?>
                    </p>
                    <p>Flights:
                        <?php echo isset($data['averageTimeSpentByService']['Flights']) ? formatTime($data['averageTimeSpentByService']['Flights']) : 'N/A'; ?>
                    </p>
                    <p>Multiple Services:
                        <?php echo isset($data['averageTimeSpentByService']['Multiple Services']) ? formatTime($data['averageTimeSpentByService']['Multiple Services']) : 'N/A'; ?>
                    </p>
                </div>
            </div>

            <!-- Priority Queue Report -->
            <!-- <div class="col-md-6">
                <div class="report-card">
                    <h4>Priority Queue Report</h4>
                    <p>Priority Queues: <?php echo $data['priorityQueue']['priorityQueues']; ?></p>
                    <p>Non-Priority Queues: <?php echo $data['priorityQueue']['nonPriorityQueues']; ?></p>
                </div>
            </div>
        </div> -->

            <!-- Date Range Filter Form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="report-card">
                        <h4>Queue Data by Date Range</h4>
                        <form id="dateRangeForm" method="GET" onsubmit="filterQueueData(event)">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="startDate" class="form-label">Start Date</label>
                                        <input type="date" id="startDate" name="start_date" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="endDate" class="form-label">End Date</label>
                                        <input type="date" id="endDate" name="end_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-5 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <button type="button" class="btn btn-success ms-2" onclick="exportToCSV()">Export to
                                        CSV</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Filtered Queue Data Table -->
            <div class="row">
                <div class="col-md-12">
                    <div class="report-card">
                        <h4>Filtered Queue Data</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer Name</th>
                                        <th>Service Type</th>
                                        <!-- <th>Region</th>
                                    <th>Priority</th>
                                    <th>Priority Type</th> -->
                                        <th>Queue Number</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Time Spent</th>
                                        <th>Payment Status</th>
                                        <th>Serving User Name</th>
                                        <th>Completed By User Name</th>
                                        <th>Payment Completed At</th>
                                    </tr>
                                </thead>
                                <tbody id="filteredQueueData">
                                    <!-- Filtered data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
    function filterQueueData(event) {
        event.preventDefault();
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        fetch(`/RajahQueue/public/DashboardController/filterQueueData?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('filteredQueueData');
                tbody.innerHTML = '';
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    const hours = Math.floor(row.time_spent / 60);
                    const minutes = row.time_spent % 60;
                    tr.innerHTML = `
                        <td>${row.id}</td>
                        <td>${row.customer_name}</td>
                        <td>${row.service_type}</td>
                        <td>${row.queue_number}</td>
                        <td>${row.status}</td>
                        <td>${row.created_at}</td>
                        <td>${row.updated_at}</td>
                        <td>${hours}h ${minutes}m</td>
                        <td>${row.payment_status}</td>
                        <td>${row.serving_user_name}</td>
                        <td>${row.completed_by_user_name}</td>
                        <td>${row.payment_completed_at}</td>
                    `;
                    tbody.appendChild(tr);
                });
            });
    }
            function exportToCSV() {
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                window.location.href = `/RajahQueue/public/DashboardController/exportQueueData?start_date=${startDate}&end_date=${endDate}`;
            }
        </script>
</body>

</html>
<?php include '../app/views/footer.php'; ?>

<?php
function formatTime($minutes)
{
    $hours = floor($minutes / 60);
    $remainingMinutes = $minutes % 60;
    return "{$hours}h {$remainingMinutes}m";
}
?>