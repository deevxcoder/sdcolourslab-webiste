
  <footer>
    <div class="footer-inner container">
      <div>
        <img src="/images/logo.png" alt="SD Colours Logo" class="footer-logo" />
        <p class="tagline">Creativity Photobook Company in India. Your Fast &amp; Professional Printing Partner delivering premium wedding albums across the nation.</p>
      </div>
      <div class="footer-col">
        <h3>Solutions</h3>
        <ul>
          <li><a href="/products.php">Wedding Albums</a></li>
          <li><a href="/products.php#combos">Combo Photo Pads</a></li>
          <li><a href="/products.php#led-frames">LED Frames</a></li>
          <li><a href="/products.php#wall-decor">Wall Canvas</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3>Company</h3>
        <ul>
          <li><a href="/about.php">About Us</a></li>
          <li><a href="/gallery.php">Gallery</a></li>
          <li><a href="/pricing.php">Pricing</a></li>
          <li><a href="/contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h3>Contact Us</h3>
        <div class="footer-contact-item"><span class="icon">📍</span><span>Madhusudan Marg, Naredi Tower Complex,<br>Rourkela – 769001, Odisha</span></div>
        <div class="footer-contact-item"><span class="icon">📞</span><span>+91 88958 38987<br>+91 82607 54410</span></div>
        <div class="footer-contact-item"><span class="icon">✉️</span><a href="mailto:sdcoloursphotobooklab@gmail.com" style="color:#d1d5db;text-decoration:none;">sdcoloursphotobooklab@gmail.com</a></div>
      </div>
    </div>
    <div class="container"><hr class="footer-divider" /><p class="footer-bottom">&copy; <?= date('Y') ?> SD Colours Photobook Lab. All rights reserved.</p></div>
  </footer>

  <a href="https://wa.me/918895838987" target="_blank" rel="noopener noreferrer" class="floating-wa" title="Chat on WhatsApp">
    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M16.004 0C7.165 0 0 7.164 0 16.004c0 2.823.74 5.467 2.032 7.766L0 32l8.455-2.014A15.945 15.945 0 0016.004 32C24.836 32 32 24.836 32 16.004 32 7.164 24.836 0 16.004 0zm0 29.254a13.19 13.19 0 01-6.723-1.843l-.483-.287-4.995 1.191 1.235-4.874-.315-.5A13.187 13.187 0 012.75 16.004c0-7.307 5.946-13.254 13.254-13.254 7.31 0 13.25 5.947 13.25 13.254 0 7.31-5.94 13.25-13.25 13.25zm7.268-9.927c-.397-.199-2.352-1.161-2.717-1.293-.364-.133-.63-.199-.895.199-.265.396-1.027 1.293-1.26 1.56-.23.264-.462.298-.86.1-.397-.2-1.677-.618-3.194-1.972-1.18-1.052-1.977-2.352-2.208-2.75-.23-.396-.025-.611.174-.808.177-.177.396-.462.595-.694.199-.23.265-.396.397-.66.132-.265.066-.497-.033-.695-.1-.199-.895-2.156-1.227-2.952-.323-.776-.65-.671-.895-.683l-.762-.013c-.265 0-.695.1-1.059.497-.364.397-1.392 1.36-1.392 3.315s1.425 3.845 1.623 4.11c.199.265 2.804 4.281 6.793 6.005.95.41 1.692.655 2.27.838.953.304 1.82.261 2.506.158.764-.113 2.353-.963 2.685-1.893.332-.93.332-1.727.232-1.893-.099-.165-.364-.264-.76-.463z"/></svg>
  </a>

  <script>
    const header = document.getElementById('site-header');
    if (header) window.addEventListener('scroll', () => { header.classList.toggle('scrolled', window.scrollY > 20); });
    const hamburger = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (hamburger) hamburger.addEventListener('click', () => mobileMenu.classList.add('open'));
    const mobileClose = document.getElementById('mobile-close');
    if (mobileClose) mobileClose.addEventListener('click', () => mobileMenu.classList.remove('open'));
    const mobileOverlay = document.getElementById('mobile-overlay');
    if (mobileOverlay) mobileOverlay.addEventListener('click', () => mobileMenu.classList.remove('open'));
  </script>
</body>
</html>
