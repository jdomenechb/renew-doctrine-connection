# Renew Doctrine Connection

This library eases the process of renewing an existing [Doctrine](https://www.doctrine-project.org/) connection in your application that has been closed due to a timeout from the server. This might happen when the connection expires or the server has become unavailable.

When the connection is lost, the application usually throws an error like (in MySQL):

```
SQLSTATE[HY000]: General error: 2006 MySQL server has gone away
```

It is recommended to install this library only in daemons, consumers, PHP-only servers or very long processes, that is, **any PHP project that relies solely in a lengthy PHP process to run**. That does not include normal PHP applications running in web servers like Nginx or Apache, unless they have expensive algorythtms that rely on the database. Otherwise, you might be suffering some undesired overhead in your application.

## Installation

Add this library to your project using [Composer](https://getcomposer.org/):

``` bash
composer require jdomenechb/renew-doctrine-connection
```

## Configuration

Modify your Doctrine configuration to use the `TimedRenewClass` contained in this library as the wrapper class of Doctrine.

```yaml
doctrine:
    dbal:
        # ...
        wrapper_class: 'Jdomenechb\Doctrine\DBAL\TimedRenewConnection'
        secondsToRenew: 300
```


You can freely customize the parameter `secondsToRenew`. The database connection will be renewed only after the seconds of inactivity you specify in this parameter. If it is set to 0 or not set at all, the connection will be renewed before every operation performed to the database.