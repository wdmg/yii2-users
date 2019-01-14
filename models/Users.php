<?php

namespace wdmg\users\models;

use Yii;

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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
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
            'auth_key' => Yii::t('app/modules/users', 'Auth Key'),
            'password_hash' => Yii::t('app/modules/users', 'Password Hash'),
            'password_reset_token' => Yii::t('app/modules/users', 'Password Reset Token'),
            'email' => Yii::t('app/modules/users', 'Email'),
            'status' => Yii::t('app/modules/users', 'Status'),
            'created_at' => Yii::t('app/modules/users', 'Created At'),
            'updated_at' => Yii::t('app/modules/users', 'Updated At'),
        ];
    }
}
