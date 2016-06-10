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
            'starterCargo' => 'Delivery\Cargo\Factory\RuntimeCargoFactory',
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
                        'Delivery\Node\ThrowableToHtml',
                        'errorLayoutViewWrapper',
                        'Delivery\Node\ViewRender',
                        'Delivery\Node\ArrayToJson'
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
                        'post' => [
                            'uri' => '/posts',
                            'view' => [
                                'uri' => '{id}',
                                'handler' => 'Controller\RestfulController',
                            ],
                            'comment' => [
                                'uri' => '{id}/comments/{comment?}',
                                'patterns' => [
                                    'comment' => '[A-Za-z0-9-]+',
                                ],
                                'handler' => 'Controller\RestfulController',
                            ]
                        ],
                        'login' => [
                            'uri' => '/login',
                            'handler' => 'Controller\ResfulController'
                        ],
                        'test' => [
                            'uri' => '{date?}/index',
                            'handler' => 'Controller\ResfulController'
                        ],
                        'index' => [
                            'uri' => '/',
                            'handler' => 'Controller\ResfulController'
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