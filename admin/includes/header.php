<?php
/**
 * Bagicha 2.0 - Admin Header Component
 */
if (!defined('BAGICHA_INIT')) {
    define('BAGICHA_INIT', true);
}
require_once __DIR__ . '/../../config/constants.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($adminTitle ?? 'Executive Dashboard - Bagicha Admin') ?></title>
    
    <!-- Theme & Sidebar Flash Prevention Script -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('bagicha_admin_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            const isSidebarCollapsed = localStorage.getItem('bagicha_admin_sidebar_collapsed') === '1';
            document.addEventListener('DOMContentLoaded', () => {
                if (document.body) {
                    document.body.setAttribute('data-bs-theme', savedTheme);
                    if (window.innerWidth >= 992 && isSidebarCollapsed) {
                        document.body.classList.add('sidebar-collapsed');
                    }
                }
            });
        })();
    </script>

    <!-- Google Fonts (Outfit & Source Sans 3) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Admin Design System & Collapsible Sidebar CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/admin-sidebar.css">
</head>
<body class="admin-body">
<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;"></div>
<div class="admin-wrapper">
