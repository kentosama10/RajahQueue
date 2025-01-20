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
                    <h4>Recalled Count</h4>
                    <p><?php echo $data['recalledCount']; ?></p>
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
                    <p>Tour Packages: <?php echo $data['serviceTypeBreakdown']['tourPackages']; ?></p>
                    <p>Travel Insurance: <?php echo $data['serviceTypeBreakdown']['travelInsurance']; ?></p>
                    <p>Flights: <?php echo $data['serviceTypeBreakdown']['flights']; ?></p>
                </div>
            </div>

            <!-- Priority Queue Report -->
            <div class="col-md-6">
                <div class="report-card">
                    <h4>Priority Queue Report</h4>
                    <p>Priority Queues: <?php echo $data['priorityQueue']['priorityQueues']; ?></p>
                    <p>Non-Priority Queues: <?php echo $data['priorityQueue']['nonPriorityQueues']; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php include '../app/views/footer.php'; ?>