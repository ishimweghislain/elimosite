<?php
require_once 'includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

global $pdo;
$stmt = $pdo->prepare("SELECT * FROM developments WHERE id = ?");
$stmt->execute([$id]);
$dev = $stmt->fetch();

if (!$dev) {
    header('Location: developments.php');
    exit;
}


// Fetch units/listings
$units_stmt = $pdo->prepare("SELECT * FROM properties WHERE development_id = ? AND status != 'draft' ORDER BY created_at DESC");
$units_stmt->execute([$id]);
$units = $units_stmt->fetchAll();

// Handle inquiry
$inquiry_result = handle_property_inquiry();

// Agent Check
$agent = null;
if (!empty($dev['agent_id'])) {
    $agent = get_record('team_members', $dev['agent_id']);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo truncate_text($dev['description'], 160); ?>">
    <title><?php echo htmlspecialchars($dev['title']); ?> - Development Details</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendors/fontawesome-pro-5/css/all.css">
    <link rel="stylesheet" href="vendors/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="vendors/slick/slick.min.css">
    <link rel="stylesheet" href="vendors/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" href="vendors/animate.css">
    <link rel="stylesheet" href="css/themes.css">
    <link rel="icon" href="images/favicon.png">
    <style>
        .hero-banner { min-height: 600px; position: relative; }
        .hero-banner img { width: 100%; height: 600px; object-fit: cover; }
        .hero-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%); padding: 80px 0 40px; pointer-events: none; }
        .unit-card:hover { transform: translateY(-5px); transition: 0.3s; }
        .gallery-item img { height: 200px; width: 100%; object-fit: cover; border-radius: 8px; cursor: pointer; }
        
        .prop-prev, .prop-next { border: 1px solid rgba(0,0,0,0.1) !important; z-index: 1000; }
        .btn-white { background: #fff; color: #252839; }
        .btn-white:hover { background: #252839; color: #fff; }

        @media (max-width: 768px) {
            .hero-banner, .hero-banner img { min-height: 350px !important; height: 350px !important; }
            .hero-overlay { display: none !important; }
            .mobile-project-info { display: block !important; padding: 20px 15px; background: #fff; border-bottom: 1px solid #e5e5e5; }
        }
        .mobile-project-info { display: none; }
        .prop-prev, .prop-next { 
            border: 1px solid rgba(0,0,0,0.1) !important; 
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 1000 !important;
        }
        .prop-prev { left: 30px !important; }
        .prop-next { right: 30px !important; }
        .border-white-5 { border-color: rgba(255,255,255,0.05) !important; }
    </style>
  </head>
  <body>
    <?php 
    $header_class = 'main-header m-0 navbar-dark bg-dark header-sticky header-sticky-smart header-mobile-xl';
    include 'header.php'; 
    ?>

    <main id="content">
      <!-- Title Section -->
      <section class="bg-dark py-5 border-top border-white-5">
        <div class="container container-xxl">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="d-flex align-items-center mb-1">
                <span class="badge badge-primary px-3 py-1 fs-12 text-uppercase mr-3">Development</span>
              </div>
              <h1 class="fs-40 text-white font-weight-bold mb-1"><?php echo htmlspecialchars($dev['title']); ?></h1>
              <p class="text-white opacity-8 mb-0 fs-18">
                <i class="fal fa-map-marker-alt mr-2 text-yellow"></i>
                <?php echo htmlspecialchars($dev['location']); ?>, <?php echo $dev['district']; ?>
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Project Hero -->
      <section class="hero-banner position-relative overflow-hidden mb-10" style="background: #000;">
          <div class="development-main-slider">
              <div class="hero-item">
                  <img src="images/<?php echo !empty($dev['image_main']) ? $dev['image_main'] : 'property-placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($dev['title']); ?>" class="w-100 object-fit-cover" style="height: 600px;">
              </div>
              <?php 
              $gallery = json_decode($dev['images'] ?? '[]', true);
              if (!empty($gallery) && is_array($gallery)):
                  foreach ($gallery as $img): ?>
                      <div class="hero-item">
                          <img src="images/<?php echo $img; ?>" alt="Gallery Image" class="w-100 object-fit-cover" style="height: 600px;">
                      </div>
                  <?php endforeach;
              endif; ?>
          </div>

          <!-- Slider Arrows -->
          <div class="slider-arrows">
             <button type="button" class="prop-prev btn btn-white rounded-circle shadow-lg p-0 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; cursor: pointer;"><i class="fas fa-chevron-left text-primary"></i></button>
             <button type="button" class="prop-next btn btn-white rounded-circle shadow-lg p-0 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; cursor: pointer;"><i class="fas fa-chevron-right text-primary"></i></button>
          </div>
      </section>

      <!-- Mobile Project Info (Show only on Mobile) -->
      <section class="mobile-project-info">
          <div class="container">
              <span class="badge badge-primary mb-2 px-2 py-1 fs-10 text-uppercase">Development</span>
              <h1 class="fs-24 lh-1 mb-2 font-weight-700 text-heading"><?php echo htmlspecialchars($dev['title']); ?></h1>
              <p class="fs-14 mb-0 text-muted"><i class="fal fa-map-marker-alt mr-2 text-primary"></i><?php echo htmlspecialchars($dev['location']); ?>, <?php echo $dev['district']; ?></p>
          </div>
      </section>

      <section class="py-12 bg-gray-01">
        <div class="container">
          <div class="row">
            <!-- Left Content -->
            <div class="col-lg-8">
              
              <!-- Tabs / Overview -->
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="fs-22 text-heading mb-4">About this Development</h3>
                <div class="text-gray-light lh-2 mb-6">
                    <?php echo nl2br(htmlspecialchars($dev['description'])); ?>
                </div>

                <?php if (!empty($dev['about_location'])): ?>
                <div class="mt-5 pt-5 border-top">
                    <h5 class="fs-18 mb-3 font-weight-600">About the location</h5>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($dev['about_location'])); ?></p>
                </div>
                <?php endif; ?>
              </div>

              <!-- Media Section -->
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6 overflow-hidden">
                <h3 class="fs-22 text-heading mb-4">Development Tour</h3>
                <?php if (!empty($dev['youtube_url'])): ?>
                <div class="col-12 mb-0 p-0">
                    <div class="rounded-lg overflow-hidden position-relative" style="padding-bottom: 56.25%; height:0;">
                        <?php 
                        $yt_url = $dev['youtube_url'];
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $yt_url, $match);
                        $video_id = $match[1] ?? '';
                        ?>
                        <iframe style="position:absolute; top:0; left:0; width:100%; height:100%;" src="https://www.youtube.com/embed/<?php echo $video_id; ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                <?php else: ?>
                    <p class="text-muted">Explore the development through the images at the top.</p>
                <?php endif; ?>
              </div>

              <!-- Project Units / Listings -->
              <div id="listings" class="mb-10">
                  <h3 class="fs-24 text-heading mb-5">Available Units in this Development</h3>
                  <div class="row">
                      <?php if (!empty($units)): ?>
                          <?php foreach ($units as $unit): ?>
                              <div class="col-md-6 mb-6">
                                  <div class="card border-0 shadow-sm unit-card h-100">
                                      <div class="position-relative">
                                          <img src="images/<?php echo $unit['image_main'] ?: 'property-placeholder.jpg'; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                          <div class="card-img-overlay p-2">
                                              <span class="badge badge-yellow"><?php echo ucfirst($unit['status']); ?></span>
                                          </div>
                                      </div>
                                      <div class="card-body p-4">
                                          <h5 class="fs-18 mb-1"><a href="property-detail.php?id=<?php echo $unit['id']; ?>" class="text-heading"><?php echo htmlspecialchars($unit['title']); ?></a></h5>
                                          <p class="text-primary font-weight-bold mb-3"><?php echo format_price($unit['price']); ?></p>
                                          <div class="d-flex border-top pt-3 text-muted fs-14">
                                              <?php if($unit['bedrooms']): ?><span class="mr-4"><i class="fas fa-bed mr-2 text-primary"></i><?php echo $unit['bedrooms']; ?> Beds</span><?php endif; ?>
                                              <?php if($unit['size_sqm']): ?><span><i class="fas fa-ruler-combined mr-2 text-primary"></i><?php echo (int)$unit['size_sqm']; ?> m²</span><?php endif; ?>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          <?php endforeach; ?>
                      <?php else: ?>
                          <div class="col-12 py-5 text-center bg-white rounded shadow-sm">
                              <p class="text-muted mb-0">No units currently listed. Please contact us for more information.</p>
                          </div>
                      <?php endif; ?>
                  </div>
              </div>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; z-index: 1;">
                    <!-- Inquiry Card -->
                    <div class="bg-white shadow rounded-lg p-6 mb-6">
                        <h4 class="mb-4">Development Inquiry</h4>
                        <?php if ($agent): ?>
                            <div class="mb-4 p-4 bg-gray-01 rounded-lg border-left border-primary border-4">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="images/<?php echo $agent['image'] ?: 'property-placeholder.jpg'; ?>" class="rounded-circle mr-3 shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0 font-weight-700 fs-16"><?php echo htmlspecialchars($agent['name']); ?></h6>
                                        <div class="text-primary small font-weight-600"><?php echo htmlspecialchars($agent['position']); ?></div>
                                    </div>
                                </div>
                                <div class="agent-contacts">
                                    <?php if (!empty($agent['phone'])): ?>
                                        <a href="tel:<?php echo $agent['phone']; ?>" class="btn btn-primary btn-block btn-sm mb-2 shadow-none">
                                            <i class="fas fa-phone-alt mr-2"></i> <?php echo htmlspecialchars($agent['phone']); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($agent['email'])): ?>
                                        <a href="mailto:<?php echo $agent['email']; ?>" class="btn btn-outline-primary btn-block btn-sm shadow-none">
                                            <i class="fas fa-envelope mr-2"></i> Message Agent
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($inquiry_result)): ?>
                            <div class="alert alert-<?php echo $inquiry_result['success'] ? 'success' : 'danger'; ?>">
                                <?php echo $inquiry_result['message']; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <input type="hidden" name="inquiry_form" value="1">
                            <input type="hidden" name="development_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="form-group mb-4">
                                <input type="text" name="name" class="form-control border-0 bg-gray-01" placeholder="Your Name" required>
                            </div>
                            <div class="form-group mb-4">
                                <input type="email" name="email" class="form-control border-0 bg-gray-01" placeholder="Your Email" required>
                            </div>
                            <div class="form-group mb-4">
                                <input type="tel" name="phone" class="form-control border-0 bg-gray-01" placeholder="Your Phone Number">
                            </div>
                            <div class="form-group mb-4">
                                <textarea name="message" class="form-control border-0 bg-gray-01" rows="4" placeholder="Request more info about this development..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block btn-lg shadow-none">Send Request</button>
                        </form>
                    </div>

                    <!-- Project Features Summary -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h5 class="mb-4 font-weight-600">Development Highlights</h5>
                        <ul class="list-unstyled mb-0 fs-14">
                            <?php if (!empty($dev['ideal_for'])): ?>
                                <li class="mb-3">
                                    <strong>Ideal For:</strong> 
                                    <span class="d-block text-muted">
                                        <?php 
                                        $ideals = json_decode($dev['ideal_for'] ?? '[]', true);
                                        echo is_array($ideals) ? implode(', ', array_map('htmlspecialchars', $ideals)) : htmlspecialchars($dev['ideal_for']);
                                        ?>
                                    </span>
                                </li>
                            <?php endif; ?>
                            <?php if (!empty($dev['proximity'])): ?>
                                <li class="mb-3"><strong>Proximity:</strong> <span class="d-block text-muted"><?php echo htmlspecialchars($dev['proximity']); ?></span></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </section>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="vendors/jquery.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.bundle.js"></script>
    <script src="vendors/slick/slick.min.js"></script>
    <script src="vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.gallery-item-link').magnificPopup({
                type: 'image',
                gallery: { enabled: true }
            });

            $('.development-main-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                fade: true,
                infinite: true,
                prevArrow: $('.prop-prev'),
                nextArrow: $('.prop-next'),
                dots: false
            });
        });
    </script>
  </body>
</html>
