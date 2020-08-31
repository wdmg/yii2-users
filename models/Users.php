<?php

namespace wdmg\users\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;


/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */

class Users extends ActiveRecord implements IdentityInterface
{

    /**
     * Ticket status
     * const, int: 0 - Inactive user, 10 - Active user, 20 - Blocked user
     */
    const USR_STATUS_DELETED = 0;
    const USR_STATUS_WAITING = 5;
    const USR_STATUS_ACTIVE = 10;
    const USR_STATUS_BLOCKED = 15;

    const USR_UPDATE_OR_CREATE_PASSWD = 'manage_user_password';

    public $password;
    public $password_confirm;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function() {
                    return date("Y-m-d H:i:s");
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::USR_STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::USR_STATUS_DELETED, self::USR_STATUS_WAITING, self::USR_STATUS_ACTIVE, self::USR_STATUS_BLOCKED]],

            [['password', 'password_confirm'], 'string', 'min' => 8, 'max' => 24, 'on' => self::USR_UPDATE_OR_CREATE_PASSWD],
            [['password', 'password_confirm'], 'match', 'pattern' => '/(.*[A-Z]){1,}.*/', 'message' => Yii::t('app/modules/users', 'The password must contains min 1 char in uppercase.'), 'on' => self::USR_UPDATE_OR_CREATE_PASSWD],
            [['password', 'password_confirm'], 'match', 'pattern' => '/(.*[\d]){1,}.*/', 'message' => Yii::t('app/modules/users', 'The password must contains min 1 char as number.'), 'on' => self::USR_UPDATE_OR_CREATE_PASSWD],
            [['password', 'password_confirm'], 'match', 'pattern' => '/(.*[a-z]){1,}.*/', 'message' => Yii::t('app/modules/users', 'The password must contains min 1 char in lowercase.'), 'on' => self::USR_UPDATE_OR_CREATE_PASSWD],
            [['password', 'password_confirm'], 'match', 'pattern' => '/(.*[\W]){1,}.*/', 'message' => Yii::t('app/modules/users', 'The password must contents min 1 special char.'), 'on' => self::USR_UPDATE_OR_CREATE_PASSWD],
            [['password_confirm'], 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('app/modules/users', 'Password and password confirmation must match.'), 'on' => self::USR_UPDATE_OR_CREATE_PASSWD],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Attach default role to new user
        /*$authManager = Yii::$app->getAuthManager();
        if($authManager) {
            foreach ($authManager->defaultRoles as $role) {
                $role = $authManager->getRole($role);
                $authManager->assign($role, $this->getId());
            }
        }*/

    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();

        // Deattach all user roles
        /*$authManager = Yii::$app->getAuthManager();
        if($authManager) {
            $authManager->revokeAll($this->getId());
        }*/

    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (
            $this->scenario == self::USR_UPDATE_OR_CREATE_PASSWD &&
            (
                $this->id == Yii::$app->user->id ||
                Yii::$app->user->can('admin')
            ) &&
            isset($this->password)
        ) {
            $this->setPassword($this->password);
            $this->generateAuthKey();
            $this->removePasswordResetToken();
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/users', 'ID'),
            'username' => Yii::t('app/modules/users', 'Username'),
            'auth_key' => Yii::t('app/modules/users', 'Auth key'),
            'password_hash' => Yii::t('app/modules/users', 'Password hash'),
            'password_reset_token' => Yii::t('app/modules/users', 'Password reset token'),
            'email' => Yii::t('app/modules/users', 'Email'),
            'password' => Yii::t('app/modules/users', 'Password'),
            'password_confirm' => Yii::t('app/modules/users', 'Password confirm'),
            'status' => Yii::t('app/modules/users', 'Status'),
            'created_at' => Yii::t('app/modules/users', 'Created at'),
            'updated_at' => Yii::t('app/modules/users', 'Updated at'),
            'roles' => Yii::t('app/modules/users', 'Roles'),
            'permissions' => Yii::t('app/modules/users', 'Permissions'),
            'assignments' => Yii::t('app/modules/users', 'Assignments'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::USR_STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //return static::findOne(['auth_key' => $token]);
        throw new NotSupportedException('Method "findIdentityByAccessToken" is not implemented.');
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token))
            return null;

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::USR_STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token))
            return false;

        // Get current module
        if (Yii::$app->hasModule('admin/users'))
            $module = Yii::$app->getModule('admin/users');
        else
            $module = Yii::$app->getModule('users');

        // Get time to expire reset token
        if($module->passwordReset["resetTokenExpire"])
            $expire = intval($module->passwordReset["resetTokenExpire"]);
        else
            $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Return username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return user e-mail
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAllUsers()
    {
        return $this->findAll(['status' => Users::USR_STATUS_ACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        $authManager = Yii::$app->getAuthManager();

        if($authManager)
            return $authManager->getRolesByUser($this->id); //@TODO: must returned ActiveQuery
        else
            return null;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignments()
    {
        $authManager = Yii::$app->getAuthManager();

        if($authManager)
            return $authManager->getAssignments($this->id); //@TODO: must returned ActiveQuery
        else
            return null;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        $authManager = Yii::$app->getAuthManager();

        if($authManager)
            return $authManager->getPermissionsByUser($this->id); //@TODO: must returned ActiveQuery
        else
            return null;
    }


    /**
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        $list = [
            self::USR_STATUS_DELETED => Yii::t('app/modules/users','Deleted'),
            self::USR_STATUS_WAITING => Yii::t('app/modules/users','Waiting'),
            self::USR_STATUS_ACTIVE => Yii::t('app/modules/users','Active'),
            self::USR_STATUS_BLOCKED => Yii::t('app/modules/users','Blocked'),
        ];

        if ($allStatuses)
            $list = ArrayHelper::merge([
                '*' => Yii::t('app/modules/users', 'All statuses')
            ], $list);

        return $list;
    }

}