# Doctrine Renew Connection

This library eases the process of renewing an existing Doctrine connection in your application that has been closed due to a timeout from the server. This might happen when the connection expires or the server has become unavailable.

When the connection is lost, the application usually throws an error like:

```
SQLSTATE[HY000]: General error: 2006 MySQL server has gone away
```

It is recommended to install this library only in daemons, consumers, PHP-only servers or very long processes, that is, **any PHP project that relies solely in a lengthy PHP process to run**. That does not include normal PHP applications running in web servers like Nginx or Apache, unless they have expensive algorythtms that rely on the database. Otherwise, you might be suffering some undesired overhead in your application.

## Installation

1. Add this library to your project using Composer:
```
composer require jdomenechb/doctrine-renew-connection
```

2. Modify your Doctrine configuration to use the class in the library as the wrapper class of Doctrine.

```
doctrine:
    dbal:
        # ...
        wrapper_class: 'Jdomenechb\Doctrine\DBAL\TimedRenewConnection'

```
