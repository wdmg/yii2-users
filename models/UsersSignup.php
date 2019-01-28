<?php

namespace wdmg\users\models;

use Yii;
use yii\base\Model;
use wdmg\users\models\Users;
use wdmg\validators\StopListValidator;

class UsersSignup extends Model
{
    public $username;
    public $email;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            [['username'], StopListValidator::className(), 'stoplist' => ['admin', 'editor', 'manager']],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => Users::className(), 'message' => Yii::t('app/modules/users', 'This email address has already been taken.')],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate())
            return null;

        $user = new Users();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        /* @var $module, array of current module */
        $module = Yii::$app->getModule('users', false);

        // Set status inactive and send confirmation email
        if ($module->options["signupConfirmation"]["needConfirmation"]) {
            $user->email_confirm_token = Yii::$app->security->generateRandomString();
            $user->status = Users::USR_STATUS_WAITING;
            $this->sendEmailConfirmation($user);
        } else {
            $user->status = Users::USR_STATUS_ACTIVE;
        }

        if($user->save()) {

            // Assign default role to new user
            $authManager = Yii::$app->getAuthManager();
            if($authManager) {
                $roles = $authManager->defaultRoles;
                foreach (array_unique($roles) as $role) {
                    $role = $authManager->getRole($role);
                    $authManager->assign($role, $user->getId());
                }
            }

            return $user;

        } else {
            throw new \RuntimeException(Yii::t('app/modules/users', 'User registration failed!'));
        }

        return null;

    }

    public function sendEmailConfirmation($user)
    {
        /* @var $module, array of current module */
        $module = Yii::$app->getModule('users', false);

        // Get route for build reset link
        $linkRoute = $module->options["signupConfirmation"]["checkTokenRoute"];
        if(!$linkRoute)
            $linkRoute = Yii::$app->requestedRoute;

        // Get sender`s email adress
        $supportEmail = $module->options["signupConfirmation"]["supportEmail"];
        if(!$supportEmail)
            $supportEmail = Yii::$app->params['supportEmail'];

        $sent = Yii::$app
            ->mailer
            ->compose(
                ['html' => $module->options["signupConfirmation"]["emailViewPath"]["html"], 'text' => $module->options["signupConfirmation"]["emailViewPath"]["text"]],
                ['user' => $user, 'linkRoute' => $linkRoute]
            )
            ->setFrom([$supportEmail => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject(Yii::t('app/modules/users', 'Signup confirmation {appname}', [
                'appname' => Yii::$app->name,
            ]))
            ->send();


        // Log to user about need confirmation
        Yii::$app->session->setFlash('success', Yii::t('app/modules/users', 'Check your email for confirmation registration.'));

        if (!$sent)
            throw new \RuntimeException(Yii::t('app/modules/users', 'Error sending registration confirmation email.'));

    }

    public function userConfirmation($token)
    {
        if (empty($token))
            throw new \DomainException(Yii::t('app/modules/users', 'Error! An empty registration confirmation token.'));

        $user = Users::findOne(['email_confirm_token' => $token]);

        if (!$user)
            throw new \DomainException(Yii::t('app/modules/users', 'Error! User not found.'));

        $user->email_confirm_token = null;
        $user->status = Users::USR_STATUS_ACTIVE;

        if (!$user->save())
            throw new \RuntimeException(Yii::t('app/modules/users', 'Error updating user data.'));

        if (!Yii::$app->getUser()->login($user))
            throw new \RuntimeException(Yii::t('app/modules/users', 'User authentication error.'));

    }

}