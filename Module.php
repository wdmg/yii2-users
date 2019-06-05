<?php

namespace wdmg\users;

/**
 * Yii2 Users
 *
 * @category        Module
 * @version         1.1.3
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-users
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;
use yii\helpers\ArrayHelper;

/**
 * Users module definition class
 */
class Module extends BaseModule
{

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\users\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "users/index";

    /**
     * @var string, the name of module
     */
    public $name = "Users";

    /**
     * @var string, the description of module
     */
    public $description = "Users management module";

    /**
     * @var string the module version
     */
    private $version = "1.1.3";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 5;

    /**
     * @var array, options of module
     */
    public $options = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set default options
        $default = [
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
        ];

        // Mixing default options with custom options
        $this->options = ArrayHelper::merge($default, $this->options);
    }

    public function bootstrap($app)
    {
        parent::bootstrap($app);
    }
}
