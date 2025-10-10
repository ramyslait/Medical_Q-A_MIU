
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 - Page Not Found | Medical Q&A</title>
    <link rel="stylesheet" href="css/main.css" />
    <link rel="stylesheet" href="css/components.css" />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet" />
    <style>
        /* Center 404 content vertically & horizontally */
        .hero-404 {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 80vh;
            text-align: center;
        }

        .hero-404 .hero-title {
            font-size: 8rem;
            margin: 0;
        }

        .hero-404 .hero-description {
            font-size: 1.5rem;
            margin: 1rem 0;
        }

        .hero-404 .hero-buttons a {
            margin: 0 0.5rem;
        }

        .hero-404 .hero-image {
            margin-top: 2rem;
        }
    </style>
</head>

<body>
    <!-- Navigation Header -->
    <?php include '../app/partials/navbar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <section class="hero hero-404">
            <h1 class="hero-title">404</h1>
            <p class="hero-description">
                Oops! The page you are looking for cannot be found.
            </p>
            <div class="hero-buttons">
                <a href="home" class="btn btn-primary">Go Back Home</a>
                <?php if (!$user): ?>
                    <a href="register" class="btn btn-secondary">Sign Up</a>
                <?php endif; ?>
            </div>
            <div class="hero-image">
                <i class="fas fa-exclamation-triangle fa-5x"></i>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include '../app/partials/footer.php'; ?>

    <!-- Scripts -->
    <script src="js/main.js"></script>
</body>

</html>