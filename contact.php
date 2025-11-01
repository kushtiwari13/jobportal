<?php
$title = 'Contact — ProJobs';
require __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    flash('success', 'Thanks for your message. We will get back to you.');
}
?>
<section class="py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <h2 class="mb-3">Contact</h2>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <div class="mb-3">
          <label class="form-label">Your Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea class="form-control" rows="5" name="message" required></textarea>
        </div>
        <button class="btn btn-primary rounded-pill" type="submit">Send</button>
      </form>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php';

