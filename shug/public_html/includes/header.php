<?php
if (!function_exists('csrf_token')) {
  require_once __DIR__ . '/functions.php';
}
if (!isset($pageTitle)) $pageTitle = 'Shugia Safaris — Authentic African Adventures';
if (!isset($pageDesc))  $pageDesc  = 'Tailor-made African safaris across Kenya since 2010.';
if (!isset($pageKey))   $pageKey   = 'home';

function nav_link($href, $key, $label, $active = false) {
  $cls = $active ? ' class="is-active"' : '';
  return "<a href=\"$href\" data-i18n=\"$key\"$cls>$label</a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= esc($pageDesc) ?>">
  <title><?= esc($pageTitle) ?></title>

  <!-- CSRF token for AJAX requests -->
  <meta name="csrf-token" content="<?= esc(csrf_token()) ?>">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Shugia Safaris">
  <meta property="og:title" content="<?= esc($pageTitle) ?>">
  <meta property="og:description" content="<?= esc($pageDesc) ?>">
  <meta property="og:url" content="https://<?= esc($_SERVER['HTTP_HOST'] ?? 'shugiasafaris.com') ?>/<?= esc($pageKey) ?>">
  <meta property="og:image" content="https://<?= esc($_SERVER['HTTP_HOST'] ?? 'shugiasafaris.com') ?>/images/og-home.jpg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/nav.css">
  <link rel="stylesheet" href="/css/reviews.css">

  <link rel="icon" type="image/png" href="/images/logo-compact.png">
</head>
<body data-page="<?= esc($pageKey) ?>">

<header class="site-header">
  <div class="container header-inner">
    <a href="/" class="brand-logo" aria-label="Shugia Safaris — Home">
      <img src="/images/logo-primary.png" alt="Shugia Safaris" class="brand-logo-img" width="400" height="100" fetchpriority="high">
    </a>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="navLinks">
      <i class="fas fa-bars"></i>
    </button>
    <nav class="nav-links" id="navLinks" aria-label="Main menu">
      <ul class="nav-menu">
        <li class="nav-item<?= $pageKey==='home'?' is-active':'' ?>"><a href="/home" data-i18n="nav_home">Home</a></li>

        <li class="nav-item has-submenu<?= $pageKey==='destinations'?' is-active':'' ?>">
          <a href="/destinations" data-i18n="nav_dest" aria-haspopup="true" aria-expanded="false">Destinations <i class="fas fa-chevron-down nav-caret"></i></a>
          <ul class="submenu">
            <li><a href="/destinations">All Destinations</a></li>
            <li><a href="/destinations#mara">Maasai Mara</a></li>
            <li><a href="/destinations#amboseli">Amboseli</a></li>
            <li><a href="/destinations#diani">Diani Beach</a></li>
            <li><a href="/destinations#watamu">Watamu Marine</a></li>
            <li><a href="/destinations#gede">Gede Ruins</a></li>
          </ul>
        </li>

        <li class="nav-item has-submenu<?= $pageKey==='packages'?' is-active':'' ?>">
          <a href="/packages" data-i18n="nav_packages" aria-haspopup="true" aria-expanded="false">Packages <i class="fas fa-chevron-down nav-caret"></i></a>
          <ul class="submenu">
            <li><a href="/packages">All Packages</a></li>
            <li><a href="/packages?filter=big5">Big Five</a></li>
            <li><a href="/packages?filter=combo">Bush & Beach</a></li>
            <li><a href="/packages?filter=marine">Marine</a></li>
            <li><a href="/packages?filter=birding">Birding</a></li>
            <li><a href="/packages?filter=beach">Beach</a></li>
            <li><a href="/packages?filter=luxury">Luxury</a></li>
          </ul>
        </li>

        <li class="nav-item has-submenu<?= $pageKey==='why-us'?' is-active':'' ?>">
          <a href="/why-us" data-i18n="nav_why" aria-haspopup="true" aria-expanded="false">Why Us <i class="fas fa-chevron-down nav-caret"></i></a>
          <ul class="submenu">
            <li><a href="/why-us">Why Travel With Us</a></li>
            <li><a href="/why-us#advantage">Our Guarantee</a></li>
            <li><a href="/why-us#stats">Our Track Record</a></li>
          </ul>
        </li>

        <li class="nav-item<?= $pageKey==='faq'?' is-active':'' ?>"><a href="/faq" data-i18n="nav_faq">FAQ</a></li>
        <li class="nav-item<?= $pageKey==='contact'?' is-active':'' ?>"><a href="/contact" data-i18n="nav_contact">Contact</a></li>
      </ul>
    </nav>
    <div class="header-actions">
      <div class="tool-pill">
        <i class="fas fa-coins"></i>
        <select id="currencySelect" aria-label="Currency">
          <option value="USD">USD $</option><option value="EUR">EUR €</option>
          <option value="KES">KES KSh</option><option value="GBP">GBP £</option>
          <option value="JPY">JPY ¥</option>
        </select>
      </div>
      <div class="tool-pill">
        <i class="fas fa-globe"></i>
        <select id="languageSelect" aria-label="Language">
          <option value="en">English</option><option value="it">Italiano</option>
          <option value="de">Deutsch</option><option value="fr">Français</option>
          <option value="es">Español</option><option value="pt">Português</option>
          <option value="sw">Kiswahili</option><option value="ar">العربية</option>
          <option value="ja">日本語</option><option value="zh">中文</option>
        </select>
      </div>
      <a href="/contact#quote" class="plan-trip-btn">
        <i class="fas fa-plane-departure"></i>
        <span data-i18n="plan_trip_cta">Plan Trip</span>
      </a>
    </div>
  </div>
</header>