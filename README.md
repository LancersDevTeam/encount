# Encount plugin for CakePHP

[![Code Quality](http://img.shields.io/scrutinizer/g/fusic/encount.svg?style=flat-square)](https://scrutinizer-ci.com/g/fusic/encount/)

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require fusic/encount
```

## Usage

```php
// config/bootstrap.php
<?php

// web
use Encount\Error\EncountErrorHandler;
(new EncountErrorHandler(Configure::read('Error')))->register();

// shell
use Encount\Console\EncountConsoleErrorHandler;
(new EncountConsoleErrorHandler(Configure::read('Error')))->register();
```

```php
// 3.4.0 or higher
// src/Application.php
<?php

use Encount\Middleware\EncountErrorHandlerMiddleware;

$middleware
    //->add(new ErrorHandlerMiddleware(Configure::read('Error.exceptionRenderer')))
    ->add(new EncountErrorHandlerMiddleware(Configure::read('Error.exceptionRenderer')))
```

## Config

```php
// config/app.php
<?php

return [

-snip-

    'Error' => [
        'errorLevel' => E_ALL & ~E_DEPRECATED,
        'exceptionRenderer' => 'Cake\Error\ExceptionRenderer',
        'skipLog' => [],
        'log' => true,
        'trace' => true,
        // Encount config
        'encount' => [
            'force' => false,
            'sender' => ['Encount.Mail'],
            'mail' => [
                'prefix' => '',
                'html' => true
            ]
        ],
    ],

-snip-

    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => 'you@localhost',
            //'charset' => 'utf-8',
            //'headerCharset' => 'utf-8',
        ],
        // Encount Email config
        'error' => [
            'transport' => 'default',
            'from' => 'from@example.com',
            'to' => 'to@example.com'
        ]
    ],

-snip-

];
```

## Sender

### Encount.Mail
### [Encount sender for faultline](https://github.com/fusic/encount-sender-faultline)
