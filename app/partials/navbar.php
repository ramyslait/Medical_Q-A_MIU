<?php
// Ensure session started so we can access $_SESSION['user']
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use the globally stored user from index.php
$user = $_SESSION['user'] ?? null;

// Detect project base path (e.g. /Medical_Q-A_MIU)
$base = dirname($_SERVER['SCRIPT_NAME']);
if ($base === '/' || $base === '\\') $base = '';

// Get current URI
$current = $_SERVER['REQUEST_URI'];
$current = str_replace($base, '', $current);
$current = rtrim($current, '/');
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
          <a href="<?= $base ?>/home" class="nav-link <?= $current == '/home' || $current == '' ? 'active' : '' ?>">Home</a>
        </li>
        <li class="nav-item">
          <a href="<?= $base ?>/ask-question" class="nav-link <?= $current == '/ask-question' ? 'active' : '' ?>">Ask Question</a>
        </li>
        <li class="nav-item">
          <a href="<?= $base ?>/forum" class="nav-link <?= $current == '/forum' ? 'active' : '' ?>">Forum</a>
        </li>

        <?php if (!$user): ?>
          <li class="nav-item">
            <a href="<?= $base ?>/login" class="nav-link <?= $current == '/login' ? 'active' : '' ?>">Login</a>
          </li>
          <li class="nav-item">
            <a href="<?= $base ?>/register" class="nav-link nav-cta <?= $current == '/register' ? 'active' : '' ?>">Sign Up</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="<?= $base ?>/logout" class="nav-link">Logout</a>
          </li>
          <li class="nav-item">
            <a href="<?= $base ?>/profile" class="nav-link <?= $current == '/profile' ? 'active' : '' ?>">
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
