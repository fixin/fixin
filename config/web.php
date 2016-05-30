<?php

return [
    'application' => [
        'cargo' => 'Delivery\Cargo\Factory\RuntimeCargoFactory',
        'route' => 'mainRoute',
        'errorRoute' => 'errorRoute'
    ],
    'resourceManager' => [
        'class' => 'Fixin\Resource\ResourceManager',
        'definitions' => [
            'defaultFileSystem' => 'Base\FileSystem\Local',
            'errorRoute' => [
                'class' => 'Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                    ]
                ]
            ],
            'mainRoute' => [
                'class' => 'Delivery\Route\Route',
                'options' => [
                    'nodes' => [
                        'Delivery\Node\JsonToArray',
                    ]
                ]
            ],
            'viewFileResolver' => [
                'class' => 'Base\FileSystem\FileResolver',
                'options' => [
                    'defaultExtension' => '.phtml',
                    'fileSystem' => 'defaultFileSystem'
                ]
            ],

            /*
            'Base\Session\SessionRepository' => [
                'class' => 'Base\Model\Repository',
                'options' => [
                    'name' => 'sessions',
                    'storage' => 'Base\Storage\Directory\DirectoryStorage'
                ]
            ],*/
            'View\View' => [
                'options' => [
                    'fileResolver' => 'viewFileResolver'
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