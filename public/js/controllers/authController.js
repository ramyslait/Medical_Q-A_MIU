/**
 * Authentication Controller
 * Handles login, register, and user authentication logic
 */

class AuthController {
  constructor() {
    this.userModel = new UserModel();
    this.authView = new AuthView();
    this.init();
  }

  init() {
    this.bindEvents();
    this.checkAuthStatus();
  }

  bindEvents() {
    // Login form submission
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
      loginForm.addEventListener("submit", (e) => this.handleLogin(e));
    }

    // Register form submission
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
      registerForm.addEventListener("submit", (e) => this.handleRegister(e));
    }

    // Real-time password validation
    const passwordInputs = document.querySelectorAll(
      'input[name="password"], input[name="confirmPassword"]'
    );
    passwordInputs.forEach((input) => {
      input.addEventListener("input", () => this.validatePasswordMatch());
    });
  }

  checkAuthStatus() {
    // Redirect if already logged in
    if (MediQA.isLoggedIn) {
      const currentPage = window.location.pathname;
      if (
        currentPage.includes("login.html") ||
        currentPage.includes("register.html")
      ) {
        MediQA.showNotification("You are already logged in!", "info");
        setTimeout(() => {
          window.location.href = "..//home";
        }, 1500);
      }
    }
  }

  async handleLogin(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const loginData = {
      email: formData.get("email"),
      password: formData.get("password"),
      remember: formData.get("remember") === "on",
    };

    try {
      // Show loading state
      const submitBtn = e.target.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<span class="loading"></span> Signing in...';
      submitBtn.disabled = true;

      // Simulate API call
      await this.simulateDelay();

      // Validate credentials (in real app, this would be an API call)
      const user = await this.userModel.validateCredentials(
        loginData.email,
        loginData.password
      );

      if (user) {
        // Save user data
        MediQA.saveUserToStorage(user);

        // Show success message
        MediQA.showNotification("Login successful! Welcome back.", "success");

        // Redirect based on user role
        setTimeout(() => {
          if (user.role === "admin") {
            window.location.href = "admin-dashboard.html";
          } else {
            window.location.href = "..//home";
          }
        }, 1500);
      } else {
        throw new Error("Invalid email or password");
      }
    } catch (error) {
      MediQA.showNotification(error.message, "error");
      this.resetSubmitButton(e.target);
    }
  }

  async handleRegister(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const registerData = {
      role: formData.get("role"),
      fullName: formData.get("fullName"),
      email: formData.get("email"),
      password: formData.get("password"),
      confirmPassword: formData.get("confirmPassword"),
      terms: formData.get("terms") === "on",
      newsletter: formData.get("newsletter") === "on",
    };

    try {
      // Validate form data
      this.validateRegisterData(registerData);

      // Show loading state
      const submitBtn = e.target.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<span class="loading"></span> Creating account...';
      submitBtn.disabled = true;

      // Simulate API call
      await this.simulateDelay();

      // Create user (in real app, this would be an API call)
      const user = await this.userModel.createUser(registerData);

      // Save user data
      MediQA.saveUserToStorage(user);

      // Show success message
      MediQA.showNotification(
        "Account created successfully! Welcome to MediQ&A.",
        "success"
      );

      // Redirect to home page
      setTimeout(() => {
        window.location.href = "..//home";
      }, 1500);
    } catch (error) {
      MediQA.showNotification(error.message, "error");
      this.resetSubmitButton(e.target);
    }
  }

  validateRegisterData(data) {
    if (!data.terms) {
      throw new Error(
        "You must agree to the Terms of Service and Privacy Policy"
      );
    }

    if (data.password !== data.confirmPassword) {
      throw new Error("Passwords do not match");
    }

    if (data.password.length < 6) {
      throw new Error("Password must be at least 6 characters long");
    }

    if (!data.role) {
      throw new Error("Please select your role");
    }

    if (!data.fullName.trim()) {
      throw new Error("Full name is required");
    }

    if (!MediQA.isValidEmail(data.email)) {
      throw new Error("Please enter a valid email address");
    }
  }

  validatePasswordMatch() {
    const password = document.querySelector('input[name="password"]');
    const confirmPassword = document.querySelector(
      'input[name="confirmPassword"]'
    );

    if (password && confirmPassword) {
      if (confirmPassword.value && password.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity("Passwords do not match");
      } else {
        confirmPassword.setCustomValidity("");
      }
    }
  }

  resetSubmitButton(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      if (form.id === "loginForm") {
        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
      } else {
        submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
      }
      submitBtn.disabled = false;
    }
  }

  simulateDelay() {
    return new Promise((resolve) => setTimeout(resolve, 1500));
  }
}

/**
 * User Model
 * Handles user data operations and validation
 */
class UserModel {
  constructor() {
    this.users = this.getDummyUsers();
  }

  getDummyUsers() {
    return [
      {
        id: "user-001",
        name: "Dr. Sarah Johnson",
        email: "sarah.johnson@hospital.com",
        password: "password123",
        role: "admin",
        avatar: "https://via.placeholder.com/100/2563eb/ffffff?text=SA",
        joinDate: "2024-01-15",
        verified: true,
      },
      {
        id: "user-002",
        name: "Dr. Michael Chen",
        email: "michael.chen@clinic.com",
        password: "password123",
        role: "provider",
        avatar: "https://via.placeholder.com/100/059669/ffffff?text=MC",
        joinDate: "2024-01-20",
        verified: true,
      },
      {
        id: "user-003",
        name: "Emily Rodriguez",
        email: "emily.rodriguez@email.com",
        password: "password123",
        role: "patient",
        avatar: "https://via.placeholder.com/100/06b6d4/ffffff?text=ER",
        joinDate: "2024-01-25",
        verified: true,
      },
    ];
  }

  async validateCredentials(email, password) {
    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    const user = this.users.find(
      (u) => u.email === email && u.password === password
    );

    if (user) {
      // Return user data without password
      const { password: _, ...userWithoutPassword } = user;
      return userWithoutPassword;
    }

    return null;
  }

  async createUser(userData) {
    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Check if email already exists
    const existingUser = this.users.find((u) => u.email === userData.email);
    if (existingUser) {
      throw new Error("An account with this email already exists");
    }

    // Create new user
    const newUser = {
      id: `user-${Date.now()}`,
      name: userData.fullName,
      email: userData.email,
      role: userData.role,
      avatar: `https://via.placeholder.com/100/2563eb/ffffff?text=${userData.fullName
        .charAt(0)
        .toUpperCase()}`,
      joinDate: new Date().toISOString().split("T")[0],
      verified: false,
    };

    // Add to users array (in real app, this would be saved to database)
    this.users.push({
      ...newUser,
      password: userData.password,
    });

    return newUser;
  }

  async getUserById(id) {
    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 500));

    const user = this.users.find((u) => u.id === id);
    if (user) {
      const { password: _, ...userWithoutPassword } = user;
      return userWithoutPassword;
    }

    return null;
  }

  async updateUser(id, updateData) {
    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    const userIndex = this.users.findIndex((u) => u.id === id);
    if (userIndex !== -1) {
      this.users[userIndex] = { ...this.users[userIndex], ...updateData };
      const { password: _, ...userWithoutPassword } = this.users[userIndex];
      return userWithoutPassword;
    }

    throw new Error("User not found");
  }
}

/**
 * Auth View
 * Handles authentication UI updates and interactions
 */
class AuthView {
  constructor() {
    this.init();
  }

  init() {
    this.initializePasswordToggle();
    this.initializeFormAnimations();
  }

  initializePasswordToggle() {
    // Add password toggle functionality
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach((input) => {
      const wrapper = input.parentNode;
      const toggleBtn = document.createElement("button");
      toggleBtn.type = "button";
      toggleBtn.className = "password-toggle";
      toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
      toggleBtn.style.cssText = `
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: var(--text-secondary);
                cursor: pointer;
                padding: 5px;
            `;

      wrapper.style.position = "relative";
      wrapper.appendChild(toggleBtn);

      toggleBtn.addEventListener("click", () => {
        const type =
          input.getAttribute("type") === "password" ? "text" : "password";
        input.setAttribute("type", type);
        toggleBtn.innerHTML =
          type === "password"
            ? '<i class="fas fa-eye"></i>'
            : '<i class="fas fa-eye-slash"></i>';
      });
    });
  }

  initializeFormAnimations() {
    // Animate form elements on load
    const formGroups = document.querySelectorAll(".form-group");
    formGroups.forEach((group, index) => {
      group.style.opacity = "0";
      group.style.transform = "translateY(20px)";
      group.style.transition = "all 0.5s ease";

      setTimeout(() => {
        group.style.opacity = "1";
        group.style.transform = "translateY(0)";
      }, index * 100);
    });
  }

  showError(message) {
    MediQA.showNotification(message, "error");
  }

  showSuccess(message) {
    MediQA.showNotification(message, "success");
  }
}

// Initialize auth controller when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  if (
    window.location.pathname.includes("login.html") ||
    window.location.pathname.includes("register.html")
  ) {
    new AuthController();
  }
});
