/**
 * Home Page Controller
 * Handles home page specific functionality
 */

class HomeController {
  constructor() {
    this.init();
  }

  init() {
    this.initializeHeroAnimations();
    this.initializeStatsCounter();
    this.initializeFeatureCards();
  }

  // Initialize hero section animations
  initializeHeroAnimations() {
    const heroTitle = document.querySelector(".hero-title");
    const heroDescription = document.querySelector(".hero-description");
    const heroButtons = document.querySelector(".hero-buttons");

    if (heroTitle) {
      // Animate hero title on load
      setTimeout(() => {
        heroTitle.style.opacity = "0";
        heroTitle.style.transform = "translateY(30px)";
        heroTitle.style.transition = "all 0.8s ease";

        setTimeout(() => {
          heroTitle.style.opacity = "1";
          heroTitle.style.transform = "translateY(0)";
        }, 100);
      }, 300);
    }

    if (heroDescription) {
      // Animate hero description
      setTimeout(() => {
        heroDescription.style.opacity = "0";
        heroDescription.style.transform = "translateY(30px)";
        heroDescription.style.transition = "all 0.8s ease";

        setTimeout(() => {
          heroDescription.style.opacity = "1";
          heroDescription.style.transform = "translateY(0)";
        }, 200);
      }, 500);
    }

    if (heroButtons) {
      // Animate hero buttons
      setTimeout(() => {
        heroButtons.style.opacity = "0";
        heroButtons.style.transform = "translateY(30px)";
        heroButtons.style.transition = "all 0.8s ease";

        setTimeout(() => {
          heroButtons.style.opacity = "1";
          heroButtons.style.transform = "translateY(0)";
        }, 300);
      }, 700);
    }
  }

  // Initialize stats counter animation
  initializeStatsCounter() {
    const statNumbers = document.querySelectorAll(".stat-number");

    const animateCounter = (element, target) => {
      let current = 0;
      const increment = target / 100;
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }

        // Format number based on target value
        if (target >= 1000) {
          element.textContent =
            Math.floor(current / 1000) +
            "," +
            String(Math.floor(current % 1000)).padStart(3, "0") +
            "+";
        } else if (target >= 100) {
          element.textContent = Math.floor(current) + "%";
        } else {
          element.textContent = Math.floor(current) + "+";
        }
      }, 20);
    };

    // Start animation when stats section comes into view
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const statNumber = entry.target.querySelector(".stat-number");
          if (statNumber) {
            const text = statNumber.textContent;
            let target = 0;

            if (text.includes("10,000")) target = 10000;
            else if (text.includes("500")) target = 500;
            else if (text.includes("50,000")) target = 50000;
            else if (text.includes("95%")) target = 95;

            if (target > 0) {
              statNumber.textContent = "0";
              animateCounter(statNumber, target);
            }
          }
          observer.unobserve(entry.target);
        }
      });
    });

    statNumbers.forEach((statNumber) => {
      const statItem = statNumber.closest(".stat-item");
      if (statItem) {
        observer.observe(statItem);
      }
    });
  }

  // Initialize feature cards hover effects
  initializeFeatureCards() {
    const featureCards = document.querySelectorAll(".feature-card");

    featureCards.forEach((card) => {
      card.addEventListener("mouseenter", () => {
        card.style.transform = "translateY(-10px) scale(1.02)";
      });

      card.addEventListener("mouseleave", () => {
        card.style.transform = "translateY(0) scale(1)";
      });
    });
  }

  // Handle CTA button clicks
  handleSignUpClick() {
    if (MediQA.isLoggedIn) {
      MediQA.showNotification("You are already logged in!", "info");
      return;
    }
    window.location.href = "pages/register.html";
  }

  handleSubmitQuestionClick() {
    if (!MediQA.isLoggedIn) {
      MediQA.showNotification("Please log in to submit a question", "warning");
      setTimeout(() => {
        window.location.href = "pages/login.html";
      }, 1500);
      return;
    }
    window.location.href = "pages/ask-question.html";
  }
}

// Initialize home controller when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  if (
    window.location.pathname.includes("index.html") ||
    window.location.pathname === "/"
  ) {
    new HomeController();
  }
});
