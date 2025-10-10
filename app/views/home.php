<?php
// Check if user cookie exists
$user = isset($_COOKIE['user']) ? json_decode($_COOKIE['user'], true) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Medical Q&A - Get Accurate Medical Answers Instantly</title>
  <link rel="stylesheet" href="css/main.css" />
  <link rel="stylesheet" href="css/components.css" />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
</head>

<body>
  <!-- Navigation Header -->
  <?php include '../app/partials/navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">Get Accurate Medical Answers Instantly</h1>
          <p class="hero-description">
            Connect with healthcare professionals and get reliable medical
            information for your health concerns. Fast, accurate, and
            trustworthy.
          </p>
          <div class="hero-buttons">
            <?php if (!$user): ?>
              <a href="register" class="btn btn-primary">Sign Up Now</a>
            <?php endif; ?>
            <a href="ask-question" class="btn btn-secondary">Submit Question</a>
          </div>

        </div>
        <div class="hero-image">
          <i class="fas fa-stethoscope"></i>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <div class="container">
        <h2 class="section-title">Why Choose MediQ&A?</h2>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-clock"></i>
            </div>
            <h3>Quick Response</h3>
            <p>
              Get answers within minutes from verified healthcare
              professionals.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Verified Experts</h3>
            <p>All responses come from certified medical professionals.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-users"></i>
            </div>
            <h3>Community Support</h3>
            <p>Connect with others who share similar health experiences.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number">10,000+</div>
            <div class="stat-label">Questions Answered</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">500+</div>
            <div class="stat-label">Medical Professionals</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">50,000+</div>
            <div class="stat-label">Happy Users</div>
          </div>
          <div class="stat-item">
            <div class="stat-number">95%</div>
            <div class="stat-label">Accuracy Rate</div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <?php include '../app/partials/footer.php'; ?>

  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="js/controllers/homeController.js"></script>
</body>

</html>