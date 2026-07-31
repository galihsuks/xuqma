<?php
$pageTitle = '404 - Page Not Found';
$statusCode = '404';
$badgeClass = 'badge-primary';
$iconBackground = '#7c3aed';
$iconSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search-x-icon lucide-search-x"><path d="m13.5 8.5-5 5"/><path d="m8.5 8.5 5 5"/><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
SVG;
$title = 'Page Not Found';
$description = $message ?? 'The page or resource you are looking for does not exist, may have been moved, or is unavailable right now.';
$heroTitle = 'The latest software and hardware are here';
$heroDescription = 'SEO storefront pages can stay on CodeIgniter, while transactional customer and admin experiences live under React.';
$primaryActionLabel = 'Go to Home';
$primaryActionUrl = base_url('/');
$secondaryActionLabel = 'Read Articles';
$secondaryActionUrl = base_url('/articles');
$hintTitle = 'Helpful note';
$hintCopy = 'This usually means the URL is incorrect, the page was moved, or the related public route is not available right now.';
$brandText = 'UQMA';
$contextLabel = 'Public Website';

include __DIR__ . DIRECTORY_SEPARATOR . '_themed_error.php';
