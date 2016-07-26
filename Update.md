更新说明
====

### 7 月 26 日
- 将 control 层的 view_xxx() 方法换成了 render() 方法。每个 control 层的类对应一个页面。
- 将 view 层的 index.phtml 显示方式换成了从数据库中读取图片信息并显示。更换了 Block 的显示方法。
- 将 Base.class.php 中的一些方法改成了静态方法。

### 6 月 16 日

- 将代码中遗留的 PUBLIC_PATH 常量改成了 INCLUDE_PATH。
- 新增了专门存放 JS 的页面，把前端的东西弄一下。

### 5月26日
---

- 修改了目录结构
  * 代码放置的位置变成了include，
  * 核心代码放在`CORE_PATH`路径下
  * phtml代码放在`VIEW_PATH`路径
  * 控制页面逻辑的代码改到了`CONTROL_PATH`下
- 修改了部分文件中代码路径（还好项目不大，不然改目录结构要累死）


### 5月24日
---

- Factory.class.php
  * 将control层中的核心代码去掉，不必使用工厂模式，因为类名不会做很大修改。
- view层
  - Index.class.php
    * 增加了显示的内容和显示逻辑
  - index.phtml
    * 将部分代码改为php逻辑显示


### 5月21日
---

修改的文件

- index.phtml
  * 增加了一点前端页面部分

增加的文件

- Info.class.php
- info.phtml
  * 将之前的index.phtml显示的内容放在了这里

### 5月20日
---

修改的文件

- Factory.class.php<br>
  * 增加了部分函数
- Limit.class.php<br>
  * 完善了限制逻辑部分
- Security.class.php<br>
  * 修改了部分代码
- DbInvitation.class.php<br>
    
- DbLimits.class.php<br>
    
- index.php<br>
  * 修改了版本号

增加的文件

- Lang.class.php<br>
  * 管理语言
- DbBlock.class.php<br>
  * 管理Block数据库
- block.phtml
- login.phtml
- register.phtml
- Block.class.php
- Login.class.php
- Register.class.php


### 5月19日
---

修改的文件

- Page.class.php<br>  
    由于上传到服务器上，原来的代码中并未区分类名大小写，导致服务器无法显示页面，经过修改后将传入参数小写后再进行首字母大写，方法名全部改为小写。避免从url访问时由于大小写问题导致网页无法显示
- Base.class.php<br>
    增加了之前没有完成的代码
- Options.class.php<br>
    完善了注释
- Factory.class.php<br>
    将新添加的类加入到静态方法中

增加的文件

- DbLimits.class.php
- Limits.class.php