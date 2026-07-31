<?php
$pageTitle = '400 - Bad Request';
$statusCode = '400';
$badgeClass = 'badge-warning';
$iconBackground = '#f59e0b';
$iconSvg = <<<SVG
<svg viewBox="0 0 24 24" aria-hidden="true">
    <path d="M12 9v4"></path>
    <path d="M12 17h.01"></path>
    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
</svg>
SVG;
$title = 'Bad Request';
$description = ENVIRONMENT !== 'production'
    ? (string) ($message ?? lang('Errors.badRequest'))
    : lang('Errors.sorryBadRequest');
$heroTitle = 'Request details need another look.';
$heroDescription = 'The public storefront is still available, but this request could not be processed with the provided input or route parameters.';
$primaryActionLabel = 'Go to Home';
$primaryActionUrl = base_url('/');
$secondaryActionLabel = 'Browse Shop';
$secondaryActionUrl = base_url('/shop');
$hintTitle = 'Helpful note';
$hintCopy = 'Double-check the URL, query string, submitted form values, or the request method that reached this page.';
$brandText = 'UQMA';
$contextLabel = 'Public Website';

include __DIR__ . DIRECTORY_SEPARATOR . '_themed_error.php';
