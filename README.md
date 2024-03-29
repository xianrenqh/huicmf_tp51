# HuiCMF 2.0 —— 基于 ThinkPHP5.1+Layui 框架二次开发

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/top-think/framework/badges/quality-score.png?b=5.1)](https://scrutinizer-ci.com/g/top-think/framework/?branch=5.1)
[![Build Status](https://travis-ci.org/top-think/framework.svg?branch=master)](https://travis-ci.org/top-think/framework)
[![Total Downloads](https://poser.pugx.org/topthink/framework/downloads)](https://packagist.org/packages/topthink/framework)
[![Latest Stable Version](https://poser.pugx.org/topthink/framework/v/stable)](https://packagist.org/packages/topthink/framework)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D5.6-8892BF.svg)](http://www.php.net/)
[![License](https://poser.pugx.org/topthink/framework/license)](https://packagist.org/packages/topthink/framework)

## 此项目不再更新，请使用tp6版本的：
https://github.com/xianrenqh/huicmf_tp6

HuiCMF 2.0 其主要特性包括：

- 一键安装程序（install文件夹）
- 后台登录模块
- 基本的权限管理模块
- 简单的内容管理模块
- 管理员管理模块
- 个人信息，密码修改模块
- 系统设置模块
- 自定义配置
- 后台菜单管理
- 后台操作（登录）日志
- 数据库备份还原
- 后台模块管理（支持自行开发安装卸载）

> HuiCMF 的运行环境要求 PHP7.0 以上。

## 安装

使用 composer 安装

```
composer create-project xianrenqh/huicmf_tp51 dev-huicmf_tp51
```

然后就可以在浏览器中访问

```
http://你的域名/install.php
```

## 目录结构

初始的目录结构如下：

```
www WEB部署目录（或者子目录）
 ├── application                应用目录
 │   ├── admin                  后台模块目录
 │   │   ├── controller         控制器目录
 │   │   ├── model              模型目录
 │   │   └── view               视图目录
 │   │  
 │   ├── ads                    广告模块
 │   │   ├── controller         控制器目录
 │   │   ├── install            安装目录
 │   │   ├── uninstall          卸载目录
 │   │   └── view               视图目录
 │   ├── api        
 │   ├── attachment             附件模块 
 │   │   |......
 │   │   
 │   ├── banner                 幻灯banner图
 │   │   |......
 │   │   
 │   ├── index                  前台首页
 │   │   |......
 │   │   
 │   ├── link                   友情链接
 │   │   |......
 │   │   
 │   ├── command.php            命令行定义文件
 │   ├── common.php             公共函数文件
 │   ├── provider.php
 │   └── tags.php               应用行为扩展定义文件
 │      
 ├─config                       应用配置目录
 │  ├─module_name               模块配置目录
 │  │  ├─database.php           数据库配置
 │  │  ├─cache                  缓存配置
 │  │  └─ ...
 │  │ 
 │  ├─app.php                   应用配置
 │  ├─cache.php                 缓存配置
 │  ├─cookie.php                Cookie配置
 │  ├─database.php              数据库配置
 │  ├─log.php                   日志配置
 │  ├─session.php               Session配置
 │  ├─template.php              模板引擎配置
 │  └─trace.php                 Trace配置
 │      
 ├── extend
 │   ├── layui              
 │   │   └── Layui.php
 │   ├── lib
 │   │   ├── Form.php
 │   │   └── Tree.php
 │   └── tpl
 │       └── dispatch_jump.tpl
 │      
 ├── public
 │   ├── favicon.ico
 │   ├── index.php
 │   ├── install
 │   ├── robots.txt
 │   ├── router.php
 │   ├── static
 │   │   ├── admin
 │   │   ├── lib
 │   │   └── water
 │   └── uploads
 │      
 │
 ├─thinkphp                     框架系统目录
 │  ├─lang                      语言文件目录
 │  ├─library                   框架类库目录
 │  │  ├─think                  Think类库包目录
 │  │  └─traits                 系统Trait目录
 │  │       
 │  ├─tpl                       系统模板目录
 │  ├─base.php                  基础定义文件
 │  ├─console.php               控制台入口文件
 │  ├─convention.php            框架惯例配置文件
 │  ├─helper.php                助手函数文件
 │  ├─phpunit.xml               phpunit配置文件
 │  └─start.php                 框架入口文件
 ├── route
 │   └── route.php
 │      
 ├── build.php
 ├── composer.json
 ├── README.md
 ├── runtime
 └── think
 ├── LICENSE.txt
```

## 后台效果图

![demo1](https://s2.ax1x.com/2019/11/29/QkqiZj.jpg)

![demo2](https://s2.ax1x.com/2019/11/29/Qkq9sg.jpg)

![demo3](https://s2.ax1x.com/2019/11/29/QkqFds.jpg)

![demo4](https://s2.ax1x.com/2019/11/29/QkqEiq.jpg)

![demo5](https://s2.ax1x.com/2019/11/29/QkqCLQ.jpg)

![demo6](https://s2.ax1x.com/2019/11/29/QkqZWV.jpg)

![demo7](https://s2.ax1x.com/2019/11/29/QkqnQU.jpg)


### 目录和文件

- 目录不强制规范，驼峰和小写+下划线模式均支持；
- 类库、函数文件统一以`.php`为后缀；
- 类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
- 类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名

- 类的命名采用驼峰法，并且首字母大写，例如 `User`、`UserType`，默认不需要添加后缀，例如`UserController`应该直接命名为`User`；
- 函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 `get_client_ip`；
- 方法的命名使用驼峰法，并且首字母小写，例如 `getUserName`；
- 属性的命名使用驼峰法，并且首字母小写，例如 `tableName`、`instance`；
- 以双下划线“**”打头的函数或方法作为魔法方法，例如 `**call`和`\_\_autoload`；

### 常量和配置

- 常量以大写字母和下划线命名，例如 `APP_PATH`和 `THINK_PATH`；
- 配置参数以小写字母和下划线命名，例如 `url_route_on` 和`url_convert`；

### 数据表和字段

- 数据表和字段采用小写加下划线方式命名，并注意字段名不要以下划线开头，例如 `hui_user` 表和 `cmf_name`字段，不建议使用驼峰和中文作为数据表字段命名。

## **特别鸣谢**

感谢以下的项目,排名不分先后

ThinkPHP：http://www.thinkphp.cn

Layui：https://www.layui.com

jQuery：http://jquery.com

## 版权信息

HuiCMF 遵循 Apache2 开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有 Copyright © 2019-2020 by 小灰灰

All rights reserved。
