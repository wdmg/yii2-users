<?php

namespace wdmg\users;

/**
 * Yii2 Users
 *
 * @category        Module
 * @version         1.2.4
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-users
 * @copyright       Copyright (c) 2019 - 2021 W.D.M.Group, Ukraine
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
    private $version = "1.2.4";

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
     * @var bool, of allow to multi Sign In
     */
    public $multiSignIn = true;

    /**
     * @var integer, session timeout in sec. of auth (where `0` is unlimited)
     */
    public $sessionTimeout = 0;

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
    public function dashboardNavItems($options = false)
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

        if (isset(Yii::$app->params['users.multiSignIn']))
            $this->multiSignIn = Yii::$app->params['users.multiSignIn'];

        if (isset(Yii::$app->params['users.sessionTimeout']))
            $this->sessionTimeout = Yii::$app->params['users.sessionTimeout'];

        if (!($app instanceof \yii\console\Application) && !$app->user->isGuest) {
            $module = $this;
            \yii\base\Event::on(\yii\web\Controller::class, \yii\web\Controller::EVENT_BEFORE_ACTION, function ($event) use ($app, $module) {
                if (!$module->isRestAPI()) {
                    $lastseen_at = $app->user->identity->lastseen_at;
                    if (strtotime('-1 minutes', strtotime(date('Y-m-d H:i:s'))) > strtotime($lastseen_at)) {
                        $app->user->identityClass::updateAll(['lastseen_at' => date('Y-m-d H:i:s')], ['id' => $app->user->id]);
                    } else if (intval($module->sessionTimeout) > 0) {
                        $time_delta = intval(strtotime(date('Y-m-d H:i:s')) - strtotime($lastseen_at)) - intval($module->sessionTimeout);

                        if ($time_delta >= intval($module->sessionTimeout)) {

                            $timeout = intval($module->sessionTimeout);
                            $hours = floor($timeout / 3600);
                            $minutes = floor(($timeout / 60) % 60);
                            $seconds = $timeout % 60;

                            if ($hours > 0) {
                                $timeout_msg = Yii::t('app/modules/users', 'Session was terminated automatically due to inactivity for more than {timeout, plural, =0{} =1{# hour} one{# hour} few{# hours} many{# hours} other{# hours}}.', [
                                    'timeout' => $hours
                                ]);
                            } else if ($minutes > 0) {
                                $timeout_msg = Yii::t('app/modules/users', 'Session was terminated automatically due to inactivity for more than {timeout, plural, =0{} =1{# minute} one{# minute} few{# minutes} many{# minutes} other{# minutes}}.', [
                                    'timeout' => $minutes
                                ]);
                            } else {
                                $timeout_msg = Yii::t('app/modules/users', 'Session was terminated automatically due to inactivity for more than {timeout, plural, =0{} =1{# second} one{# second} few{# seconds} many{# seconds} other{# seconds}}.', [
                                    'timeout' => $seconds
                                ]);
                            }

                            $app->user->logout();
                            $app->getSession()->setFlash(
                                'warning',
                                $timeout_msg
                            );
                            $app->controller->goHome();
                        }
                    }
                }
            });
        }
    }
}
