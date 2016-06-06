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

            'controllerHub' => [
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
                    ]
                ]
            ],
            'starterCargo' => 'Delivery\Cargo\Factory\RuntimeCargoFactory',
            'starterRoute' => [
                'class' => 'Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'Delivery\Node\JsonToArray',
                        'controllerHub',
                        'Delivery\Node\HttpNotFoundFallback',
                        'Delivery\Node\HttpErrorHub',
                        'Delivery\Node\ArrayToJson',
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