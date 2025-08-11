<?php

/**
 * Yii2 Forum
 * *************
 *  
 * @link https://github.com/ZakharovAndrew/yii2-forum/
 * @copyright Copyright (c) 2025 Zakharov Andrew
 */
 
namespace ZakharovAndrew\forum;

use Yii;

/**
 * Yii2 Forum Module 
 */
class Module extends \yii\base\Module
{
   
    public $bootstrapVersion = '';

    public $uploadWebDir = '';
    
    /**
     * @var string show H1
     */
    public $showTitle = true;
       
    /**
     *
     * @var string source language for translation 
     */
    public $sourceLanguage = 'en-US';
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'ZakharovAndrew\forum\controllers';

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        
        self::registerTranslations();
    }
    
    /**
     * Registers the translation files
     */
    protected static function registerTranslations()
    {
        if (isset(Yii::$app->i18n->translations['extension/yii2-forum/*'])) {
            return;
        }
        
        Yii::$app->i18n->translations['extension/yii2-forum/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/zakharov-andrew/yii2-forum/src/messages',
            'on missingTranslation' => ['app\components\TranslationEventHandler', 'handleMissingTranslation'],
            'fileMap' => [
                'extension/yii2-forum/forum' => 'forum.php',
            ],
        ];
    }

    /**
     * Translates a message. This is just a wrapper of Yii::t
     *
     * @see Yii::t
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($message, $params = [], $language = null)
    {
        static::registerTranslations();
        
        $category = 'forum';
        
        return Yii::t('extension/yii2-forum/' . $category, $message, $params, $language);
    }
    
}
