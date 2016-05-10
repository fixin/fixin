<?php

return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'Request' => 'Fixin\ResourceManager\Factory\RequestFactory',
        ],
        'abstractFactories' => [
            'prefixFallback' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackAbstractFactory'
        ]
    ],
];