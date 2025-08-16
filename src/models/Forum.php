<?php

namespace ZakharovAndrew\forum\models;

use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use ZakharovAndrew\forum\Module;

/**
 * Forum Subforum Model
 * 
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property int $order
 * @property $created_at
 * @property $updated_at
 */
class Forum extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%forum_forum}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Required fields
            [['title', 'category_id'], 'required'],
            
            // Strings
            [['title', 'slug'], 'string', 'max' => 255],
            [['description'], 'string'],
            
            // Unique slug
            [['slug'], 'unique'],
            
            // Integers
            [['category_id', 'order'], 'integer'],
            
            // Defaults
            [['order'], 'default', 'value' => 0],
            
            // Category exists
            [['category_id'], 'exist', 
                'skipOnError' => true, 
                'targetClass' => Category::class, 
                'targetAttribute' => ['category_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => Module::t('Category'),
            'title' => 'Forum Name',
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
            // Auto-slug from title (with Russian transliteration)
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'transliterator' => 'Russian-Latin/BGN',
            ],
            // Auto timestamps
            TimestampBehavior::class,
        ];
    }

    /**
     * Get parent category
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Get related topics (newest first)
     * @return \yii\db\ActiveQuery
     */
    public function getTopics()
    {
        return $this->hasMany(Topic::class, ['forum_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Get count of topics in this forum
     * @return int
     */
    public function getTopicsCount()
    {
        return $this->getTopics()->count();
    }
}
