<?php
namespace wdmg\users\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use wdmg\users\models\Users;


class UsersPasswordRequest extends Model
{
    /**
     * @var string, user email adress
     */
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\wdmg\users\models\Users',
                'filter' => ['status' => Users::USR_STATUS_ACTIVE],
                'message' => Yii::t('app/modules/users', 'There is no user with this email address.')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {

        // Get current module
        if (Yii::$app->hasModule('admin/users'))
            $module = Yii::$app->getModule('admin/users');
        else
            $module = Yii::$app->getModule('users');

        // Get route for build reset link
        $linkRoute = $module->passwordReset["checkTokenRoute"];
        if(!$linkRoute)
            $linkRoute = Yii::$app->requestedRoute;

        // Get sender`s email adress
        $supportEmail = $module->passwordReset["supportEmail"];
        if(!$supportEmail)
            $supportEmail = Yii::$app->params['supportEmail'];

        /* @var $user User */
        $user = Users::findOne([
            'status' => Users::USR_STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user)
            return false;

        if (!Users::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();

            if (!$user->save())
                return false;

        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => $module->passwordReset["emailViewPath"]["html"], 'text' => $module->passwordReset["emailViewPath"]["text"]],
                ['user' => $user, 'linkRoute' => $linkRoute]
            )
            ->setFrom([$supportEmail => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject(Yii::t('app/modules/users', 'Password reset for {appname}', [
                'appname' => Yii::$app->name,
            ]))
            ->send();

    }
}