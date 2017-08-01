#ResourceManager

##Example Configuration
Here are possible definitions for resources.

**_Do not use closures in production configuration._**
```
return [
    'resourceManager' => [
        'class' => 'Fixin\ResourceManager\ResourceManager',
        'definitions' => [
            'byClassName' => 'Fixin\ResourceManager\Test',
            'byArray' => [
                'class' => 'Fixin\ResourceManager\Test',
                'options' => [
                    'param1' => 'test'
                ]
            ],
            'byClosure' => function($resourceManager) {
                return new Fixin\ResourceManager\Test($resourceManager);
            },
            'byFactory' => 'Fixin\ResourceManager\Factory\RequestFactory',
            'byPrefixFallback' => 'ResourceManager\Test'
        ],
        'abstractFactories' => [
            'prefixFallback' => [
                'class' => 'Fixin\ResourceManager\AbstractFactory\PrefixFallbackAbstractFactory'
            ]
        ]
    ]
];
```
