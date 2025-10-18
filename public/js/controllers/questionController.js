/**
 * Question Controller
 * Handles question submission, management, and display functionality
 */

class QuestionController {
  constructor() {
    this.questionView = new QuestionView();
    this.questionModel = new QuestionModel();
    this.init();
  }

  init() {
    this.bindEvents();
    this.loadDraftQuestion();
  }

  bindEvents() {
    // Question form submission
    const questionForm = document.getElementById("questionForm");
    if (questionForm) {
      questionForm.addEventListener("submit", (e) =>
        this.handleQuestionSubmission(e)
      );
    }

    // Auto-save draft functionality
    const formInputs = document.querySelectorAll(
      "#questionForm input, #questionForm textarea, #questionForm select"
    );
    formInputs.forEach((input) => {
      input.addEventListener("input", () => this.autoSaveDraft());
    });

    // Character counter for textarea
    const descriptionTextarea = document.getElementById("questionDescription");
    if (descriptionTextarea) {
      this.addCharacterCounter(descriptionTextarea);
    }

    // Category-specific guidance
    const categorySelect = document.getElementById("questionCategory");
    if (categorySelect) {
      categorySelect.addEventListener("change", (e) =>
        this.showCategoryGuidance(e.target.value)
      );
    }
  }

  async handleQuestionSubmission(e) {
    // Validate form data before submission
    const formData = new FormData(e.target);
    const questionData = {
      title: formData.get("questionTitle"),
      category: formData.get("questionCategory"),
      description: formData.get("questionDescription"),
      urgency: formData.get("urgency"),
      age: formData.get("age"),
      gender: formData.get("gender"),
      anonymous: formData.get("anonymous") === "on",
      followUp: formData.get("followUp") === "on",
      terms: formData.get("terms") === "on",
      emergency: formData.get("emergency") === "on",
    };

    try {
      // Validate question data
      this.validateQuestionData(questionData);

      // Show loading state
      const submitBtn = e.target.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<span class="loading"></span> Submitting...';
      submitBtn.disabled = true;

      // Let the form submit naturally to the PHP backend
      // The form will be submitted to the server and handled by questionController.php
      
    } catch (error) {
      e.preventDefault(); // Only prevent default if validation fails
      MediQA.showNotification(error.message, "error");
      this.resetSubmitButton(e.target);
    }
  }

  validateQuestionData(data) {
    if (!data.terms || !data.emergency) {
      throw new Error("Please accept the terms and conditions");
    }

    if (data.description.length < 50) {
      throw new Error(
        "Please provide a more detailed description (at least 50 characters)"
      );
    }

    if (data.title.length < 10) {
      throw new Error("Please provide a more descriptive title");
    }
  }

  addCharacterCounter(textarea) {
    const counter = document.createElement("div");
    counter.className = "character-counter";
    counter.style.cssText = `
            text-align: right;
            font-size: 0.8rem;
            color: var(--text-light);
            margin-top: 0.25rem;
        `;

    textarea.parentNode.appendChild(counter);

    const updateCounter = () => {
      const count = textarea.value.length;
      counter.textContent = `${count} characters`;

      if (count < 50) {
        counter.style.color = "#dc2626";
      } else if (count > 1000) {
        counter.style.color = "#f59e0b";
      } else {
        counter.style.color = "var(--text-light)";
      }
    };

    textarea.addEventListener("input", updateCounter);
    updateCounter();
  }

  showCategoryGuidance(category) {
    const guidance = {
      symptoms:
        "Please describe your symptoms in detail, including when they started, severity, and any triggers.",
      treatments:
        "Describe your current treatment plan and any concerns or questions you have about it.",
      drugs:
        "List all medications and supplements you're taking, including dosages and any side effects.",
      preventive:
        "Share your health goals and any preventive measures you're considering.",
      diagnosis:
        "Provide details about any tests, results, or diagnostic procedures you've had.",
      emergency:
        "If this is truly an emergency, please call emergency services immediately.",
      other:
        "Provide as much context as possible to help our experts understand your situation.",
    };

    // Remove existing guidance
    const existingGuidance = document.querySelector(".category-guidance");
    if (existingGuidance) {
      existingGuidance.remove();
    }

    if (category && guidance[category]) {
      const guidanceElement = document.createElement("div");
      guidanceElement.className = "category-guidance alert alert-info";
      guidanceElement.innerHTML = `
                <i class="fas fa-info-circle"></i>
                <strong>Tip:</strong> ${guidance[category]}
            `;

      const categorySelect = document.getElementById("questionCategory");
      categorySelect.parentNode.appendChild(guidanceElement);
    }
  }

  autoSaveDraft() {
    const formData = new FormData(document.getElementById("questionForm"));
    const draftData = {
      title: formData.get("questionTitle"),
      category: formData.get("questionCategory"),
      description: formData.get("questionDescription"),
      urgency: formData.get("urgency"),
      age: formData.get("age"),
      gender: formData.get("gender"),
      anonymous: formData.get("anonymous") === "on",
      followUp: formData.get("followUp") === "on",
      timestamp: new Date().toISOString(),
    };

    localStorage.setItem("question_draft", JSON.stringify(draftData));
  }

  loadDraftQuestion() {
    const draft = localStorage.getItem("question_draft");
    if (draft) {
      const draftData = JSON.parse(draft);

      // Check if draft is recent (within 24 hours)
      const draftAge = Date.now() - new Date(draftData.timestamp).getTime();
      if (draftAge < 24 * 60 * 60 * 1000) {
        this.questionView.showDraftNotification(draftData);
      } else {
        localStorage.removeItem("question_draft");
      }
    }
  }

  clearDraft() {
    localStorage.removeItem("question_draft");
  }

  resetSubmitButton(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.innerHTML =
        '<i class="fas fa-paper-plane"></i> Submit Question';
      submitBtn.disabled = false;
    }
  }

  simulateDelay() {
    return new Promise((resolve) => setTimeout(resolve, 2000));
  }
}

/**
 * Question Model
 * Handles question data operations
 */
class QuestionModel {
  constructor() {
    this.questions = [];
    this.questionIdCounter = 1;
  }

  async submitQuestion(questionData) {
    // Simulate API delay
    await new Promise((resolve) => setTimeout(resolve, 1500));

    const question = {
      id: `MQ-2024-${String(this.questionIdCounter).padStart(3, "0")}`,
      title: questionData.title,
      category: questionData.category,
      description: questionData.description,
      urgency: questionData.urgency,
      age: questionData.age,
      gender: questionData.gender,
      anonymous: questionData.anonymous,
      followUp: questionData.followUp,
      status: "pending",
      submittedAt: new Date().toISOString(),
      userId: MediQA.currentUser?.id || "anonymous",
      responses: [],
    };

    this.questions.push(question);
    this.questionIdCounter++;

    // Save to localStorage for demo purposes
    localStorage.setItem("submitted_questions", JSON.stringify(this.questions));

    return question;
  }

  async getQuestions(status = null) {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const questions = JSON.parse(
      localStorage.getItem("submitted_questions") || "[]"
    );

    if (status) {
      return questions.filter((q) => q.status === status);
    }

    return questions;
  }

  async getQuestionById(id) {
    await new Promise((resolve) => setTimeout(resolve, 300));

    const questions = JSON.parse(
      localStorage.getItem("submitted_questions") || "[]"
    );
    return questions.find((q) => q.id === id);
  }

  async updateQuestionStatus(id, status) {
    await new Promise((resolve) => setTimeout(resolve, 500));

    const questions = JSON.parse(
      localStorage.getItem("submitted_questions") || "[]"
    );
    const questionIndex = questions.findIndex((q) => q.id === id);

    if (questionIndex !== -1) {
      questions[questionIndex].status = status;
      localStorage.setItem("submitted_questions", JSON.stringify(questions));
      return questions[questionIndex];
    }

    throw new Error("Question not found");
  }
}

/**
 * Question View
 * Handles question UI updates and interactions
 */
class QuestionView {
  constructor() {
    this.init();
  }

  init() {
    this.initializeFormAnimations();
  }

  initializeFormAnimations() {
    // Animate form sections on load
    const formSections = document.querySelectorAll(".form-section");
    formSections.forEach((section, index) => {
      section.style.opacity = "0";
      section.style.transform = "translateY(20px)";
      section.style.transition = "all 0.5s ease";

      setTimeout(() => {
        section.style.opacity = "1";
        section.style.transform = "translateY(0)";
      }, index * 200);
    });
  }

  showSubmissionSuccess(question) {
    // Hide form and show success status
    const formContainer = document.querySelector(".question-form-container");
    const statusContainer = document.getElementById("questionStatus");

    formContainer.classList.add("hidden");
    statusContainer.classList.remove("hidden");

    // Update question ID
    const questionIdElement = document.getElementById("questionId");
    if (questionIdElement) {
      questionIdElement.textContent = `#${question.id}`;
    }

    // Show success notification
    MediQA.showNotification("Question submitted successfully!", "success");

    // Scroll to status section
    statusContainer.scrollIntoView({ behavior: "smooth" });
  }

  showDraftNotification(draftData) {
    const notification = document.createElement("div");
    notification.className = "draft-notification alert alert-info";
    notification.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <i class="fas fa-save"></i>
                    <strong>Draft Found:</strong> You have an unsaved draft from ${new Date(
                      draftData.timestamp
                    ).toLocaleString()}
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn btn-small btn-primary" onclick="loadDraft()">Load Draft</button>
                    <button class="btn btn-small btn-outline" onclick="discardDraft()">Discard</button>
                </div>
            </div>
        `;

    // Insert after page header
    const pageHeader = document.querySelector(".page-header");
    pageHeader.insertAdjacentElement("afterend", notification);

    // Make functions globally available
    window.loadDraft = () => {
      this.loadDraftData(draftData);
      notification.remove();
    };

    window.discardDraft = () => {
      localStorage.removeItem("question_draft");
      notification.remove();
    };
  }

  loadDraftData(draftData) {
    // Populate form with draft data
    if (draftData.title)
      document.getElementById("questionTitle").value = draftData.title;
    if (draftData.category)
      document.getElementById("questionCategory").value = draftData.category;
    if (draftData.description)
      document.getElementById("questionDescription").value =
        draftData.description;
    if (draftData.urgency)
      document.getElementById("urgency").value = draftData.urgency;
    if (draftData.age) document.getElementById("age").value = draftData.age;
    if (draftData.gender)
      document.getElementById("gender").value = draftData.gender;

    // Handle checkboxes
    document.querySelector('input[name="anonymous"]').checked =
      draftData.anonymous;
    document.querySelector('input[name="followUp"]').checked =
      draftData.followUp;

    MediQA.showNotification("Draft loaded successfully", "success");
  }

  displayQuestion(question) {
    // Display question details (for question details page)
    const questionContainer = document.getElementById("questionContainer");
    if (questionContainer) {
      questionContainer.innerHTML = `
                <div class="question-detail">
                    <div class="question-header">
                        <h1>${question.title}</h1>
                        <div class="question-meta">
                            <span class="badge badge-${this.getStatusBadgeClass(
                              question.status
                            )}">${question.status}</span>
                            <span class="question-category">${
                              question.category
                            }</span>
                            <span class="question-date">${this.formatDate(
                              question.submittedAt
                            )}</span>
                        </div>
                    </div>
                    
                    <div class="question-content">
                        <p>${question.description}</p>
                    </div>
                    
                    ${
                      question.responses.length > 0
                        ? `
                        <div class="responses-section">
                            <h3>Expert Responses</h3>
                            ${question.responses
                              .map((response) => this.renderResponse(response))
                              .join("")}
                        </div>
                    `
                        : ""
                    }
                </div>
            `;
    }
  }

  renderResponse(response) {
    return `
            <div class="response-item">
                <div class="response-header">
                    <div class="expert-info">
                        <img src="${
                          response.expertAvatar
                        }" alt="Expert" class="expert-avatar">
                        <div class="expert-details">
                            <div class="expert-name">${
                              response.expertName
                            }</div>
                            <div class="expert-title">${
                              response.expertTitle
                            }</div>
                        </div>
                    </div>
                    <div class="response-date">${this.formatDate(
                      response.submittedAt
                    )}</div>
                </div>
                <div class="response-content">
                    ${response.content}
                </div>
                <div class="response-actions">
                    <button class="btn btn-small btn-success" onclick="rateResponse('${
                      response.id
                    }', 'helpful')">
                        <i class="fas fa-thumbs-up"></i> Helpful
                    </button>
                    <button class="btn btn-small btn-outline" onclick="rateResponse('${
                      response.id
                    }', 'not-helpful')">
                        <i class="fas fa-thumbs-down"></i> Not Helpful
                    </button>
                </div>
            </div>
        `;
  }

  getStatusBadgeClass(status) {
    const statusClasses = {
      pending: "warning",
      answered: "success",
      closed: "info",
    };
    return statusClasses[status] || "info";
  }

  formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  }
}

// Global functions for question actions
window.rateResponse = function (responseId, rating) {
  MediQA.showNotification(`Response ${rating} feedback recorded`, "success");
};

// Initialize question controller when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  if (window.location.pathname.includes("ask-question.html")) {
    new QuestionController();
  }
});
