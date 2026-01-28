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
    <title><?php echo htmlspecialchars($dev['title']); ?> - Project Details</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendors/fontawesome-pro-5/css/all.css">
    <link rel="stylesheet" href="vendors/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="vendors/slick/slick.min.css">
    <link rel="stylesheet" href="vendors/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" href="vendors/animate.css">
    <link rel="stylesheet" href="css/themes.css">
    <link rel="icon" href="images/favicon.png">
    <style>
        .hero-banner { height: 500px; position: relative; }
        .hero-banner img { width: 100%; height: 100%; object-fit: cover; }
        .hero-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.8)); padding: 60px 0; }
        .unit-card:hover { transform: translateY(-5px); transition: 0.3s; }
        .gallery-item img { height: 200px; width: 100%; object-fit: cover; border-radius: 8px; cursor: pointer; }
        
        @media (max-width: 768px) {
            .hero-banner { height: 300px !important; }
            .hero-overlay { display: none !important; }
            .mobile-project-info { display: block !important; padding: 20px 15px; background: #fff; border-bottom: 1px solid #e5e5e5; }
        }
        .mobile-project-info { display: none; }
        
        .development-gallery-slider .slick-prev, .development-gallery-slider .slick-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 40px;
            height: 40px;
            background: #fff;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }
        .development-gallery-slider .slick-prev { left: 10px; }
        .development-gallery-slider .slick-next { right: 10px; }
        .development-gallery-slider .slick-dots { bottom: -30px; }
    </style>
  </head>
  <body>
    <?php include 'header.php'; ?>

    <main id="content">
      <!-- Project Hero -->
      <section class="hero-banner">
          <img src="images/<?php echo !empty($dev['image_main']) ? $dev['image_main'] : 'property-placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($dev['title']); ?>">
          <div class="hero-overlay">
              <div class="container text-white">
                  <span class="badge badge-primary mb-3">Development Project</span>
                  <h1 class="fs-40 lh-1 mb-2 font-weight-700 text-white"><?php echo htmlspecialchars($dev['title']); ?></h1>
                  <p class="fs-18 mb-0 opacity-09"><i class="fal fa-map-marker-alt mr-2"></i><?php echo htmlspecialchars($dev['location']); ?>, <?php echo $dev['district']; ?></p>
              </div>
          </div>
      </section>

      <!-- Mobile Project Info (Show only on Mobile) -->
      <section class="mobile-project-info">
          <div class="container">
              <span class="badge badge-primary mb-2 px-2 py-1 fs-10 text-uppercase">Development Project</span>
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
                <h3 class="fs-22 text-heading mb-4">About this Project</h3>
                <div class="text-gray-light lh-2 mb-6">
                    <?php echo nl2br(htmlspecialchars($dev['description'])); ?>
                </div>

                <?php if (!empty($dev['about_location'])): ?>
                <div class="mt-5 pt-5 border-top">
                    <h5 class="fs-18 mb-3 font-weight-600">The Environment</h5>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($dev['about_location'])); ?></p>
                </div>
                <?php endif; ?>
              </div>

              <!-- Media Section -->
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6 overflow-hidden">
                <h3 class="fs-22 text-heading mb-4">Gallery & Video</h3>
                
                <?php if (!empty($dev['youtube_url'])): ?>
                <div class="col-12 mb-4 p-0">
                    <div class="rounded-lg overflow-hidden position-relative" style="padding-bottom: 56.25%; height:0;">
                        <?php 
                        $yt_url = $dev['youtube_url'];
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $yt_url, $match);
                        $video_id = $match[1] ?? '';
                        ?>
                        <iframe style="position:absolute; top:0; left:0; width:100%; height:100%;" src="https://www.youtube.com/embed/<?php echo $video_id; ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                <?php endif; ?>

                <div class="development-gallery-slider">
                    <?php 
                    $gallery = json_decode($dev['images'] ?? '[]', true);
                    foreach ($gallery as $img): ?>
                        <div class="px-2">
                             <a href="images/<?php echo $img; ?>" class="gallery-item-link">
                                <div class="gallery-item-full rounded-lg overflow-hidden">
                                    <img src="images/<?php echo $img; ?>" style="height: 350px; width: 100%; object-fit: cover;">
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                        <h4 class="mb-4">Project Inquiry</h4>
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
                                <textarea name="message" class="form-control border-0 bg-gray-01" rows="4" placeholder="Request more info about this project..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block btn-lg shadow-none">Send Request</button>
                        </form>
                    </div>

                    <!-- Project Features Summary -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h5 class="mb-4 font-weight-600">Project Highlights</h5>
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

            $('.development-gallery-slider').slick({
                slidesToShow: 2,
                slidesToScroll: 1,
                arrows: true,
                dots: true,
                prevArrow: '<button type="button" class="slick-prev"><i class="far fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="far fa-chevron-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        });
    </script>
  </body>
</html>
