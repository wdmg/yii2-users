<?php

namespace wdmg\users;

/**
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 */

use yii\base\BootstrapInterface;
use Yii;


class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Get the module instance
        $module = Yii::$app->getModule('users');

        // Get URL path prefix if exist
        if (isset($module->routePrefix)) {
            $app->getUrlManager()->enableStrictParsing = true;
            $prefix = $module->routePrefix . '/';
        } else {
            $prefix = '';
        }

        // Add module URL rules
        $app->getUrlManager()->addRules(
            [
                $prefix . '<module:users>/' => '<module>/users/index',
                $prefix . '<module:users>/view' => '<module>/users/view',
                $prefix . '<module:users>/<controller:(users)>/' => '<module>/<controller>',
                $prefix . '<module:users>/<controller:(users)>/<action:\w+>' => '<module>/<controller>/<action>',
                [
                    'pattern' => $prefix . '<module:users>/',
                    'route' => '<module>/users/index',
                    'suffix' => '',
                ], [
                'pattern' => $prefix . '<module:users>/view',
                'route' => '<module>/users/view',
                'suffix' => '',
            ], [
                'pattern' => $prefix . '<module:users>/<controller:(users)>/',
                'route' => '<module>/<controller>',
                'suffix' => '',
            ], [
                'pattern' => $prefix . '<module:users>/<controller:(users)>/<action:\w+>',
                'route' => '<module>/<controller>/<action>',
                'suffix' => '',
            ],
            ],
            true
        );
    }
}
