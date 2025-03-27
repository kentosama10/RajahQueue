<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" href="/RajahQueue/app/assets/images/RTC LOGO 2017 - Vector-02-ORB.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        .navbar {
            background-color: #F08221;
            /* Header background color */
        }

        .navbar-nav .nav-link {
            color: white !important;
            /* Default link color */
            font-weight: 500;
            transition: color 0.3s ease;
            /* Smooth hover transition */
        }

        .navbar-nav .nav-link:hover {
            color: #CE007C !important;
            /* Lightened hover effect */
        }

        .navbar-brand img {
            height: 50px;
            /* Set consistent logo size */
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            /* Add spacing between icon and text */
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            background-color: #dc3545;
            border-color: #dc3545;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #c82333;
            /* Darker red on hover */
            border-color: #c82333;
        }

        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }

            .btn-logout {
                width: 100%;
                /* Full width on smaller screens */
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <!-- Logo -->
            <a class="navbar-brand" href="#">
                <img src="/RajahQueue/app/assets/images/01 RTC Logo.png" alt="RajahQueue Logo">
            </a>

            <!-- Hamburger Toggle for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/RajahQueue/public/queue/index">Form</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/RajahQueue/public/dashboard/index">Queue Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/RajahQueue/public/payment/index">Payment Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/RajahQueue/public/dashboard/display">Customer Serving</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/RajahQueue/public/dashboard/reports">Reports</a>
                    </li>

                    <!-- Welcome Message -->
                    <?php if (isset($_SESSION['first_name'])): ?>
                        <li class="nav-item" style="margin-right: 10px;">
                            <span class="navbar-text text-white ms-3">
                                Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!
                            </span>
                        </li>
                    <?php endif; ?>

                    <!-- Logout Button -->
                    <li class="nav-item">
                        <a class="btn btn-danger btn-logout" href="/RajahQueue/public/user/logout">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>

</html>