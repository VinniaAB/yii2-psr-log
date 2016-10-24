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

Add a log target to your yii config:
```php
return [
    ...
    'log' => [
        'targets' => [
            'class' => \Vinnia\Yii2\PsrTarget::class,
            'logger' => \Psr\Log\LoggerInterface::class,
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

The `logger` property can be set to any class that implements `\Psr\Log\LoggerInterface`.

If you have set the property to an interface (like above), remember to tell Yii how to construct it. Here is an example using Monolog:
```php
Yii::$container->setSingleton(\Psr\Log\LoggerInterface::class, function() {
    $monolog = new \Monolog\Logger('app', [
        new \Monolog\Handler\StreamHandler(STDOUT),
    ]);
    return $monolog;
});
```
