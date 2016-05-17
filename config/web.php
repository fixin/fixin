<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'cargo' => 'Delivery\Cargo\Factory\RuntimeCargoFactory',
            'dispatcher' => [
                'class' => 'Delivery\Dispatcher\Dispatcher',
                'options' => [
                    'nodes' => [
                        'Delivery\Node\JsonToArrayNode',
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
            'requestUri' => 'Base\Uri\Factory\EnvironmentUriFactory',
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
];