<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'markdown-it' => [
        'version' => '14.1.0',
    ],
    'mdurl' => [
        'version' => '2.0.0',
    ],
    'uc.micro' => [
        'version' => '2.1.0',
    ],
    'entities' => [
        'version' => '4.5.0',
    ],
    'linkify-it' => [
        'version' => '5.0.0',
    ],
    'punycode.js' => [
        'version' => '2.3.1',
    ],
];
