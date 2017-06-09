# zeus-php

## 图例
![alt 架构](https://github.com/nathena/zeus-php/blob/master/resource/1.jpg "架构")

## 写在前面的话
为什么建立zeus-php，因为看到越来越多的php开发人员在构建应用程序时太过注重数据库结构设计，往往一个应用的开始就伴随着数据库结构的开始。
- 好的架构并非数据库优先，而是业务模型优先；数据库的职责是存储数据，业务不应该过度的依赖数据库。
- MVC的M不是数据源，而是业务模型。
- 框架的用意不是功能库，而是约定与约束。
- 领域驱动是目前较好的抽象业务的方式。
- 模块化垂直架构 or 功能性水平分层架构的选择，zeus选择前者。
- 聚合根：业务抽象入口。注意：不使用集合管理聚合下的实体。
- 实体：实体保留聚合根引用。
- 值对象：无状态的数据值。
- 领域服务：业务可确定的聚合间协作。
- 领域事件：业务完成后广播的事件，通常用户消息通知，日志记录等行为。
- 事件源：目前框架暂未启用mq队列实现事件源。
- 命令：事件触发的上下文，事件的开始。
- 存储库：一个聚合根一个存储库，存储聚合内所有实体数据。
- 上下文：业务行为的生命周期。
- 单一职责，依赖倒置，接口隔离。
- 代码即注解。

## 文档
zeus以模块化来设计架构，使用领域驱动来组织代码。关注约定与约束，不造功能性轮子，各种功能性轮子可以使用autoload方式，以模块形式加入到“上下文”中。
使用MVC为业务行为驱动的基础模型，并约定了行为的路由规则（详见：zeus\mvc\Router）。

### config
提供默认的配置文件“config.php”,应用层可使用APP_ENV_PATH来定义应用自身的配置文件（test\bootstrap.php ）,
配置文件的管理方式详见[zeus\sandbox\ConfigManager] (# ConfigManager)

### bootstap
顾名思义框架启动入口，初始化上下文。见[zeus\sandbox\ApplicationContext] (# ApplicationContext)

### 基本模块
- base：定义上下文管理方式、组件、事件。
- domain：聚合、实体、可以发生事件源的聚合
- http：request、response、cookie、session
- mvc：mvc 分发上下文、路由等。
- database：pdo抽象层，定义了xa事务、active record等基础组件，使用规约提供统一的执行入口（zeus\database\specification\AbstractSpecification）。
- utils：cryto、uploader、download等，可根据模块分类。
