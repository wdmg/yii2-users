<?php

/* @var $this yii\web\View */
/* @var $user \wdmg\users\models\Users */

$confirmLink = Yii::$app->urlManager->createAbsoluteUrl([$linkRoute, 'token' => $user->email_confirm_token]);

?>
<?= Yii::t('app/modules/users', 'Hi {username}!', [
    'username' => $user->username,
]); ?>

<?= Yii::t('app/modules/users', 'Someone or you have registered on the site {appname}.', [
    'appname' => Yii::$app->name,
]); ?>

<?= Yii::t('app/modules/users', 'Follow the link below to confirm your registration: {link}', [
    'link' => $confirmLink,
]); ?>
