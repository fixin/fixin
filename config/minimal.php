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
                    'keyPrefix' => 'repository.',
                    'classPrefix' => '*\\',
                    'entityCache' => '*\Model\Entity\Cache\RuntimeCache',
                    'storage' => 'dbStorage'
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
