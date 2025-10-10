<?php
  // Get current request URI (e.g. /Medical_Q-A_MIU/home)
  $current = $_SERVER['REQUEST_URI'];

  // Detect your project base path dynamically (e.g. /Medical_Q-A_MIU)
  $base = dirname($_SERVER['SCRIPT_NAME']);
  if ($base === '/' || $base === '\\') $base = '';

  // Normalize URI by removing base path so it becomes /home, /login, etc.
  $current = str_replace($base, '', $current);
  $current = rtrim($current, '/'); // remove trailing slash for consistency
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
        <li class="nav-item">
          <a href="<?= $base ?>/login" class="nav-link <?= $current == '/login' ? 'active' : '' ?>">Login</a>
        </li>
        <li class="nav-item">
          <a href="<?= $base ?>/register" class="nav-link nav-cta <?= $current == '/register' ? 'active' : '' ?>">Sign Up</a>
        </li>
      </ul>

      <div class="hamburger">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </div>
    </div>
  </nav>
</header>
