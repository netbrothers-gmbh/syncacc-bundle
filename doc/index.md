# NetBrothersSyncAccBundle Documentation

This document provides a detailed guide to installing, configuring, and using the NetBrothersSyncAccBundle.

## 1. Installation

This bundle is for use with Symfony 7.4 or higher.

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```console
composer require netbrothers-gmbh/syncacc-bundle
```

### Step 2: Enable the Bundle

If you are not using Symfony Flex, you must enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    NetBrothers\SyncAccBundle\NetBrothersSyncAccBundle::class => ['all' => true],
];
```

### Step 3: Create the Configuration File

Copy the example configuration file from the bundle into your project's configuration directory.

```console
cp vendor/netbrothers-gmbh/syncacc-bundle/doc/packages/net_brothers_sync_acc.yaml config/packages/
```

### Step 4: Create Database Tables

This bundle requires several database tables to store the synchronized data. You can create these tables by generating and running a Doctrine migration.

```console
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## 2. Configuration

The bundle can be configured by setting environment variables in your `.env` file. This is the recommended method. The configuration file is located at `config/packages/net_brothers_sync_acc.yaml`.

### Parameters

| Environment Variable      | YAML Key                  | Type      | Default Value       | Description                               |
|---------------------------|---------------------------|-----------|---------------------|-------------------------------------------|
| `ACC_ENABLE`              | `acc_enable`              | `bool`    | `false`             | Enables or disables the bundle.           |
| `ACC_SERVER`              | `acc_server`              | `string`  | `https://localhost` | URL to your ACC-Server instance.          |
| `ACC_SOFTWARE_TOKEN`      | `acc_software_token`      | `string`  | `SOFTWARE_APP_ID`   | The Software Token (AppId) from your ACC. |
| `ACC_SERVER_TOKEN`        | `acc_server_token`        | `string`  | `SERVER_TOKEN`      | The Server Token from your ACC.           |
| `ACC_USE_BASIC_AUTH`      | `acc_use_basic_auth`      | `bool`    | `false`             | Whether to use Basic Authentication.      |
| `ACC_BASIC_AUTH_USER`     | `acc_basic_auth_user`     | `string`  | `netbrothers`       | Username for Basic Authentication.        |
| `ACC_BASIC_AUTH_PASSWORD` | `acc_basic_auth_password` | `string`  | `password`          | Password for Basic Authentication.        |

### Example Configuration

A full example of the configuration file (`config/packages/net_brothers_sync_acc.yaml`):

```yaml
net_brothers_sync_acc:
  acc_enable: '%env(bool:ACC_ENABLE)%'
  acc_server: '%env(default:net_brothers_sync_acc.default_null:ACC_SERVER)%'
  acc_software_token: '%env(default:net_brothers_sync_acc.default_null:ACC_SOFTWARE_TOKEN)%'
  acc_server_token: '%env(default:net_brothers_sync_acc.default_null:ACC_SERVER_TOKEN)%'
  acc_use_basic_auth: '%env(default:net_brothers_sync_acc.default_null:bool:ACC_USE_BASIC_AUTH)%'
  acc_basic_auth_user: '%env(default:net_brothers_sync_acc.default_null:ACC_BASIC_AUTH_USER)%'
  acc_basic_auth_password: '%env(default:net_brothers_sync_acc.default_null:ACC_BASIC_AUTH_PASSWORD)%'
```

## 3. Usage

### Synchronizing Data

The primary way to use this bundle is via the provided console command. This command fetches the latest permissions from your Access Control Center (ACC) instance and stores them in your local database.

To run a full synchronization (roles and ACLs), execute the following command:

```console
php bin/console netbrothers:sync-acc
```

### Command Options

You can control which parts of the data are synchronized by using the `--sync-table` option.

| Option Value | Description                                       |
|--------------|---------------------------------------------------|
| `all`        | (Default) Synchronizes both roles and ACLs.       |
| `role`       | Synchronizes only the roles (`acl_role` table).   |
| `acl`        | Synchronizes only the permissions (`acl_allow` table). |

**Example: Synchronizing only roles**
```console
php bin/console netbrothers:sync-acc --sync-table=role
```

> **Note:** The `acl` option requires the roles to be present in the database. It is recommended to run a full sync first.
