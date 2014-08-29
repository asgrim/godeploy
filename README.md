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

You'll need to create a SQLite database in `data/godeploy.sqlite` or override the database settings by creating `config/autoload/database.local.php` and setting the database connection there. Initialise the database using `data/schema.sqlite.sql` (no translations exist for other DBs yet...).

Upgrade
-------

```bash
$ git fetch origin
$ git checkout [version]
$ composer install --no-dev
$ bower install
```
