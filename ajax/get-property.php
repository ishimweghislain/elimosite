<?php
require_once '../includes/config.php';

if (!isset($_GET['id'])) {
    echo 'Property ID is required.';
    exit;
}

$id = (int)$_GET['id'];
$property = get_property($id);

if (!$property) {
    echo 'Property not found.';
    exit;
}

// Format price
$price = format_price($property['price']);

// Image
$image = !empty($property['image_main']) ? 'images/' . $property['image_main'] : 'images/property-placeholder.jpg';

// Decoding JSON strings
$features = json_decode($property['features'] ?? '[]', true);
if (!is_array($features)) $features = [];

$amenities = json_decode($property['amenities'] ?? '[]', true);
if (!is_array($amenities)) $amenities = [];

// Development Check
$development = null;
if (!empty($property['development_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM developments WHERE id = ?");
    $stmt->execute([$property['development_id']]);
    $development = $stmt->fetch();
}
?>
<style>
    @media print {
        body * {
            visibility: hidden !important;
        }
        .modal-dialog {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .property-modal-details, .property-modal-details * {
            visibility: visible !important;
        }
        .property-modal-details {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }
        .d-print-none {
            display: none !important;
        }
        .property-modal-details img {
            max-height: 400px !important;
            width: auto !important;
            margin: 0 auto !important;
            display: block !important;
        }
        .list-unstyled li {
            page-break-inside: avoid;
        }
    }
    .modal-property-slider .slider-item img {
        height: 400px;
        width: 100%;
        object-fit: cover;
    }
    .slider-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        background: rgba(255,255,255,0.9);
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
    .slider-nav-btn:hover {
        background: #28a745;
        color: #fff;
        transform: translateY(-50%) scale(1.1);
    }
    .slider-nav-btn i {
        font-size: 18px;
    }
    .slider-prev { left: 15px; }
    .slider-next { right: 15px; }
</style>

<div class="property-modal-details overflow-y-auto" style="max-height: 80vh;">
    <?php 
    $sub_images = json_decode($property['images'] ?? '[]', true);
    if (!is_array($sub_images)) $sub_images = [];
    ?>
    <div class="mb-4 position-relative">
        <div class="modal-property-slider">
            <div class="slider-item">
                <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" class="img-fluid rounded-lg w-100">
            </div>
            <?php foreach ($sub_images as $sub_img): ?>
                <div class="slider-item">
                    <img src="images/<?php echo $sub_img; ?>" alt="Sub image" class="img-fluid rounded-lg w-100">
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($sub_images)): ?>
            <button class="slider-nav-btn slider-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="slider-nav-btn slider-next"><i class="fas fa-chevron-right"></i></button>
        <?php endif; ?>
    </div>

    <!-- YouTube Video Tour & Instagram Links -->
    <?php if (!empty($property['youtube_url']) || !empty($property['instagram_url'])): ?>
    <div class="row g-3 mb-4">
        <?php if (!empty($property['youtube_url'])): ?>
        <div class="col-12 mb-4">
            <h5 class="fs-18 mb-3 text-heading font-weight-600">
                <i class="fab fa-youtube text-danger mr-2"></i>Property Video Tour
            </h5>
            <?php
            // Extract YouTube video ID
            $youtube_url = $property['youtube_url'];
            $video_id = '';
            if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $youtube_url, $id)) {
                $video_id = $id[1];
            } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $youtube_url, $id)) {
                $video_id = $id[1];
            } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $youtube_url, $id)) {
                $video_id = $id[1];
            }
            
            if ($video_id): ?>
                <div class="video-container rounded-lg overflow-hidden shadow-sm bg-black mb-3" style="position: relative; padding-bottom: 56.25%; height: 0;">
                    <iframe 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                        src="https://www.youtube.com/embed/<?php echo htmlspecialchars($video_id); ?>?rel=0&modestbranding=1" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
                <a href="<?php echo fix_url($property['youtube_url']); ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="fab fa-youtube mr-1"></i> Watch on YouTube
                </a>
            <?php else: ?>
                <a href="<?php echo fix_url($property['youtube_url']); ?>" target="_blank" class="d-flex align-items-center p-3 rounded-lg bg-red-opacity-01 border border-red text-red text-decoration-none hover-shine">
                    <div class="icon-circle bg-red text-white mr-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
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
        <div class="col-md-6 mb-3">
            <a href="<?php echo fix_url($property['instagram_url']); ?>" target="_blank" class="d-flex align-items-center p-3 rounded-lg bg-primary-opacity-01 border border-primary text-primary text-decoration-none hover-shine">
                <div class="icon-circle bg-primary text-white mr-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                    <i class="fab fa-instagram"></i>
                </div>
                <div>
                    <h5 class="fs-15 mb-0 font-weight-600">View on Instagram</h5>
                    <span class="fs-12 opacity-07">Property Reel/Post</span>
                </div>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-2">
        <h2 class="fs-22 mb-0"><?php echo htmlspecialchars($property['title']); ?></h2>
        <div class="d-flex align-items-center">
            <button onclick="window.print();" class="btn btn-outline-primary btn-sm mr-2 d-print-none">
                <i class="fas fa-print mr-1"></i> Print PDF
            </button>
            <?php if (!empty($property['prop_id'])): ?>
                <span class="badge badge-light border text-muted">ID: <?php echo htmlspecialchars($property['prop_id']); ?></span>
            <?php endif; ?>
        </div>
    </div>
    
    <p class="fs-18 text-primary font-weight-bold mb-3"><?php echo $price; ?></p>
    
    <div class="mb-4 d-flex align-items-center flex-wrap">
        <span class="badge badge-yellow mr-2 p-2 px-3 mb-1"><?php echo htmlspecialchars($property['status']); ?></span>
        <span class="text-gray-light fs-15 mb-1 mr-3"><i class="fas fa-map-marker-alt mr-2 text-primary"></i><?php echo htmlspecialchars($property['location']); ?></span>
        
        <?php if ($development): ?>
        <a href="development-detail.php?id=<?php echo $development['id']; ?>" class="badge border border-primary text-primary p-2 px-3 mb-1 text-decoration-none hover-primary">
            <i class="fas fa-building mr-1"></i> Part of <?php echo htmlspecialchars($development['title']); ?>
        </a>
        <?php endif; ?>
    </div>
    <!-- Key Specs -->
    <div class="row g-2 mb-4">
        <?php if ($property['bedrooms']): ?>
        <div class="col-4">
            <div class="p-2 border rounded text-center">
                <svg class="icon icon-bedroom fs-24 text-primary d-block mb-1 mx-auto" style="height: 24px; width: 24px;">
                    <use xlink:href="#icon-bedroom"></use>
                </svg>
                <span class="fs-13"><?php echo $property['bedrooms']; ?> Beds</span>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($property['bathrooms']): ?>
        <div class="col-4">
            <div class="p-2 border rounded text-center">
                <svg class="icon icon-shower fs-24 text-primary d-block mb-1 mx-auto" style="height: 24px; width: 24px;">
                    <use xlink:href="#icon-shower"></use>
                </svg>
                <span class="fs-13"><?php echo $property['bathrooms']; ?> Baths</span>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($property['size_sqm']) && $property['size_sqm'] > 0): ?>
        <div class="col-4">
            <div class="p-2 border rounded text-center">
                <svg class="icon icon-square fs-24 text-primary d-block mb-1 mx-auto" style="height: 24px; width: 24px;">
                    <use xlink:href="#icon-square"></use>
                </svg>
                <span class="fs-13">Build: <?php echo (int)$property['size_sqm']; ?>m²</span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h5 class="fs-16 mb-3 border-bottom pb-2">Additional Details</h5>
            <ul class="list-unstyled mb-0 fs-14">
                <?php if ($property['property_type']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Property type:</strong> <span><?php echo htmlspecialchars($property['property_type']); ?></span></li>
                <?php endif; ?>
                <?php if ($property['stories']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Floors:</strong> <span><?php echo $property['stories']; ?></span></li>
                <?php endif; ?>
                <?php if ($property['garage']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Parking Space:</strong> <span><?php echo $property['garage']; ?></span></li>
                <?php endif; ?>
                <?php if ($property['furnished']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Furnished:</strong> <span><?php echo htmlspecialchars($property['furnished']); ?></span></li>
                <?php endif; ?>
                <?php if ($property['multi_family']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Multi-family:</strong> <span><?php echo htmlspecialchars($property['multi_family']); ?></span></li>
                <?php endif; ?>
                <?php if (!empty($property['plot_size']) && $property['plot_size'] > 0): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Plot Size:</strong> <span><?php echo (int)$property['plot_size']; ?> m²</span></li>
                <?php endif; ?>
                <?php if ($property['zoning']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Zoning:</strong> <span><?php echo htmlspecialchars($property['zoning']); ?></span></li>
                <?php endif; ?>
                <?php if ($property['year_built']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Year built:</strong> <span><?php echo $property['year_built']; ?></span></li>
                <?php endif; ?>
                <?php if ($property['views']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Views:</strong> <span><?php echo htmlspecialchars($property['views']); ?></span></li>
                <?php endif; ?>
                <?php if ($property['ideal_for']): ?>
                    <li class="mb-2 d-flex justify-content-between">
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
        <div class="col-md-6">
            <h5 class="fs-16 mb-3 border-bottom pb-2">Description</h5>
            <p class="text-muted fs-14 lh-16"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <?php if (!empty($features)): ?>
        <div class="col-md-6">
            <h5 class="fs-16 mb-3 border-bottom pb-2">Property Features</h5>
            <ul class="list-unstyled row no-gutters fs-13">
                <?php foreach ($features as $feature): ?>
                    <li class="col-6 mb-2"><i class="fas fa-check text-primary mr-2"></i><?php echo htmlspecialchars($feature); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($amenities)): ?>
        <div class="col-md-6">
            <h5 class="fs-16 mb-3 border-bottom pb-2">Amenities</h5>
            <ul class="list-unstyled row no-gutters fs-13">
                <?php foreach ($amenities as $amenity): ?>
                    <li class="col-6 mb-2"><i class="fas fa-star text-warning mr-2"></i><?php echo htmlspecialchars($amenity); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($property['proximity'])): ?>
    <div class="p-3 bg-gray-01 rounded-lg">
        <h5 class="fs-15 mb-2"><i class="fas fa-map-marked-alt mr-2 text-primary"></i>In close proximity to:</h5>
        <p class="mb-0 fs-13 text-muted"><?php echo htmlspecialchars($property['proximity']); ?></p>
    </div>
    <?php endif; ?>
</div>
