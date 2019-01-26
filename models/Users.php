<?php

namespace wdmg\users\models;

use Yii;
use \yii\behaviors\TimeStampBehavior;

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
class Users extends \yii\db\ActiveRecord
{

    /**
     * Ticket status
     * const, int: 0 - Deleted user, 10 - Active user
     */
    const USR_STATUS_DELETED = 0;
    const USR_STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{users}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
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
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
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
            'status' => Yii::t('app/modules/users', 'Status'),
            'created_at' => Yii::t('app/modules/users', 'Created at'),
            'updated_at' => Yii::t('app/modules/users', 'Updated at'),
        ];
    }
}
