<?php
require_once 'includes/config.php';

// Get featured properties for homepage
$featured_properties = get_featured_properties(12);
$recent_properties = get_recent_properties(12);
$stats = get_property_stats();
$blog_posts = get_blog_posts(3);
$agents = get_team_members();

// Handle search form submission
$search_results = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_search'])) {
    $filters = [];
    
    if (!empty($_POST['status'])) {
        $filters['status'] = $_POST['status'];
    }
    
    if (!empty($_POST['type'])) {
        $filters['property_type'] = $_POST['type'];
    }
    
    if (!empty($_POST['location'])) {
        $filters['location'] = $_POST['location'];
    }
    
    if (!empty($_POST['max_price'])) {
        $filters['max_price'] = (float)$_POST['max_price'];
    }
    
    if (!empty($_POST['category'])) {
        $filters['category'] = $_POST['category'];
    }
    
    if (!empty($_POST['bedroom'])) {
        $filters['bedrooms'] = (int)$_POST['bedroom'];
    }
    
    if (!empty($_POST['bathroom'])) {
        $filters['bathrooms'] = (int)$_POST['bathroom'];
    }
    
    $search_results = get_properties($filters);
    
    // Redirect to search results page
    header('Location: search-results.php?' . http_build_query($filters));
    exit;
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
    <title><?php echo get_page_title(); ?></title>
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
    <meta name="twitter:title" content="<?php echo get_setting('site_name'); ?>">
    <meta name="twitter:description" content="<?php echo get_setting('site_description'); ?>">
    <meta name="twitter:image" content="images/white-logo.png">
    <!-- Facebook -->
    <meta property="og:url" content="home-01.html">
    <meta property="og:title" content="<?php echo get_setting('site_name'); ?>">
    <meta property="og:description" content="<?php echo get_setting('site_description'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="images/white-logo.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <style>
      /* Pulse Animation */
      @keyframes pulse-custom {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(var(--primary-rgb), 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(var(--primary-rgb), 0); }
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
      }
      /* Ensure search box is modern */
      .search-box {
        border-radius: 15px !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
        top: 40% !important;
      }
      .slick-prev { left: -25px !important; }
      .slick-next { right: -25px !important; }
      .slick-prev:before, .slick-next:before { content: '' !important; }
      .slick-prev:hover, .slick-next:hover {
        background: #28a745 !important;
        color: #fff !important;
      }
      .slick-prev i, .slick-next i {
        font-size: 20px !important;
        line-height: 50px !important;
      }
      .slick-dots li button:before {
        font-size: 12px !important;
        color: #28a745 !important;
      }
      .slick-dots li.slick-active button:before {
        color: #28a745 !important;
      }
    </style>
  </head>
  <body>
    <?php
    $districts = [
        'Kigali' => ['Gasabo', 'Kicukiro', 'Nyarugenge'],
        'Eastern' => ['Bugesera', 'Gatsibo', 'Kayonza', 'Kirehe', 'Ngoma', 'Nyagatare', 'Rwamagana'],
        'Northern' => ['Burera', 'Gakenke', 'Gicumbi', 'Musanze', 'Rulindo'],
        'Southern' => ['Gisagara', 'Huye', 'Kamonyi', 'Muhanga', 'Nyamagabe', 'Nyanza', 'Nyaruguru', 'Ruhango'],
        'Western' => ['Karongi', 'Ngororero', 'Nyabihu', 'Nyamasheke', 'Rubavu', 'Rusizi', 'Rutsiro']
    ];
    ?>
    <?php include 'header.php'; ?>
    <main id="content">
      <section class="d-flex flex-column">
        <div style="background-image: url('images/banner-2.jpg')"
	     class="bg-cover d-flex align-items-center custom-vh-100">
          <div class="container pt-lg-15 py-8" data-animate="zoomIn">
            <p class="text-white fs-md-18 fs-14 font-weight-500 letter-spacing-367 mb-2 text-center text-uppercase">Let
              us help you</p>
            <h2 class="text-white display-2 text-center mb-sm-5 mb-5">Find Your Dream Home</h2>
            <img class="mxw-180 d-block mx-auto mt-4 mb-6" src="images/line-01.png" alt="">
            <form class="property-search py-lg-0 z-index-2 position-relative d-none d-lg-block pt-5" action="index.php" method="POST">
              <input type="hidden" name="property_search" value="1">
              <div class="row no-gutters">
                <div class="col-md-12 col-lg-8 col-xl-8">
                  <input class="search-field" type="hidden" name="status" value="for-sale">
                  <ul class="nav nav-pills property-search-status-tab">
                    
                    <li class="nav-item bg-dark rounded-top" role="presentation">
                      <a href="#" role="tab" aria-selected="false"
								   class="nav-link btn shadow-none rounded-bottom-0 text-btn-focus-secondary text-uppercase d-flex align-items-center fs-13 rounded-bottom-0 bg-active-white text-active-secondary letter-spacing-087 flex-md-1 px-4 py-2"
								   data-toggle="pill" data-value="for-rent">
                        <svg class="icon icon-building fs-22 mr-2">
                          <use xlink:href="#icon-building"></use>
                        </svg>
                        for rent
                      </a>
                    </li>
                    <li class="nav-item bg-dark rounded-top" role="presentation">
                      <a href="#" role="tab" aria-selected="true"
                   class="nav-link btn shadow-none rounded-bottom-0 text-btn-focus-secondary text-uppercase d-flex align-items-center fs-13 rounded-bottom-0 bg-active-white text-active-secondary letter-spacing-087 flex-md-1 px-4 py-2 active"
                   data-toggle="pill" data-value="for-sale">
                        <svg class="icon icon-villa fs-22 mr-2">
                          <use xlink:href="#icon-villa"></use>
                        </svg>
                        for sale
                      </a>
                    </li>
                    <li class="nav-item bg-dark rounded-top" role="presentation">
                      <a href="#" role="tab" aria-selected="true"
                   class="nav-link btn shadow-none rounded-bottom-0 text-btn-focus-secondary text-uppercase d-flex align-items-center fs-13 rounded-bottom-0 bg-active-white text-active-secondary letter-spacing-087 flex-md-1 px-4 py-2"
                   data-toggle="pill" data-value="developments">
                        <svg class="icon icon-villa fs-22 mr-2">
                          <use xlink:href="#icon-villa"></use>
                        </svg>
                        developments
                      </a>
                    </li>
                    <li class="nav-item bg-dark rounded-top" role="presentation">
                      <a href="#" role="tab" aria-selected="true"
                   class="nav-link btn shadow-none rounded-bottom-0 text-btn-focus-secondary text-uppercase d-flex align-items-center fs-13 rounded-bottom-0 bg-active-white text-active-secondary letter-spacing-087 flex-md-1 px-4 py-2"
                   data-toggle="pill" data-value="developments">
                        <svg class="icon icon-villa fs-22 mr-2">
                          <use xlink:href="#icon-building"></use>
                        </svg>
                        commercial
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="bg-white search-box px-6 rounded-bottom rounded-top-right pb-6 pb-lg-0">
                <div class="row align-items-center"
					     id="accordion-4">
               <div class="col-md-6 col-lg-2 col-xl-2 pt-6 pt-lg-0 order-1">
                    <label class="text-uppercase font-weight-500 mb-1">Category</label>
                    <select class="form-control selectpicker bg-transparent border-bottom rounded-0 border-color-input"
                      title="Select" data-style="p-0 h-24 lh-17 text-dark" name="category">
                      <option>Residential</option>
                      <option>Commercial</option>
                      <option>Developments</option>
                      <option>Land</option>
                    </select>
                  </div>
                  <div class="col-md-6 col-lg-2 col-xl-2 pt-6 pt-lg-0 order-1">
                    <label class="text-uppercase font-weight-500 mb-1">Property Type</label>
                    <select class="form-control selectpicker bg-transparent border-bottom rounded-0 border-color-input"
							        title="Select" data-style="p-0 h-24 lh-17 text-dark" name="type">
                      <option>Apartment</option>
                      <option>House</option>
                      <option>Townhouse</option>
                      <option>Semi Detached</option>
                      <option>Office</option>
                      <option>Retail</option>
                      <option>Industrial</option>
                      <option>Land</option>
                    </select>
                  </div>
                  <div class="col-md-6 col-lg-3 col-xl-3 pt-6 pt-lg-0 order-2">
                    <label class="text-uppercase font-weight-500">Location</label>
                    <select class="form-control selectpicker bg-transparent border-bottom rounded-0 border-color-input"
                      title="Select District" data-style="p-0 h-24 lh-17 text-dark" data-live-search="true" name="location">
                      <?php foreach ($districts as $province => $dist_list): ?>
                        <optgroup label="<?php echo $province; ?>">
                          <?php foreach ($dist_list as $dist): ?>
                            <option><?php echo $dist; ?></option>
                          <?php endforeach; ?>
                        </optgroup>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6 col-lg-2 col-xl-2 pt-6 pt-lg-0 order-2">
                    <label class="text-uppercase font-weight-500">Max Price</label>
                    <div class="position-relative">
                      <input type="number" name="max_price"
                       class="form-control bg-transparent shadow-none border-top-0 border-right-0 border-left-0 border-bottom rounded-0 h-24 lh-17 pl-0 pr-4 font-weight-600 border-color-input placeholder-muted"
                       placeholder="10 000 000">
                    </div>
                  </div>
                  <div class="col-sm pt-6 pt-lg-0 order-sm-4 order-5">
                    <button type="submit"
							        class="btn btn-primary shadow-none fs-16 font-weight-600 w-100 py-lg-2 lh-213">
                      Search
                    </button>
                  </div>

                  <div class="col-lg-12 order-6 text-center">
                    <a href="#advanced-search-filters-4"
                 class="btn advanced-search btn-sm btn-accent shadow-none text-secondary font-weight-400 text-center collapsed"
                 data-toggle="collapse" data-target="#advanced-search-filters-4" aria-expanded="true"
                 aria-controls="advanced-search-filters-4">
                      Advanced Search
                    </a>
                  </div>
                  <div id="advanced-search-filters-4" class="col-12 pt-4 pb-sm-4 order-sm-5 order-4 collapse"
						     data-parent="#accordion-4">
                    <div class="row">
                      <div class="col-sm-4 col-lg-3 pt-6">
                        <label class="text-uppercase font-weight-500 letter-spacing-093 mb-1">Bedrooms</label>
                        <select class="form-control selectpicker bg-transparent border-bottom rounded-0 border-color-input"
									        name="bedroom"
									        title="All Bedrooms" data-style="p-0 h-24 lh-17 text-dark">
                          <option>Any Bedrooms</option>
                          <option>1</option>
                          <option>2</option>
                          <option>3</option>
                          <option>4</option>
                          <option>5</option>
                          <option>6</option>
                          <option>7</option>
                          <option>8</option>
                          <option>9</option>
                          <option>10</option>
                        </select>
                      </div>
                      <div class="col-sm-4 col-lg-4 pt-6">
                        <label class="text-uppercase font-weight-500 letter-spacing-093 mb-1">Bathrooms</label>
                        <select class="form-control selectpicker bg-transparent border-bottom rounded-0 border-color-input"
									        title="All Bathrooms" data-style="p-0 h-24 lh-17 text-dark" name="bathroom">
                          <option>Any Bathrooms</option>
                          <option>1</option>
                          <option>2</option>
                          <option>3</option>
                          <option>4</option>
                          <option>5</option>
                          <option>6</option>
                          <option>7</option>
                          <option>8</option>
                          <option>9</option>
                          <option>10</option>
                        </select>
                      </div>
                     <div class="col-sm-4 col-lg-4 pt-6">
                        <label class="text-uppercase font-weight-500 letter-spacing-093 mb-1">Property
                          ID</label>
                        <input type="text" name="search"
                         class="form-control bg-transparent shadow-none border-top-0 border-right-0 border-left-0 border-bottom rounded-0 h-24 lh-17 p-0 font-weight-600 border-color-input"
                         placeholder="Enter ID...">
                      </div>
                    </div>
                    <div class="row pt-2">
                      
                      
                      <div class="col-12 pt-6 pb-2">
                        <a class="lh-17 d-inline-block other-feature collapsed" data-toggle="collapse"
									   href="#other-feature-4"
									   role="button"
									   aria-expanded="false" aria-controls="other-feature-4">
                          <span class="fs-15 text-heading font-weight-500 hover-primary">Other Features</span>
                        </a>
                      </div>
                      <div class="collapse row mx-0 w-100" id="other-feature-4">
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check1-4"
											       name="features[]">
                            <label class="custom-control-label" for="check1-4">Air Conditioning</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check2-4"
											       name="features[]">
                            <label class="custom-control-label" for="check2-4">Laundry</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check4-4"
											       name="features[]">
                            <label class="custom-control-label" for="check4-4">Washer</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check5-4"
											       name="features[]">
                            <label class="custom-control-label" for="check5-4">Barbeque</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check6-4"
											       name="features[]">
                            <label class="custom-control-label" for="check6-4">Lawn</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check7-4"
											       name="features[]">
                            <label class="custom-control-label" for="check7-4">Sauna</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check8-4"
											       name="features[]">
                            <label class="custom-control-label" for="check8-4">WiFi</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check9-4"
											       name="features[]">
                            <label class="custom-control-label" for="check9-4">Dryer</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check10-4"
											       name="features[]">
                            <label class="custom-control-label" for="check10-4">Microwave</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check11-4"
											       name="features[]">
                            <label class="custom-control-label" for="check11-4">Swimming Pool</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check12-4"
											       name="features[]">
                            <label class="custom-control-label" for="check12-4">Window Coverings</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check13-4"
											       name="features[]">
                            <label class="custom-control-label" for="check13-4">Gym</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check14-4"
											       name="features[]">
                            <label class="custom-control-label" for="check14-4">Outdoor Shower</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check15-4"
											       name="features[]">
                            <label class="custom-control-label" for="check15-4">TV Cable</label>
                          </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3 py-2">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="check16-4"
											       name="features[]">
                            <label class="custom-control-label" for="check16-4">Refrigerator</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <form class="property-search property-search-mobile d-lg-none z-index-2 position-relative bg-white rounded mx-md-10"  action="search-results.html">
              <div class="row align-items-lg-center" id="accordion-4-mobile">
                <div class="col-12">
                  <div class="form-group mb-0 position-relative">
                    <a href="#advanced-search-filters-4-mobile"
							   class="text-secondary btn advanced-search shadow-none pr-3 pl-0 d-flex align-items-center position-absolute pos-fixed-left-center py-0 h-100 border-right collapsed"
							   data-toggle="collapse" data-target="#advanced-search-filters-4-mobile"
							   aria-expanded="true"
							   aria-controls="advanced-search-filters-4-mobile">
                    </a>
                    <input type="text"
							       class="form-control form-control-lg border shadow-none pr-9 pl-11 bg-white placeholder-muted"
							       name="key-word"
							       placeholder="Search...">
                    <button type="submit"
							        class="btn position-absolute pos-fixed-right-center p-0 text-heading fs-20 px-3 shadow-none h-100 border-left">
                      <i class="far fa-search"></i>
                    </button>
                  </div>
                </div>
                <div id="advanced-search-filters-4-mobile" class="col-12 pt-2 px-7 collapse"
					     data-parent="#accordion-4-mobile">
                  <div class="row mx-n2">
                    <div class="col-sm-6 pt-4 px-2">
                      <select class="form-control border shadow-none form-control-lg selectpicker bg-transparent"
								        title="Select" data-style="btn-lg py-2 h-52 bg-transparent" name="type">
                        <option>All status</option>
                        <option>For Rent</option>
                        <option>For Sale</option>
                      </select>
                    </div>
                    <div class="col-sm-6 pt-4 px-2">
                      <select class="form-control border shadow-none form-control-lg selectpicker bg-transparent"
								        title="All Types" data-style="btn-lg py-2 h-52 bg-transparent" name="type">
                        <option>Condominium</option>
                        <option>Single-Family Home</option>
                        <option>Townhouse</option>
                        <option>Multi-Family Home</option>
                      </select>
                    </div>
                    <div class="col-sm-6 pt-4 px-2">
                      <select class="form-control border shadow-none form-control-lg selectpicker bg-transparent"
								        name="bedroom"
								        title="Bedrooms" data-style="btn-lg py-2 h-52 bg-transparent">
                        <option>Any Bedrooms</option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                      </select>
                    </div>
                    <div class="col-sm-6 pt-4 px-2">
                      <select class="form-control border shadow-none form-control-lg selectpicker bg-transparent"
								        name="bathrooms"
								        title="Bathrooms" data-style="btn-lg py-2 h-52 bg-transparent">
                        <option>Any Bathrooms</option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                      </select>
                    </div>
                    
                  </div>
                  <div class="row">
                    <div class="col-sm-4 col-lg-4 pt-6">
                        <label class="text-uppercase font-weight-500 letter-spacing-093 mb-1">Property
                          ID</label>
                        <input type="text" name="search"
                         class="form-control bg-transparent shadow-none border-top-0 border-right-0 border-left-0 border-bottom rounded-0 h-24 lh-17 p-0 font-weight-600 border-color-input"
                         placeholder="Enter ID...">
                      </div>
                    </div>
                    <div class="col-12 pt-4 pb-2">
                      <a class="lh-17 d-inline-block other-feature collapsed" data-toggle="collapse"
								   href="#other-feature-4-mobile"
								   role="button"
								   aria-expanded="false" aria-controls="other-feature-4-mobile">
                        <span class="fs-15 font-weight-500 hover-primary">Other Features</span>
                      </a>
                    </div>
                    <div class="collapse row mx-0 w-100" id="other-feature-4-mobile">
                      <div class="col-sm-6 py-2">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" name="features[]"
										       id="check1-4-mobile">
                          <label class="custom-control-label" for="check1-4-mobile">Air
                            Conditioning</label>
                        </div>
                      </div>
                      <div class="col-sm-6 py-2">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" name="features[]"
										       id="check2-4-mobile">
                          <label class="custom-control-label" for="check2-4-mobile">Laundry</label>
                        </div>
                      </div>
                      <div class="col-sm-6 py-2">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" name="features[]"
										       id="check4-4-mobile">
                          <label class="custom-control-label" for="check4-4-mobile">Washer</label>
                        </div>
                      </div>
                      <div class="col-sm-6 py-2">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" name="features[]"
										       id="check5-4-mobile">
                          <label class="custom-control-label" for="check5-4-mobile">Barbeque</label>
                        </div>
                      </div>
                      <div class="col-sm-6 py-2">
                        <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" name="features[]"
										       id="check6-4-mobile">
                          <label class="custom-control-label" for="check6-4-mobile">Lawn</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </section>
      <section class="py-lg-12 my-lg-1 py-11">
        <div class="container container-xxl">
          <div class="row">
            <div class="col-md-6">
              <h2 class="text-heading">Featured Properties</h2>
              <span class="heading-divider"></span>
              <p class="mb-7">Top rated properties in Kigali City and other provinces</p>
            </div>
            <div class="col-md-6 text-md-right">
              <a href="search-results.php?label=&category=&type=&district=&beds=&baths=&ref="
                   class="btn btn-lg btn-primary mb-8">See all properties
                <i class="far fa-long-arrow-right ml-1"></i>
              </a>
            </div>
          </div>
          
          <?php if (!empty($featured_properties)): ?>
            <div class="slick-slider mx-n3 mt-8" data-slick-options='{"slidesToShow": 3, "slidesToScroll": 1, "autoplay": false, "infinite": true, "arrows": true, "dots": true, "responsive": [{"breakpoint": 1200, "settings": {"slidesToShow": 2}}, {"breakpoint": 768, "settings": {"slidesToShow": 1}}]}'>
               <?php foreach ($featured_properties as $property): ?>
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
                      <p class="mb-3 text-muted fs-14 description-truncate" style="flex-grow: 1; min-height: 52px;"><?php echo truncate_text($property['description'], 120); ?></p>
                      <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                        <div class="d-flex flex-wrap">
                          <?php if ($property['bedrooms']): ?>
                            <span class="badge badge-light mr-1"><i class="fas fa-bed mr-1 text-primary"></i><?php echo $property['bedrooms']; ?></span>
                          <?php endif; ?>
                          <?php if ($property['bathrooms']): ?>
                            <span class="badge badge-light mr-1"><i class="fas fa-bath mr-1 text-primary"></i><?php echo $property['bathrooms']; ?></span>
                          <?php endif; ?>
                          <?php if (!empty($property['size_sqm']) && $property['size_sqm'] > 0): ?>
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
            </div>
          <?php else: ?>
            <div class="text-center py-8">
              <p class="text-muted">No featured properties added yet.</p>
            </div>
          <?php endif; ?>
        </div>
      </section>
      
      <!-- Testimonials Section -->
      <section class="">
        <div class="bg-single-image pt-lg-13 pb-lg-12 py-11 bg-dark">
          <div class="container">
          <div class="row">
            <div class="col-lg-5 pr-xl-17" data-animate="fadeInLeft">
              <h2 class="mt-5 text-white">What our clients say about us and our service</h2>
              <span class="heading-divider"></span>
              <p class="mb-6 text-white">Customer satisfaction is a primary goal for our company. To that end, we are passionate about meeting and surpassing the expectations of our varied clientele. Don't take our word for it, listen to what our clients have to say.</p>
              <a href="team.php" class="btn btn-lg btn-primary rmb-8 mb-lg-0">View our team
                <i class="far fa-long-arrow-right ml-1"></i>
              </a>
            </div>
            <div class="col-lg-7" data-animate="fadeInRight">
              <div class="slick-slider custom-vertical mx-0"
             data-slick-options='{"slidesToShow": 1,"vertical":true,"verticalSwiping":true,"centerMode":true,"swipeToSlide":true,"focusOnSelect":true,"centerPadding":"120px","infinite":true,"autoplay":true,"dots":false,"arrows":false,"autoplaySpeed":15000}'>
             <div class="box px-sm-8">
                  <div class="card border-0 shadow-lg-3 px-3 pl-md-9 pr-md-9 pt-8 pb-7">
                    <div class="card-body p-0">
                      <h5 class="card-title fs-18 text-secondary mb-3 lh-17">Simply Amazing!</h5>
                      <p class="card-text fs-14 lh-2 text-heading mb-5">
                        “Grace is an amazing agent. She is very patient and listens carefully to what her customer needs. She took care of the conversation with the respective landlord and negotiated a good price as well as good rental conditions for us. We do highly recommend her to any future customers.“
                      </p>
                      <div class="media align-items-center">
                        <div class="media-body">
                          <p class="fs-17 lh-1 text-heading font-weight-600 mb-2">Dr. Pascal Lopez</p>
                          <p class="fs-15 lh-12 mb-0">GIZ</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="box px-sm-8">
                  <div class="card border-0 shadow-lg-3 px-3 pl-md-9 pr-md-9 pt-8 pb-7">
                    <div class="card-body p-0">
                      <h5 class="card-title fs-18 text-secondary mb-3 lh-17">Simply Amazing!</h5>
                      <p class="card-text fs-14 lh-2 text-heading mb-5">
                        “Grace helped us find a great house in Kigali within just a few days which met all of our requirements.  She understood our requests, gave us realistic options, accommodated repeated viewings, and  then provided hands-on help with negotiating, contracting, and move-in.“
                      </p>
                      <div class="media align-items-center">
                        <div class="media-body">
                          <p class="fs-17 lh-1 text-heading font-weight-600 mb-2">Zach Raymond</p>
                          <p class="fs-15 lh-12 mb-0">AB Bank</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="box px-sm-8">
                  <div class="card border-0 shadow-lg-3 px-3 pl-md-9 pr-md-9 pt-8 pb-7">
                    <div class="card-body p-0">
                      <h5 class="card-title fs-18 text-secondary mb-3 lh-17">Very Professional!</h5>
                      <p class="card-text fs-14 lh-2 text-heading mb-5">
                        “ I have worked with Aristide several times on different Real Estate projects. Aristide has been very professional and he clearly understood what we were looking for. He has a great sense of communication throughout the process. I highly recommend him “
                      </p>
                      <div class="media align-items-center">
                        <div class="media-body">
                          <p class="fs-17 lh-1 text-heading font-weight-600 mb-2">Aimé</p>
                          <p class="fs-15 lh-12 mb-0">CANAL+ Rwanda</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="box px-sm-8">
                  <div class="card border-0 shadow-lg-3 px-3 pl-md-9 pr-md-9 pt-8 pb-7">
                    <div class="card-body p-0">
                      <h5 class="card-title fs-18 text-secondary mb-3 lh-17">Above and beyond</h5>
                      <p class="card-text fs-14 lh-2 text-heading mb-5">
                        “ We've used Elimo on two occasions to find a new rental house, and once to help find a new office for a business. In all instances, Aristide was professional, reliable, and knowledgeable. They went above and beyond to facilitate the transactions. “
                      </p>
                      <div class="media align-items-center">
                        <div class="media-body">
                          <p class="fs-17 lh-1 text-heading font-weight-600 mb-2">James Setzler</p>
                          <p class="fs-15 lh-12 mb-0">GAC-R</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="box px-sm-8">
                  <div class="card border-0 shadow-lg-3 px-3 pl-md-9 pr-md-9 pt-8 pb-7">
                    <div class="card-body p-0">
                      <h5 class="card-title fs-18 text-secondary mb-3 lh-17">Time saver!</h5>
                      <p class="card-text fs-14 lh-2 text-heading mb-5">
                        He thus saved us time, and out of the 3 houses visited with him, two were our top choices of all houses visited (even prior consulting him), and eventually we opted for the house that has become our home.
                      </p>
                      <div class="media align-items-center">
                        <div class="media-body">
                          <p class="fs-17 lh-1 text-heading font-weight-600 mb-2">Mr Sotirios Bazikamwe</p>
                          <p class="fs-15 lh-12 mb-0">European Union</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="box px-sm-8">
                  <div class="card border-0 shadow-lg-3 px-3 pl-md-9 pr-md-9 pt-8 pb-7">
                    <div class="card-body p-0">
                      <h5 class="card-title fs-18 text-secondary mb-3 lh-17">Efficient experience!</h5>
                      <p class="card-text fs-14 lh-2 text-heading mb-5">
                        It was a really good, professional, and efficient experience. Aristide was really keen on understanding what I was specifically looking for and liaised properly with the owner.
                      </p>
                      <div class="media align-items-center">
                        <div class="media-body">
                          <p class="fs-17 lh-1 text-heading font-weight-600 mb-2">Ana Paula Bedoya</p>
                          <p class="fs-15 lh-12 mb-0">WFP</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="box px-sm-8">
                  <div class="card border-0 shadow-lg-3 px-3 pl-md-9 pr-md-9 pt-8 pb-7">
                    <div class="card-body p-0">
                      <h5 class="card-title fs-18 text-secondary mb-3 lh-17">Time saver!</h5>
                      <p class="card-text fs-14 lh-2 text-heading mb-5">
                        We are very happy and grateful to have found Aristide in Kigali. He reliably and patiently helped us to find a fantastic house in Kimihurura. Moreover, he supported the negotiations with the landlord very efficiently. We fully recommend him.
                      </p>
                      <div class="media align-items-center">
                        <div class="media-body">
                          <p class="fs-17 lh-1 text-heading font-weight-600 mb-2">DAVID BOERNER</p>
                          <p class="fs-15 lh-12 mb-0">GIZ Rwanda</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </section>

      <!-- News Section -->
      <section class="bg-gray-02 pt-10 pb-10">
        <div class="container">
          <p class="text-danger letter-spacing-263 text-uppercase lh-186 text-center mb-0">news & articles</p>
          <h2 class="text-center lh-1625 text-dark pb-1">
            Check Out Recent News & Articles
          </h2>
          <img class="mxw-180 d-block mx-auto mt-4 mb-1" src="images/newimages/line-01.png" alt="">
          <div class="mx-n2">
            <div class="slick-slider mt-6 mx-n1 slick-dots-mt-0"
                 data-slick-options='{"slidesToShow": 3, "autoplay":true,"arrows":false,"dots":true,"infinite": true,"responsive":[{"breakpoint": 992,"settings": {"slidesToShow":2}},{"breakpoint": 768,"settings": {"slidesToShow": 2,"autoplay":true}},{"breakpoint": 576,"settings": {"slidesToShow": 1,"arrows":false,"dots":true,"autoplay":true}}]}'>
              <?php if (!empty($blog_posts)): ?>
              <?php foreach ($blog_posts as $row): ?>
              <div class="item py-4" data-animate="fadeInUp">
                <div class="card border-0 shadow-xxs-3">
                  <div class="position-relative d-flex align-items-end card-img-top">
                    <a href="blog.php" class="hover-shine">
                      <img src="images/<?php echo $row['image']; ?>"
                                     alt="<?php echo $row['title']; ?>">
                    </a>
                  </div>
                  <div class="card-body px-5 pt-3 pb-5">
                    <p class="mb-1 fs-13"><?php echo format_date($row['created_at'], 'M d, Y'); ?></p>
                    <h3 class="fs-18 text-heading lh-15 mb-1">
                      <a href="blog.php" class="text-heading hover-primary"><?php echo $row['title']; ?></a>
                    </h3>
                    <a class="text-heading font-weight-500" href="blog.php">Read more <i
                                    class="far fa-long-arrow-right text-primary ml-1"></i></a>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Report Section -->
      <section class="py-8 bg-yellow">
        <div class="container">
          <h3 class="mb-2 fs-22 lh-15 text-heading">Get first hand information by reading our curated property reports. </h3>
          <p class="mb-3 pb-1">Pricing, market, location reports that help you make the right decision for your home or investment property.</p>
          <a href="blog.php" class="btn btn-lg btn-dark rmb-8 mb-lg-0">Learn more
                <i class="far fa-long-arrow-right ml-1"></i>
              </a>
        </div>
      </section>

      <!-- Partners Section -->
      <section class="bg-patten-05">
        <div class="container container-xxl pt-10 pb-8">
          <h2 class="text-dark text-center mxw-751 fs-26 lh-184 px-md-8">
            Trusted by the biggest local and international institutions</h2>
          <img class="mxw-180 d-block mx-auto mt-4 mb-1" src="images/newimages/line-01.png" alt="">
          <div class="py-lg-8 py-6">
            <div class="slick-slider mx-0 partners"
           data-slick-options='{"slidesToShow": 6, "autoplay":true,"dots":false,"arrows":false,"responsive":[{"breakpoint": 1200,"settings": {"slidesToShow":4}},{"breakpoint": 992,"settings": {"slidesToShow":3}},{"breakpoint": 768,"settings": {"slidesToShow": 3}},{"breakpoint": 576,"settings": {"slidesToShow": 2}}]}'>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/british-high-commission.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/british-high-commission.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/dalberg.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/dalberg.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/usa.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/usa.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/giz.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/giz.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/dallaire.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/dallaire.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/global-health.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/global-health.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/gva.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/gva.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/ihs.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/ihs.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/kics.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/kics.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/un-eca.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/un-eca.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/vipp.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/vipp.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/wfp.jpg"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/wfp.jpg" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/mastercard.png"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 1">
                  <img src="images/newimages/mastercard.png" alt="Partner 1"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/sa-embassy.png"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 2">
                  <img src="images/newimages/sa-embassy.png" alt="Partner 2"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/wildlife.png"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 3">
                  <img src="images/newimages/wildlife.png" alt="Partner 3"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item">
                  <img src="images/newimages/british-council.png" alt=""
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item position-relative hover-change-image">
                  <img src="images/newimages/care.png"
                 class="hover-image position-absolute pos-fixed-top" alt="Partner 5">
                  <img src="images/newimages/care.png" alt="Partner 5"
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item">
                  <img src="images/newimages/canal.png" alt=""
                 class="image">
                </a>
              </div>

              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item">
                  <img src="images/newimages/save-the-children.png" alt=""
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item">
                  <img src="images/newimages/unhcr.png" alt=""
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item">
                  <img src="images/newimages/coat-of-arms.png" alt=""
                 class="image">
                </a>
              </div>
              <div class="box d-flex align-items-center justify-content-center" data-animate="fadeInUp">
                <a href="#" class="item">
                  <img src="images/newimages/one-acre-fund.png" alt=""
                 class="image">
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Agents Section -->
      <section class="pt-7 pb-10 pb-xl-6 agents-section bg-primary">
        <div class="container container-xxl">
          <p class="text-yellow letter-spacing-263 text-uppercase lh-186 text-center mb-0">Meet our team</p>
          <h2 class="text-center text-white lh-1625 mxw-940 mb-1">
            Meet our agents. Experienced professionals with local expertise to help sell your home.
          </h2>
           <img class="mxw-180 d-block mx-auto mt-4 mb-1" src="images/newimages/line-01.png" alt="">
          <div class="slick-slider slick-dots-mt-0 item-nth-2-active-lg"
         data-slick-options='{"slidesToShow": 3, "dots":false,"arrows":false,"responsive":[{"breakpoint": 1600,"settings": {"slidesToShow":3,"dots":false}},{"breakpoint": 1200,"settings": {"slidesToShow":4,"dots":false}},{"breakpoint": 992,"settings": {"slidesToShow":3 ,"dots":false}},{"breakpoint": 768,"settings": {"slidesToShow": 2 ,"dots":false}},{"breakpoint": 576,"settings": {"slidesToShow": 1,"dots":false}}]}'>
            
            <?php if (!empty($agents)): ?>
            <?php foreach ($agents as $row): ?> 
            <div class="py-8">
              <div class="card border-lg-0 shadow-hover-xs-4 hover-change-image" data-animate="flipInX">
                <div class="card-body text-center pt-6 pb-3 px-3">
                  <a href="agent-details.php?id=<?php echo $row['id']; ?>" class="d-inline-block mb-2">
                    <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" style="width:120px; height:120px; object-fit:cover; border-radius:50%;">
                  </a>
                  <a href="agent-details.php?id=<?php echo $row['id']; ?>"
               class="d-block fs-16 lh-1 text-dark mb-0 font-weight-500 hover-primary team-member"><?php echo $row['name']; ?></a>
                  <p class="mb-2 fs-13 text-danger"><?php echo $row['position']; ?></p>
                  
                </div>
               
              </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            
          </div>
          <div class="text-center pb-5" data-animate="fadeInLeft">
            <a href="team.php" class="btn btn-lg btn-primary rmb-8 mb-lg-0">View our team
              <i class="far fa-long-arrow-right ml-1"></i>
            </a>
          </div>
        </div>
      </section>

      <!-- Property Types -->
      <section class="pt-lg-12 pb-lg-15 py-11">
        <div class="container container-xxl">
          <h2 class="text-heading">Property of all types, for all your needs</h2>
          <span class="heading-divider"></span>
          <p class="mb-7">We offer a large variety of property to suit your residential, commercial and industrial needs.</p>
          <div class="slick-slider mx-n2"
         data-slick-options='{"slidesToShow": 5,"arrows":false, "autoplay":false,"dots":false,"responsive":[{"breakpoint": 1200,"settings": {"slidesToShow":3}},{"breakpoint": 992,"settings": {"slidesToShow":3}},{"breakpoint": 768,"settings": {"slidesToShow": 2}},{"breakpoint": 576,"settings": {"slidesToShow": 1}}]}'>
            <div class="box px-2" data-animate="fadeInUp">
              <div class="card text-white bg-overlay-gradient-8 hover-zoom-in">
                <img src="images/newimages/townhouse.jpg" class="card-img" alt="Town Houses">
                <div class="card-img-overlay d-flex justify-content-end flex-column p-4">
                  <h2 class="card-title mb-0 fs-20 lh-182"><a href="search-results.php?label=&category=&type=3&district=&max_price=&beds=&baths=&ref="
                                                        class="text-white">Town Houses</a></h2>
                </div>
              </div>
            </div>
            <div class="box px-2" data-animate="fadeInUp">
              <div class="card text-white bg-overlay-gradient-8 hover-zoom-in">
                <img src="images/newimages/house.jpg" class="card-img" alt="Houses">
                <div class="card-img-overlay d-flex justify-content-end flex-column p-4">
                  <h2 class="card-title mb-0 fs-20 lh-182"><a href="search-results.php?label=&category=&type=2&district=&max_price=&beds=&baths=&ref="
                                                        class="text-white">Houses</a></h2>
                </div> 
              </div>
            </div>
            <div class="box px-2" data-animate="fadeInUp">
              <div class="card text-white bg-overlay-gradient-8 hover-zoom-in">
                <img src="images/newimages/commercial.jpg" class="card-img" alt="Commercial">
                <div class="card-img-overlay d-flex justify-content-end flex-column p-4">
                  <h2 class="card-title mb-0 fs-20 lh-182"><a href="search-results.php?label=&category=2&type=&district=&max_price=&beds=&baths=&ref="
                                                        class="text-white">Commercial</a></h2>
                </div>
              </div>
            </div>
            <div class="box px-2" data-animate="fadeInUp">
              <div class="card text-white bg-overlay-gradient-8 hover-zoom-in">
                <img src="images/newimages/land.jpg" class="card-img" alt="Vacant Land">
                <div class="card-img-overlay d-flex justify-content-end flex-column p-4">
                  <h2 class="card-title mb-0 fs-20 lh-182"><a href="search-results.php?label=&category=4&type=&district=&max_price=&beds=&baths=&ref="
                                                        class="text-white">Vacant Land</a></h2>
                </div>
              </div>
            </div>
            <div class="box px-2" data-animate="fadeInUp">
              <div class="card text-white bg-overlay-gradient-8 hover-zoom-in">
                <img src="images/newimages/apartment.jpg" class="card-img" alt="Apartments">
                <div class="card-img-overlay d-flex justify-content-end flex-column p-4">
                  <h2 class="card-title mb-0 fs-20 lh-182"><a href="search-results.php?label=&category=&type=1&district=&max_price=&beds=&baths=&ref="
                                                        class="text-white">Apartments</a></h2>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Banner -->
      <section class="pt-11 pb-13 bg-cover" style="background-image: url(images/newimages/banner-1.jpg)">
        <div class="container">
          <form class="text-center" data-animate="fadeInUp">
            <h2 class="fs-34 font-weight-normal lh-141 text-white mxw-740">
             Be the first to view the latest developments and projects under offer. 
            </h2>
            <img class="mxw-180 d-block mx-auto mt-4 mb-1" src="images/newimages/line-01.png" alt="">
            <div class="text-center mt-8">
                <a class="btn btn-primary btn-lg" href="developments.php">View Developments</a>
            </div>
           
          </form>
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

    <script>
    $(document).ready(function() {
        // View Details Click
        $(document).on('click', '.view-details-btn', function(e) {
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
                        $('#inquiryResponse').html('<div class="alert alert-success mt-3">Message sent successfully!</div>');
                        $('#modalInquiryForm')[0].reset();
                    } else {
                        $('#inquiryResponse').html('<div class="alert alert-danger mt-3">' + res.message + '</div>');
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
</html>
