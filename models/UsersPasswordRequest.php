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
                'message' => 'There is no user with this email address.'
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
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();

    }
}