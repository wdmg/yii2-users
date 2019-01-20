[![Progress](https://img.shields.io/badge/required-Yii2_v2.0.13-blue.svg)](https://packagist.org/packages/yiisoft/yii2) [![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-users/total.svg)](https://GitHub.com/wdmg/yii2-users/releases/) [![GitHub version](https://badge.fury.io/gh/wdmg%2Fyii2-users.svg)](https://github.com/wdmg/yii2-users) ![Progress](https://img.shields.io/badge/progress-in_development-red.svg) [![GitHub license](https://img.shields.io/github/license/wdmg/yii2-users.svg)](https://github.com/wdmg/yii2-users/blob/master/LICENSE)

# Yii2 Users Module
Users management module for Yii2

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.13 and newest

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-users:dev-master"`

After configure db connection, run the following command in the console:

`$ php yii users/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations
  3) Batch insert demo data<sup>*</sup>

\* - The demo database contains 6 demo user`s with:

| ID   | Username  | Password  | Email               | Status        |
| ---- | --------- | --------- | ------------------- | ------------- |
| 100  | admin     | admin     | admin@example.com   |               |
| 101  | demo      | demo      | demo@example.com    |               |
| 102  | alice     | alice     | alice@example.com   |               |
| 103  | bob       | bob       | bob@example.com     |               |
| 104  | johndoe   | johndoe   | johndoe@example.com | `diactivated` |
| 105  | janedoe   | janedoe   | janedoe@example.com | `diactivated` |

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-users/migrations`

# Configure

To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'tickets' => [
            'class' => 'wdmg\users\Module',
            'routePrefix' => 'admin'
        ],
        ...
    ],

and Bootstrap section:

`
$config['bootstrap'][] = 'wdmg\users\Bootstrap';
`

# Routing
`http://example.com/admin/users` - Module dashboard

# Status and version
* v.1.0.0 - Module in progress development.