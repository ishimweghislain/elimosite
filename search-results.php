<?php
require_once 'includes/config.php';

// Get search filters from URL
$filters = [];
$page = (int)($_GET['page'] ?? 1);
$per_page = 6;

// Parse URL parameters
if (!empty($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}
if (!empty($_GET['type'])) {
    $filters['property_type'] = $_GET['type'];
}
if (!empty($_GET['location'])) {
    $filters['location'] = $_GET['location'];
}
if (!empty($_GET['category'])) {
    $filters['category'] = $_GET['category'];
} else {
    // If no category specified, exclude Developments to separate them
    $filters['exclude_category'] = 'Developments';
}
if (!empty($_GET['max_price'])) {
    $filters['max_price'] = (float)$_GET['max_price'];
}
if (!empty($_GET['bedroom'])) {
    $filters['bedrooms'] = (int)$_GET['bedroom'];
}
if (!empty($_GET['bathroom'])) {
    $filters['bathrooms'] = (int)$_GET['bathroom'];
}

// Get properties
$properties_data = get_properties($filters, $page, $per_page);
$properties = $properties_data['properties'];
$total = $properties_data['total'];
$total_pages = $properties_data['total_pages'];
$current_page = $properties_data['page'];

// Get search query for display
$search_query = '';
if (!empty($_GET['search'])) {
    $search_query = clean_input($_GET['search']);
    $search_data = search_properties($search_query, $page, $per_page);
    $properties = $search_data['properties'];
    $total = $search_data['total'];
    $total_pages = $search_data['total_pages'];
    $current_page = $search_data['page'];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo get_setting('site_description', 'Elimo Real Estate'); ?>">
    <meta name="author" content="">
    <meta name="generator" content="Jekyll">
    <title>Search Results - <?php echo get_setting('site_name'); ?></title>
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
  </head>
  <body>
    <?php $sticky_area_class = 'inner-page'; include 'header.php'; ?>
    <main id="content">
      <section class="pb-6 pt-6 pt-lg-14 page-title shadow bg-primary">
        <div class="container pt-5">
          <h1 class="fs-30 lh-1 mb-0 text-white font-weight-600">
            <?php echo $search_query ? 'Search Results for "' . htmlspecialchars($search_query) . '"' : 'Properties Gallery'; ?>
          </h1>
          <h2 class="fs-16 text-white mt-3">We found <span class="text-yellow"><?php echo $total; ?></span> properties
            available for you
          </h2>
        </div>
      </section>

      <section class="pt-6 pb-7">
        <div class="container">
          <div class="row align-items-sm-center">
            <div class="col-md-6">
              <h2 class="fs-15 text-dark mb-0">We found <span class="text-primary"><?php echo $total; ?></span> properties
                available for you
              </h2>
            </div>
          </div>
        </div>
      </section>

      <section class="pb-9 pb-md-11">
        <div class="container">
          <?php if (!empty($properties)): ?>
            <?php foreach ($properties as $property): ?>
              <div class="media p-4 border rounded-lg shadow-hover-1 pr-lg-8 mb-6 flex-column flex-lg-row no-gutters" data-animate="fadeInUp">
                <div class="col-lg-4 mr-lg-5 card border-0 hover-change-image bg-hover-overlay">
                  <img src="<?php echo !empty($property['image_main']) ? 'images/' . $property['image_main'] : 'images/property-placeholder.jpg'; ?>" 
                       class="card-img" alt="<?php echo htmlspecialchars($property['title']); ?>">
                  <div class="card-img-overlay p-2 d-flex flex-column">
                    <div>
                      <span class="badge badge-yellow"><?php echo htmlspecialchars($property['status']); ?></span>
                    </div>
                    <div class="mt-auto d-flex hover-image">
                      <ul class="list-inline mb-0 d-flex align-items-end mr-auto">
                        <li class="list-inline-item mr-2" data-toggle="tooltip" title="Images">
                          <a href="#" class="text-white hover-primary">
                            <i class="far fa-images"></i>
                          </a>
                        </li>
                        <li class="list-inline-item">
                                <button class="btn btn-primary btn-sm rounded-lg view-details-btn" data-id="<?php echo $property['id']; ?>" data-toggle="tooltip" title="View Details">
                                    <i class="far fa-eye mr-1"></i> View Details
                                </button>
                        </li>
                      </ul>
                      <ul class="list-inline mb-0 d-flex align-items-end mr-n3">
                        <li class="list-inline-item mr-3 h-32" data-toggle="tooltip" title="Wishlist">
                          <a href="#" class="text-white fs-20 hover-primary">
                            <i class="far fa-heart"></i>
                          </a>
                        </li>
                        <li class="list-inline-item mr-3 h-32" data-toggle="tooltip" title="Compare">
                          <a href="#" class="text-white fs-20 hover-primary">
                            <i class="fas fa-exchange-alt"></i>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="media-body mt-5 mt-lg-0">
                  <div class="d-lg-flex justify-content-lg-between">
                    <h2 class="my-0">
                      <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="fs-18 lh-2 text-dark hover-primary d-block">
                        <?php echo htmlspecialchars($property['title']); ?>
                      </a>
                    </h2>
                    <?php if ($property['price']): ?>
                      <p class="listing-price fs-18 pt-2 font-weight-bold text-heading lh-1 mb-0 pr-lg-3 mb-lg-2 mt-3 mt-lg-0">
                        <?php echo format_price($property['price']); ?>
                      </p>
                    <?php endif; ?>
                  </div>
                  <p class="mb-2 font-weight-500 text-gray-light"><?php echo htmlspecialchars($property['location']); ?></p>
                  <p class="mb-6 mxw-571 ml-0"><?php echo truncate_text($property['description'], 150); ?></p>
                  <div class="d-lg-flex justify-content-lg-between">
                    <ul class="list-inline d-flex mb-0 flex-wrap">
                      <?php if ($property['bedrooms']): ?>
                        <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="<?php echo $property['bedrooms']; ?> Bedroom">
                          <svg class="icon icon-bedroom fs-18 text-primary mr-1">
                            <use xlink:href="#icon-bedroom"></use>
                          </svg>
                          <?php echo $property['bedrooms']; ?> Beds
                        </li>
                      <?php endif; ?>
                      <?php if ($property['bathrooms']): ?>
                        <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="<?php echo $property['bathrooms']; ?> Bathrooms">
                          <svg class="icon icon-shower fs-18 text-primary mr-1">
                            <use xlink:href="#icon-shower"></use>
                          </svg>
                          <?php echo $property['bathrooms']; ?> Baths
                        </li>
                      <?php endif; ?>
                      <?php if ($property['garage']): ?>
                        <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="<?php echo $property['garage']; ?> Parking Space">
                          <svg class="icon icon-Garage fs-18 text-primary mr-1">
                            <use xlink:href="#icon-Garage"></use>
                          </svg>
                          <?php echo $property['garage']; ?> Pkg
                        </li>
                      <?php endif; ?>
                      <?php if (!empty($property['size_sqm']) && $property['size_sqm'] > 0): ?>
                        <li class="list-inline-item text-gray font-weight-500 fs-13 d-flex align-items-center mr-5" data-toggle="tooltip" title="Build Size">
                          <svg class="icon icon-square fs-18 text-primary mr-1">
                            <use xlink:href="#icon-square"></use>
                          </svg>
                          <?php echo (int)$property['size_sqm']; ?>m²
                        </li>
                      <?php endif; ?>
                    </ul>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
              <nav class="pt-4">
                <ul class="pagination rounded-active justify-content-center mb-0">
                  <?php if ($current_page > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>">
                        <i class="far fa-angle-double-left"></i>
                      </a>
                    </li>
                  <?php endif; ?>

                  <?php 
                  $start = max(1, $current_page - 2);
                  $end = min($total_pages, $current_page + 2);
                  for ($i = $start; $i <= $end; $i++): 
                  ?>
                    <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                      <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>">
                        <?php echo $i; ?>
                      </a>
                    </li>
                  <?php endfor; ?>

                  <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                      <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>">
                        <i class="far fa-angle-double-right"></i>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>
            <?php endif; ?>

          <?php else: ?>
            <div class="text-center py-8">
              <div class="mb-4">
                <i class="fas fa-search fa-3x text-muted"></i>
              </div>
              <h3 class="text-muted mb-3">No properties found</h3>
              <p class="text-muted">Try adjusting your search criteria or browse our featured properties.</p>
              <a href="index.php" class="btn btn-primary">Browse All Properties</a>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/login-modal.php'; ?>

    <!-- Property Details Modal -->
    <div class="modal fade" id="propertyDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row">
                        <!-- Left Side: Details -->
                        <div class="col-lg-7" id="modalPropertyContent">
                             <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
                        </div>
                        
                        <!-- Right Side: Inquiry Form -->
                        <div class="col-lg-5 border-left-lg">
                            <div class="pl-lg-4">
                                <h4 class="mb-4">Inquire about this property</h4>
                                <form id="modalInquiryForm">
                                    <input type="hidden" name="property_id" id="modalPropertyId">
                                    <div class="form-group mb-4">
                                        <input type="text" class="form-control form-control-lg border-0 bg-gray-01 shadow-none" name="full_name" placeholder="Full Name" required>
                                    </div>
                                    <div class="form-group mb-4">
                                        <input type="email" class="form-control form-control-lg border-0 bg-gray-01 shadow-none" name="email" placeholder="Email Address" required>
                                    </div>
                                    <div class="form-group mb-4">
                                        <input type="tel" class="form-control form-control-lg border-0 bg-gray-01 shadow-none" name="phone" placeholder="Phone Number">
                                    </div>
                                    <div class="form-group mb-4">
                                        <textarea class="form-control border-0 bg-gray-01 shadow-none" name="message" rows="4" placeholder="Message" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg btn-block shadow-none">Send Inquiry</button>
                                </form>
                                <div id="inquiryResponse" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script>
    $(document).ready(function() {
        // View Details Click
        $('.view-details-btn').click(function(e) {
            e.preventDefault();
            var propertyId = $(this).data('id');
            $('#modalPropertyId').val(propertyId);
            $('#propertyDetailsModal').modal('show');
            $('#modalPropertyContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
            
            // Fetch Details
            $.ajax({
                url: 'ajax/get-property.php',
                type: 'GET',
                data: { id: propertyId },
                success: function(response) {
                    $('#modalPropertyContent').html(response);
                    
                    // Initialize Slider
                    setTimeout(function() {
                        $('.modal-property-slider').slick({
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
                    $('#modalPropertyContent').html('<div class="text-danger text-center py-5">Failed to load property details.</div>');
                }
            });
        });

        // Submit Inquiry
        $('#modalInquiryForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var btn = $(this).find('button[type="submit"]');
            var originalText = btn.text();
            
            btn.prop('disabled', true).text('Sending...');
            
            $.ajax({
                url: 'ajax/submit-inquiry.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.success) {
                        $('#inquiryResponse').html('<div class="alert alert-success">Message sent successfully!</div>');
                        $('#modalInquiryForm')[0].reset();
                    } else {
                        $('#inquiryResponse').html('<div class="alert alert-danger">' + res.message + '</div>');
                    }
                },
                error: function() {
                    $('#inquiryResponse').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                },
                complete: function() {
                    btn.prop('disabled', false).text(originalText);
                }
            });
        });
    });
    </script>
  </body>
</html>
