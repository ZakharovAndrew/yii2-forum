<?php

use yii\db\Migration;

class m250801_123000_create_forum_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // Категории форума
        $this->createTable('{{%forum_category}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'order' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'updated_at' => $this->timestamp()
        ], $tableOptions);

        // Подфорумы (разделы внутри категорий)
        $this->createTable('{{%forum_forum}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'order' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'updated_at' => $this->timestamp(),
        ], $tableOptions);

        // Темы (топики)
        $this->createTable('{{%forum_topic}}', [
            'id' => $this->primaryKey(),
            'forum_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'status' => $this->smallInteger()->defaultValue(1), // 1=активно, 0=закрыто, 2=удалено
            'views' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'updated_at' => $this->timestamp(),
        ], $tableOptions);

        // Сообщения (посты)
        $this->createTable('{{%forum_post}}', [
            'id' => $this->primaryKey(),
            'topic_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'content' => $this->text()->notNull(),
            'likes' => $this->integer()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
            'updated_at' => $this->timestamp(),
        ], $tableOptions);

        // Теги для тем
        $this->createTable('{{%forum_tag}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->unique(),
        ], $tableOptions);

        // Связь тегов с темами (многие-ко-многим)
        $this->createTable('{{%forum_topic_tag}}', [
            'topic_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], $tableOptions);

        // Подписки пользователей на темы
        $this->createTable('{{%forum_subscription}}', [
            'user_id' => $this->integer()->notNull(),
            'topic_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
        ], $tableOptions);

        // Модераторы форумов
        $this->createTable('{{%forum_moderator}}', [
            'user_id' => $this->integer()->notNull(),
            'forum_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
        ], $tableOptions);

        // Индексы и внешние ключи
        $this->addForeignKey(
            'fk-forum_forum-category_id',
            '{{%forum_forum}}',
            'category_id',
            '{{%forum_category}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_topic-forum_id',
            '{{%forum_topic}}',
            'forum_id',
            '{{%forum_forum}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_topic-user_id',
            '{{%forum_topic}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_post-topic_id',
            '{{%forum_post}}',
            'topic_id',
            '{{%forum_topic}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_post-user_id',
            '{{%forum_post}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_topic_tag-topic_id',
            '{{%forum_topic_tag}}',
            'topic_id',
            '{{%forum_topic}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_topic_tag-tag_id',
            '{{%forum_topic_tag}}',
            'tag_id',
            '{{%forum_tag}}',
            'id',
            'CASCADE'
        );

        $this->addPrimaryKey(
            'pk-forum_topic_tag',
            '{{%forum_topic_tag}}',
            ['topic_id', 'tag_id']
        );

        $this->addForeignKey(
            'fk-forum_subscription-user_id',
            '{{%forum_subscription}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_subscription-topic_id',
            '{{%forum_subscription}}',
            'topic_id',
            '{{%forum_topic}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_moderator-user_id',
            '{{%forum_moderator}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-forum_moderator-forum_id',
            '{{%forum_moderator}}',
            'forum_id',
            '{{%forum_forum}}',
            'id',
            'CASCADE'
        );

        // Индексы для поиска
        $this->createIndex('idx-forum_topic-status', '{{%forum_topic}}', 'status');
        $this->createIndex('idx-forum_post-topic_id', '{{%forum_post}}', 'topic_id');
        $this->createIndex('idx-forum_topic_tag-tag_id', '{{%forum_topic_tag}}', 'tag_id');
    }

    public function safeDown()
    {
        // Удаляем таблицы в обратном порядке (чтобы избежать ошибок с внешними ключами)
        $this->dropTable('{{%forum_moderator}}');
        $this->dropTable('{{%forum_subscription}}');
        $this->dropTable('{{%forum_topic_tag}}');
        $this->dropTable('{{%forum_tag}}');
        $this->dropTable('{{%forum_post}}');
        $this->dropTable('{{%forum_topic}}');
        $this->dropTable('{{%forum_forum}}');
        $this->dropTable('{{%forum_category}}');
    }
}
