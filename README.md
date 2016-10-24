# PSR log target for Yii2

## Dependencies
- php 7.0+
- composer

## Installation
Add a repository to your composer.json like so:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/VinniaAB/yii2-psr-log.git"
    }
  ]
}
```

And then require the package with composer:

```shell
composer require vinnia/yii2-psr-log
```

## Usage

Configure the dependency container to build `\Vinnia\Yii2\PsrTarget` with your preferred PSR-compatible logger.
Here is an example using Monolog:

```php
Yii::$container->set(\Vinnia\Yii2\PsrTarget::class, function($container, $params, $config) {
    $monolog = new \Monolog\Logger('app', [
        new \Monolog\Handler\StreamHandler(STDOUT),
    ]);
    return new \Vinnia\Yii2\PsrTarget($monolog, $config);
});
```

Then add a log target to your yii config:
```php
return [
    ...
    'log' => [
        'targets' => [
            'class' => \Vinnia\Yii2\PsrTarget::class,
            'levels' => [
                'error',
                'warning',
            ],
            ...
        ]
    ]
    ...
]
```
