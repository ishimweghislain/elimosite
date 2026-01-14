<?php
require_once 'includes/config.php';

// Get team members
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
    <title>Our Team - <?php echo get_setting('site_name'); ?></title>
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
    <meta name="twitter:title" content="Our Team">
    <meta name="twitter:description" content="<?php echo get_setting('site_description'); ?>">
    <meta name="twitter:image" content="images/banner-1.jpg">
    <!-- Facebook -->
    <meta property="og:url" content="agents-grid-1.html">
    <meta property="og:title" content="Our Team">
    <meta property="og:description" content="<?php echo get_setting('site_description'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="images/banner-1.jpg">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
  </head>
  <body>
    <?php $sticky_area_class = 'bg-primary'; include 'header.php'; ?>
    <main id="content">
      <section class="position-relative pb-15 pt-10 page-title bg-patten bg-primary">
        <div class="container">
          <h1 class="fs-32 mb-0 text-white font-weight-600 text-center pt-11 mb-5 lh-17 mxw-478" data-animate="fadeInDown">Meet The Agents
            Transforming Real Estate </h1>
        </div>
      </section>
     
      <section class="pt-12 pb-13 agents-section">
        <div class="container">
          <div class="row">
            <?php if (!empty($team_members)): ?>
              <?php foreach ($team_members as $member): ?>
                <div class="col-sm-6 col-md-4 col-lg-4 mb-6">
                  <div class="card shadow-hover-xs-4 agent-3">
                    <div class="card-header text-center pt-6 pb-3 bg-transparent text-center">
                      <a href="#" class="d-inline-block mb-2 w-120px h-120">
                        <img src="<?php echo !empty($member['image']) ? 'images/' . $member['image'] : 'images/team-placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                      </a>
                      <a href="#"
                               class="d-block fs-16 lh-2 text-dark mb-0 font-weight-500 hover-primary"><?php echo htmlspecialchars($member['name']); ?></a>
                      <p class="mb-2"><?php echo htmlspecialchars($member['position']); ?></p>
                      <ul class="list-inline mb-0">
                        <?php
                        $social_links = json_decode($member['social_links'] ?? '{}', true);
                        if (!empty($social_links['twitter'])): ?>
                          <li class="list-inline-item mr-6">
                            <a href="<?php echo htmlspecialchars($social_links['twitter']); ?>"
                                       class="text-muted hover-primary"><i
                                        class="fab fa-twitter"></i></a>
                          </li>
                        <?php endif; ?>
                        <?php if (!empty($social_links['facebook'])): ?>
                          <li class="list-inline-item mr-6">
                            <a href="<?php echo htmlspecialchars($social_links['facebook']); ?>"
                                       class="text-muted hover-primary"><i
                                        class="fab fa-facebook-f"></i></a>
                          </li>
                        <?php endif; ?>
                        <?php if (!empty($social_links['linkedin'])): ?>
                          <li class="list-inline-item">
                            <a href="<?php echo htmlspecialchars($social_links['linkedin']); ?>"
                                       class="text-muted hover-primary"><i
                                        class="fab fa-linkedin-in"></i></a>
                          </li>
                        <?php endif; ?>
                      </ul>
                    </div>
                    <div class="card-body text-center pt-2 px-0">
                      <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="text-body"><?php echo htmlspecialchars($member['email']); ?></a>
                      <a href="tel:<?php echo htmlspecialchars($member['phone']); ?>" class="text-heading font-weight-600 d-block mb-3"><?php echo htmlspecialchars($member['phone']); ?></a>
                      
                    </div>
                    <div class="card-footer px-0 text-center hover-image border-0">
                      <a href="#"
                               class="d-flex align-items-center justify-content-center text-heading">
                        <span class="badge badge-white badge-circle border fs-13 font-weight-bold mr-2 lh-12"><?php echo $member['listed_properties']; ?></span>
                        <span class="font-weight-500 mr-2">Listed Properties</span>
                        <span class="text-primary fs-16 icon"><i class="far fa-long-arrow-right"></i></span>
                      </a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-8">
                <div class="mb-4">
                  <i class="fas fa-users fa-3x text-muted"></i>
                </div>
                <h3 class="text-muted mb-3">No team members added yet</h3>
                <p class="text-muted">Our team information will be available soon.</p>
              </div>
            <?php endif; ?>
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
