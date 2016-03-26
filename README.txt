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





