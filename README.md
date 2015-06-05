# MODX Installer

MODX Installer is A simple helper to help install MODX Revolution from the CLI.


## Goals

MODX Installer has originally been developed to be used in Composer [scripts](https://getcomposer.org/doc/articles/scripts.md), to automatically : 

* build MODX Revolution package
* move folders in custom locations if needed
* install/upgrade MODX Revolution

The underlying idea is to be able to "consume" MODX Revolution as a dependency.


## Requirements

* PHP 5.4+
* MODX Revolution 2.3+


## Documentation

TODO

Config keys

Key                         | Description
----------------------------|------------
database_type               |
database_server             |
database                    |
database_user               |
database_password           |
database_connection_charset |
database_collation          |
cmsadmin                    |
cmspassword                 |
cmsadminemail               |
table_prefix                |
https_port                  |
http_host                   |
cache_disabled              |
language                    |
core_path                   |
context_mgr_path            |
context_mgr_url             |
context_connectors_path     |
context_connectors_url      |
context_web_path            |
context_web_url             |
inplace                     |
unpacked                    |
remove_setup_directory      |


## License

MODX Installer is licensed under the [MIT license](LICENSE).
Copyright 2015 Melting Media <https://github.com/meltingmedia>
