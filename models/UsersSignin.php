<?php
namespace wdmg\users\models;

use Yii;
use yii\base\Model;
use wdmg\users\models\Users;

class UsersSignin extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if(!$user) {
                $this->addError($attribute, Yii::t('app/modules/users', 'Unknown authorization error.'));
            } else if (!$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app/modules/users', 'Incorrect username or password.'));
            } else if ($user && $user->validatePassword($this->password) && $user->status == Users::USR_STATUS_BLOCKED) {
                $this->addError($attribute, Yii::t('app/modules/users', 'Sorry, but your account has been blocked.'));
            } else if ($user && $user->validatePassword($this->password) && $user->status == Users::USR_STATUS_DELETED) {
                $this->addError($attribute, Yii::t('app/modules/users', 'Sorry, but your account has been deleted.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {

        if ($this->validate()) {

            // Get current module
            if (Yii::$app->hasModule('admin/users'))
                $module = Yii::$app->getModule('admin/users');
            else
                $module = Yii::$app->getModule('users');

            // Get time to remember user
            if ($module->rememberDuration)
                $duration = intval($module->rememberDuration);
            else
                $duration = (3600 * 24 * 30);

            $user = $this->getUser();
            if ($user->status === Users::USR_STATUS_ACTIVE) {
                if ($module->multiSignIn && (strtotime('-1 minutes', strtotime(date('Y-m-d H:i:s'))) <= strtotime($user->lastseen_at)))
                    throw new \DomainException(Yii::t('app/modules/users', 'It looks like you are already logged in! Sign out of your account in another browser/device and try to auth in a minute.'));

                return Yii::$app->user->login($user, $this->rememberMe ? $duration : 0);
            }

            if ($user->status === Users::USR_STATUS_WAITING)
                throw new \DomainException(Yii::t('app/modules/users', 'Registration is not complete. Confirm the email address of the link from the letter.'));

            if ($user->status === Users::USR_STATUS_BLOCKED)
                throw new \DomainException(Yii::t('app/modules/users', 'The user has been blocked by the administrator.'));

        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null)
            $this->_user = Users::findByUsername($this->username);

        return $this->_user;
    }

}