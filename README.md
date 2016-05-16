monolog-pomm
=============

PostgreSQL Handler for Monolog utilizing the POMM library (http://www.pomm-project.org/), which allows to store log messages in a Postgres Table.
It can log text messages to a specific table, and creates the table automatically if it does not exist.

Based on https://github.com/wiosna-dev/monolog-pg

# Installation
monolog-pomm is available via composer. Just add the following line to your required section in composer.json and do a `php composer.phar update`.

```
"crxgames/monolog-pomm": ">1.0.0"
```

# Usage
Just use it as any other Monolog Handler, push it to the stack of your Monolog Logger instance. The Handler however needs some parameters:

- **$pomm** POMM Session instance of your database. Pass along the POMM instantiation of your database connection with your database selected.
- **$table** The table name where the logs should be stored
- **$level** can be any of the standard Monolog logging levels. Use Monologs statically defined contexts. _Defaults to Logger::DEBUG_
- **$bubble** _Defaults to true_

# Examples
Given that $pomm is your database session instance, you could use the class as follows:

```php
//Import class
use PommPGHandler\PommPGHandler;

//Create MysqlHandler
$pommHandler = new PommPGHandler($pomm, "log");

//Create logger
$logger = new \Monolog\Logger($context);
$logger->pushHandler($mySQLHandler);

//Now you can use the logger, and further attach additional information
$logger->addWarning("This is a great message, woohoo!", array('username'  => 'John Doe', 'userid'  => 245));
```

# License
This tool is free software and is distributed under the MIT license. Please have a look at the LICENSE file for further information.
