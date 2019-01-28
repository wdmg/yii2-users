<?php

/* @var $this yii\web\View */
/* @var $user \wdmg\users\models\Users */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl([$linkRoute, 'token' => $user->password_reset_token]);

?>
<?= Yii::t('app/modules/users', 'Hi {username}!', [
    'username' => $user->username,
]); ?>

<?= Yii::t('app/modules/users', 'Follow the link below to reset your password: {link}!', [
    'link' => $resetLink,
]); ?>
