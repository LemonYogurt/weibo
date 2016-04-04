国内使用redis最多的就是新浪微博了，而且它还要牵涉到热门微博的存取
热门微博肯定要放在redis中的

将会用redis来存储热数据，与mysql相结合，完成冷热数据的交换，来达到高并发，快速发布微博的目的。

使用传统的关系型数据，重点是设计表结构上，现在换成了redis数据库，重点是来设计它的key

如果key设计的合理，那它就是高效而且易于理解

-------------------------------------
设计user表--对应的key规则

注册用户

incr global:userid
set user:userid:1: username zhangsan
user表userid为1的那一列的username是张三

set user:userid:1:password 111111

由于登录的时候，需要根据username来进行查询，所以要进行username的设计，进行一个冗余的设计
set user:username:zhangsan:userid 1

如果说表里面除了主键之外，有username和email也是需要经常查询的

-------------------------------------

发送微博：post表
post:postid:3:time timestamp
post:postid:3:userid 5
post:postid:3:content 'this is my home'

incr global:postid
set post:postid:$postid:time timestamp
set post:postid:$postid:userid $userid
set post:postid:$postid:content $ content

cookie很容易被篡改，所以需要给cookie加盐，cookie salt

为了防止伪造cookie，可以在用户每次登陆的时候生成一个随机数，保存在服务器端
当用户退出时，就删除这个随机数


微博项目的key设计：
在mysql中，开发网站前，重要的设计是设计表，设计表说白了就是设计列的类型，
在redis里面，重要的就是设计它的key

一共设计了这样几个key：
用户相关的key
列名：
global:userid global:postid
操作：
incr    incr
备注：
产生全局的userid 产生全局的postid

用户相关的key：
在mysql中：
userid  username    password    authsecret
3       test3       111111      545678……%￥##
在redis中变成以下几个key：
key前缀：
user:userid             user:userid 3
user:userid:*:username  user:userid:3:username test3
user:userid:*:password  user:userid:3:password 111111
user:userid:*:authsecret user:userid:3:authsecret 545678……%￥##

微博相关的key：
在mysql中：
postid  userid  username    time            content
4		3       test3       8763456789      测试内容
在redis中，与表对应的key设计

post:postid *           4
post:postid:*:userid    3
post:postid:*:username  test3
post:postid:*:time      8763456789
post:postid:*:content   测试内容

关注表、粉丝表：
关注表：
following:userid value是一个集合

粉丝表：
follower:userid value是一个集合

推送表：
每个人都有自己应该看到的内容
receivepost
receivepost:userid -> 链表list

新浪微博的特点：
一个人需要看到的微博，从注册账号以来，收到的微博肯定不止1000条了，但是它只显示了10页，也就是1000条
因为它就是出于时效性的考虑，没有必要维护一个完完全全的列表，维护1000条也就够了。

如果使用推模型，有一个不好的地方：
比如说：3个月没有登录了，3个月发生了很多事情，一下子推过来2000条，但是你想想，一下子给推送2000条，你会一条条的看吗？
其实也不会，所以追求实时统计，在网站中，真的实时，真的完全的统计其实并不多
信息都稍微的打过折
比如说：让你统计在线人数，统计当前这一秒有多少人在线，我想你付出的精力会非常的大，还统计不准。
如果说，我就统计10分钟之内的在线人数，这样就好统计多了。

也就是说，你是否真的要求数据丝毫无误。

在个人中心，只能看到1000条，当然点击个人主页的，都是能看到的
我们针对实时性热点，就只能看到1000条，1000条又是关注的人发过来的，所以考虑一下，我们如何来设计这个表












