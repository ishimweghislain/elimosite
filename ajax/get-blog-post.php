<?php
require_once '../includes/config.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Post ID is required.</div>';
    exit;
}

// In this system, we usually use ID for AJAX details
$id = (int)$_GET['id'];
$post = get_record('blog_posts', $id);

if (!$post) {
    echo '<div class="alert alert-danger">Blog post not found.</div>';
    exit;
}

$image = !empty($post['image']) ? 'images/' . $post['image'] : 'images/blog-details.jpg';
$date = format_date($post['created_at'], 'F d, Y');
?>
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .blog-modal-content, .blog-modal-content * {
            visibility: visible;
        }
        .blog-modal-content {
            position: absolute;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .d-print-none {
            display: none !important;
        }
    }
</style>

<div class="blog-modal-content">
    <div class="mb-4 overflow-hidden rounded-lg">
        <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="img-fluid w-100" style="height: 400px; object-fit: cover;">
    </div>
    
    <div class="d-flex align-items-center mb-3 text-muted fs-14">
        <span class="badge badge-primary mr-3 px-3 py-2 fs-13"><?php echo htmlspecialchars($post['category']); ?></span>
        <span><i class="far fa-calendar-alt mr-2"></i><?php echo $date; ?></span>
        <span class="ml-3"><i class="far fa-user mr-2"></i>Admin</span>
        <button onclick="window.print();" class="btn btn-outline-primary btn-sm ml-auto d-print-none">
            <i class="fas fa-print mr-1"></i> Print PDF
        </button>
    </div>
    
    <h2 class="fs-32 text-dark font-weight-600 mb-4"><?php echo htmlspecialchars($post['title']); ?></h2>
    
    <div class="blog-main-content fs-16 lh-18 line-height-2 text-gray-light mb-5">
        <?php echo nl2br($post['content']); ?>
    </div>
    
    <?php if (!empty($post['tags'])): ?>
    <div class="pt-4 border-top d-flex align-items-center flex-wrap">
        <h5 class="fs-16 mb-0 mr-3">Tags:</h5>
        <?php 
        $tags = explode(',', $post['tags']);
        foreach($tags as $tag): 
        ?>
            <span class="badge badge-light border mr-2 p-2 fs-12"><?php echo trim(htmlspecialchars($tag)); ?></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="mt-5 pt-4 border-top">
        <div class="d-flex align-items-center">
            <h5 class="fs-16 mb-0 mr-4">Share this article:</h5>
            <ul class="list-inline mb-0">
                <li class="list-inline-item mr-3">
                    <a href="#" class="text-muted hover-primary fs-18"><i class="fab fa-facebook-f"></i></a>
                </li>
                <li class="list-inline-item mr-3">
                    <a href="#" class="text-muted hover-primary fs-18"><i class="fab fa-twitter"></i></a>
                </li>
                <li class="list-inline-item mr-3">
                    <a href="#" class="text-muted hover-primary fs-18"><i class="fab fa-linkedin-in"></i></a>
                </li>
                <li class="list-inline-item">
                    <a href="#" class="text-muted hover-primary fs-18"><i class="fas fa-link"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>
