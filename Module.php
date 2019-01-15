<?php

namespace wdmg\users;

use Yii;


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
    public $vendor = "wdmg";

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'wdmg\users\commands';
        }

        // Register translations
        $this->registerTranslations();
    }

    // Registers translations for the module
    public function registerTranslations()
    {
        Yii::$app->i18n->translations['app/modules/users'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/wdmg/yii2-users/messages',
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('app/modules/users' . $category, $message, $params, $language);
    }
}
