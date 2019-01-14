<?php

namespace wdmg\users;

use yii\base\BootstrapInterface;
use Yii;


class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Get the module instance
        $module = Yii::$app->getModule('users');

        // Get URL path prefix if exist
        $prefix = (isset($module->routePrefix) ? $module->routePrefix . '/' : '');

        // Add module URL rules
        /*$app->getUrlManager()->addRules(
            [
                $prefix.'<controller:(default)>/' => 'users/<controller>/index',
                $prefix.'users/<controller:(default)>/<action:\w+>' => 'users/<controller>/<action>',
                $prefix.'<controller:(default)>/<action:\w+>' => 'users/<controller>/<action>',
            ],
            false
        );*/
    }
}
