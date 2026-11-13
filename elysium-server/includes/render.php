<?php
declare(strict_types=1);

function render_html_with_injections(string $sourcePath, string $injectedHead = '', string $injectedBodyEnd = ''): void
{
    $html = file_get_contents($sourcePath);
    if ($html === false) {
        http_response_code(500);
        echo 'Template load failed.';
        exit;
    }

    if ($injectedHead !== '') {
        $html = str_replace('</head>', $injectedHead . PHP_EOL . '</head>', $html);
    }
    if ($injectedBodyEnd !== '') {
        $html = str_replace('</body>', $injectedBodyEnd . PHP_EOL . '</body>', $html);
    }

    echo $html;
}
