laravel-needs-auto-rehash ![From Ethiopia](https://img.shields.io/badge/From-Ethiopia-brightgreen.svg)
=========================

[![Build Status](https://travis-ci.org/SamAsEnd/laravel-needs-auto-rehash.svg?branch=master)](https://travis-ci.org/SamAsEnd/laravel-needs-auto-rehash)
[![StyleCI](https://github.styleci.io/repos/297123581/shield?branch=master)](https://github.styleci.io/repos/297123581?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SamAsEnd/laravel-needs-auto-rehash/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SamAsEnd/laravel-needs-auto-rehash/?branch=master)

This package automates the common password [`Hash::needsRehash`](https://laravel.com/docs/8.x/hashing#basic-usage) routine by hooking into the [built-in event system](https://laravel.com/docs/8.x/authentication#events).

Use case
--------
When a user register, Laravel uses `bcrypt` algorithm with a cost factor of `10` to hash passwords.

The ~~problem~~ is when you change [the default hashing algorithm](https://github.com/laravel/laravel/blob/master/config/hashing.php#L18) or
when Laravel eventually changes [the default algorithm](https://github.com/laravel/framework/blob/master/src/Illuminate/Hashing/HashManager.php#L95) to `argon2i`
or PHP recommended [`PASSWORD_DEFAULT` constant](https://www.php.net/manual/en/password.constants.php) changes, and you want to keep up
or simply want to upgrade the `cost` factor of `bcrypt`; your changes will only be reflected on **newly registered users** or when **existing users change their password**.

You have to implement a common routine task to upgrade users' password hash by checking `Hash::needsRehash` whenever the user provides a valid credential.

Prerequisites
-------------
 - **PHP** 7.2 or greater.
 - **Laravel** 6.x || 7.x || 8.x

Installation
------------
```bash
composer require samasend/laravel-needs-auto-rehash
```

Basic Usage
-----------
That's it, you just need to install the package. :rocket:

How does this works?
--------------------
 - This magical package listen for the built-in `Illuminate\Auth\Events\Attempting` [event fired from the framework](https://laravel.com/docs/8.x/authentication#events) and validate the credentials [using the built-in infrastructure](https://laravel.com/docs/8.x/authentication#the-user-provider-contract).
 - If the user password needs rehashing, it will rehash the password and update the model.

Contributing
------------
    Fork it
    Create your feature branch (git checkout -b my-new-feature)
    Commit your changes (git commit -am 'Add some feature')
    Push to the branch (git push origin my-new-feature)
    Create new Pull Request
