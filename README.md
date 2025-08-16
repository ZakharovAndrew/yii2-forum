# 🚀 Yii2 Forum Module  

**A powerful, flexible, and lightweight forum engine for Yii2**  
✨ Modern features | 🛠 Easy integration | ⚡ High performance  

---

## 🔥 **Key Features**  

### 🗂 **Forum Structure**  
- **Categories & Subforums** – Tree-like organization  
- **Topics & Threads** – Discussions with pagination  
- **Posts & Replies** – Rich text (BBcode/Markdown)  
- **Tags & Labels** – Categorize content (#help, #bug)  

### 👥 **User Management**  
- **Roles & Permissions** (RBAC) – Admin, Moderator, User  
- **User Profiles** – Avatars, signatures, activity history  
- **Reputation System** – Likes, upvotes, badges (🌟)  
- **Notifications** – Replies, mentions, moderation alerts  

### 🛡 **Moderation Tools**  
- **Report System** – Flag inappropriate content  
- **Soft Delete** – Restore posts if needed  
- **Ban Users** – Temporary/permanent bans  
- **IP Tracking** – Prevent spam attacks  

### ⚙ **Technical Highlights**  
- **REST API** – For mobile apps & SPA  
- **SEO-Friendly** – Slugs, meta tags, sitemap  
- **Caching** – Redis, Memcached, or file-based  
- **ElasticSearch** – Blazing-fast search  

## 🚀 Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require zakharov-andrew/yii2-forum
```
or add

```
"zakharov-andrew/yii2-forum": "*"
```

to the ```require``` section of your ```composer.json``` file.

Subsequently, run

```
./yii migrate/up --migrationPath=@vendor/zakharov-andrew/yii2-forum/migrations
```

in order to create the settings table in your database.

Or add to console config

```php
return [
    // ...
    'controllerMap' => [
        // ...
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@console/migrations', // Default migration folder
                '@vendor/zakharov-andrew/yii2-forum/src/migrations'
            ]
        ]
        // ...
    ]
    // ...
];
```

## 👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

🚀 **Star us on GitHub if you love it!** ⭐️
