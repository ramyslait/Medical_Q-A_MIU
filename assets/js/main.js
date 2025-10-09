/**
 * Medical Q&A - Main JavaScript File
 * Handles common functionality across all pages
 */

// Global state management
window.MediQA = {
  currentUser: null,
  isLoggedIn: false,
  userRole: null,

  // Initialize the application
  init: function () {
    this.loadUserFromStorage();
    this.initializeNavigation();
    this.initializeEventListeners();
  },

  // Load user data from localStorage
  loadUserFromStorage: function () {
    const userData = localStorage.getItem("mediqa_user");
    if (userData) {
      this.currentUser = JSON.parse(userData);
      this.isLoggedIn = true;
      this.userRole = this.currentUser.role;
      this.updateNavigationForLoggedInUser();
    }
  },

  // Save user data to localStorage
  saveUserToStorage: function (userData) {
    localStorage.setItem("mediqa_user", JSON.stringify(userData));
    this.currentUser = userData;
    this.isLoggedIn = true;
    this.userRole = userData.role;
    this.updateNavigationForLoggedInUser();
  },

  // Clear user data from localStorage
  clearUserData: function () {
    localStorage.removeItem("mediqa_user");
    this.currentUser = null;
    this.isLoggedIn = false;
    this.userRole = null;
    this.updateNavigationForGuest();
  },

  // Update navigation for logged-in users
  updateNavigationForLoggedInUser: function () {
    const navMenu = document.querySelector(".nav-menu");
    if (navMenu) {
      // Remove login/register links
      const loginLink = navMenu.querySelector('a[href*="login.html"]');
      const registerLink = navMenu.querySelector('a[href*="register.html"]');

      if (loginLink) loginLink.parentElement.remove();
      if (registerLink) registerLink.parentElement.remove();

      // Add user menu
      const userMenu = document.createElement("li");
      userMenu.className = "nav-item user-menu";
      userMenu.innerHTML = `
                <div class="user-dropdown">
                    <a href="#" class="nav-link user-toggle">
                        <i class="fas fa-user-circle"></i>
                        ${this.currentUser.name}
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a href="pages/profile.html" class="dropdown-item">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        ${
                          this.userRole === "admin"
                            ? '<a href="pages/admin-dashboard.html" class="dropdown-item"><i class="fas fa-cog"></i> Admin Dashboard</a>'
                            : ""
                        }
                        <a href="#" class="dropdown-item" onclick="MediQA.logout()">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            `;

      navMenu.appendChild(userMenu);
    }
  },

  // Update navigation for guest users
  updateNavigationForGuest: function () {
    const navMenu = document.querySelector(".nav-menu");
    if (navMenu) {
      // Remove user menu
      const userMenu = navMenu.querySelector(".user-menu");
      if (userMenu) userMenu.remove();

      // Add back login/register links if they don't exist
      if (!navMenu.querySelector('a[href*="login.html"]')) {
        const loginItem = document.createElement("li");
        loginItem.className = "nav-item";
        loginItem.innerHTML =
          '<a href="pages/login.html" class="nav-link">Login</a>';
        navMenu.appendChild(loginItem);
      }

      if (!navMenu.querySelector('a[href*="register.html"]')) {
        const registerItem = document.createElement("li");
        registerItem.className = "nav-item";
        registerItem.innerHTML =
          '<a href="pages/register.html" class="nav-link nav-cta">Sign Up</a>';
        navMenu.appendChild(registerItem);
      }
    }
  },

  // Initialize navigation functionality
  initializeNavigation: function () {
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");

    if (hamburger && navMenu) {
      hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
      });

      // Close mobile menu when clicking on a link
      document.querySelectorAll(".nav-link").forEach((link) => {
        link.addEventListener("click", () => {
          hamburger.classList.remove("active");
          navMenu.classList.remove("active");
        });
      });
    }

    // Handle user dropdown
    document.addEventListener("click", (e) => {
      if (e.target.closest(".user-dropdown")) {
        const dropdown = e.target.closest(".user-dropdown");
        dropdown.classList.toggle("active");
      } else {
        document.querySelectorAll(".user-dropdown").forEach((dropdown) => {
          dropdown.classList.remove("active");
        });
      }
    });
  },

  // Initialize common event listeners
  initializeEventListeners: function () {
    // Form validation
    document.addEventListener("submit", (e) => {
      if (e.target.classList.contains("needs-validation")) {
        e.preventDefault();
        if (this.validateForm(e.target)) {
          e.target.submit();
        }
      }
    });

    // Show loading states on form submissions
    document.querySelectorAll("form").forEach((form) => {
      form.addEventListener("submit", (e) => {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.innerHTML = '<span class="loading"></span> Submitting...';
          submitBtn.disabled = true;
        }
      });
    });
  },

  // Form validation
  validateForm: function (form) {
    let isValid = true;
    const inputs = form.querySelectorAll("input, select, textarea");

    // Clear previous errors
    form.querySelectorAll(".form-error").forEach((error) => {
      error.remove();
    });

    inputs.forEach((input) => {
      const value = input.value.trim();
      const required = input.hasAttribute("required");
      const type = input.type;

      if (required && !value) {
        this.showFieldError(input, "This field is required");
        isValid = false;
      } else if (type === "email" && value && !this.isValidEmail(value)) {
        this.showFieldError(input, "Please enter a valid email address");
        isValid = false;
      } else if (type === "password" && value && value.length < 6) {
        this.showFieldError(input, "Password must be at least 6 characters");
        isValid = false;
      }
    });

    // Check password confirmation
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirmPassword"]');
    if (
      password &&
      confirmPassword &&
      password.value !== confirmPassword.value
    ) {
      this.showFieldError(confirmPassword, "Passwords do not match");
      isValid = false;
    }

    return isValid;
  },

  // Show field error
  showFieldError: function (input, message) {
    const error = document.createElement("div");
    error.className = "form-error";
    error.textContent = message;
    input.parentNode.appendChild(error);
    input.classList.add("error");
  },

  // Email validation
  isValidEmail: function (email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  },

  // Show notification
  showNotification: function (message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
            </div>
        `;

    // Insert at the top of the page
    const container = document.querySelector(".container") || document.body;
    container.insertBefore(notification, container.firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.remove();
      }
    }, 5000);
  },

  // Logout function
  logout: function () {
    this.clearUserData();
    this.showNotification("You have been logged out successfully", "success");
    setTimeout(() => {
      window.location.href = "index.html";
    }, 1500);
  },

  // Redirect to login if not authenticated
  requireAuth: function () {
    if (!this.isLoggedIn) {
      this.showNotification("Please log in to access this page", "warning");
      setTimeout(() => {
        window.location.href = "pages/login.html";
      }, 1500);
      return false;
    }
    return true;
  },

  // Require admin role
  requireAdmin: function () {
    if (!this.requireAuth()) return false;
    if (this.userRole !== "admin") {
      this.showNotification(
        "Access denied. Admin privileges required.",
        "error"
      );
      setTimeout(() => {
        window.location.href = "index.html";
      }, 1500);
      return false;
    }
    return true;
  },
};

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  MediQA.init();
});

// Add some CSS for dropdown menu
const dropdownStyles = `
<style>
.user-dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    min-width: 200px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: var(--transition);
    z-index: 1000;
}

.user-dropdown.active .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: var(--text-primary);
    text-decoration: none;
    transition: var(--transition);
    gap: 0.5rem;
}

.dropdown-item:hover {
    background-color: var(--bg-accent);
    color: var(--primary-color);
}

.dropdown-item i {
    width: 16px;
}

.user-toggle .fa-chevron-down {
    margin-left: 0.5rem;
    font-size: 0.8rem;
    transition: var(--transition);
}

.user-dropdown.active .user-toggle .fa-chevron-down {
    transform: rotate(180deg);
}

.form-input.error {
    border-color: #dc2626;
}

@media (max-width: 768px) {
    .dropdown-menu {
        right: auto;
        left: 50%;
        transform: translateX(-50%) translateY(-10px);
    }
    
    .user-dropdown.active .dropdown-menu {
        transform: translateX(-50%) translateY(0);
    }
}
</style>
`;

document.head.insertAdjacentHTML("beforeend", dropdownStyles);
