<?php
// Contact Page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us – Yelagiri Properties</title>

    <link rel="stylesheet" href="assets/css/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        :root {
            --green: #2e7d32;
            --green-dark: #1b5e20;
            --green-soft: #e8f5e9;
            --border: #e5e7eb;
            --muted: #6b7280;
            --white: #ffffff;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 0 24px 80px;
        }

        .page-header {
            margin: 48px 0 40px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .page-header p {
            color: var(--muted);
            margin: 0 auto;
            max-width: 600px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }

        .info-card {
            padding: 24px;
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .info-icon {
            background: var(--green-soft);
            color: var(--green);
            padding: 12px;
            border-radius: 14px;
            font-size: 20px;
        }

        .info-card h4 {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .info-card p {
            font-size: 14px;
            color: var(--muted);
        }

        .map-wrapper {
            margin-bottom: 24px;
        }

        .map {
            border-radius: 24px;
            overflow: hidden;
            height: 400px;
            border: 1px solid var(--border);
        }

        .form-wrapper {
            padding: 48px;
        }

        .form-wrapper h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 32px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid #d1e7d8;
            background: #f1f8f4;
            font-size: 14px;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--green);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.15);
        }

        textarea {
            resize: none;
        }

        .btn {
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            border: none;
            background: var(--green);
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 16px;
            margin-top: 12px;
        }

        .btn:hover {
            background: var(--green-dark);
        }

        .success {
            display: none;
            margin-top: 24px;
            padding: 16px;
            background: var(--green-soft);
            border: 1px solid #c8e6c9;
            color: var(--green-dark);
            border-radius: 16px;
            text-align: center;
            font-weight: 600;
        }

        @media (max-width: 768px) {

            .info-row,
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-wrapper {
                padding: 24px;
            }

            .page-header h1 {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>
    <div id="loader">
    <div class="loader-content">
      <div class="loader-text">YELAGIRI PROPERTIES</div>
      <div class="loader-bar-container">
        <div class="loader-bar" id="loaderBar"></div>
      </div>
      <p style="color: rgba(255,255,255,0.4); font-size: 10px; letter-spacing: 4px; margin-top: 15px; font-family: sans-serif;">
        PROPERTIES
      </p>
    </div>
</div>

<!-- HEADER -->
<?php include 'partials/header.php'; ?>

<br><br>

<main class="container">

    <div class="page-header">
        <h1>Contact Yelagiri Properties</h1>
        <p>Reach out for property inquiries, site visits, or investment guidance in Yelagiri.</p>
    </div>

    <!-- ROW 1: INFO CARDS -->
    <div class="info-row">
        <div class="card info-card">
            <div class="info-icon">📍</div>
            <div>
                <h4>Office Address</h4>
                <p>
156/5A3 Thiruvalluvar Nagar Village,
Ponneri post, Jolarpet Town
Thirupattur Dt, Tamil Nadu - 635851</p>
            </div>
        </div>

        <div class="card info-card">
            <div class="info-icon">📞</div>
            <div>
                <h4>Phone & Email</h4>
                <p>+91 8925833003<br>contact@yelagiriproperties.com</p>
            </div>
        </div>
    </div>

    <!-- ROW 2: MAP -->
    <div class="map-wrapper">
        <div class="map card">
            <iframe width="100%" height="100%" style="border:0"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2315.272233712495!2d78.59119766578334!3d12.594886087050012!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3badab5999c65743%3A0xbe330ad019949d5f!2sNsuresafe%20Automation%20Technologies!5e0!3m2!1sen!2sin!4v1768885125231!5m2!1sen!2sin"></iframe>
        </div>
    </div>

    <!-- ROW 3: CONTACT FORM -->
    <div class="card form-wrapper">
        <h3>Send us a Message</h3>

        <form id="contactForm">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input class="form-input" placeholder="e.g. Rahul" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input class="form-input" placeholder="e.g. Sharma" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-input" placeholder="rahul.sharma@example.com" required>
            </div>

            <div class="form-group">
                <label>Subject</label>
                <select class="form-input">
                    <option value="" disabled selected>Select an option</option>
                    <option>Property Inquiry</option>
                    <option>Plot Purchase</option>
                    <option>Site Visit</option>
                    <option>Investment Consultation</option>
                </select>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea rows="5" class="form-input"
                    placeholder="How can we help you today? Please share your requirements..." required></textarea>
            </div>

            <button class="btn">Send Inquiry</button>
        </form>

        <div id="successMsg" class="success">
            ✓ Message sent successfully! Our team will contact you shortly.
        </div>
    </div>

<br>
</main>

<!-- FOOTER -->
<?php include 'partials/footer.php'; ?>

<script>
document.getElementById('contactForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const success = document.getElementById('successMsg');
    this.style.display = 'none';
    success.style.display = 'block';

    setTimeout(() => {
        this.reset();
        this.style.display = 'block';
        success.style.display = 'none';
    }, 4000);
});
</script>

<script src="assets/js/script.js"></script>

</body>
</html>
