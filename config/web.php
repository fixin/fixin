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
            'Delivery\Node\HttpErrorHub' => [
                'options' => [
                    'route' => 'errorRoute'
                ]
            ],
            'View\View' => [
                'options' => [
                    'fileResolver' => 'viewFileResolver'
                ]
            ],

            'controllerClassHub' => [
                'class' => 'Delivery\Node\HttpClassHub',
                'options' => [
                    'basePath' => '/',
                    'classPrefix' => 'Controller',
                    'depth' => 2
                ]
            ],
            'defaultFileSystem' => 'Base\FileSystem\Local',
            'errorRoute' => [
                'class' => 'Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'Delivery\Node\ThrowableToHtml',
                        'Delivery\Node\ArrayToJson'
                    ]
                ]
            ],
            'routerHub' => [
                'class' => 'Delivery\Node\Factory\HttpRouterHubFactory',
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
            'starterCargo' => 'Delivery\Cargo\Factory\RuntimeCargoFactory',
            'starterRoute' => [
                'class' => 'Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'Delivery\Node\JsonToArray',
                        'routerHub',
                        'controllerClassHub',
                        'Delivery\Node\HttpNotFoundFallback',
                        'Delivery\Node\HttpErrorHub',
                        'Delivery\Node\ArrayToJson'
                    ]
                ]
            ],
            'viewFileResolver' => [
                'class' => 'Base\FileSystem\FileResolver',
                'options' => [
                    'defaultExtension' => '.phtml',
                    'fileSystem' => 'defaultFileSystem'
                ]
            ]
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\Resource\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
];