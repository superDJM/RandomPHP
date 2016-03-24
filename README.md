# RandomPHP
任意门团队协作开发的php mvc框架
=======
一期要实现的功能:
-------
1. psr4自动加载(autoload).  
1. 实现工厂类(Factory),代码里面不用写new.  
1. 实现注册树(Register),所有资源都存放在里面.  
1. 实现Http的封装(Http),把有关http通信的变量全部放在Request和Response两个成员里面  
1. 异常类(Exception)实现.  
1. debug类(Debug)的实现.  
1. 核心(Core)类实现框架的初始化.  
1. Model类实现ORM.  
1. 配置(Config)类,实现配置的动态加载.  
1. 数据缓存(DataCache)类实现.  
1. 钩子(Hook)类实现.  
1. 模版(View)类实现模版渲染,模版标签.  

目录结构
--------
    RandomPHP  
        -- Application      存放业务逻辑  
            -- Module       模块
                -- Common   存放公共函数
                -- Config   存放配置
                -- Controller
                -- Model
                -- View
        -- Db               存放数据库备份文件
        -- Library          类库
        -- Web              网站目录
            --Public        公共资源
            index.php       网站唯一入口
        -- Temp             存放缓存和日志
        -- Tests            存放单元测试类