<header class="<?php echo $header_class ?? 'main-header position-absolute fixed-top m-0 navbar-dark header-sticky header-sticky-smart header-mobile-xl'; ?>">
  <div class="sticky-area <?php echo $sticky_area_class ?? ''; ?>">
    <div class="container container-xxl">
      <div class="d-flex align-items-center">
        <nav class="navbar navbar-expand-xl bg-transparent px-0 w-100 w-xl-auto">
          <a class="navbar-brand mr-7" href="index.php">
            <img src="images/white-logo.jpg" alt="Elimo Logo" class="normal-logo">
            <img src="images/white-logo.jpg" alt="Elimo Logo" class="sticky-logo">
          </a>
          <button class="navbar-toggler border-0 px-0" type="button" data-toggle="collapse" data-target="#primaryMenu02" aria-controls="primaryMenu02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="text-white fs-24"><i class="fal fa-bars"></i></span>
          </button>
          <div class="collapse navbar-collapse mt-3 mt-xl-0" id="primaryMenu02">
            <?php echo render_nav_menu(); ?>
            <div class="d-block d-xl-none">
              <ul class="navbar-nav flex-row ml-auto align-items-center justify-content-lg-end flex-wrap py-2">
                <li class="nav-item">
                  <a class="nav-link pr-2 pl-0" href="#">English</a>
                </li>
                <li class="divider text-white-50 mx-2">|</li>
                <li class="nav-item">
                  <a class="nav-link pl-3 pr-2" data-toggle="modal" href="#login-register-modal">Sign In</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <div class="ml-auto d-none d-xl-block">
          <ul class="navbar-nav flex-row ml-auto align-items-center justify-content-lg-end flex-wrap py-2">
            <li class="nav-item">
              <a class="nav-link pr-2 pl-0 text-white" href="#">English</a>
            </li>
            <li class="divider text-white-50 mx-2">|</li>
            <li class="nav-item">
              <a class="nav-link pl-3 pr-2 text-white" data-toggle="modal" href="#login-register-modal">Sign In</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</header>
<?php include 'includes/login-modal-simple.php'; ?>
