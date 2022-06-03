# dacore-core
登录权限插件

## 安装  
```bash
composer require dacore/core
```

## 配置
1. 注册 `ServiceProvider`: 
    ```php
    DACore\Core\ServiceProvider::class,
    ```
    > laravel 5.5+ 版本不需要手动注册