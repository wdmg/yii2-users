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

        /* @var $module, array of current module */
        $module = Yii::$app->getModule('users', false);

        if($module->options["rememberDuration"])
            $duration = intval($module->options["rememberDuration"]);
        else
            $duration = (3600 * 24 * 30);

        if ($this->validate())
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? $duration : 0);

        return false;
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