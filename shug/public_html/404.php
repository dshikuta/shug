<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Page Not Found — Shugia Safaris';
$pageDesc  = 'The page you are looking for could not be found.';
$pageKey   = '404';
http_response_code(404);
include __DIR__ . '/includes/header.php';
?>

<main>
  <section class="page-section" style="min-height:60vh;display:grid;place-items:center;text-align:center;">
    <div class="container">
      <h1 style="font-family:var(--font-heading);font-size:clamp(5rem,15vw,10rem);color:var(--accent);margin:0;">404</h1>
      <h2 style="margin-bottom:16px;">You've wandered off the trail</h2>
      <p style="color:var(--gray-text);max-width:520px;margin:0 auto 32px;">
        The page you're looking for has moved or never existed. But the safari goes on.
      </p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="/home" class="btn btn-accent">Back to Home</a>
        <a href="/packages" class="btn">Browse Packages</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>