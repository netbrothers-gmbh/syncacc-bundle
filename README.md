NetBrothers SyncAcc for Symfony
===================================
This is a symfony bundle for using NetBrothers Access Control Center (ACC).

The ACC offers you a way to handle permissions based on roles and routes.
This bundle communicates with your ACC-instance and synchronizes your defined permissions
into your project.


Installation
============
Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
composer require netbrothers-gmbh/version-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require netbrothers-gmbh/version-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    NetBrothers\VersionBundle\NetBrothersVersionBundle::class => ['all' => true],
];
```

Setup
=============
You have to set up the bundle:

1. Copy `installation/config/packages/netbrothers_syncacc.yaml` to symfony's config path.


2. Set the credentials either in `.env`-file or `netbrothers_syncacc.yaml`:
   
| `.env`                            |   `netbrothers_syncacc.yaml`      | Description                       |
| --------                          | --------                          | ----------                        |
| ACC_ENABLE                        | acc_enable                        | enable acc                        |
| ACC_SERVER                        | acc_server                        | Url ACC-Server                    |
| ACC_SOFTWARE_TOKEN                | acc_software_token                | SoftwareToken                     |
| ACC_SERVER_TOKEN                  | acc_server_token                  | Server-Token                      |
| ACC_USE_BASIC_AUTH                | acc_use_basic_auth                | enable Authentication Basic-Auth  |
| ACC_BASIC_AUTH_USER               | acc_basic_auth_user               | Username Basic-Auth               |
| ACC_BASIC_AUTH_PASSWORD           | acc_basic_auth_password           | Password Basic-Auth               |

3. Clear symfony's cache.
   

4. Create tables by migration.


Usage
=====

1. Open a command console, enter your project directory and execute the following command:
```console
php bin/console netbrothers:acc 
```

You can specify some options:

| option                    | meaning           |
| -----------               | -------           |
| all (default)             | get roles and acl |
| roles                     | get roles         |
| acl                       | get acls          |

__CAUTION__: Option `acl` only works, if table acl_role is filled.



Author
======
[Stefan Wessel, NetBrothers GmbH](https://netbrothers.de)

[![nb.logo](https://netbrothers.de/wp-content/uploads/2020/12/netbrothers_logo.png)](https://netbrothers.de)

Licence
=======
MIT