<?php
require_once 'includes/config.php';

// Get blog posts
$blog_posts = get_blog_posts(9);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo get_setting('site_description', 'Elimo Real Estate'); ?>">
    <meta name="author" content="">
    <meta name="generator" content="Jekyll">
    <title>Blog Grid - <?php echo get_setting('site_name'); ?></title>
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
    <meta name="twitter:title" content="Blog Grid">
    <meta name="twitter:description" content="<?php echo get_setting('site_description'); ?>">
    <meta name="twitter:image" content="images/banner-1.jpg">
    <!-- Facebook -->
    <meta property="og:url" content="blog-grid.html">
    <meta property="og:title" content="Blog Grid">
    <meta property="og:description" content="<?php echo get_setting('site_description'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="images/banner-1.jpg">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <style>
      .blog-modal-content img {
        transition: transform 0.5s ease;
      }
      .blog-modal-content:hover img {
        transform: scale(1.02);
      }
      .blog-main-content {
        color: #666;
        font-size: 1.1rem;
        line-height: 1.8;
      }
      .line-height-2 {
        line-height: 2 !important;
      }
      .t-15 { top: 15px; }
      .r-15 { right: 15px; }
    </style>
  </head>
  <body>
    <?php $sticky_area_class = 'bg-primary'; include 'header.php'; ?>
    <main id="content">
      <section class="py-13 bg-gray-01 mt-10">
        <div class="container">
          <p class="letter-spacing-263 text-uppercase text-danger mb-0 font-weight-500 text-center">our blog</p>
          <h2 class="fs-30 lh-16 mb-10 text-dark font-weight-600 text-center">Interesting articles updated weekly</h2>
          <div class="row">
            <?php if (!empty($blog_posts)): ?>
              <?php foreach ($blog_posts as $post): ?>
                <div class="col-md-4 mb-6">
                  <div class="card border-0 shadow-xxs-3">
                    <div class="position-relative d-flex align-items-end card-img-top">
                      <a href="#" class="hover-shine blog-view-btn" data-id="<?php echo $post['id']; ?>">
                        <img src="<?php echo !empty($post['image']) ? 'images/' . $post['image'] : 'images/blog-details.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($post['title']); ?>" class="card-img">
                      </a>
                      <a href="#"
                               class="badge text-white bg-dark-opacity-04 fs-13 font-weight-500 bg-hover-primary hover-white mx-2 my-4 position-absolute pos-fixed-bottom">
                        <?php echo htmlspecialchars($post['category']); ?>
                      </a>
                    </div>
                    <div class="card-body px-5 pt-3 pb-5 d-flex flex-column" style="min-height: 250px;">
                      <p class="mb-1 fs-13"><?php echo format_date($post['created_at'], 'M d, Y'); ?></p>
                      <h3 class="fs-18 text-heading lh-194 mb-1" style="min-height: 54px;">
                        <a href="#" class="text-heading hover-primary blog-view-btn" data-id="<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                      </h3>
                      <p class="mb-3 text-muted" style="flex-grow: 1;"><?php echo truncate_text($post['excerpt'], 100); ?></p>
                      <a class="text-heading font-weight-500 blog-view-btn" href="#" data-id="<?php echo $post['id']; ?>">Read more <i
                                class="far fa-long-arrow-right text-primary ml-1"></i></a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12 text-center py-8">
                <div class="mb-4">
                  <i class="fas fa-blog fa-3x text-muted"></i>
                </div>
                <h3 class="text-muted mb-3">No blog posts added yet</h3>
                <p class="text-muted">Check back soon for interesting articles about real estate.</p>
              </div>
            <?php endif; ?>
          </div>
          <nav class="pt-4">
            <ul class="pagination rounded-active justify-content-center mb-0">
              <li class="page-item"><a class="page-link" href="#"><i class="far fa-angle-double-left"></i></a>
              </li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item active"><a class="page-link" href="#">2</a></li>
              <li class="page-item d-none d-sm-block"><a class="page-link" href="#">3</a></li>
              <li class="page-item">...</li>
              <li class="page-item"><a class="page-link" href="#">6</a></li>
              <li class="page-item"><a class="page-link" href="#"><i
                        class="far fa-angle-double-right"></i></a>
              </li>
            </ul>
          </nav>
        </div>
      </section>
    </main>

    <!-- Blog Details Modal -->
    <div class="modal fade" id="blogDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 position-relative">
                    <button type="button" class="close position-absolute t-15 r-15 z-index-10 bg-white rounded-circle p-2 shadow-sm" data-dismiss="modal" aria-label="Close" style="width: 40px; height: 40px; line-height: 20px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-5 pt-0" id="blogModalContent">
                    <div class="text-center py-10">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Vendors scripts -->
    <script src="vendors/jquery.min.js"></script>
    <script src="vendors/jquery-ui/jquery-ui/jquery-ui.min.js"></script>
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
    
    <script>
        $(document).ready(function() {
            $('.blog-view-btn').on('click', function(e) {
                e.preventDefault();
                const postId = $(this).data('id');
                
                $('#blogDetailsModal').modal('show');
                $('#blogModalContent').html('<div class="text-center py-10"><div class="spinner-border text-primary" role="status"></div></div>');
                
                $.ajax({
                    url: 'ajax/get-blog-post.php',
                    type: 'GET',
                    data: { id: postId },
                    success: function(response) {
                        $('#blogModalContent').html(response);
                        
                        // Initialize Slider
                        setTimeout(function() {
                            $('.modal-blog-slider').slick({
                                dots: false,
                                infinite: true,
                                speed: 300,
                                slidesToShow: 1,
                                adaptiveHeight: true,
                                prevArrow: $('.slider-prev'),
                                nextArrow: $('.slider-next')
                            });
                        }, 200);
                    },
                    error: function() {
                        $('#blogModalContent').html('<div class="alert alert-danger">Error loading blog details. Please try again.</div>');
                    }
                });
            });
        });
    </script>
    

  </body>
</html>
