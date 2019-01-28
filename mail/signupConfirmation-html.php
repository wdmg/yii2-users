<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user \wdmg\users\models\Users */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([$linkRoute, 'token' => $user->email_confirm_token]);

?>
<div class="password-reset">
    <h3><?= Html::encode(Yii::t('app/modules/users', 'Hi {username}!', [
            'username' => $user->username,
        ])); ?></h3>
    <p><?= Yii::t('app/modules/users', 'Someone or you have registered on the site {appname}.', [
            'appname' => Yii::$app->name,
        ]); ?></p>
    <p><?= Yii::t('app/modules/users', 'Follow the link below to confirm your registration: {link}', [
        'link' => Html::a(Html::encode($confirmLink), $confirmLink),
        ]); ?></p>
</div>
