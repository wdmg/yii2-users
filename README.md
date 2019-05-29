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

| ID   | Username  | Password        | Email               | Status        |
| ---- | --------- | --------------- | ------------------- | ------------- |
| 100  | admin     | adminadmin      | admin@example.com   |               |
| 101  | demo      | demodemo        | demo@example.com    |               |
| 102  | alice     | alicealice      | alice@example.com   |               |
| 103  | bob       | bobbob          | bob@example.com     |               |
| 104  | johndoe   | johndoejohndoe  | johndoe@example.com | `diactivated` |
| 105  | janedoe   | janedoejanedoe  | janedoe@example.com | `diactivated` |

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-users/migrations`

# Configure

To add a module to the project, add the following data in your configuration file:

    
    'components' => [
        'user' => [
            'identityClass' => 'wdmg\users\models\Users',
        ],
        ...
    ],
    'modules' => [
        'tickets' => [
            'class' => 'wdmg\users\Module',
            'routePrefix' => 'admin'
            'options' => [
                'rememberDuration' => (3600 * 24 * 30),
                'signupConfirmation' => [
                    'needConfirmation' => false,
                    'checkTokenRoute' => 'site/signup-confirm',
                    'supportEmail' => 'noreply@example.com',
                    'emailViewPath' => [
                        'html' => '@vendor/wdmg/yii2-users/mail/signupConfirmation-html',
                        'text' => '@vendor/wdmg/yii2-users/mail/signupConfirmation-text',
                    ],
                ],
                'passwordReset' => [
                    'resetTokenExpire' => 3600,
                    'checkTokenRoute' => 'site/reset-password',
                    'supportEmail' => 'noreply@example.com',
                    'emailViewPath' => [
                        'html' => '@vendor/wdmg/yii2-users/mail/passwordReset-html',
                        'text' => '@vendor/wdmg/yii2-users/mail/passwordReset-text',
                    ],
                ],
            ],
        ],
        ...
    ],

If you have connected the module not via a composer add Bootstrap section:

`
$config['bootstrap'][] = 'wdmg\users\Bootstrap';
`

# Usage
See the [USECASES.md](https://github.com/wdmg/yii2-users/blob/master/USECASES.md) for more details.

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('users')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [in progress development]
* v.1.1.0 - Added module name and description, translations. Routing fix.
* v.1.0.4 - Added dashboard navigation items for NavBar