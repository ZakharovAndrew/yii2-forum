<?php

namespace ZakharovAndrew\forum\models;

use yii\db\ActiveRecord;
use yii\helpers\Inflector; // For slug generation
use yii\behaviors\SluggableBehavior; // Auto-slug from title
use yii\behaviors\TimestampBehavior; // Auto timestamp
use ZakharovAndrew\forum\Module;

/**
 * Forum Category Model
 * 
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property int $order
 * @property int $created_at
 * @property int $updated_at
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%forum_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Required fields
            [['title'], 'required'],
            
            // String validation
            [['title', 'slug'], 'string', 'max' => 255],
            [['description'], 'string'],
            
            // Slug should be unique
            [['slug'], 'unique'],
            
            [['order'], 'integer'],
            
            // Default values
            [['order'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Category Name',
            'slug' => 'URL Slug',
            'description' => Module::t('Description'),
            'order' => 'Sort Order',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            // Auto-generate slug from title using transliteration
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'immutable' => true,
                'transliterator' => 'Russian-Latin/BGN', // Transliteration rule
            ],
            // Auto timestamps
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
        ];
    }

    /**
     * Get related forums
     * @return \yii\db\ActiveQuery
     */
    public function getForums()
    {
        return $this->hasMany(Forum::class, ['category_id' => 'id'])
            ->orderBy(['order' => SORT_ASC]);
    }

    /**
     * Before save hook
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        // Manual slug fallback if behavior didn't work
        if (empty($this->slug)) {
            $this->slug = Inflector::slug($this->title);
        }
        
        return true;
    }
}
