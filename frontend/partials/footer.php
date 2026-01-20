<?php
// Footer File
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">

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

      <div class="footer-col">
        <h4>Contact</h4>
        <ul class="footer-links">
          <li><a href="tel:+918925833003">📞 +91 8925833003</a></li>
          <li><a href="mailto:contact@yelagiriproperties.com">✉️ contact@yelagiriproperties.com</a></li>
          <li><a href="https://maps.google.com" target="_blank" rel="noopener">📍 Yelagiri / Tirupattur, Tamil Nadu</a></li>
          <li><a href="https://wa.me/918925833003" target="_blank" rel="noopener">💬 WhatsApp Us</a></li>
        </ul>
      </div>

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

<a href="https://wa.me/918925833003?text=Hi%20I%20am%20interested%20in%20Yelagiri%20Properties"
   class="whatsapp-float hidden-by-loader"
   target="_blank"
   rel="noopener"
   aria-label="Chat on WhatsApp">
  
  <svg viewBox="0 0 32 32" class="whatsapp-icon" xmlns="http://www.w3.org/2000/svg">
    <path d="M16 0C7.163 0 0 7.163 0 16c0 2.825.741 5.476 2.033 7.781L0 32l8.414-2.208A15.926 15.926 0 0 0 16 32c8.837 0 16-7.163 16-16S24.837 0 16 0z" fill="#25d366"/>
    <path d="M23.181 19.31c-.389-.195-2.305-1.138-2.662-1.267-.357-.129-.617-.195-.877.195-.26.39-1.006 1.266-1.233 1.526-.227.26-.454.292-.844.097a10.635 10.635 0 0 1-3.13-1.928 11.731 11.731 0 0 1-2.166-2.697c-.227-.39-.024-.601.171-.795.176-.174.39-.454.584-.681.195-.227.26-.39.389-.649s.065-.487-.033-.681c-.097-.195-.877-2.112-1.201-2.891-.315-.759-.636-.656-.877-.669-.227-.013-.487-.013-.747-.013s-.682.097-1.038.487c-.357.39-1.363 1.331-1.363 3.245s1.402 3.765 1.597 4.024c.195.26 2.758 4.211 6.681 5.911.933.404 1.662.645 2.23.825.937.297 1.789.256 2.463.155.753-.113 2.305-.941 2.63-1.85.324-.908.324-1.687.227-1.85-.098-.162-.357-.26-.747-.455z" fill="#fff"/>
  </svg>
</a>

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

/* Floating WhatsApp Style Updates */
.whatsapp-float{
  position:fixed;
  right:25px;
  bottom:25px;
  width:60px;
  height:60px;
  z-index:9999;
  transition: transform .3s ease, opacity .5s ease;
  filter: drop-shadow(0 8px 15px rgba(0,0,0,0.3));
}

.whatsapp-icon {
  width: 100%;
  height: 100%;
}

/* Subtle Pulse Animation */
.whatsapp-float::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  background: #25d366;
  border-radius: 50%;
  z-index: -1;
  animation: wa-pulse 2s infinite;
}

@keyframes wa-pulse {
  0% { transform: scale(1); opacity: 0.6; }
  100% { transform: scale(1.5); opacity: 0; }
}

.whatsapp-float:hover{
  transform: scale(1.1);
}

.hidden-by-loader{
  opacity:0;
  pointer-events:none;
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
  .whatsapp-float {
    right: 20px;
    bottom: 20px;
    width: 52px;
    height: 52px;
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

<script>
/* Show WhatsApp after screen loader finishes */
window.addEventListener('load', function () {
  setTimeout(function() {
    const wa = document.querySelector('.whatsapp-float');
    if (wa) wa.classList.remove('hidden-by-loader');
  }, 3000); // Small delay for smoother appearance
});
</script>