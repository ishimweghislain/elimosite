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
?>
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .property-modal-details, .property-modal-details * {
            visibility: visible;
        }
        .property-modal-details {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
        }
        .d-print-none {
            display: none !important;
        }
        .property-modal-details img {
            max-height: 500px !important;
            width: 100% !important;
        }
    }
</style>

<div class="property-modal-details overflow-y-auto" style="max-height: 80vh;">
    <div class="mb-4">
        <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" class="img-fluid rounded-lg w-100" style="height: 400px; object-fit: cover;">
    </div>
    
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
    
    <div class="mb-4">
        <span class="badge badge-yellow mr-2 p-2 px-3"><?php echo htmlspecialchars($property['status']); ?></span>
        <span class="text-gray-light fs-15"><i class="fas fa-map-marker-alt mr-2 text-primary"></i><?php echo htmlspecialchars($property['location']); ?></span>
    </div>

    <!-- Key Specs -->
    <div class="row g-2 mb-4">
        <?php if ($property['bedrooms']): ?>
        <div class="col-4">
            <div class="p-2 border rounded text-center">
                <i class="fas fa-bed text-primary d-block mb-1"></i>
                <span class="fs-13"><?php echo $property['bedrooms']; ?> Beds</span>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($property['bathrooms']): ?>
        <div class="col-4">
            <div class="p-2 border rounded text-center">
                <i class="fas fa-bath text-primary d-block mb-1"></i>
                <span class="fs-13"><?php echo $property['bathrooms']; ?> Baths</span>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($property['size_sqm']): ?>
        <div class="col-4">
            <div class="p-2 border rounded text-center">
                <i class="fas fa-ruler-combined text-primary d-block mb-1"></i>
                <span class="fs-13"><?php echo (int)$property['size_sqm']; ?> m²</span>
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
                    <li class="mb-2 d-flex justify-content-between"><strong>Stories:</strong> <span><?php echo $property['stories']; ?></span></li>
                <?php endif; ?>
                <?php if ($property['garage']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Garage:</strong> <span><?php echo $property['garage']; ?></span></li>
                <?php endif; ?>
                <?php if ($property['furnished']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Furnished:</strong> <span><?php echo htmlspecialchars($property['furnished']); ?></span></li>
                <?php endif; ?>
                <?php if ($property['multi_family']): ?>
                    <li class="mb-2 d-flex justify-content-between"><strong>Multi-family:</strong> <span><?php echo htmlspecialchars($property['multi_family']); ?></span></li>
                <?php endif; ?>
                <?php if ($property['plot_size']): ?>
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
                    <li class="mb-2 d-flex justify-content-between"><strong>Ideal for:</strong> <span><?php echo htmlspecialchars($property['ideal_for']); ?></span></li>
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
