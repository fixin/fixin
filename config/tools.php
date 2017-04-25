<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
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
                    'searchOrder' => ['App', 'Fixin']
                ]
            ]
        ]
    ]
];
