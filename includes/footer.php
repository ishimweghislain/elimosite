<footer class="bg-dark pt-8 pb-6 footer text-muted">
  <div class="container container-xxl">
    <div class="row">
      <div class="col-md-6 col-lg-4 mb-6 mb-md-0">
        <a class="d-block mb-2" href="index.php">
          <img src="images/white-logo.jpg" alt="Elimo Logo">
        </a>
        <div class="lh-26 font-weight-400">
          <p class="mb-0"><?php echo get_setting('contact_address', 'KG 622, Street 19 P.O. BOX 4566 <br> Rugando - Kigali <br>Rwanda'); ?></p>
          <a class="text-muted hover-white" href="mailto:<?php echo get_setting('contact_email', 'info@elimo.rw'); ?>"><?php echo get_setting('contact_email', 'info@elimo.rw'); ?></a> | 
          <a class="text-muted hover-white" href="tel:<?php echo get_setting('contact_phone', '+250-789-517-737'); ?>"><?php echo get_setting('contact_phone', '+250-789-517-737'); ?></a>
        </div>
      </div>
      <div class="col-md-6 col-lg-2 mb-6 mb-md-0">
        <h4 class="text-white fs-16 my-4 font-weight-400">Popular Searches</h4>
        <ul class="list-group list-group-flush list-group-no-border">
          <li class="list-group-item bg-transparent p-0">
            <a href="search-results.php?location=Kigali" class="text-muted lh-26 font-weight-400 hover-white">Kigali</a>
          </li>
          <li class="list-group-item bg-transparent p-0">
            <a href="search-results.php?province=Northern" class="text-muted lh-26 font-weight-400 hover-white">Northern Province</a>
          </li>
          <li class="list-group-item bg-transparent p-0">
            <a href="search-results.php?province=Eastern" class="text-muted lh-26 font-weight-400 hover-white">Eastern Province</a>
          </li>
          <li class="list-group-item bg-transparent p-0">
            <a href="search-results.php?province=Western" class="text-muted lh-26 font-weight-400 hover-white">Western Province</a>
          </li>
        </ul>
      </div>
      <div class="col-md-6 col-lg-2 mb-6 mb-md-0">
        <h4 class="text-white fs-16 my-4 font-weight-500">Quick links</h4>
        <ul class="list-group list-group-flush list-group-no-border">
          <li class="list-group-item bg-transparent p-0">
            <a href="about-us.php" class="text-muted lh-26 font-weight-400 hover-white">About Us</a>
          </li>
          <li class="list-group-item bg-transparent p-0">
            <a href="team.php" class="text-muted lh-26 font-weight-400 hover-white">Our Team</a>
          </li>
          <li class="list-group-item bg-transparent p-0">
            <a href="properties.php" class="text-muted lh-26 font-weight-400 hover-white">Properties</a>
          </li>
          <li class="list-group-item bg-transparent p-0">
            <a href="developments.php" class="text-muted lh-26 font-weight-400 hover-white">Developments</a>
          </li>
          <li class="list-group-item bg-transparent p-0">
            <a href="faqs.php" class="text-muted lh-26 font-weight-400 hover-white">FAQs</a>
          </li>
          <li class="list-group-item bg-transparent p-0">
            <a href="contact-us.php" class="text-muted lh-26 hover-white font-weight-400">Contact Us</a>
          </li>
        </ul>
      </div>
      <div class="col-md-6 col-lg-4 mb-6 mb-md-0">
        <h4 class="text-white fs-16 my-4 font-weight-500">Sign Up for Our Newsletter</h4>
        <p class="font-weight-400 text-muted lh-184">Get the latest property trends in your inbox.</p>
        
        <?php
        $newsletter_result = handle_newsletter_subscription();
        if ($newsletter_result):
        ?>
          <div class="alert alert-<?php echo $newsletter_result['success'] ? 'success' : 'danger'; ?> alert-sm">
            <?php echo htmlspecialchars($newsletter_result['message']); ?>
          </div>
        <?php endif; ?>
        
        <form method="POST">
          <input type="hidden" name="newsletter_form" value="1">
          <div class="input-group input-group-lg mb-6">
            <input type="email" name="email" class="form-control bg-white shadow-none border-0 z-index-1" placeholder="Your email" required>
            <div class="input-group-append">
              <button class="btn btn-primary" type="submit">Subscribe</button>
            </div>
          </div>
        </form>
        
        <ul class="list-inline mb-0">
          <li class="list-inline-item mr-0">
            <a href="#" class="text-white opacity-8 fs-18 px-2 opacity-hover-10"><i class="fab fa-twitter"></i></a>
          </li>
          <li class="list-inline-item mr-0">
            <a href="#" class="text-white opacity-8 fs-18 px-2 opacity-hover-10"><i class="fab fa-facebook-f"></i></a>
          </li>
          <li class="list-inline-item mr-0">
            <a href="#" class="text-white opacity-8 fs-18 px-2 opacity-hover-10"><i class="fab fa-skype"></i></a>
          </li>
          <li class="list-inline-item mr-0">
            <a href="#" class="text-white opacity-8 fs-18 px-2 opacity-hover-10"><i class="fab fa-linkedin-in"></i></a>
          </li>
        </ul>
      </div>
    </div>
    <div class="mt-0 mt-md-8 copyright-section row">
      <ul class="list-inline mb-0 col-md-6 mr-auto">
        <li class="list-inline-item mr-6">
          <a href="#" class="text-muted lh-26 font-weight-400 hover-white">Terms of Use</a>
        </li>
        <li class="list-inline-item">
          <a href="#" class="text-muted lh-26 font-weight-400 hover-white">Privacy Policy</a>
        </li>
      </ul>
      <p class="col-md-auto mb-0 text-muted">
        © <?php echo date('Y'); ?> <?php echo get_setting('site_name', 'Elimo Real Estate'); ?>. All Rights Reserved
      </p>
    </div>
  </div>
</footer>
