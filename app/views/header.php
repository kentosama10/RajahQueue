<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/RajahQueue/app/assets/css/dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <header class="text-white p-3" style="background-color: #F08221;">
        <div class="container d-flex justify-content-between align-items-center">

            <img src="/RajahQueue/app/assets/images/01 RTC Logo.png" alt="RajahQueue Logo" height="50">
            <nav>

                <a href="/RajahQueue/public/DashboardController/index" class="text-white me-3">Dashboard</a>
                <a href="/RajahQueue/public/QueueController/index" class="text-white me-3">Queue</a>
                <a href="/RajahQueue/public/UserController/logout" class="btn btn-danger">Logout</a>
                <?php if (isset($_SESSION['first_name'])): ?>
                    <span class="text-white">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</span>
                <?php endif; ?>
            </nav>

        </div>
    </header>
    <div class="container mt-4"></div>