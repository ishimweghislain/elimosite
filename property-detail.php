<?php
require_once 'includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$property = get_property($id);

if (!$property || $property['status'] === 'draft') {
    header('Location: index.php');
    exit;
}

// Handle inquiry form
$inquiry_result = handle_property_inquiry();


// Development Check
$development = null;
if (!empty($property['development_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM developments WHERE id = ?");
    $stmt->execute([$property['development_id']]);
    $development = $stmt->fetch();
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo get_setting('site_description'); ?>">
    <title><?php echo htmlspecialchars($property['title']); ?> - <?php echo get_setting('site_name'); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendors/fontawesome-pro-5/css/all.css">
    <link rel="stylesheet" href="vendors/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="vendors/slick/slick.min.css">
    <link rel="stylesheet" href="vendors/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" href="vendors/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="vendors/animate.css">
    <link rel="stylesheet" href="css/themes.css">
    <link rel="icon" href="images/favicon.png">
  </head>
  <body>
    <?php include 'header.php'; ?>

    <main id="content">
      <!-- Property Hero -->
      <section class="pt-16 pb-12 page-title shadow" data-animate="fadeInUp">
        <div class="container">
          <div class="row">
            <div class="col-md-8">
              <h1 class="fs-30 lh-1 mb-2 text-primary font-weight-600"><?php echo htmlspecialchars($property['title']); ?></h1>
              <p class="mb-0 text-gray-light"><i class="fal fa-map-marker-alt mr-2"></i><?php echo htmlspecialchars($property['location']); ?></p>
              <?php if ($development): ?>
              <div class="mt-4 d-flex align-items-center">
                  <span class="badge badge-yellow mr-3 px-3 py-2 fs-12 text-uppercase font-weight-700">Project Unit</span>
                  <a href="development-detail.php?id=<?php echo $development['id']; ?>" class="text-primary font-weight-600 hover-underline">
                      <i class="fas fa-building mr-1"></i> Part of <?php echo htmlspecialchars($development['title']); ?>
                  </a>
              </div>
              <?php endif; ?>
            </div>
            <div class="col-md-4 text-md-right mt-4 mt-md-0">
              <?php if (!empty($property['price']) && $property['price'] > 0): ?>
                <p class="fs-22 text-heading font-weight-bold mb-0">RWF <?php echo number_format($property['price']); ?></p>
              <?php endif; ?>
              <span class="badge badge-primary mt-2"><?php echo ($property['status'] === 'under-construction') ? 'Off-plan Purchase' : ucwords(str_replace('-', ' ', $property['status'])); ?></span>
            </div>
          </div>
        </div>
      </section>

      <!-- Property Image -->
      <section class="mb-10">
        <div class="container">
            <div class="rounded-lg overflow-hidden position-relative" style="max-height: 600px;">
                <?php 
                $img_src = !empty($property['image_main']) ? 'images/' . $property['image_main'] : 'images/property-placeholder.jpg';
                ?>
                <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" class="w-100 h-100 object-fit-cover">
            </div>
        </div>
      </section>

      <!-- Details & Sidebar -->
      <section class="pb-12">
        <div class="container">
          <div class="row">
            <!-- Main Details -->
            <div class="col-lg-8 mb-6 mb-lg-0">
              
              <?php if ($development): ?>
              <div class="card border-0 shadow-sm rounded-lg mb-8 overflow-hidden animate__animated animate__fadeInUp">
                  <div class="row no-gutters">
                      <div class="col-md-4">
                          <img src="images/<?php echo $development['image_main'] ?: 'property-placeholder.jpg'; ?>" class="w-100 h-100 object-fit-cover" style="min-height: 180px;">
                      </div>
                      <div class="col-md-8">
                          <div class="card-body p-6">
                              <div class="d-flex justify-content-between align-items-start mb-2">
                                  <div>
                                      <span class="badge badge-primary mb-2">Development Project</span>
                                      <h4 class="fs-20 font-weight-700 mb-1"><?php echo htmlspecialchars($development['title']); ?></h4>
                                  </div>
                                  <a href="development-detail.php?id=<?php echo $development['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-4">View Project</a>
                              </div>
                              <p class="text-muted fs-14 mb-0"><?php echo truncate_text($development['description'], 150); ?></p>
                              <div class="mt-3 fs-13 text-primary">
                                  <i class="fas fa-map-marker-alt mr-1"></i> <?php echo htmlspecialchars($development['location']); ?>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <?php endif; ?>

              <!-- Description -->
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <!-- YouTube Video Embed & Instagram Link -->
                <?php if (!empty($property['youtube_url']) || !empty($property['instagram_url'])): ?>
                <div class="mb-6">
                    <?php if (!empty($property['youtube_url'])): ?>
                    <div class="mb-4">
                        <h5 class="fs-18 mb-3 text-heading font-weight-600">
                            <i class="fab fa-youtube text-danger mr-2"></i>Property Video Tour
                        </h5>
                        <?php
                        // Extract YouTube video ID from URL
                        $youtube_url = $property['youtube_url'];
                        $video_id = '';
                        
                        // Handle different YouTube URL formats
                        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $youtube_url, $matches)) {
                            $video_id = $matches[1];
                        } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $youtube_url, $matches)) {
                            $video_id = $matches[1];
                        } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $youtube_url, $matches)) {
                            $video_id = $matches[1];
                        } elseif (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $youtube_url, $matches)) {
                            $video_id = $matches[1];
                        }
                        
                        if ($video_id): ?>
                            <!-- YouTube Embed Player -->
                            <div class="video-container rounded-lg overflow-hidden shadow-sm" style="position: relative; padding-bottom: 56.25%; height: 0; background: #000;">
                                <iframe 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                                    src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video_id); ?>?rel=0&modestbranding=1" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="fas fa-info-circle me-1"></i>Video hosted on YouTube - 
                                <a href="<?php echo htmlspecialchars($youtube_url); ?>" target="_blank" class="text-primary">Watch on YouTube</a>
                            </p>
                        <?php else: ?>
                            <!-- Fallback if video ID can't be extracted -->
                            <a href="<?php echo fix_url($property['youtube_url']); ?>" target="_blank" class="d-flex align-items-center p-3 rounded-lg bg-red-opacity-01 border border-red text-red text-decoration-none hover-shine">
                                <div class="icon-circle bg-red text-white mr-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border-radius: 50%;">
                                    <i class="fab fa-youtube"></i>
                                </div>
                                <div>
                                    <h5 class="fs-15 mb-0 font-weight-600">Watch on YouTube</h5>
                                    <span class="fs-12 opacity-07">Property Video Tour</span>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($property['instagram_url'])): ?>
                    <div class="mb-3">
                        <a href="<?php echo fix_url($property['instagram_url']); ?>" target="_blank" class="d-flex align-items-center p-3 rounded-lg bg-primary-opacity-01 border border-primary text-primary text-decoration-none hover-shine">
                            <div class="icon-circle bg-primary text-white mr-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border-radius: 50%;">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div>
                                <h5 class="fs-15 mb-0 font-weight-600">View on Instagram</h5>
                                <span class="fs-12 opacity-07">Property Reel/Post</span>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>

                    <hr class="my-4">
                </div>
                <?php endif; ?>

                <h3 class="fs-22 text-heading mb-4">Description</h3>
                <div class="mb-0 text-gray-light lh-2">
                    <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                </div>
              </div>

              <!-- Features -->
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="fs-22 text-heading mb-4">Property Details</h3>
                <div class="row">
                    <?php if (!empty($property['bedrooms'])): ?>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-bed"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Bedrooms</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['bedrooms']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($property['bathrooms'])): ?>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-bath"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Bathrooms</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['bathrooms']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($property['garage'])): ?>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-car"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Parking Space</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['garage']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($property['size_sqm']) && $property['size_sqm'] > 0): ?>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-ruler-combined"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Build Size</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['size_sqm']; ?> m²</span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($property['year_built'])): ?>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-calendar-alt"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Year Built</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['year_built']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($property['property_type'])): ?>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-home"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Type</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['property_type']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
              </div>

              <!-- Additional Details -->
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="fs-22 text-heading mb-4">Additional Details</h3>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <?php if ($property['prop_id']): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Property ID:</strong> <span class="text-primary"><?php echo htmlspecialchars($property['prop_id']); ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($property['stories'])): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Floors:</strong> <span><?php echo $property['stories']; ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($property['furnished'])): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Furnished:</strong> <span><?php echo htmlspecialchars($property['furnished']); ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($property['multi_family'])): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Multi-family:</strong> <span><?php echo htmlspecialchars($property['multi_family']); ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($property['plot_size']) && $property['plot_size'] > 0): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Plot Size:</strong> <span><?php echo (int)$property['plot_size']; ?> m²</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <?php if ($property['zoning']): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Zoning:</strong> <span><?php echo htmlspecialchars($property['zoning']); ?></span></li>
                            <?php endif; ?>
                            <?php if ($property['views']): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Views:</strong> <span><?php echo htmlspecialchars($property['views']); ?></span></li>
                            <?php endif; ?>
                            <?php if ($property['ideal_for']): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                    <strong>Ideal for:</strong> 
                                    <span>
                                        <?php 
                                        $ideals = json_decode($property['ideal_for'] ?? '[]', true);
                                        echo is_array($ideals) ? implode(', ', array_map('htmlspecialchars', $ideals)) : htmlspecialchars($property['ideal_for']);
                                        ?>
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
              </div>

              <!-- Features & Amenities -->
              <div class="row">
                  <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6 h-100">
                        <h3 class="fs-20 text-heading mb-4">Features</h3>
                        <?php 
                        $features = json_decode($property['features'] ?? '[]', true);
                        if (!empty($features)): 
                        ?>
                            <ul class="list-unstyled row no-gutters">
                                <?php foreach ($features as $feature): ?>
                                    <li class="col-md-12 mb-2"><i class="fas fa-check-circle text-primary mr-2"></i><?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No specific features listed.</p>
                        <?php endif; ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6 h-100">
                        <h3 class="fs-20 text-heading mb-4">Amenities</h3>
                        <?php 
                        $amenities = json_decode($property['amenities'] ?? '[]', true);
                        if (!empty($amenities)): 
                        ?>
                            <ul class="list-unstyled row no-gutters">
                                <?php foreach ($amenities as $amenity): ?>
                                    <li class="col-md-12 mb-2"><i class="fas fa-star text-warning mr-2"></i><?php echo htmlspecialchars($amenity); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No specific amenities listed.</p>
                        <?php endif; ?>
                    </div>
                  </div>
              </div>

              <?php if (!empty($property['proximity'])): ?>
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="fs-20 text-heading mb-4">In close proximity to</h3>
                <p class="text-muted mb-0"><?php echo htmlspecialchars($property['proximity']); ?></p>
              </div>
              <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                    <h3 class="fs-22 text-heading mb-4">Interested?</h3>
                    <?php if (isset($inquiry_result)): ?>
                        <div class="alert alert-<?php echo $inquiry_result['success'] ? 'success' : 'danger'; ?>">
                            <?php echo $inquiry_result['message']; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="inquiry_form" value="1">
                        <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>"> 
                        
                        <div class="form-group mb-4">
                            <input type="text" name="name" class="form-control border-0 bg-gray-01" placeholder="Your Name" required>
                        </div>
                        <div class="form-group mb-4">
                            <input type="email" name="email" class="form-control border-0 bg-gray-01" placeholder="Your Email" required>
                        </div>
                        <div class="form-group mb-4">
                            <input type="tel" name="phone" class="form-control border-0 bg-gray-01" placeholder="Your Phone">
                        </div>
                        <div class="form-group mb-4">
                            <textarea name="message" class="form-control border-0 bg-gray-01" rows="4" placeholder="I am interested in this property..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Send Inquiry</button>
                    </form>
                </div>
            </div>
          </div>
        </div>
      </section>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="vendors/jquery.min.js"></script>
    <script src="vendors/jquery-ui/jquery-ui.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.bundle.js"></script>
    <script src="vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="vendors/slick/slick.min.js"></script>
    <script src="vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="js/theme.js"></script>
  </body>
</html>
