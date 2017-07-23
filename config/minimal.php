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
            'defaultFileSystem' => '*\Base\FileSystem\Local',
        ],

        'abstractFactories' => [
            'namespaceFallback' => [
                'class' => 'Fixin\Resource\AbstractFactory\NamespaceFallbackFactory',
                'options' => [
                    'searchOrder' => ['App', 'Fixin']
                ]
            ]
        ]
    ]
];
