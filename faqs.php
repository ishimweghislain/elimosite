<?php
require_once 'includes/config.php';

// Get all FAQs
$faqs = get_faqs();
// Group FAQs by category
$grouped_faqs = [];
foreach ($faqs as $faq) {
    $cat = !empty($faq['category']) ? ucfirst($faq['category']) : 'General';
    $grouped_faqs[$cat][] = $faq;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo get_setting('site_description', 'Elimo Real Estate'); ?>">
    <title>Frequently Asked Questions - <?php echo get_setting('site_name'); ?></title>
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="vendors/fontawesome-pro-5/css/all.css">
    <link rel="stylesheet" href="css/themes.css">
    <link rel="icon" href="images/favicon.png">
    <style>
      .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: var(--primary);
        box-shadow: none;
      }
      .accordion-item {
        border: 1px solid #edf2f7;
        margin-bottom: 10px;
        border-radius: 8px !important;
        overflow: hidden;
      }
      .accordion-button {
        padding: 1.25rem;
        font-weight: 600;
        color: #2D3748;
      }
      .accordion-button:focus {
        box-shadow: none;
      }
      .faq-category-title {
        position: relative;
        padding-bottom: 10px;
        margin-bottom: 25px;
        font-weight: 700;
        color: #1a202c;
      }
      .faq-category-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background: var(--primary);
      }
    </style>
  </head>
  <body>
    <?php include 'header.php'; // I'll assume header is consistent or I'll fix it if needed ?>
    
    <main id="content">
      <section class="pb-6 pt-6 pt-lg-14 page-title shadow bg-primary text-center">
        <div class="container pt-5">
          <h1 class="fs-30 lh-1 mb-0 text-white font-weight-600">Frequently Asked Questions</h1>
          <p class="text-white mt-3 opacity-08">How can we help you? Find answers to commonly asked questions.</p>
        </div>
      </section>

      <section class="py-12 bg-gray-01">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-9">
              <?php if (!empty($grouped_faqs)): ?>
                <?php foreach ($grouped_faqs as $category => $category_faqs): ?>
                  <div class="mb-10">
                    <h3 class="faq-category-title fs-22"><?php echo htmlspecialchars($category); ?></h3>
                    <div class="accordion" id="accordion-<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                      <?php foreach ($category_faqs as $index => $faq): ?>
                        <div class="accordion-item bg-white">
                          <h2 class="accordion-header" id="heading-<?php echo $faq['id']; ?>">
                            <button class="accordion-button collapsed fs-16" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $faq['id']; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $faq['id']; ?>">
                              <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                          </h2>
                          <div id="collapse-<?php echo $faq['id']; ?>" class="collapse" aria-labelledby="heading-<?php echo $faq['id']; ?>" data-parent="#accordion-<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                            <div class="accordion-body text-muted lh-18">
                              <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-center py-10">
                  <i class="fal fa-question-circle fs-large-5 text-muted mb-4 d-block"></i>
                  <h3>No FAQs available at the moment.</h3>
                  <p>Please check back later or contact us directly.</p>
                </div>
              <?php endif; ?>
              
              <div class="card border-0 shadow-sm mt-10 bg-white p-8 text-center">
                <h4 class="mb-4">Still have questions?</h4>
                <p class="text-muted mb-6">If you can't find your answer in our FAQ, you can always contact us. We will answer to you shortly!</p>
                <div>
                  <a href="contact-us.php" class="btn btn-primary px-8">Contact Us</a>
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
    <script src="js/theme.js"></script>
  </body>
</html>
