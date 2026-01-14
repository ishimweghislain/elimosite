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
            </div>
            <div class="col-md-4 text-md-right mt-4 mt-md-0">
              <p class="fs-22 text-heading font-weight-bold mb-0">RWF <?php echo number_format($property['price']); ?></p>
              <span class="badge badge-primary mt-2"><?php echo ucfirst($property['status']); ?></span>
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
              
              <!-- Description -->
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="fs-22 text-heading mb-4">Description</h3>
                <div class="mb-0 text-gray-light lh-2">
                    <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                </div>
              </div>

              <!-- Features -->
              <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h3 class="fs-22 text-heading mb-4">Property Details</h3>
                <div class="row">
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-bed"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Bedrooms</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['bedrooms']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-bath"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Bathrooms</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['bathrooms']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-car"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Garage</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['garage']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-ruler-combined"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Size</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['size_sqm']; ?> SqM</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-calendar-alt"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Year Built</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['year_built']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="d-flex align-items-center">
                            <span class="text-primary fs-20 mr-3"><i class="fal fa-home"></i></span>
                            <div>
                                <span class="d-block text-gray-light fs-13">Type</span>
                                <span class="d-block text-heading font-weight-500"><?php echo $property['property_type']; ?></span>
                            </div>
                        </div>
                    </div>
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
                            <?php if ($property['stories']): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Stories:</strong> <span><?php echo $property['stories']; ?></span></li>
                            <?php endif; ?>
                            <?php if ($property['furnished']): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Furnished:</strong> <span><?php echo htmlspecialchars($property['furnished']); ?></span></li>
                            <?php endif; ?>
                            <?php if ($property['multi_family']): ?>
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Multi-family:</strong> <span><?php echo htmlspecialchars($property['multi_family']); ?></span></li>
                            <?php endif; ?>
                            <?php if ($property['plot_size']): ?>
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
                                <li class="mb-3 d-flex justify-content-between border-bottom pb-2"><strong>Ideal for:</strong> <span><?php echo htmlspecialchars($property['ideal_for']); ?></span></li>
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
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; // Assuming csrf logic exists or handled by functions ?>"> 
                        
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
