<?php
require_once 'includes/config.php';

// Get team members for about page
$team_members = get_team_members();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo get_setting('site_description', 'Elimo Real Estate'); ?>">
    <meta name="author" content="">
    <meta name="generator" content="Jekyll">
    <title>About Us - <?php echo get_setting('site_name'); ?></title>
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
    <meta name="twitter:title" content="About Us">
    <meta name="twitter:description" content="<?php echo get_setting('site_description'); ?>">
    <meta name="twitter:image" content="images/homeid-social-logo.png">
    <!-- Facebook -->
    <meta property="og:url" content="about-us.html">
    <meta property="og:title" content="About Us">
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
      
      <section style="background-image: url('images/bg-about-us.jpg')"
         class="bg-img-cover-center py-10 pt-md-16 pb-md-17 bg-overlay">
        <div class="container position-relative z-index-2 text-center">
          
          <div class="mxw-751">
            <h1 class="text-white fs-30 fs-md-42 lh-15 font-weight-normal mt-4 mb-5" data-animate="fadeInRight">We are passionate and experienced real estate experts in Rwanda</h1>
              <img class="mxw-180 d-block mx-auto mt-4 mb-1" src="images/line-01.png" alt="">
          </div>
        </div>
      </section>
      <section class="bg-gray-01 pb-13">
        <div class="container">
          <div class="card border-0 mt-n13 z-index-3 mb-8">
            <div class="card-body p-6 px-lg-14 py-lg-13">
              <p class="letter-spacing-263 text-uppercase text-danger mb-2 font-weight-500 text-center">welcome to
                elimo real estate</p>
              <h2 class="text-heading mb-4 fs-22 fs-md-32 text-center lh-16 px-6">Your trusted resourceful companion on your real estate journey</h2>
              <p class="text-center px-lg-11 fs-15 lh-17 mb-5"> <?php echo get_setting('site_description', 'Elimo Real Estate Ltd\'s story began back in 2019. It was born out of the identification and eagerness to tackle several industry challenges that limit real estate agents\' collaborations and the availability of qualitative information on the local market. In our opinion, this will enhance real estate service providers\' ability to give the best quality services to customers consistently.'); ?>
              </p>
              <p class="text-center px-lg-11 fs-15 lh-17 mb-5"> It's through finding solutions to these challenges that we are creating a network of opportunities for others and for us. This path has taught us the value of building and maintaining relationships through meeting, networking, and assisting people with their needs, and this over time has become our business ethos.
              </p>
              
            </div>
          </div>

          <div class="row galleries mb-12">
            <div class="col-sm-6 mb-6">
              <div class="item item-size-2-1">
                <a href="images/gallery-lg-10.jpg" class="card p-0 hover-zoom-in"
                       data-gtf-mfp="true" data-gallery-id="02">
                  <div class="card-img img"
                             style="background-image:url('images/kigali-arena.jpg')">
                  </div>
                </a>
              </div>
            </div>
            <div class="col-sm-6 mb-6">
              <div class="item item-size-2-1">
                <a href="images/gallery-lg-11.jpg" class="card p-0 hover-zoom-in"
                       data-gtf-mfp="true" data-gallery-id="02">
                  <div class="card-img img"
                             style="background-image:url('images/kigali-road.jpg')">
                  </div>
                </a>
              </div>
            </div>
          </div>

          <!-- Team Section -->
          <h2 class="text-heading mb-4 fs-22 fs-md-32 text-center lh-16 px-md-13">Meet Our Team</h2>
          <p class="text-center px-md-17 fs-15 lh-17 mb-8">Dedicated professionals ready to assist you.</p>

          <div class="row mb-12 justify-content-center">
            <?php if (!empty($team_members)): ?>
                <?php foreach ($team_members as $member): ?>
                    <div class="col-md-4 mb-6">
                        <div class="card border-0 shadow-hover-3 h-100">
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 300px; overflow: hidden;">
                                <?php if (!empty($member['image'])): ?>
                                    <img src="images/<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div class="text-muted"><i class="fas fa-user fa-5x"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title text-dark mb-1"><?php echo htmlspecialchars($member['name']); ?></h5>
                                <p class="text-gray-light mb-3"><?php echo htmlspecialchars($member['position']); ?></p>
                                <ul class="list-inline text-gray-lighter">
                                    <?php $social = json_decode($member['social_links'] ?? '{}', true); ?>
                                    <li class="list-inline-item mr-3">
                                      <?php if (!empty($social['facebook'])): ?>
                                        <a href="<?php echo fix_url($social['facebook']); ?>" target="_blank" class="text-hover-dark" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                      <?php else: ?>
                                        <span class="text-light opacity-25" title="Facebook (Not provided)"><i class="fab fa-facebook-f"></i></span>
                                      <?php endif; ?>
                                    </li>
                                    <li class="list-inline-item mr-3">
                                      <?php if (!empty($social['twitter'])): ?>
                                        <a href="<?php echo fix_url($social['twitter']); ?>" target="_blank" class="text-hover-dark" title="Twitter"><i class="fab fa-twitter"></i></a>
                                      <?php else: ?>
                                        <span class="text-light opacity-25" title="Twitter (Not provided)"><i class="fab fa-twitter"></i></span>
                                      <?php endif; ?>
                                    </li>
                                    <li class="list-inline-item mr-3">
                                      <?php if (!empty($social['linkedin'])): ?>
                                        <a href="<?php echo fix_url($social['linkedin']); ?>" target="_blank" class="text-hover-dark" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                      <?php else: ?>
                                        <span class="text-light opacity-25" title="LinkedIn (Not provided)"><i class="fab fa-linkedin-in"></i></span>
                                      <?php endif; ?>
                                    </li>
                                    <li class="list-inline-item mr-3"><a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="text-hover-dark" title="Email"><i class="fas fa-envelope"></i></a></li>
                                    <?php if (!empty($member['phone'])): ?>
                                        <li class="list-inline-item"><a href="tel:<?php echo htmlspecialchars($member['phone']); ?>" class="text-hover-dark" title="Phone"><i class="fas fa-phone"></i></a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted">No team members listed yet.</div>
            <?php endif; ?>
          </div>

          <h2 class="text-dark lh-1625 text-center mb-2 fs-22 fs-md-32">Our Services</h2>
          <p class="mxw-751 text-center mb-1 px-8"> Buying or renting a new property can be a stressful process but hiring a professional real estate company makes it easy.</p>
          <img class="mxw-180 d-block mx-auto mt-4 mb-1" src="images/line-01.png" alt="">
          <div class="row mt-8">
            <div class="col-md-4 mb-6 mb-lg-0">
              <div class="card shadow-2 px-7 pb-6 pt-4 h-100 border-0">
                <div class="card-body px-0 pt-6 pb-0 text-center">
                  <h4 class="card-title lh-17 text-dark mb-2 ">Property Management</h4>
                  <p class="card-text px-2 fs-12">
                    Elimo real estate has a team of experienced agents who are committed and dedicated to helping you find your next commercial, residential or industrial property based on your needs.
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-6 mb-lg-0">
              <div class="card shadow-2 px-7 pb-6 pt-4 h-100 border-0">
                <div class="card-body px-0 pt-6 pb-0 text-center">
                  <h4 class="card-title lh-17 text-dark mb-2 ">Property Valuation</h4>
                  <p class="card-text px-2 fs-12">
                    We perform extensive research and offer comparatives in order to help guide clients' decision-making during every step of a project. Rather than focusing on a transaction, we ensure overall investment success for the client.
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-6 mb-lg-0">
              <div class="card shadow-2 px-7 pb-6 pt-4 h-100 border-0">
                <div class="card-body px-0 pt-6 text-center pb-0">
                  <h4 class="card-title lh-17 text-dark mb-2">Property Consulting</h4>
                  <p class="card-text px-2 fs-12">
                   Using our knowledge of the Rwanda Real Estate market and our network of real estate professionals, we offer expert advice and recommendations to anyone looking to invest in real estate.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <section class="pt-12 bg-yellow">
        <div class="container">
          <h2 class="text-heading mb-4 fs-22 fs-md-32 text-center lh-16 px-md-13">
            We are committed and dedicated to helping you find a new property based on your needs.
          </h2>
          <p class="text-center px-md-17 fs-15 lh-17 mb-8">
            We not only help you look for that perfect space, we also arrange visits, help you with negotiations, contracts and legal processes at your comfort.
          </p>
          <div class="text-center pb-11">
            <a href="search-results.php" class="btn btn-lg btn-dark">View all properties</a>
          </div>
          
        </div>
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
