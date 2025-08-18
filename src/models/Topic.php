<?php

namespace ZakharovAndrew\forum\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\models\User;
use ZakharovAndrew\forum\Module;

/**
 * Forum Topic Model
 * 
 * @property int $id
 * @property int $forum_id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property int $status
 * @property int $views
 * @property int $is_pinned
 * @property int $is_locked
 * @property $created_at
 * @property $updated_at
 */
class Topic extends ActiveRecord
{
    // Status constants
    const STATUS_ACTIVE = 1;
    const STATUS_CLOSED = 0;
    const STATUS_DELETED = 2;

    /**
     * {@inheritdoc}
     * @translate table name
     */
    public static function tableName()
    {
        return '{{%forum_topic}}';
    }

    /**
     * {@inheritdoc}
     * @translate validation rules
     */
    public function rules()
    {
        return [
            // Required fields
            [['title', 'content', 'forum_id'], 'required'],
            
            // Strings
            [['title', 'slug'], 'string', 'max' => 255],
            [['content'], 'string'],
            
            // Unique slug
            [['slug'], 'unique'],
            
            // Integers
            [['forum_id', 'user_id', 'status', 'views', 'is_pinned', 'is_locked'], 'integer'],
            
            // Default values
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['views'], 'default', 'value' => 0],
            [['is_pinned', 'is_locked'], 'default', 'value' => 0],
            
            // Forum exists
            [['forum_id'], 'exist', 
                'skipOnError' => true, 
                'targetClass' => Forum::class, 
                'targetAttribute' => ['forum_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     * @translate attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'forum_id' => 'Forum',
            'user_id' => 'Author',
            'title' => 'Topic Title',
            'slug' => 'URL Slug',
            'content' => 'Content',
            'status' => Module::t('Status'),
            'views' => 'Views',
            'is_pinned' => 'Pinned',
            'is_locked' => 'Locked',
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
            // Auto-slug from title
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'transliterator' => 'Russian-Latin/BGN',
            ],
            // Auto timestamps
            TimestampBehavior::class,
            // Auto set current user
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => null,
            ],
        ];
    }

    /**
     * Get status options for dropdown
     * @return array
     * @translate status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_DELETED => 'Deleted',
        ];
    }

    /**
     * Get parent forum
     * @return \yii\db\ActiveQuery
     */
    public function getForum()
    {
        return $this->hasOne(Forum::class, ['id' => 'forum_id']);
    }

    /**
     * Get author
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Get all posts
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['topic_id' => 'id'])
            ->orderBy(['created_at' => SORT_ASC]);
    }

    /**
     * Get first post (original post)
     * @return \yii\db\ActiveQuery
     */
    public function getFirstPost()
    {
        return $this->hasOne(Post::class, ['topic_id' => 'id'])
            ->orderBy(['created_at' => SORT_ASC])
            ->limit(1);
    }

    /**
     * Get last post
     * @return \yii\db\ActiveQuery
     */
    public function getLastPost()
    {
        return $this->hasOne(Post::class, ['topic_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(1);
    }

    /**
     * Get tags
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('{{%forum_topic_tag}}', ['topic_id' => 'id']);
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->updateCounters(['views' => 1]);
    }

    /**
     * Before save hook
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        // Manual slug fallback
        if (empty($this->slug)) {
            $this->slug = Inflector::slug($this->title);
        }
        
        return true;
    }
}
