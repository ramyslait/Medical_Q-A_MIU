<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Feedback & Contact - Medical Q&A</title>
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
        <h1>Feedback & Contact</h1>
        <p>
          We value your feedback and are here to help with any questions or
          concerns
        </p>
      </div>

      <div class="contact-container">
        <div class="contact-form-section">
          <div class="contact-card">
            <div class="contact-header">
              <h2><i class="fas fa-envelope"></i> Send us a Message</h2>
              <p>
                Have a question, suggestion, or need support? We'd love to
                hear from you.
              </p>
            </div>

            <form class="contact-form needs-validation" id="contactForm">
              <div class="form-group">
                <label for="contactType" class="form-label">Type of Inquiry *</label>
                <select
                  id="contactType"
                  name="contactType"
                  class="form-select"
                  required>
                  <option value="">Select inquiry type</option>
                  <option value="general">General Question</option>
                  <option value="technical">Technical Support</option>
                  <option value="feedback">Feedback & Suggestions</option>
                  <option value="complaint">Complaint</option>
                  <option value="partnership">Partnership Inquiry</option>
                  <option value="media">Media Inquiry</option>
                </select>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="firstName" class="form-label">First Name *</label>
                  <input
                    type="text"
                    id="firstName"
                    name="firstName"
                    class="form-input"
                    required />
                </div>
                <div class="form-group">
                  <label for="lastName" class="form-label">Last Name *</label>
                  <input
                    type="text"
                    id="lastName"
                    name="lastName"
                    class="form-input"
                    required />
                </div>
              </div>

              <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  class="form-input"
                  required />
              </div>

              <div class="form-group">
                <label for="subject" class="form-label">Subject *</label>
                <input
                  type="text"
                  id="subject"
                  name="subject"
                  class="form-input"
                  placeholder="Brief description of your inquiry"
                  required />
              </div>

              <div class="form-group">
                <label for="message" class="form-label">Message *</label>
                <textarea
                  id="message"
                  name="message"
                  class="form-textarea"
                  placeholder="Please provide detailed information about your inquiry..."
                  required></textarea>
              </div>

              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" name="newsletter" checked />
                  <span class="checkmark"></span>
                  Subscribe to our newsletter for updates and health tips
                </label>
              </div>

              <button type="submit" class="btn btn-primary w-full">
                <i class="fas fa-paper-plane"></i>
                Send Message
              </button>
            </form>
          </div>
        </div>

        <div class="contact-info-section">
          <div class="info-card">
            <div class="info-header">
              <h3><i class="fas fa-phone"></i> Contact Information</h3>
            </div>

            <div class="contact-methods">
              <div class="contact-method">
                <div class="contact-icon">
                  <i class="fas fa-envelope"></i>
                </div>
                <div class="contact-details">
                  <h4>Email Support</h4>
                  <p>info@mediqa.com</p>
                  <small>We typically respond within 24 hours</small>
                </div>
              </div>

              <div class="contact-method">
                <div class="contact-icon">
                  <i class="fas fa-phone"></i>
                </div>
                <div class="contact-details">
                  <h4>Phone Support</h4>
                  <p>+1 (555) 123-4567</p>
                  <small>Monday - Friday, 9 AM - 6 PM EST</small>
                </div>
              </div>

              <div class="contact-method">
                <div class="contact-icon">
                  <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="contact-details">
                  <h4>Office Address</h4>
                  <p>123 Medical Plaza<br />Health City, HC 12345</p>
                  <small>By appointment only</small>
                </div>
              </div>
            </div>
          </div>

          <div class="faq-card">
            <h3>
              <i class="fas fa-question-circle"></i> Frequently Asked
              Questions
            </h3>
            <div class="faq-list">
              <div class="faq-item">
                <div class="faq-question">
                  <h4>
                    How quickly will I get a response to my medical question?
                  </h4>
                  <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                  <p>
                    Most medical questions receive expert responses within 2-4
                    hours during business hours. Emergency questions are
                    prioritized for faster response.
                  </p>
                </div>
              </div>

              <div class="faq-item">
                <div class="faq-question">
                  <h4>
                    Is the medical advice provided by qualified professionals?
                  </h4>
                  <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                  <p>
                    Yes, all responses are provided by verified healthcare
                    professionals including doctors, nurses, and specialists
                    with appropriate credentials.
                  </p>
                </div>
              </div>

              <div class="faq-item">
                <div class="faq-question">
                  <h4>Can I ask questions anonymously?</h4>
                  <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                  <p>
                    Absolutely. We respect your privacy and allow anonymous
                    question submissions. Your personal information is never
                    shared without consent.
                  </p>
                </div>
              </div>

              <div class="faq-item">
                <div class="faq-question">
                  <h4>What should I do in a medical emergency?</h4>
                  <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                  <p>
                    For medical emergencies, call emergency services (911)
                    immediately. Do not wait for an online response as this
                    platform is not for emergency care.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../app/partials/footer.php'; ?>

  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="js/controllers/feedbackController.js"></script>
</body>

</html>