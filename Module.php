<?php

namespace wdmg\users;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * users module definition class
 */
class Module extends \yii\base\Module
{

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\users\controllers';

    /**
     * @var string the prefix for routing of module
     */
    public $routePrefix = "admin";

    /**
     * @var string the vendor name of module
     */
    private $vendor = "wdmg";

    /**
     * @var string the module version
     */
    private $version = "1.0.3";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 5;

    /**
     * @var array of strings missing translations
     */
    public $missingTranslation;

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

        // Set controller namespace for console commands
        if (Yii::$app instanceof \yii\console\Application)
            $this->controllerNamespace = 'wdmg\users\commands';

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


        // Set current version of module
        $this->setVersion($this->version);

        // Register translations
        $this->registerTranslations();
    }

    /**
     * Return module vendor
     * @var string of current module vendor
     */
    public function getVendor() {
        return $this->vendor;
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {

        // Log to debuf console missing translations
        if (is_array($this->missingTranslation) && YII_ENV == 'dev')
            Yii::warning('Missing translations: ' . var_export($this->missingTranslation, true), 'i18n');

        $result = parent::afterAction($action, $result);
        return $result;

    }

    // Registers translations for the module
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['app/modules/users'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/wdmg/yii2-users/messages',
            'on missingTranslation' => function($event) {

                if (YII_ENV == 'dev')
                    $this->missingTranslation[] = $event->message;

            },
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('app/modules/users' . $category, $message, $params, $language);
    }
}
