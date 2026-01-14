<?php
require_once 'includes/config.php';

// Handle contact form submission
$contact_result = handle_contact_form();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo get_setting('site_description', 'Elimo Real Estate'); ?>">
    <meta name="author" content="">
    <meta name="generator" content="Jekyll">
    <title>Contact <?php echo get_setting('site_name'); ?></title>
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet">
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="vendors/fontawesome-pro-5/css/all.css">
    <link rel="stylesheet" href="vendors/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="vendors/slick/slick.min.css">
    <link rel="stylesheet" href="vendors/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" href="vendors/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="vendors/chartjs/Chart.min.css">
    <link rel="stylesheet" href="vendors/dropzone/css/dropzone.min.css">
    <link rel="stylesheet" href="vendors/animate.css">
    <link rel="stylesheet" href="vendors/timepicker/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="vendors/mapbox-gl/mapbox-gl.min.css">
    <link rel="stylesheet" href="vendors/dataTables/jquery.dataTables.min.css">
    <!-- Themes core CSS -->
    <link rel="stylesheet" href="css/themes.css">
    <!-- Favicons -->
    <link rel="icon" href="images/favicon.png">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@">
    <meta name="twitter:creator" content="@">
    <meta name="twitter:title" content="Contact Us">
    <meta name="twitter:description" content="<?php echo get_setting('site_description'); ?>">
    <meta name="twitter:image" content="images/banner-1.jpg">
    <!-- Facebook -->
    <meta property="og:url" content="contact-us-1.html">
    <meta property="og:title" content="Contact Us">
    <meta property="og:description" content="<?php echo get_setting('site_description'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="images/banner-1.jpg">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
  </head>
  <body>
    <?php include 'header.php'; ?>
    <main id="content">
      <section class="py-14 py-lg-17 page-title bg-overlay-opacity-02"
         style="background-image: url('images/gallery-lg-10-2.jpg');background-size: cover;background-position: center">
        <div class="container">
          <h1 class="fs-22 fs-md-42 lh-15 mb-8 mb-lg-8 font-weight-normal text-center mxw-774 pt-2 text-white position-relative z-index-3" data-animate="fadeInDown">
            For more
            information about our services, get in touch with our property experts</h1>
        </div>
      </section>
      <section>
        <div class="container">
          <div class="card border-0 mt-n13 z-index-3 pb-8 pt-10">
            <div class="card-body p-0">
              <h2 class="text-heading mb-2 fs-22 fs-md-32 text-center lh-16">We're always eager to hear from
                you!</h2>
              <p class="text-center mxw-670 mb-8">
                For all your residential, commercial and industrial property needs.
              </p>
              
              <?php if ($contact_result): ?>
                <div class="alert alert-<?php echo $contact_result['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show mxw-751 mx-auto" role="alert">
                  <?php echo htmlspecialchars($contact_result['message']); ?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
              <?php endif; ?>
              
              <form class="mxw-751 px-md-5" method="POST">
                <input type="hidden" name="contact_form" value="1">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="text" placeholder="First Name"
                                       class="form-control form-control-lg border-0" name="first-name" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="text" placeholder="Last Name" name="last-name"
                                       class="form-control form-control-lg border-0" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <input placeholder="Your Email"
                                       class="form-control form-control-lg border-0"
                                       type="email" name="email" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="text" placeholder="Your Phone" name="phone"
                                       class="form-control form-control-lg border-0">
                    </div>
                  </div>
                </div>
                <div class="form-group mb-6">
                  <textarea class="form-control border-0" placeholder="Message" name="message"
                                  rows="5" required></textarea>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-lg btn-primary px-9">Submit</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
      <section>
        <div id="map" class="mapbox-gl map-point-animate"
         style="height: 400px"
         data-mapbox-access-token="pk.eyJ1IjoiZnVuZGFyaXNyIiwiYSI6ImNrOTRiaXpnajA4OTUzZW55cmo3cjNiODEifQ.4LcJwkTM3sYbVA89soIXeQ"
         data-mapbox-options='{"center":[-1.957912, 30.0845522],"setLngLat":[-1.957912, 30.0845522]}'
    ></div>
      </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Vendors scripts -->
    <script src="vendors/jquery.min.js"></script>
    <script src="vendors/jquery-ui/jquery-ui.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.bundle.js"></script>
    <script src="vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="vendors/slick/slick.min.js"></script>
    <script src="vendors/waypoints/jquery.waypoints.min.js"></script>
    <script src="vendors/counter/countUp.js"></script>
    <script src="vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="vendors/chartjs/Chart.min.js"></script>
    <script src="vendors/dropzone/js/dropzone.min.js"></script>
    <script src="vendors/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="vendors/hc-sticky/hc-sticky.min.js"></script>
    <script src="vendors/jparallax/TweenMax.min.js"></script>
    <script src="vendors/mapbox-gl/mapbox-gl.js"></script>
    <script src="vendors/dataTables/jquery.dataTables.min.js"></script>
    <!-- Theme scripts -->
    <script src="js/theme.js"></script>
    

  </body>
</html>
