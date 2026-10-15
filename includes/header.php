<?php

/**
 * Bagicha 2.0 - Header Template with SEO & OpenGraph Meta Tags
 */
if (!defined('BAGICHA_INIT')) {
    define('BAGICHA_INIT', true);
}
require_once __DIR__ . '/../config/constants.php';

$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$metaTitle = htmlspecialchars($pageTitle ?? APP_NAME . ' - ' . APP_TAGLINE);
$metaDesc = htmlspecialchars($pageDescription ?? 'Bring nature into your space with Bagicha indoor plants, air purifiers, garden accessories, seeds & pots.');
$metaImage = htmlspecialchars($pageImage ?? 'https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=1200&q=80');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $metaTitle ?></title>
    <meta name="description" content="<?= $metaDesc ?>">
    <meta name="keywords" content="plants online, indoor plants, succulents, buy plants india, urban gardening, plant nursery, monstera, snake plant">
    <meta name="author" content="Bagicha Nursery">
    <meta name="theme-color" content="#034324">
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/img/logo-3.png">
    <link rel="canonical" href="<?= $currentUrl ?>">

    <!-- OpenGraph Meta Tags for Social Media & Search Engine Previews -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Bagicha">
    <meta property="og:title" content="<?= $metaTitle ?>">
    <meta property="og:description" content="<?= $metaDesc ?>">
    <meta property="og:image" content="<?= $metaImage ?>">
    <meta property="og:url" content="<?= $currentUrl ?>">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $metaTitle ?>">
    <meta name="twitter:description" content="<?= $metaDesc ?>">
    <meta name="twitter:image" content="<?= $metaImage ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Source+Sans+3:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom Design System CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>

<body>
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;"></div>
