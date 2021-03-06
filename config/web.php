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
        'route' => 'mainRoute',
        'errorRoute' => 'errorRoute'
    ],

    'resourceManager' => [
        'class' => 'Fixin\Resource\ResourceManager',

        'definitions' => [
            // By class
            '*\Base\Session\SessionManager' => [
                'options' => [
                    'repository' => '*\Base\Session\SessionRepository'
                ]
            ],
            '*\Base\Session\SessionRepository' => [
                'options' => [
                    'name' => 'system__sessions',
                    'storage' => 'dbStorage',
                    'entityPrototype' => '*\Base\Session\SessionEntity',
                    'entityCache' => '*\Model\Entity\Cache\RuntimeCache'
                ]
            ],
            '*\Delivery\Node\HttpRouterHub' => [
                'class' => '*\Delivery\Node\Factory\HttpRouterHubFactory'
            ],
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
                        'errorRoute.layoutViewWrapper',
                        'errorRoute.viewRender'
                    ]
                ]
            ],
            'errorRoute.layoutViewWrapper' => [
                'class' => '*\Delivery\Node\WrapInView',
                'options' => [
                    'template' => 'layout/error.phtml',
                ]
            ],
            'errorRoute.throwableToText' => '*\Delivery\Node\ThrowableToText',
            'errorRoute.viewRender' => '*\Delivery\Node\ViewRender',

            // Main route
            'mainRoute' => [
                'class' => '*\Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'mainRoute.jsonToArray',
                        'mainRoute.routerHub',
                        'mainRoute.controllerClassHub',
                        'mainRoute.notFoundFallback',
                        'mainRoute.errorHub',
                        'mainRoute.layoutViewWrapper',
                        'mainRoute.viewRender',
                        'mainRoute.arrayToJson'
                    ]
                ]
            ],
            'mainRoute.arrayToJson' => '*\Delivery\Node\ArrayToJson',
            'mainRoute.controllerClassHub' => [
                'class' => '*\Delivery\Node\HttpClassHub',
                'options' => [
                    'basePath' => '/',
                    'classPrefix' => 'App\Controller',
                    'depth' => 2
                ]
            ],
            'mainRoute.errorHub' => [
                'class' => '*\Delivery\Node\HttpErrorHub',
                'options' => [
                    'route' => 'errorRoute'
                ]
            ],
            'mainRoute.jsonToArray' => '*\Delivery\Node\JsonToArray',
            'mainRoute.layoutViewWrapper' => [
                'class' => '*\Delivery\Node\WrapInView',
                'options' => [
                    'template' => 'layout/default.phtml',
                ]
            ],
            'mainRoute.notFoundFallback' => '*\Delivery\Node\HttpNotFoundFallback',
            'mainRoute.routerHub' => [
                'class' => '*\Delivery\Node\HttpRouterHub',
                'options' => [
                    'routes' => [
                        'index' => [
                            'uri' => '/',
                            'handler' => 'App\Controller\Index'
                        ]
                    ],
                    'patterns' => [
                        'id' => '[0-9]+'
                    ]
                ]
            ],
            'mainRoute.viewRender' => '*\Delivery\Node\ViewRender'
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
