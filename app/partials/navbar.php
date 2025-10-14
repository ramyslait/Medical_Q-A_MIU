<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

// Get the current path from the URL (e.g., "/Medical_Q-A_MIU/home" or "/home")
$current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize it (remove trailing slashes except for root)
$current = rtrim($current, '/');
if ($current === '') $current = '/';

// Extract only the last part (e.g., "home", "forum")
$currentPage = basename($current);
?>

<header class="header">
  <nav class="navbar">
    <div class="nav-container">
      <div class="nav-logo">
        <i class="fas fa-user-md"></i>
        <span>MediQ&A</span>
      </div>

      <ul class="nav-menu">
        <li class="nav-item">
          <a href="home" class="nav-link <?= $currentPage == 'home' || $currentPage == '' ? 'active' : '' ?>">Home</a>
        </li>
        <li class="nav-item">
          <a href="ask-question" class="nav-link <?= $currentPage == 'ask-question' ? 'active' : '' ?>">Ask Question</a>
        </li>
        <li class="nav-item">
          <a href="forum" class="nav-link <?= $currentPage == 'forum' ? 'active' : '' ?>">Forum</a>
        </li>

        <?php if (!$user): ?>
          <li class="nav-item">
            <a href="login" class="nav-link <?= $currentPage == 'login' ? 'active' : '' ?>">Login</a>
          </li>
          <li class="nav-item">
            <a href="register" class="nav-link nav-cta <?= $currentPage == 'register' ? 'active' : '' ?>">Sign Up</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="logout" class="nav-link">Logout</a>
          </li>
          <li class="nav-item">
            <a href="profile" class="nav-link <?= $currentPage == 'profile' ? 'active' : '' ?>">
              <?= htmlspecialchars($user['name'] ?? 'User') ?>
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <div class="hamburger">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </div>
    </div>
  </nav>
</header>
