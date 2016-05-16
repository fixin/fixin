<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'cargo' => 'Fixin\Delivery\Cargo\Factory\RuntimeCargoFactory',
            'dispatcher' => [
                'class' => 'Fixin\Delivery\Dispatcher\Dispatcher',
                'stations' => [
                    'Fixin\Delivery\Station\JsonToArrayFacility',
                ]
            ],
            'errorDispatcher' => [
                'class' => 'Fixin\Delivery\Dispatcher\Dispatcher',
                'stations' => [

                ]
            ],
            'requestUri' => 'Fixin\Base\Uri\Factory\EnvironmentUriFactory',
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
];