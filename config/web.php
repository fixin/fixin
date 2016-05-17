<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'cargo' => 'Delivery\Cargo\Factory\RuntimeCargoFactory',
            'dispatcher' => [
                'class' => 'Delivery\Dispatcher\Dispatcher',
                'facilities' => [
                    'Delivery\Station\JsonToArrayFacility',
                ]
            ],
            'errorDispatcher' => [
                'class' => 'Delivery\Dispatcher\Dispatcher',
                'facilities' => [

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