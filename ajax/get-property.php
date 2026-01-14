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

?>
<div class="mb-4">
    <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($property['title']); ?>" class="img-fluid rounded-lg w-100" style="height: 400px; object-fit: cover;">
</div>
<h2 class="fs-22 mb-2"><?php echo htmlspecialchars($property['title']); ?></h2>
<p class="fs-18 text-primary font-weight-bold mb-3"><?php echo $price; ?></p>
<div class="mb-4">
    <span class="badge badge-yellow mr-2 p-2 px-3"><?php echo htmlspecialchars($property['status']); ?></span>
    <span class="text-gray-light fs-15"><i class="fas fa-map-marker-alt mr-2 text-primary"></i><?php echo htmlspecialchars($property['location']); ?></span>
</div>

<div class="row mb-4">
    <?php if ($property['bedrooms']): ?>
    <div class="col-4 mb-2">
        <div class="d-flex align-items-center text-gray">
            <i class="fas fa-bed fs-20 mr-2 text-primary"></i>
            <span><?php echo $property['bedrooms']; ?> Beds</span>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($property['bathrooms']): ?>
    <div class="col-4 mb-2">
        <div class="d-flex align-items-center text-gray">
            <i class="fas fa-bath fs-20 mr-2 text-primary"></i>
            <span><?php echo $property['bathrooms']; ?> Baths</span>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($property['size_sqm']): ?>
    <div class="col-4 mb-2">
        <div class="d-flex align-items-center text-gray">
            <i class="fas fa-ruler-combined fs-20 mr-2 text-primary"></i>
            <span><?php echo $property['size_sqm']; ?> m²</span>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="mb-4">
    <h5 class="mb-2">Description</h5>
    <p class="text-muted"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
</div>

<div>
    <h5 class="mb-2">Features</h5>
    <ul class="list-unstyled row">
        <?php 
        // Assuming features are not stored as JSON but maybe in the description or separate table.
        // For now, listing valid fields.
        if ($property['garage']) echo '<li class="col-md-6 mb-1"><i class="fas fa-check text-primary mr-2"></i>Garage (' . $property['garage'] . ')</li>';
        if ($property['year_built']) echo '<li class="col-md-6 mb-1"><i class="fas fa-check text-primary mr-2"></i>Built in ' . $property['year_built'] . '</li>';
        ?>
    </ul>
</div>
