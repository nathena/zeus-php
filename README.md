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
- 领域事件：业务完成后广播的事件，通常用于消息通知，日志记录等行为。
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
配置文件的管理方式详见[zeus\sandbox\ConfigManager]。

### bootstap
顾名思义框架启动入口，初始化上下文。见[zeus\sandbox\ApplicationContext] (# ApplicationContext)

### 基本模块
- base：定义上下文管理方式、组件、事件。
- domain：聚合、实体、可以发生事件源的聚合
- http：request、response、cookie、session
- mvc：mvc 分发上下文、路由等。
- database：pdo抽象层，定义了xa事务、active record等基础组件，使用规约提供统一的执行入口（zeus\database\specification\AbstractSpecification）。
- utils：cryto、uploader、download等，可根据模块分类。

### Roadmap
- plugin机制
- EventSouring事件源
- Snapshot 事件源归档快照

### Usage
#### 加载框架
```
define("CURRENT_DIR",dirname(__FILE__));//项目根目录，非必选
define("APP_ENV_PATH",CURRENT_DIR.DIRECTORY_SEPARATOR."config.php");//项目配置文件路径

define("ROOT",dirname(CURRENT_DIR));//项目目录与框架目录所在的根目录，非必须
include_once ROOT.DIRECTORY_SEPARATOR."zeus".DIRECTORY_SEPARATOR."bootstrap.php";//加载框架
```
#### 路由
zeus使用两种路由方式，1、正则表达式；2、模块约定路径。
- 正则表达式，即传统的URL重写方式，并通过正则分组获取url中的参数。
- 模块约定路径，预定路由方式为/module/[controller]/[action]/[params options][?query_string]
    - module 必须的，且已注册到路由中的模块。第一项；
    - controller 可选。如果未指定则使用router.default_controller。第二项；
    - actioin 可选。如果未指定则使用router.default_controller_action。第三项目。
    - controller约定的格式为“ucfirst($fragment)Controller"”，
      如果约定格式的controller文件不存在，则判定此controller fragment为action，
      忽略原定的action fragment(第三项)并当作params。
    - controller的匹配格式为模块定义的命名空间前缀+controller名称。
      例如Router::addModule("test","com\\oa\\test");，
      假设router.default_controller为“indexController”、router.default_controller_action为“index”
      url为“/test/echo”，默认匹配com\oa\test\EchoController并调用index方法，如果com\oa\test\EchoController则匹配
      com\oa\test\IndexController并调用echo方法。


url rewrite
```
<?php
Router::addRouter('/',IndexPlatformController::class); 
Router::addRouter('/welcome',WelcomePlatformController::class);
```
模块约定
```aidl
Router::addModule("account","account");
Router::addModule("account_auth","account_auth");
Router::addModule("bus","bus");
Router::addModule("report","report");
Router::addModule("customer","customer");
Router::addModule("test","com\\oa\\test");
```

#### controller
```
class IndexController extends Controller
{
    public function test2($a){
        print_r($this->request->getData());
        echo $a;
    }
}
```
#### Command&Event
```
//test
use test\EchoCommand;

include_once 'bootstrap.php';

$command = new EchoCommand();
$command->execute();//or ApplicationContext::currentContext()->getCommandBus()->execute($command);

//command
use zeus\base\command\AbstractCommand;
use zeus\sandbox\ApplicationContext;

class EchoCommand extends AbstractCommand
{
    public function __construct()
    {
        parent::__construct();

        $this->setData([1,2,3]);
    }

    public function start(){
        echo "{$this->commandType} => starting \r\n";
    }

    public function finished(){
        echo "{$this->commandType} => finished \r\n";
    }

}

ApplicationContext::currentContext()->getCommandBus()->register(EchoCommand::class,EchoCommandHandler::class);

//commandhandler
use zeus\base\AbstractComponent;
use zeus\base\command\AbstractCommand;
use zeus\base\command\CommandHandlerInterface;

class EchoCommandHandler extends AbstractComponent implements CommandHandlerInterface
{
    public function execute(AbstractCommand $command)
    {
        print_r($command->getData());

        $this->raise(new EchoedEvent());
        
        //$event = new new EchoedEvent();
        //$msg   = new EventMessage($this,$event);
        //ApplicationContext::currentContext()->getEventBus()->publish($msg);
    }
}

//event
class EchoedEvent extends AbstractEvent
{
    public function __construct()
    {
        parent::__construct();

        $this->setData(["a","b","c"]);
    }

    public function start(){
        echo "{$this->eventType} => starting \r\n";
    }

    public function finished(){
        echo "{$this->eventType} => finished \r\n";
    }
}

ApplicationContext::currentContext()->getEventBus()->subscribe(EchoedEvent::class,EchoedEventHandler::class);

//listener
use zeus\base\event\EventListenerInterface;
use zeus\base\event\EventMessage;

class EchoedEventHandler implements EventListenerInterface
{
    public function handler(EventMessage $eventMessage)
    {
        $sender = $eventMessage->getSender();
        $event  = $eventMessage->getEvent();

        echo "sender : ".get_class($sender)."=>\r\n";
        print_r($event->getData());
    }
}
```


### License
- All code in this repository is covered by the terms of the Apache2.0 License.