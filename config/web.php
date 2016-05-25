<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'cargo' => 'Delivery\Cargo\Factory\RuntimeCargoFactory',
            'applicationDispatcher' => [
                'class' => 'Delivery\Dispatcher\Dispatcher',
                'options' => [
                    'nodes' => [
                        'Delivery\Node\JsonToArray',
                    ]
                ]
            ],
            'errorDispatcher' => [
                'class' => 'Delivery\Dispatcher\Dispatcher',
                'options' => [
                    'nodes' => [
                    ]
                ]
            ],
            'View\View\FileResolver' => [
                'class' => 'Base\FileResolver\FileResolver',
                'options' => [
                    'defaultExtension' => '.phtml'
                ]
            ]
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
];