<?php

namespace wdmg\users\models;

use wdmg\helpers\StringHelper;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use wdmg\helpers\ArrayHelper;
use wdmg\validators\JsonValidator;


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
 * @property string $options
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


    const SCENARIO_CREATE = 'create_user';
	const SCENARIO_UPDATE = 'update_user';
	const USR_UPDATE_OR_CREATE_PASSWD = 'manage_user_password';

    public $is_online;
    public $role;
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
            [['created_at', 'updated_at', 'lastseen_at'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::USR_STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::USR_STATUS_DELETED, self::USR_STATUS_WAITING, self::USR_STATUS_ACTIVE, self::USR_STATUS_BLOCKED]],
	        ['options', JsonValidator::class, 'message' => Yii::t('app/modules/menu', 'The value of field `{attribute}` must be a valid JSON, error: {error}.')],
	        [['role'], 'in', 'range' => $this->getAllRoles(false), 'on' => self::USR_UPDATE_OR_CREATE_PASSWD],
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
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['username', 'email', 'password', 'password_confirm'];
		$scenarios[self::SCENARIO_UPDATE] = ['email', 'password', 'password_confirm'];
		return $scenarios;
	}

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (
            $this->scenario == self::USR_UPDATE_OR_CREATE_PASSWD &&
            (($authManager = Yii::$app->getAuthManager()) && Yii::$app->user->can('admin')) &&
            isset($this->role)
        ) {
            $authManager->revokeAll($this->id);
            $role = $authManager->getRole(trim($this->role));
            $authManager->assign($role, $this->id);
        }

    }

    public function afterFind()
    {
        parent::afterFind();
        $this->role = $this->getDefaultRole(false);

        // Check only for current authorized user
        if (Yii::$app->getUser()) {
            if ($this->id == Yii::$app->getUser()->getId()) {
                $this->is_online = $this->getIsOnline();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();
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
            (!empty($this->password) && !empty($this->password_confirm))
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
            'role' => Yii::t('app/modules/users', 'Role'),
            'is_online' => Yii::t('app/modules/users', 'Is online?'),
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
        //return $this->getPrimaryKey();
	    return $this->id;
    }

	/**
	 * {@inheritdoc}
	 */
	public function getUserId()
	{
		return $this->id;
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
     * @param bool $instance
     * @return array|\yii\rbac\Role[]|null
     */
    public function getRoles($instance = true)
    {
        $authManager = Yii::$app->getAuthManager();
        if ($authManager)
            return ($instance) ? $authManager->getRolesByUser($this->id) : array_keys($authManager->getRolesByUser($this->id));
        else
            return null;
    }

    /**
     * @param bool $instance
     * @return int|mixed|string|null
     */
    public function getDefaultRole($instance = true)
    {
        $roles = $this->getRoles();
        if ($roles)
            return ($instance) ? end($roles) : ArrayHelper::keyLast($roles);
        else
            return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignments()
    {
        $authManager = Yii::$app->getAuthManager();
        if ($authManager)
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
        if ($authManager)
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

    public function getAllRoles($instance = true)
    {
        $authManager = Yii::$app->getAuthManager();
        if ($authManager){
            return ($instance) ? $authManager->roles : array_keys($authManager->roles);
        } else
            return null;
    }

    /**
     * @return array
     */
    public function getRolesList($allRoles = false)
    {

        $list = [];
        if ($roles = $this->getAllRoles(false)) {
            $list = array_reverse(array_combine($roles, $roles));
        }

        if ($allRoles)
            $list = ArrayHelper::merge([
                '*' => Yii::t('app/modules/users', 'All roles')
            ], $list);

        return $list;
    }


    /** Check and return user is online?
     *
     * @return bool
     */
    public function getIsOnline()
    {
        if (strtotime('-1 minutes', strtotime(date('Y-m-d H:i:s'))) <= strtotime($this->lastseen_at))
            $this->is_online = true;
        else
            $this->is_online = false;

        return $this->is_online;
    }

    /** Check and return user option(s)
     *
     * @return mixed
     */
    public static function getOptions($name = null, $default = null)
    {

	    $user = static::findOne(Yii::$app->getUser()->getIdentity()->getId());
        if (!empty($user->options)) {

	        $options = $user->options;
	        if (!is_null($name)) {

		        if (isset($options[$name]))
			        return $options[$name];

	        } else {
		        return $options;
	        }
        }

        return $default;
    }

    /** Check and return user option(s)
     *
     * @return mixed
     */
    public static function setOptions($values = null, $json_validation = true)
    {
		$options = self::getOptions();
	    $options = array_merge($options, $values);
	    $user = static::findOne(Yii::$app->getUser()->getIdentity()->getId());
	    $user->options = $options;
        return $user->update((bool)$json_validation, ['options']);
    }

    /**
     * Return stats count by all users
     *
     * @return array|null
     */
    public static function getStatsCount($onlyActive = true, $asArray = false) {
        $counts = static::find()
            ->select([new \yii\db\Expression('SUM( CASE WHEN `created_at` >= TIMESTAMP(CURRENT_TIMESTAMP() - INTERVAL 1 DAY) THEN 1 END ) AS count')])
            ->addSelect([new \yii\db\Expression('SUM( CASE WHEN `lastseen_at` >= TIMESTAMP(CURRENT_TIMESTAMP() - INTERVAL 1 MINUTE) THEN 1 END ) AS online')])
            ->addSelect([new \yii\db\Expression('SUM( CASE WHEN `id` > 0 THEN 1 END ) AS total')]);

        if ($onlyActive)
            $counts->where(['status' => static::USR_STATUS_ACTIVE]);

        if ($asArray)
            return $counts->asArray()->one();

        return $counts->one();
    }
}