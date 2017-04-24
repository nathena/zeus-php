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

etc以约定约束项目默认配置文件，实际项目可使用APP_ENV_DIR（见webroot/index.php）常量重载。关于APP_ENV_DIR，详见[zeus\sandbox\ConfigManager] (#ConfigManager)
- config.php 默认项目环境配置文件
- database.php 默认的数据库配置文件
- mimes.php 目前用处不大，文件mime头
- router.php 推荐使用路由配置，而不使用框架默认的路由判断

先写到这里，待续......