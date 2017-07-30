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
            'repository' => [
                'class' => 'Fixin\Resource\AbstractFactory\RepositoryFactory',
                'options' => [
                    'prefixDepth' => 1,
                    'nameDepth' => 1
                ]
            ],
            'namespaceFallback' => [
                'class' => 'Fixin\Resource\AbstractFactory\NamespaceFallbackFactory',
                'options' => [
                    'searchOrder' => ['App', 'Fixin']
                ]
            ],
            'default' => 'Fixin\Resource\AbstractFactory\DefaultFactory'
        ]
    ]
];
