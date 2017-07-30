<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

return [
    'application' => [
        'cargo' => 'cargo',
        'errorRoute' => 'errorRoute',
        'route' => 'mainRoute',
    ],

    'resourceManager' => [
        'class' => 'Fixin\Resource\ResourceManager',

        'definitions' => [
            // By class
            '*\View\View' => [
                'options' => [
                    'fileResolver' => 'templateFileResolver',
                ]
            ],

            // By name
            'cargo' => '*\Delivery\Cargo\Factory\RuntimeCargoFactory',
            'config' => [
                'class' => '*\Base\Container\Container',
            ],
            'dbStorage' => [
                'class' => '*\Model\Storage\Pdo\PdoStorage',
            ],
            'localFileSystem' => '*\Base\FileSystem\Local',
            'templateFileResolver' => [
                'class' => '*\Base\FileSystem\FileResolver',
                'options' => [
                    'defaultExtension' => '.phtml',
                    'fileSystem' => 'localFileSystem'
                ]
            ],

            // Error route
            'errorRoute' => [
                'class' => '*\Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'errorRoute.throwableToText',
                        'errorRoute.viewRender'
                    ]
                ]
            ],
            'errorRoute.throwableToText' => '*\Delivery\Node\ThrowableToText',
            'errorRoute.viewRender' => '*\Delivery\Node\ViewRender',

            // Main route
            'mainRoute' => [
                'class' => '*\Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'mainRoute.errorHub',
                        'mainRoute.viewRender',
                    ]
                ]
            ],
            'mainRoute.errorHub' => [
                'class' => '*\Delivery\Node\HttpErrorHub',
                'options' => [
                    'route' => 'errorRoute'
                ]
            ],
            'mainRoute.viewRender' => '*\Delivery\Node\ViewRender'
        ],

        'abstractFactories' => [
            'repository' => [
                'class' => 'Fixin\Resource\AbstractFactory\RepositoryFactory',
                'options' => [
                    'prefixDepth' => 1,
                    'nameDepth' => 1,
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
