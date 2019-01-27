<?php
namespace wdmg\users\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use wdmg\users\models\Users;


class UsersResetPassword extends Model
{

    /**
     * @var string, user password
     */
    public $password;

    /**
     * @var \wdmg\users\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws InvalidArgumentException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token))
            throw new InvalidArgumentException(Yii::t('app/modules/users', 'Password reset token cannot be blank.'));


        $this->_user = Users::findByPasswordResetToken($token);

        if (!$this->_user)
            throw new InvalidArgumentException(Yii::t('app/modules/users', 'Wrong password reset token.'));

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save(false);
    }
}