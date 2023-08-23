<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "datos_personales_e_d".
 *
 * @property int $id
 * @property string $ci
 * @property string $ApellidoPaterno
 * @property string $ApellidoMaterno
 * @property string $Nombres
 * @property string $email
 */
class Datospersonalesed extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'datos_personales_e_d';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ci', 'ApellidoPaterno', 'ApellidoMaterno', 'Nombres', 'email'], 'required'],
            [['ci'], 'string', 'max' => 15],
            [['ApellidoPaterno', 'ApellidoMaterno'], 'string', 'max' => 20],
            [['Nombres', 'email'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Iddatos Personales',
            'ci' => 'Ci',
            'ApellidoPaterno' => 'Apellido Paterno',
            'ApellidoMaterno' => 'Apellido Materno',
            'Nombres' => 'Nombres',
            'email' => 'Email',
        ];
    }
}
