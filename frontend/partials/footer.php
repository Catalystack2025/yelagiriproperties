<?php
// Footer File
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">

      <!-- Brand / About -->
      <div class="footer-col footer-brand">
        <h4 class="footer-logo">Yelagiri <br> Properties</h4>
        <p>
          Premium real estate partner in the Eastern Ghats. We specialize in
          verified, clear-title plots and investments with end-to-end support.
        </p>

        <ul class="footer-badges">
          <li>✅ Clear Title Verified</li>
          <li>✅ Site Visit Support</li>
          <li>✅ Documentation Guidance</li>
        </ul>
      </div>

      <!-- Contact -->
      <div class="footer-col">
        <h4>Contact</h4>
        <ul class="footer-links">
          <li><a href="tel:+919999999999">📞 +91 99999 99999</a></li>
          <li><a href="mailto:info@yelagiriproperties.com">✉️ info@yelagiriproperties.com</a></li>
          <li><a href="https://maps.google.com" target="_blank" rel="noopener">📍 Yelagiri / Tirupattur, Tamil Nadu</a></li>
          <li><a href="https://wa.me/919999999999" target="_blank" rel="noopener">💬 WhatsApp Us</a></li>
        </ul>
      </div>

      <!-- Quick Links -->
      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul class="footer-links">
          <li><a href="about.php">About</a></li>
          <li><a href="listings.php">Listings</a></li>
          <li><a href="investment-guide.php">Investment Guide</a></li>
          <li><a href="privacy-policy.php">Privacy Policy</a></li>
          <li><a href="terms.php">Terms of Service</a></li>
        </ul>
      </div>

      <!-- Social / CTA -->
      <div class="footer-col">
        <h4>Follow Us</h4>
        <p>New listings, site updates & investment tips.</p>

        <div class="footer-social">
          <a href="#" aria-label="Instagram">IG</a>
          <a href="#" aria-label="Facebook">FB</a>
          <a href="#" aria-label="YouTube">YT</a>
          <a href="#" aria-label="LinkedIn">IN</a>
        </div>

        <div class="footer-cta">
          <a class="btn-footer" href="contact.php">Book a Site Visit</a>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      <p>© <?php echo date("Y"); ?> Yelagiri Properties. All Rights Reserved.</p>
      <p class="footer-note">Disclaimer: Property availability & pricing may change without prior notice.</p>
    </div>
  </div>
</footer>

<style>
/* Footer CSS */
.site-footer{
  background:#0b0f14;
  color:#c9d1d9;
  padding:60px 0 30px;
  border-top:1px solid rgba(255,255,255,.08);
}
.site-footer .footer-grid{
  display:grid;
  grid-template-columns:1.4fr 1fr 1fr 1fr;
  gap:28px;
}
.site-footer h4{
  color:#fff;
  font-size:18px;
  margin:0 0 14px;
}
.site-footer p{
  margin:0 0 14px;
  line-height:1.7;
  color:rgba(255,255,255,.75);
}
.footer-links, .footer-badges{
  list-style:none;
  padding:0;
  margin:0;
  display:grid;
  gap:10px;
}
.footer-links a{
  color:rgba(255,255,255,.75);
  text-decoration:none;
  transition:.2s;
}
.footer-links a:hover{
  color:#fff;
  transform:translateX(2px);
}
.footer-badges li{
  color:rgba(255,255,255,.8);
  font-size:14px;
}
.footer-social{
  display:flex;
  gap:10px;
  margin:12px 0 16px;
}
.footer-social a{
  width:38px;
  height:38px;
  display:grid;
  place-items:center;
  border:1px solid rgba(255,255,255,.12);
  border-radius:10px;
  color:#fff;
  text-decoration:none;
  transition:.2s;
}
.footer-social a:hover{
  border-color:rgba(255,255,255,.28);
  transform:translateY(-2px);
}
.btn-footer{
  display:inline-block;
  padding:12px 16px;
  border-radius:12px;
  background:#16a34a;
  color:#fff;
  text-decoration:none;
  font-weight:600;
  transition:.2s;
}
.btn-footer:hover{
  transform:translateY(-2px);
  opacity:.95;
}
.footer-bottom{
  margin-top:34px;
  padding-top:18px;
  border-top:1px solid rgba(255,255,255,.08);
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  justify-content:space-between;
  align-items:center;
}
.footer-note{
  font-size:13px;
  color:rgba(255,255,255,.55);
  margin:0;
}
@media (max-width: 992px){
  .site-footer .footer-grid{
    grid-template-columns:1fr 1fr;
  }
}
@media (max-width: 560px){
  .site-footer .footer-grid{
    grid-template-columns:1fr;
  }
  .footer-bottom{
    flex-direction:column;
    align-items:flex-start;
  }
}
.footer-logo{
  font-family:'Ethnocentric', sans-serif;
  letter-spacing:1.5px;
  font-size:20px;
  color:#ffffff;
  text-transform:uppercase;
}
</style>
