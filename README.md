GoDeploy - [www.godeploy.com](http://www.godeploy.com/)

[![Build Status](https://secure.travis-ci.org/asgrim/godeploy.png?branch=master)](https://travis-ci.org/asgrim/godeploy)

Install
-------

```bash
$ git clone git@github.com:asgrim/godeploy.git
$ cd godeploy
$ git checkout [version]
$ composer install --no-dev
$ bower install
```

Replace `[version]` with the version you wish to use (just use `master` for latest).

Then copy `config/autoload/local.php.dist` to `config/autoload/local.php` and configure as you please (documentation does not exist for the structure yet...).

You'll need to create a MySQL database using `data/schema.sql` and override the database settings by copy `config/autoload/database.local.php.dist` to `config/autoload/database.local.php` and configuring your MySQL settings.

Generate the SSH private/public key to use, and put them in `data/ssh/id_rsa` and `data/ssh/id_rsa.pub` - if you put them elsewhere or use existing SSH keys, make sure to update the path in `config/autoload/local.php` - make sure they are accessible to PHP process.

Upgrade
-------

```bash
$ git fetch origin
$ git checkout [version]
$ composer install --no-dev
$ bower install
```
