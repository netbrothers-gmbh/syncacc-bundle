# NetBrothers SyncAcc Bundle for Symfony

<!-- Badges will go here -->
<p>
    <a href="https://packagist.org/packages/netbrothers-gmbh/syncacc-bundle"><img src="https://img.shields.io/packagist/v/netbrothers-gmbh/syncacc-bundle" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/netbrothers-gmbh/syncacc-bundle"><img src="https://img.shields.io/packagist/dt/netbrothers-gmbh/syncacc-bundle" alt="Total Downloads"></a>
    <a href="https://github.com/netbrothers-gmbh/syncacc-bundle/blob/main/LICENSE"><img src="https://img.shields.io/github/license/netbrothers-gmbh/syncacc-bundle" alt="License"></a>
</p>

This Symfony bundle provides an easy way to integrate the NetBrothers Access Control Center (ACC) into your Symfony application. 
It allows you to synchronize roles and permissions from your central ACC instance into your local Symfony application's database.

## Features

- Synchronize roles and permissions via a console command.
- Easy configuration through environment variables.
- Compatible with Symfony 7.4 and 8.x.

## Installation

```console
composer require netbrothers-gmbh/syncacc-bundle
```

## Quick Start

1.  **Configure your environment variables** in the `.env` file. At a minimum, you need:

    ```dotenv
    ACC_ENABLE=true
    ACC_SERVER=https://your-acc-instance.com
    ACC_SOFTWARE_TOKEN=your_software_token
    ACC_SERVER_TOKEN=your_server_token
    ```

2.  **Run the synchronization command:**

    ```console
    php bin/console netbrothers:sync-acc
    ```

## Documentation

For detailed instructions on installation, configuration, and usage, please see the **[full documentation](doc/index.md)**.

## Changelog

See the [CHANGELOG.md](CHANGELOG.md) file for a log of all changes and versions.

## License

This bundle is released under the MIT license. See the bundled [LICENSE](LICENSE) file for details.

## Author

This bundle is developed and maintained by [Stefan Wessel, NetBrothers GmbH](https://netbrothers.de).

[![NetBrothers Logo](https://netbrothers.de/wp-content/uploads/2020/12/netbrothers_logo.png)](https://netbrothers.de)
