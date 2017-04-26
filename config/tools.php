<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

return [
    'resourceManager' => [
        'definitions' => [
            'defaultFileSystem' => 'Base\FileSystem\Local',
        ],

        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\Resource\AbstractFactory\PrefixFallbackFactory',
                'options' => [
                    'searchOrder' => ['Fixin']
                ]
            ]
        ]
    ]
];
