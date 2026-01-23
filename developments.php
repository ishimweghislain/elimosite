<?php
require_once 'includes/config.php';

// Get developments (properties with category 'Developments')
$page = (int)($_GET['page'] ?? 1);
$per_page = 12;

$filters = ['category' => 'Developments'];
if (!empty($_GET['province'])) {
    $filters['province'] = $_GET['province'];
}
if (!empty($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}

$developments_data = get_properties($filters, $page, $per_page);
$developments = $developments_data['properties'];
$total = $developments_data['total'];
$total_pages = $developments_data['total_pages'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo get_setting('site_description', 'Elimo Real Estate'); ?>">
    <title>Developments - <?php echo get_setting('site_name'); ?></title>
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="vendors/fontawesome-pro-5/css/all.css">
    <link rel="stylesheet" href="vendors/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="vendors/slick/slick.min.css">
    <link rel="stylesheet" href="vendors/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" href="css/themes.css">
    <link rel="icon" href="images/favicon.png">
    <style>
      @keyframes pulse-custom {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
      }
      .view-details-btn {
        animation: pulse-custom 2s infinite;
        position: absolute;
        bottom: 15px;
        right: 15px;
        z-index: 10;
      }
      .card-hover-primary {
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
      }
      .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
      }
      .card-img-top {
        height: 220px;
        overflow: hidden;
      }
      .card-img-top img {
        height: 100%;
        width: 100%;
        object-fit: cover;
      }
      /* Slick Arrows */
      .slick-prev, .slick-next {
        width: 50px !important;
        height: 50px !important;
        background: #fff !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 10 !important;
        transition: all 0.3s ease !important;
        color: #333 !important;
        top: 35% !important;
      }
      .slick-prev { left: -15px !important; }
      .slick-next { right: -15px !important; }
      .slick-prev:before, .slick-next:before { content: '' !important; }
      .slick-prev:hover, .slick-next:hover {
        background: #28a745 !important;
        color: #fff !important;
      }
      .slick-prev i, .slick-next i {
        font-size: 20px !important;
        line-height: 50px !important;
      }
    </style>
  </head>
  <body>
    <?php $sticky_area_class = 'inner-page'; include 'header.php'; ?>

    <main id="content">
      <section class="pb-6 pt-6 pt-lg-14 page-title shadow bg-primary">
        <div class="container pt-5">
           <h1 class="fs-30 lh-1 mb-0 text-white font-weight-600 text-center">Developments</h1>
           <h2 class="fs-16 text-white mt-3 text-center">Explore modern development projects across Rwanda</h2>
            <div class="mt-8 mx-auto" style="max-width: 800px;">
              <form class="bg-white p-4 rounded-lg shadow-sm d-flex flex-wrap align-items-center justify-content-center" action="developments.php" method="GET">
                <div class="form-group mb-2 mb-md-0 mr-md-3 flex-grow-1" style="min-width: 200px;">
                  <label class="sr-only">Province</label>
                  <select class="form-control selectpicker w-100" title="All Provinces" name="province">
                    <option value="" <?php echo empty($_GET['province']) ? 'selected' : ''; ?>>All Provinces</option>
                    <option <?php echo ($_GET['province'] ?? '') === 'Kigali City' ? 'selected' : ''; ?>>Kigali City</option>
                    <option <?php echo ($_GET['province'] ?? '') === 'Eastern Province' ? 'selected' : ''; ?>>Eastern Province</option>
                    <option <?php echo ($_GET['province'] ?? '') === 'Northern Province' ? 'selected' : ''; ?>>Northern Province</option>
                    <option <?php echo ($_GET['province'] ?? '') === 'Southern Province' ? 'selected' : ''; ?>>Southern Province</option>
                    <option <?php echo ($_GET['province'] ?? '') === 'Western Province' ? 'selected' : ''; ?>>Western Province</option>
                  </select>
                </div>
                <div class="form-group mb-2 mb-md-0 mr-md-3 flex-grow-1" style="min-width: 150px;">
                  <label class="sr-only">Status</label>
                  <select class="form-control selectpicker w-100" title="Status" name="status">
                    <option value="" <?php echo empty($_GET['status']) ? 'selected' : ''; ?>>All Status</option>
                    <option value="for-rent" <?php echo ($_GET['status'] ?? '') === 'for-rent' ? 'selected' : ''; ?>>To Let</option>
                    <option value="for-sale" <?php echo ($_GET['status'] ?? '') === 'for-sale' ? 'selected' : ''; ?>>For Sale</option>
                  </select>
                </div>
                <button type="submit" class="btn btn-primary px-6 mb-2 mb-md-0">Search</button>
              </form>
            </div>
        </div>
      </section>

      <section class="pt-8 pb-11">
        <div class="container">
          <div class="row align-items-sm-center mb-6">
            <div class="col-md-6">
              <h2 class="fs-15 text-dark mb-0">We found <span class="text-primary"><?php echo $total; ?></span> developments available</h2>
            </div>
          </div>
          
          <div class="slick-slider mx-n3" data-slick-options='{"slidesToShow": 3, "slidesToScroll": 1, "autoplay": false, "infinite": true, "arrows": true, "dots": false, "responsive": [{"breakpoint": 1200, "settings": {"slidesToShow": 2}}, {"breakpoint": 768, "settings": {"slidesToShow": 1}}]}'>
            <?php if (!empty($developments)): ?>
              <?php foreach ($developments as $property): ?>
                <div class="px-3">
                  <div class="card border-0 shadow-hover-1 card-hover-primary mb-6">
                    <div class="card-img-top position-relative">
                      <img src="<?php echo !empty($property['image_main']) ? 'images/' . $property['image_main'] : 'images/property-placeholder.jpg'; ?>" 
                           alt="<?php echo htmlspecialchars($property['title']); ?>">
                      <div class="card-img-overlay p-2">
                        <span class="badge badge-yellow"><?php echo htmlspecialchars($property['status']); ?></span>
                      </div>
                      <button class="btn btn-primary btn-sm rounded-lg view-details-btn" data-id="<?php echo $property['id']; ?>" data-category="<?php echo htmlspecialchars($property['category']); ?>">
                          <i class="far fa-eye mr-1"></i> View Details
                      </button>
                    </div>
                    <div class="card-body px-5 pt-3 pb-5">
                      <h3 class="fs-18 text-heading lh-194 mb-1" style="min-height: 54px;">
                        <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="text-heading hover-primary">
                          <?php echo htmlspecialchars($property['title']); ?>
                        </a>
                      </h3>
                      <p class="mb-2 font-weight-500 text-gray-light fs-14"><?php echo htmlspecialchars($property['location']); ?></p>
                      <p class="mb-3 text-muted fs-14" style="flex-grow: 1; min-height: 52px;"><?php echo truncate_text($property['description'], 120); ?></p>
                      <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                        <div class="d-flex flex-wrap">
                          <?php if ($property['bedrooms']): ?>
                            <span class="badge badge-light mr-1"><i class="fas fa-bed mr-1 text-primary"></i><?php echo $property['bedrooms']; ?></span>
                          <?php endif; ?>
                          <?php if ($property['size_sqm']): ?>
                            <span class="badge badge-light"><i class="fas fa-ruler-combined mr-1 text-primary"></i><?php echo (int)$property['size_sqm']; ?>m²</span>
                          <?php endif; ?>
                        </div>
                        <?php if ($property['price']): ?>
                          <span class="text-primary font-weight-bold"><?php echo format_price($property['price']); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12 text-center py-10 w-100">
                <i class="fal fa-building fs-large-5 text-muted mb-4 d-block"></i>
                <h3>No developments found matching your criteria.</h3>
                <a href="developments.php" class="btn btn-lg btn-primary mt-4">View All Developments</a>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($total_pages > 1): ?>
            <nav class="pt-6">
              <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                  <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_GET['province']) ? '&province='.$_GET['province'] : ''; ?><?php echo !empty($_GET['status']) ? '&status='.$_GET['status'] : ''; ?>"><?php echo $i; ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
          <?php endif; ?>
        </div>
      </section>
    </main>

    <!-- Property Details Modal -->
    <div class="modal fade" id="propertyDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 shadow-none">
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
                                <h4 class="mb-4">Inquire about this project</h4>
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

    <?php include 'includes/footer.php'; ?>

    <script src="vendors/jquery.min.js"></script>
    <script src="vendors/jquery-ui/jquery-ui.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.bundle.js"></script>
    <script src="vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="vendors/slick/slick.min.js"></script>
    <script src="vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="js/theme.js"></script>

    <script>
        $(document).ready(function() {
            // View Details Click
            $(document).on('click', '.view-details-btn', function(e) {
                e.preventDefault();
                var propertyId = $(this).data('id');
                
                $('#modalPropertyId').val(propertyId);
                $('#propertyDetailsModal').modal('show');
                $('#modalPropertyContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
                $('#inquiryResponse').html('');
                if($('#modalInquiryForm').length) $('#modalInquiryForm')[0].reset();
                
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
            $(document).on('submit', '#modalInquiryForm', function(e) {
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
                        try {
                            var res = JSON.parse(response);
                            if (res.success) {
                                $('#inquiryResponse').html('<div class="alert alert-success mt-3">Message sent successfully!</div>');
                                $('#modalInquiryForm')[0].reset();
                            } else {
                                $('#inquiryResponse').html('<div class="alert alert-danger mt-3">' + res.message + '</div>');
                            }
                        } catch(e) {
                            $('#inquiryResponse').html('<div class="alert alert-danger mt-3">Error processing response.</div>');
                        }
                    },
                    error: function() {
                        $('#inquiryResponse').html('<div class="alert alert-danger mt-3">An error occurred. Please try again.</div>');
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
