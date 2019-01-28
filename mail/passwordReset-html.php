<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user \wdmg\users\models\Users */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl([$linkRoute, 'token' => $user->password_reset_token]);

?>
<div class="password-reset">
    <h3><?= Html::encode(Yii::t('app/modules/users', 'Hi {username}!', [
            'username' => $user->username,
        ])); ?></h3>
    <p><?= Yii::t('app/modules/users', 'Follow the link below to reset your password: {link}', [
        'link' => Html::a(Html::encode($resetLink), $resetLink),
        ]); ?></p>
</div>
