<div class="right-rail">
  <div class="rail-card">
    <div class="eyebrow">Latest picks</div>
    <ul class="rail-list">
      <?php foreach (latest_jobs(6) as $j): ?>
        <li>
          <a href="<?php echo e(base_url()); ?>job/<?php echo (int)$j['id']; ?>/<?php echo e(slugify($j['title'])); ?>">
            <span class="rail-title"><?php echo e($j['title']); ?></span>
            <span class="rail-meta"><?php echo e($j['company']); ?> · <?php echo e($j['location'] ?: 'Remote'); ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="rail-card">
    <div class="eyebrow">Quick filters</div>
    <div class="chip-row">
      <?php foreach (['Backend','Frontend','QA','DevOps','Data','Security','Cloud','Mobile'] as $chip): ?>
        <a class="chip" href="<?php echo e(base_url()); ?>?q=<?php echo urlencode($chip); ?>"><?php echo e($chip); ?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="rail-card">
    <div class="eyebrow">Newsletter</div>
    <p class="rail-text">Weekly headline of top tech roles—no spam.</p>
    <form class="newsletter-form" action="#" method="post">
      <input type="email" name="email" placeholder="you@example.com">
      <button type="submit">Subscribe</button>
    </form>
  </div>
</div>
