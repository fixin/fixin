<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackFactory'
            ]
        ]
    ],
];