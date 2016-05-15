<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'requestUri' => 'Fixin\Base\Uri\Factory\EnvironmentUriFactory',
            'dispatcher' => 'Fixin\Delivery\Dispatcher\Dispatcher'
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
    'dispatcherConfig' => [

    ]
];