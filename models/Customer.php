<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


/**
 * This is the model class for table "{{%customers}}".
 *
 * @property int $id
 * @property int $status
 * @property string $login
 * @property string $auth_key
 * @property string $password
 * @property string $name
 * @property string $surname
 * @property string $created
 */
class Customer extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customers}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['login', 'password', 'name', 'surname'], 'required'],
            [['created'], 'safe'],
            [['login'], 'string', 'max' => 125],
            [['auth_key'], 'string', 'max' => 32],
            [['password'], 'string', 'max' => 60],
            [['name', 'surname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'login' => 'Login',
            'auth_key' => 'Auth Key',
            'password' => 'Password',
            'name' => 'Name',
            'surname' => 'Surname',
            'created' => 'Created',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\CustomerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\CustomerQuery(get_called_class());
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }
    public function getId()
    {
        return $this->id;
    }
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }
}
