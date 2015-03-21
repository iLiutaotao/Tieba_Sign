贴吧签到助手
=============

基于**[Kookxiang](http://ikk.me)**的贴吧签到系统经过多次更改得到的稳定的签到系统

## 2015年3月21日（自由版本9）
- 正式启用**差量更新**服务
 废弃原有更新方式，集成后台差量更新方式

- Cron保护设置更简单(非SAE环境)
 精简代码，提升**效率**

## 2015年2月28日（自有版本8）
- 集成cron保护设置(非SAE环境)
 > 可在后台-系统设置-cron执行密码中设置你的执行密码
 > 设置后cron设置需改为：http://你的域名/cron.php?pw=你设置的值 

- SAE环境用户参见下面设置方法

> SAE用户在更新cron.php的$word值时，需在config.yaml中第七行改写

```
cron:
	- description: SignTask
  	url: cron.php?pw=password（pw值需改为在cron.php中设置的$word的值）
  	schedule: */1 * * * *
```

## 2015年2月21日(SAE特有更新)
- 改写yaml规则

## 2015年2月19日(自有版本6更新)已集成后台，请更新
- 增加**CRON**任务保护系统：
```
在cron.php中，修改第三行代码为你想要的key，然后在执行cron任务时就需要加上一个头例如：
http://sign.liujiantao.me/cron.php?pw=password
pw的值为你在cron.php中设置的$word = 'password';的值
执行成功就会返回ok，如果密码错误则返回no
```

## 2015年2月12日
- 增加**自动升级**功能

## 2015年2月11日
- 新增**云平台**发信插件
- 检测**更新**开始推送信息，目前尚无法做到差量升级，请耐心等待

## 2015年1月27日
- 更新回帖插件无法添加帖子

## 2015年1月23日更新
- 修正 回帖插件设置
- 替换 回帖内容

-------------
本人博客：http://www.liujiantao.me
签到站：http://sign.liujiantao.me