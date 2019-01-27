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

        if ($user->save()) {

            // Assign default role to new user
            $authManager = Yii::$app->getAuthManager();
            if($authManager) {
                foreach ($authManager->defaultRoles as $role) {
                    $role = $authManager->getRole($role);
                    $authManager->assign($role, $user->getId());
                }
            }

            return $user;
        } else {
            return null;
        }

    }
}