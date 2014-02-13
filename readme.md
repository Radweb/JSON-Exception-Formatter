[<img src="http://i.imgur.com/7BSeLZL.png" align="right" height="60">](http://radweb.co.uk)

[![Latest Stable Version](https://poser.pugx.org/radweb/json-exception-formatter/v/stable.png)](https://packagist.org/packages/radweb/json-exception-formatter) [![License](https://poser.pugx.org/radweb/json-exception-formatter/license.png)](https://packagist.org/packages/radweb/json-exception-formatter)

# Laravel JSON Exception Formatter

A small Laravel package to format & output exceptions in JSON format when required.

By default in Laravel, throwing an Exception in debug mode will display a nice JSON response when required (eg. an AJAX response, or an `Accept: application/javascript` header).

However once you're not in debug mode (ie. a production environment), a whole HTML response is displayed instead.

With this package, when you're not in debug mode, exceptions will be output as JSON (only without debug information like the file name & line number).

**Note** This does NOT affect HTML requests. Only AJAX/JSON requests are altered.

## Installation

Add `radweb/json-exception-formatter` to your `composer.json` file.

```json
{
    "require": {
        "radweb/json-exception-formatter": "dev-master"
    }
}
```

In `app/config/app.php`, add the Service Provider to the `providers` array:

```php
array(
    'providers' => array(
        // ...
        'Radweb\JsonExceptionFormatter\JsonExceptionFormatterServiceProvider',
    )
)
```

## Custom Formatters

You can override the default JSON exception formatter to use a different format, or provide more detail in the output.

To override, implement the `Radweb\JsonExceptionFormatter\FormatterInterface` interface, and bind with the IoC container. This requires you to implement two methods: `formatDebug()` and `formatPlain()`.

Example implementation:

```php
<?php

use Radweb\JsonExceptionFormatter\FormatterInterface;

class CustomDebugFormatter implements FormatterInterface {

    public function formatDebug(Exception $e)
    {
        return array(
            'theError' => array(
                'message' => $e->getMessage(),
                'detail' => 'In file '.$e->getFile().' on line '.$e->getLine(),
            ),
        );
    }

    public function formatPlain(Exception $e)
    {
        return array(
            'theError' => array(
                'message' => $e->getMessage(),
                // we don't want to display debug details in production
            ),
        );
    }

}
```

Now we just have to bind it in the IoC container. Add this anywhere in your app's bootstrap code (if you have nowhere, `routes.php` will do):

```php
App::bind('Radweb\JsonExceptionFormatter\FormatterInterface', 'CustomDebugFormatter');
```

## Preview

Normal Request, Debug Mode **ENABLED**

![](http://i.imgur.com/esu68bm.png)

Normal Request, Debug Mode **DISABLED**

![](http://i.imgur.com/9LsfiX7.png)

JSON Request, Debug Mode **ENABLED**

![](http://i.imgur.com/SH5kvwK.png)

JSON Request, Debug Mode **DISABLED**

![](http://i.imgur.com/bX6L8d3.png)
