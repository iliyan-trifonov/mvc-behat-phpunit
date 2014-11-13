Custom MVC App with Users and Notepads tested with Behat/Mink and PHPUnit+Mockery
===

[![Build Status](https://travis-ci.org/iliyan-trifonov/mvc-behat-phpunit.svg?branch=master)](https://travis-ci.org/iliyan-trifonov/mvc-behat-phpunit)
[![Coverage Status](https://img.shields.io/coveralls/iliyan-trifonov/mvc-behat-phpunit.svg)](https://coveralls.io/r/iliyan-trifonov/mvc-behat-phpunit)

[![Build Status](https://api.shippable.com/projects/54637ba0c6f0803064f43dab/badge?branchName=master)](https://app.shippable.com/projects/54637ba0c6f0803064f43dab/builds/latest)
[![Build Status](https://drone.io/github.com/iliyan-trifonov/mvc-behat-phpunit/status.png)](https://drone.io/github.com/iliyan-trifonov/mvc-behat-phpunit/latest)
[![codecov.io](https://codecov.io/github/iliyan-trifonov/mvc-behat-phpunit/coverage.svg?branch=master)](https://codecov.io/github/iliyan-trifonov/mvc-behat-phpunit?branch=master)

[ ![Codeship Status for iliyan-trifonov/mvc-behat-phpunit](https://codeship.com/projects/fdd76750-4cb3-0132-5fa7-4a214f75c8af/status)](https://codeship.com/projects/47201)

I wanted first to create this small real world project and use it as a template later for making the same but with
TDD and BDD tests in mind. I expect the tests to be a little different when approached that way.

I wanted to create this complete PHP application and see what I will need to test it, like what composer.json
configuration as well as the Jenkins CI PHP configuration and tools I used here.

I discovered some interesting for me things in the process about Behat, Mockery, Ant and Composer.

Requirements
---

You only need PHP CLI to work with and test this application.

The PHP version should be at least ~5.4.

The PHP extentsions used are: sqlite xsl curl xdebug.

The app alone needs only: PHP CLI and the sqlite extension.

The phpdox tool needs the xsl extension.

The instaclick/php-webdriver needs the curl extension.

A phpunit test(and probably the coverage creation) needs the xdebug extension.

All php testing tools required by the Jenkins configuration are downloaded with Composer and called from projroot/bin.


Try the application.
---

Go to the project's root and:

Initialize the SQLite database:

```bash
php src/scripts/build_test_db.php
```

Run the PHP built-in server:

```bash
php -S localhost:8888 -t src/public router.php
```

Instead of `php -S...` You can use the ready made Bash script:

```bash
./phpserver.sh start
```

Now go to [http://localhost:8888](http://localhost:8888 "The Application") in your browser and try the application.

When you don't need the server anymore stop it like this:

```bash
./phpserver.sh stop
```

There's a command to find all php servers started and terminate them in case of emergency:

```bash
./phpserver.sh stopall
```

If you need to see the server's output, do it like this:

```bash
tail -f build/phpserver.log
```

For Testing
---

First install all needed tools through Composer:

In project's root run:

```bash
composer install
```

The [Selenium Server](http://www.seleniumhq.org/download/ "Selenium") is needed to run the JavaScript Behat tests.
After downloading it run it with:

```bash
java -jar selenium-server-standalone-*.jar
```

You should have the `firefox` binary in your PATH. Afte running the Selenium Server continue with:

Again from the project's root, run these commands one after another and watch the information on the screen:

```bash
bin/phpunit -c build/phpunit-nologging.xml

./phpserver.sh start

bin/behat

./phpserver.sh stop
```

The testing configuration for Behat and PHPUnit can be easily integrated into IDEs like PHPStorm.
For that use vendor/autoload.php and build/phpunit-nologging.xml.

If you have Ant installed you can run it from the project's root:

```bash
ant
```

When ant with build.xml is used, there are artefacts created in the build/ directory.
They are used by the Jenkins CI configured for PHP [like here](http://jenkins-php.org/ "Template for Jenkins Jobs for PHP Projects").

The Application
---

#### MVC

I've built this app by separating/widgetizing it to a Model, a View and a Controller.

PSR-4 Autoloading is used with Namespaces. The main Namespace is \Notepads.

#### The Model

The Model containes Domain Objects and Data Mappers. They are glued together with Service Classes/Objects.

The Domain Objects care about their integrity by checking the variables before allowing them to be assigned.
For example id=-1 will produce an InvalidArgumentException. This can be used to be sure about the objects's stability
when given to the Mappers without the need to check the params there again. 

PDO is used with a SQLite file connection. This is done only once in the bootstrap index.php and can be changed quickly
to use another database.

Of course when you change the database type you usually will need to create new Data Mappers.

There are only 2 Mapper classes: UserMapper and NotepadMapper. They are behind the Service Object. Change them if you want
to work with for example XML or CSV files instead of SQLite.

The MapperFactory passes the database connection to the Mappers. This one may need to change if the mappers are changed.

#### The View

The View is controlled by the Template class which is made to be easily integrated with any PHP template engine.
Currently the templates are in .phtml format.

#### The Controller

Everything starts from the Controller. The bootstrap code in public/index.php initializes the Controller
and calls its Action.

Before the Controller and its Action are used, the Router parses the url and decides if the params are valid.
Reflection is also used to check for valid Action.
After that the bootstrap can use $router->controller, $router->action and $router->params without worrying.

The Controller receives access to the Request, Router, ServiceFactory and View through Dependency Injection through
its constructor.

The Controller accesses the Model through the Service Objects. They are created and cached by the ServiceFactory.

Also the Controller assigns variables to the View and sets the subtemplate to be used. The Template class
allows only one template to be shown or it may be wrapped by a layout template. In this application there is one
common layout.phtml and the subtemplates are changed depending on the need of the executed controller's Action.

The Actions in the Controller are sufixed with 'Action' and all other functions in the Controller are used internally
and cannot be called from outside.

#### Others

Router, Request and Template are stand-alone classes used by the application in the Contrllers and the bootstrap index.php.

A config/config.ini file is used for easy settings. For now it only keeps the database file name.
The database file is created in data/test.sq3

There is some JavaScript and CSS in public/js and public/css to make the app's interface a little more good looking
and also to create the confirmation popup when deleting a Notepad.

In scripts/ there is a script used to initialize the application's database. Other similar scripts can be put there.

router.php is used by the built-in PHP server when the app is started that way.

#### The Tests

Unit tests are made on all PHP files to check how testable are they.

The Behat tests are in features/. The MinkExtension is used most of the time with Goutte and there is one scenario 
when we need to test the JavaScript confirmation popup with Selenium.

#### Sample [Docker](https://www.docker.com/ "Docker") Container to test it

```bash
sudo docker run -ti --name test -p 127.0.0.1:8888:8888 ubuntu:14.04 bash

apt-get update

apt-get install git

git clone git@github.com:iliyan-trifonov/mvc-behat-phpunit.git

cd mvc-behat-phpunit

apt-get install php5-cli php5-sqlite

#run the php built-in server and test the app in your browser at http://127.0.0.1:8888

php -S 0.0.0.0:8888 -t src/public router.php

#now the testing

#stop (Ctrl-C) the php server and run it with the script on the localhost

./phpserver.sh start

apt-get install curl php5-xsl php5-curl php5-xdebug

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

composer install

bin/phpunit -c build/phpunit-nologging.xml

#run behat and skip the javascript tests (for now here)

bin/behat --tags ~@javascript
```
