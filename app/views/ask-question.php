<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ask Question - Medical Q&A</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/components.css">
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
</head>

<body>
  <!-- Navigation Header -->
  <?php include '../app/partials/navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <div class="page-header">
        <h1>Ask a Medical Question</h1>
        <p>
          Get expert medical advice from certified healthcare professionals
        </p>
      </div>

      <div class="question-form-container">
        <form class="question-form needs-validation" id="questionForm">
          <div class="form-section">
            <h3><i class="fas fa-edit"></i> Question Details</h3>

            <div class="form-group">
              <label for="questionTitle" class="form-label">Question Title *</label>
              <input
                type="text"
                id="questionTitle"
                name="questionTitle"
                class="form-input"
                placeholder="Brief description of your question"
                required />
              <div class="form-help">Keep it concise and descriptive</div>
            </div>

            <div class="form-group">
              <label for="questionCategory" class="form-label">Category *</label>
              <select
                id="questionCategory"
                name="questionCategory"
                class="form-select"
                required>
                <option value="">Select a category</option>
                <option value="symptoms">Symptoms</option>
                <option value="treatments">Treatments</option>
                <option value="drugs">Drugs & Medications</option>
                <option value="preventive">Preventive Care</option>
                <option value="diagnosis">Diagnosis</option>
                <option value="emergency">Emergency</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="form-group">
              <label for="questionDescription" class="form-label">Detailed Description *</label>
              <textarea
                id="questionDescription"
                name="questionDescription"
                class="form-textarea"
                placeholder="Please provide detailed information about your question, including:
• Your symptoms or concerns
• Duration of the issue
• Any medications you're taking
• Relevant medical history
• Any specific questions you have"
                required></textarea>
              <div class="form-help">
                The more details you provide, the better our experts can help
                you
              </div>
            </div>

            <div class="form-group">
              <label for="urgency" class="form-label">Urgency Level</label>
              <select id="urgency" name="urgency" class="form-select">
                <option value="low">Low - General question</option>
                <option value="medium" selected>
                  Medium - Needs attention within 24 hours
                </option>
                <option value="high">
                  High - Urgent, needs quick response
                </option>
              </select>
            </div>
          </div>

          <div class="form-section">
            <h3><i class="fas fa-user-circle"></i> Additional Information</h3>

            <div class="form-group">
              <label for="age" class="form-label">Age (Optional)</label>
              <input
                type="number"
                id="age"
                name="age"
                class="form-input"
                placeholder="Your age"
                min="1"
                max="120" />
            </div>

            <div class="form-group">
              <label for="gender" class="form-label">Gender (Optional)</label>
              <select id="gender" name="gender" class="form-select">
                <option value="">Prefer not to say</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="anonymous" checked />
                <span class="checkmark"></span>
                Ask anonymously (recommended for privacy)
              </label>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="followUp" checked />
                <span class="checkmark"></span>
                Allow follow-up questions from healthcare providers
              </label>
            </div>
          </div>

          <div class="form-section">
            <h3><i class="fas fa-shield-alt"></i> Terms & Conditions</h3>

            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="terms" required />
                <span class="checkmark"></span>
                I understand that this is for informational purposes only and
                does not replace professional medical consultation
              </label>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="emergency" required />
                <span class="checkmark"></span>
                I understand that for medical emergencies, I should call
                emergency services immediately
              </label>
            </div>
          </div>

          <div class="form-actions">
            <button
              type="button"
              class="btn btn-outline"
              onclick="saveDraft()">
              <i class="fas fa-save"></i>
              Save as Draft
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-paper-plane"></i>
              Submit Question
            </button>
          </div>
        </form>

        <div class="question-guidelines">
          <h3>
            <i class="fas fa-lightbulb"></i> Guidelines for Better Answers
          </h3>
          <div class="guidelines-grid">
            <div class="guideline-item">
              <div class="guideline-icon">
                <i class="fas fa-info-circle"></i>
              </div>
              <div class="guideline-content">
                <h4>Be Specific</h4>
                <p>
                  Provide detailed information about your symptoms, duration,
                  and any relevant context.
                </p>
              </div>
            </div>

            <div class="guideline-item">
              <div class="guideline-icon">
                <i class="fas fa-clock"></i>
              </div>
              <div class="guideline-content">
                <h4>Include Timeline</h4>
                <p>
                  Mention when symptoms started and how they've changed over
                  time.
                </p>
              </div>
            </div>

            <div class="guideline-item">
              <div class="guideline-icon">
                <i class="fas fa-pills"></i>
              </div>
              <div class="guideline-content">
                <h4>List Medications</h4>
                <p>
                  Include any medications, supplements, or treatments you're
                  currently using.
                </p>
              </div>
            </div>

            <div class="guideline-item">
              <div class="guideline-icon">
                <i class="fas fa-history"></i>
              </div>
              <div class="guideline-content">
                <h4>Medical History</h4>
                <p>
                  Share relevant medical history that might be related to your
                  question.
                </p>
              </div>
            </div>
          </div>

          <div class="emergency-notice">
            <div class="emergency-icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="emergency-content">
              <h4>Medical Emergency?</h4>
              <p>
                If you're experiencing a medical emergency, please call
                emergency services immediately. Do not wait for an online
                response.
              </p>
              <button
                class="btn btn-danger btn-small"
                onclick="callEmergency()">
                <i class="fas fa-phone"></i>
                Call Emergency Services
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Question Status Section (Hidden initially) -->
      <div class="question-status hidden" id="questionStatus">
        <div class="status-card">
          <div class="status-header">
            <div class="status-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="status-content">
              <h3>Question Submitted Successfully!</h3>
              <p>
                Your question has been sent to our medical experts for review.
              </p>
            </div>
          </div>

          <div class="status-details">
            <div class="status-item">
              <span class="status-label">Question ID:</span>
              <span class="status-value" id="questionId">#MQ-2024-001</span>
            </div>
            <div class="status-item">
              <span class="status-label">Status:</span>
              <span class="status-value"><span class="badge badge-warning">Pending Review</span></span>
            </div>
            <div class="status-item">
              <span class="status-label">Expected Response:</span>
              <span class="status-value">Within 2-4 hours</span>
            </div>
          </div>

          <div class="status-actions">
            <button class="btn btn-primary" onclick="viewQuestion()">
              <i class="fas fa-eye"></i>
              View Question
            </button>
            <button class="btn btn-outline" onclick="askAnother()">
              <i class="fas fa-plus"></i>
              Ask Another Question
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../app/partials/footer.php'; ?>

  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="<script src=" js/main.js"></script>/js/controllers/questionController.js"></script>
  <script>
    // Global functions for question actions
    function saveDraft() {
      MediQA.showNotification("Draft saved successfully", "success");
    }

    function callEmergency() {
      if (
        confirm("This will redirect you to emergency services. Continue?")
      ) {
        window.open("tel:911", "_self");
      }
    }

    function viewQuestion() {
      MediQA.showNotification("Viewing your question...", "info");
      // In a real app, this would redirect to the question details page
    }

    function askAnother() {
      document.getElementById("questionForm").reset();
      document.getElementById("questionStatus").classList.add("hidden");
      document
        .querySelector(".question-form-container")
        .classList.remove("hidden");
    }
  </script>
</body>

</html>