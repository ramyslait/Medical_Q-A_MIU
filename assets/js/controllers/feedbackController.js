/**
 * Feedback Controller
 * Handles feedback and contact form functionality
 */

class FeedbackController {
  constructor() {
    this.feedbackView = new FeedbackView();
    this.feedbackModel = new FeedbackModel();
    this.init();
  }

  init() {
    this.bindEvents();
  }

  bindEvents() {
    // Contact form submission
    const contactForm = document.getElementById("contactForm");
    if (contactForm) {
      contactForm.addEventListener("submit", (e) =>
        this.handleContactSubmission(e)
      );
    }

    // Initialize FAQ accordions
    this.initializeFAQAccordions();
  }

  async handleContactSubmission(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const contactData = {
      type: formData.get("contactType"),
      firstName: formData.get("firstName"),
      lastName: formData.get("lastName"),
      email: formData.get("email"),
      subject: formData.get("subject"),
      message: formData.get("message"),
      newsletter: formData.get("newsletter") === "on",
    };

    try {
      // Validate contact data
      this.validateContactData(contactData);

      // Show loading state
      const submitBtn = e.target.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<span class="loading"></span> Sending...';
      submitBtn.disabled = true;

      // Simulate API call
      await this.simulateDelay();

      // Submit contact form
      await this.feedbackModel.submitContact(contactData);

      // Show success state
      this.feedbackView.showSubmissionSuccess();
    } catch (error) {
      MediQA.showNotification(error.message, "error");
      this.resetSubmitButton(e.target);
    }
  }

  validateContactData(data) {
    if (!data.firstName.trim()) {
      throw new Error("First name is required");
    }

    if (!data.lastName.trim()) {
      throw new Error("Last name is required");
    }

    if (!MediQA.isValidEmail(data.email)) {
      throw new Error("Please enter a valid email address");
    }

    if (!data.subject.trim()) {
      throw new Error("Subject is required");
    }

    if (!data.message.trim()) {
      throw new Error("Message is required");
    }

    if (data.message.length < 10) {
      throw new Error("Please provide a more detailed message");
    }
  }

  initializeFAQAccordions() {
    document.querySelectorAll(".faq-question").forEach((question) => {
      question.addEventListener("click", () => {
        const faqItem = question.parentElement;
        const answer = faqItem.querySelector(".faq-answer");
        const icon = question.querySelector("i");

        // Close other open items
        document.querySelectorAll(".faq-item").forEach((item) => {
          if (item !== faqItem) {
            item.classList.remove("active");
            answer.style.maxHeight = "0";
            icon.style.transform = "rotate(0deg)";
          }
        });

        // Toggle current item
        faqItem.classList.toggle("active");
        if (faqItem.classList.contains("active")) {
          answer.style.maxHeight = answer.scrollHeight + "px";
          icon.style.transform = "rotate(180deg)";
        } else {
          answer.style.maxHeight = "0";
          icon.style.transform = "rotate(0deg)";
        }
      });
    });
  }

  resetSubmitButton(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
      submitBtn.disabled = false;
    }
  }

  simulateDelay() {
    return new Promise((resolve) => setTimeout(resolve, 2000));
  }
}

/**
 * Feedback Model
 * Handles feedback data operations
 */
class FeedbackModel {
  constructor() {
    this.feedbackSubmissions = [];
  }

  async submitContact(contactData) {
    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 1500));

    const submission = {
      id: `feedback-${Date.now()}`,
      ...contactData,
      submittedAt: new Date().toISOString(),
      status: "pending",
    };

    this.feedbackSubmissions.push(submission);

    // Save to localStorage for demo purposes
    localStorage.setItem(
      "feedback_submissions",
      JSON.stringify(this.feedbackSubmissions)
    );

    return submission;
  }

  async getFeedbackSubmissions() {
    await new Promise((resolve) => setTimeout(resolve, 500));
    return JSON.parse(localStorage.getItem("feedback_submissions") || "[]");
  }
}

/**
 * Feedback View
 * Handles feedback UI updates
 */
class FeedbackView {
  constructor() {
    this.init();
  }

  init() {
    this.initializeFormAnimations();
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

  showSubmissionSuccess() {
    // Show success message
    MediQA.showNotification(
      "Message sent successfully! We'll get back to you soon.",
      "success"
    );

    // Reset form
    const form = document.getElementById("contactForm");
    if (form) {
      form.reset();
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
}

// Initialize feedback controller when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("feedback.html")) {
    new FeedbackController();
  }
});
