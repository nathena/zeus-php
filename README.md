#zeus-php

# 图例

![alt 架构][id]

[id]: https://github.com/nathena/zeus-php/blob/master/resource/1.jpg "架构"

# 写在前面的话

为什么创建了zeus-php这个项目，并逐步衍生成一个框架（小型？）？其原因其实是一直以来在考虑，如何在php项目中运用clean或ddd架构（接触过很多项目，新的或旧的，很多组员都考虑如何设计数据库）。从而有了zeus这个代码仓库。

实际上个人觉得，好的架构并非数据库优先，而是业务模型优先——即优先确定业务模型关系，而后基于内存实现业务逻辑，再按需异步持久化到硬盘（数据库），如此这般在扩展与性能间找到一个好的临界点。因此，稀稀落落地着手写了zeus-php的代码。

其中关隘，有空再文字化。。。

# 文档

## etc

etc以约定约束项目默认配置文件，实际项目可使用APP_ENV_DIR（见webroot/index.php）常量重载。关于APP_ENV_DIR，详见[zeus\foundation\ConfigManager] (#ConfigManager)
- config.php 默认项目环境配置文件
- database.php 默认的数据库配置文件
- mimes.php 目前用处不大，文件mime头
- router.php 推荐使用路由配置，而不使用框架默认的路由判断

## foundation

顾名思义foundation为框架的基础支撑代码，下面一一简单说明：

### zeus\foundation\ConfigManager<a name="ConfigManager"></a>

ConfigManager 更多的扮演了环境变量存取的角色。默认的环境变量配置路径为zeus/etc下的约定配置变量，其中APP_ENV_DIR常量可通过项目主引导文件指定，从而调整实际项目中的相关环境参数。

目前框架通过ConfigManager托管了cookie、session、database、router的配置cookie、session使用全局config（config.php）配置，后两者使用与配置同名的文件，例如：router.php等。

由于php的运行机制，基本可以做到多个子项目使用独立配置或公共配置的能力。

### zeus\foundation\Autoloader

框架类文件加载。提供了registerNamespaces、registerDirs、registerClassMap三种类文件扫描方式。zeus约定使用了php PSR-4?的规范——命名空间为相对路径，类名即文件名。

### zeus\foundation\util包

一些帮助类，做为函数库存在，没有什么具体的功能。

>注意：个人一直认为，框架可以不需要任何的代码，只需要相互间的约束即可。针对相关的约束，配以有助于实现约束的函数接口即可，例如：Autoloader、ConfigManager就是上述接口的一种。

### zeus\mvc\Application

zeus使用mvc架构为框架的前端上下文容器（application），通过application托管response、request、loader、router等。application容器，内部自实现命名控制，因此同一个子项目内可以启动多个application，application间相互隔离。

同时，在application容器内监听异常与错误handler，实现框架整体的异常捕获体系。

===========================

先写到这里，待续......