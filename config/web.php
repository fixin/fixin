<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'requestUri' => 'Fixin\Base\Uri\Factory\EnvironmentUriFactory',
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
];