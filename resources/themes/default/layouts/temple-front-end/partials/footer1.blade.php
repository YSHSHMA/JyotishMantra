@php
$logo = is_object($temple) ? $temple->logo : $temple;
@endphp
<!-- Mobile Call & WhatsApp Buttons -->
<div class="mobile-action-bar">
  <a href="#poojas"  class="darshan-btn">
    <svg xmlns="http://www.w3.org/2000/svg"
      width="24" height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      class="w-6 h-6">
      <!-- Temple Roof -->
      <path d="M4 9L12 4L20 9" />
      <!-- Temple Body -->
      <path d="M6 9V18H18V9" />
      <!-- Garbh Griha / Idol -->
      <circle cx="12" cy="13" r="2" />
      <!-- Calendar (Booking) -->
      <rect x="2" y="14" width="6" height="6" rx="1" />
      <path d="M2 16h6" />
    </svg>
    Darshan
  </a>
  <a href="#poojas" class="pooja-btn">
    <svg xmlns="http://www.w3.org/2000/svg"
      width="24" height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      class="w-6 h-6">
      <!-- Flame -->
      <path d="M12 2c2 3 3 4.5 3 6a3 3 0 1 1-6 0c0-1.5 1-3 3-6z" />
      <!-- Diya bowl -->
      <path d="M4 14c2 3 14 3 16 0" />
      <!-- Base -->
      <path d="M6 18h12" />
    </svg>
    Puja
  </a>
</div>
<!-- start footer section  -->
<footer class="footer-section">
  <div class="container">
    <div class="row gy-4">
      <!-- Logo & About -->
      <div class="col-lg-4 col-md-6">
        <div class="footer-brand d-flex align-items-center mb-3">
          <img src="{{ theme_asset(path: 'storage/app/public/temple/logo/'.$logo) }}"
            alt="Manglanath Mandir" class="me-2">
        </div>
        <p class="footer-text">
          The sacred temple in Ujjain – the birthplace of Planet Mars and the
          most powerful location for Mangal Dosh Nivaran.
        </p>
        <p class="footer-highlight">जय श्री महाकाल!</p>
      </div>
      <!-- Our Poojas -->
      <div class="col-lg-2 col-md-6">
        <h6 class="footer-title">Our Poojas</h6>
        <ul class="footer-links">
          <li><a href="#">Mangal Dosh Puja</a></li>
          <li><a href="#">Bhat Puja</a></li>
          <li><a href="#">Kaal Sarp Dosh Puja</a></li>
          <li><a href="#">Navgrah Shanti Puja</a></li>
          <li><a href="#">Rudrabhishek Puja</a></li>
        </ul>
      </div>
      <!-- Quick Links -->
      <div class="col-lg-2 col-md-6">
        <h6 class="footer-title">Quick Links</h6>
        <ul class="footer-links">
          <li><a href="#">Home</a></li>
          <li><a href="#">About Temple</a></li>
          <li><a href="#">Gallery</a></li>
          <li><a href="#">Contact Us</a></li>
        </ul>
      </div>
      <!-- Contact -->
      <div class="col-lg-4 col-md-6">
        <h6 class="footer-title">Contact Us</h6>
        <ul class="footer-contact">
          <li><i class="fa-solid fa-phone"></i> +91 9098 88831</li>
          <li><i class="fa-solid fa-envelope"></i> info@manglanathmandir.in</li>
          <li><i class="fa-solid fa-envelope"></i> admin@manglanathmandir.in</li>
          <li>
            <i class="fa-solid fa-location-dot"></i>
            Ankpat Marg, Manglanath Mandir, Agar Rd,<br>
            Ujjain, Madhya Pradesh 456006
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!-- Bottom Bar -->
  <div class="footer-bottom">
    <div class="container d-flex flex-column flex-md-row justify-content-between">
      <p class="mb-0">© 2025 Shree Manglanath Mandir, Ujjain. All Rights Reserved.</p>
      <p class="mb-0">जय श्री महाकाल</p>
    </div>
  </div>
</footer>