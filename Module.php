<?php

namespace wdmg\users;

/**
 * Yii2 Users
 *
 * @category        Module
 * @version         1.1.7
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
    private $version = "1.1.7";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 2;

    public $rememberDuration = (3600 * 24 * 30);
    public $signupConfirmation = [
        'needConfirmation' => false,
        'checkTokenRoute' => 'site/signup-confirm',
        'supportEmail' => 'noreply@example.com',
        'emailViewPath' => [
            'html' => '@vendor/wdmg/yii2-users/mail/signupConfirmation-html',
            'text' => '@vendor/wdmg/yii2-users/mail/signupConfirmation-text',
        ],
    ];
    public $passwordReset = [
        'resetTokenExpire' => 3600,
        'checkTokenRoute' => 'site/reset-password',
        'supportEmail' => 'noreply@example.com',
        'emailViewPath' => [
            'html' => '@vendor/wdmg/yii2-users/mail/passwordReset-html',
            'text' => '@vendor/wdmg/yii2-users/mail/passwordReset-text',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($createLink = false)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa fa-fw fa-user',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id])
        ];
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);
    }
}
