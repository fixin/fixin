<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'requestUri' => 'Fixin\Base\Uri\Factory\EnvironmentUriFactory',
            'dispatcher' => [
                'class' => 'Fixin\Delivery\Dispatcher\Dispatcher',
                'stations' => [

                ]
            ],
            'errorDispatcher' => [
                'class' => 'Fixin\Delivery\Dispatcher\Dispatcher',
                'stations' => [

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