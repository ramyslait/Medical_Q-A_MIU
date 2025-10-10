<?php
  $current = $_SERVER['REQUEST_URI']; // e.g., /home, /login, etc.
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
          <a href="home" class="nav-link <?= $current == '/home' || $current == '/' ? 'active' : '' ?>">Home</a>
        </li>
        <li class="nav-item">
          <a href="ask-question" class="nav-link <?= $current == '/ask-question' ? 'active' : '' ?>">Ask Question</a>
        </li>
        <li class="nav-item">
          <a href="forum" class="nav-link <?= $current == '/forum' ? 'active' : '' ?>">Forum</a>
        </li>
        <li class="nav-item">
          <a href="login" class="nav-link <?= $current == '/login' ? 'active' : '' ?>">Login</a>
        </li>
        <li class="nav-item">
          <a href="register" class="nav-link nav-cta <?= $current == '/register' ? 'active' : '' ?>">Sign Up</a>
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
