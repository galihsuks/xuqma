<?php
$pageTitle = '500 - Internal Server Error';
$statusCode = '500';
$badgeClass = 'badge-danger';
$iconBackground = '#e11d48';
$iconSvg = <<<SVG
<svg viewBox="0 0 24 24" aria-hidden="true">
    <path d="M12 8v4"></path>
    <path d="M12 16h.01"></path>
    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
</svg>
SVG;
$title = 'Internal Server Error';
$description = 'Something went wrong while loading this page. Please try again in a moment.';
$heroTitle = 'Public pages can still fail gracefully.';
$heroDescription = 'Even when the server hits an unexpected problem, the website should remain calm, readable, and aligned with the project theme.';
$primaryActionLabel = 'Try Home';
$primaryActionUrl = base_url('/');
$secondaryActionLabel = 'Open About Page';
$secondaryActionUrl = base_url('/about');
$hintTitle = 'Helpful note';
$hintCopy = 'If this issue continues, review the server logs and check the controller, route, or related view that was being loaded.';
$brandText = getenv('app.name') ?: 'Base App Galih';

include __DIR__ . DIRECTORY_SEPARATOR . '_themed_error.php';
