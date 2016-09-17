<?php

return [
    'application' => [
        'cargo' => 'starterCargo',
        'route' => 'starterRoute',
        'errorRoute' => 'errorRoute'
    ],
    'resourceManager' => [
        'class' => 'Fixin\Resource\ResourceManager',
        'definitions' => [
            // Classes
            'Base\Session\SessionManager' => [
                'options' => [
                    'repository' => 'sessionRepository'
                ]
            ],
            'Delivery\Node\HttpErrorHub' => [
                'options' => [
                    'route' => 'errorRoute'
                ]
            ],
            'Delivery\Node\HttpRouterHub' => [
                'class' => 'Delivery\Node\Factory\HttpRouterHubFactory'
            ],
            'View\View' => [
                'options' => [
                    'fileResolver' => 'viewFileResolver'
                ]
            ],

            // Basics
            'defaultFileSystem' => 'Base\FileSystem\Local',
            'fileStorage' => [
                'class' => 'Model\Storage\Directory\DirectoryStorage',
                'options' => [
                    'fileSystem' => 'defaultFileSystem'
                ]
            ],
            'starterCargo' => 'Delivery\Cargo\Factory\RuntimeCargoFactory',
            'sessionRepository' => [
                'class' => 'Model\Repository\Repository',
                'options' => [
                    'name' => 'session',
                    'storage' => 'fileStorage',
                    'entityPrototype' => 'Model\Entity\Entity'
                ]
            ],
            'viewFileResolver' => [
                'class' => 'Base\FileSystem\FileResolver',
                'options' => [
                    'defaultExtension' => '.phtml',
                    'fileSystem' => 'defaultFileSystem'
                ]
            ],

            // View Wrappers
            'errorLayoutViewWrapper' => [
                'class' => 'Delivery\Node\WrapInView',
                'options' => [
                    'template' => 'layout/error.phtml',
                    'contentName' => 'content'
                ]
            ],
            'layoutViewWrapper' => [
                'class' => 'Delivery\Node\WrapInView',
                'options' => [
                    'template' => 'layout/default.phtml',
                    'contentName' => 'content'
                ]
            ],

            // Routes
            'errorRoute' => [
                'class' => 'Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'Delivery\Node\ThrowableToText',
                        'errorLayoutViewWrapper',
                        'Delivery\Node\ViewRender',
                    ]
                ]
            ],
            'starterRoute' => [
                'class' => 'Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'Delivery\Node\JsonToArray',
                        'routerHub',
                        'controllerClassHub',
                        'Delivery\Node\HttpNotFoundFallback',
                        'Delivery\Node\HttpErrorHub',
                        'layoutViewWrapper',
                        'Delivery\Node\ViewRender',
                        'Delivery\Node\ArrayToJson'
                    ]
                ]
            ],

            // Hubs
            'controllerClassHub' => [
                'class' => 'Delivery\Node\HttpClassHub',
                'options' => [
                    'basePath' => '/',
                    'classPrefix' => 'Controller',
                    'depth' => 2
                ]
            ],
            'routerHub' => [
                'class' => 'Delivery\Node\HttpRouterHub',
                'options' => [
                    'routes' => [
                        'index' => [
                            'uri' => '/',
                            'handler' => 'Controller\Index'
                        ]
                    ],
                    'patterns' => [
                        'id' => '[0-9]+'
                    ]
                ]
            ],

        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\Resource\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
];