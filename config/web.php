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
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
];