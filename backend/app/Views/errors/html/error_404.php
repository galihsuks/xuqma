<?php
$pageTitle = '404 - Page Not Found';
$statusCode = '404';
$badgeClass = 'badge-primary';
$iconBackground = '#0284c7';
$iconSvg = <<<SVG
<svg viewBox="0 0 24 24" aria-hidden="true">
    <circle cx="11" cy="11" r="7"></circle>
    <path d="m21 21-4.35-4.35"></path>
    <path d="M8.5 8.5h5"></path>
    <path d="M11 6v5"></path>
</svg>
SVG;
$title = 'Page Not Found';
$description = $message ?? 'The page you are looking for could not be found.';
$heroTitle = 'A clean fallback when a public page is missing.';
$heroDescription = 'Search engines and users should still land on a polished page when a route does not exist on the public side.';
$primaryActionLabel = 'Go to Home';
$primaryActionUrl = base_url('/');
$secondaryActionLabel = 'Read Articles';
$secondaryActionUrl = base_url('/articles');
$hintTitle = 'What happened';
$hintCopy = 'This usually means the URL is incorrect, the page was moved, or the route has not been registered yet.';
$brandText = getenv('app.name') ?: 'Base App Galih';

include __DIR__ . DIRECTORY_SEPARATOR . '_themed_error.php';
