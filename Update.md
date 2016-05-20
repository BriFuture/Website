更新说明
====

### 5月20日
---

修改的文件

- Factory.class.php<br>
    增加了部分函数
- Limit.class.php<br>
    完善了限制逻辑部分
- Security.class.php<br>
    修改了部分代码
- DbInvitation.class.php<br>
    
- DbLimits.class.php<br>
    
- index.php<br>
    修改了版本号

增加的文件

- Lang.class.php<br>
    管理语言
- DbBlock.class.php<br>
    管理Block数据库
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