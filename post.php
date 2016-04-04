// 这个页面是微博发布信息存储的php页面
// 发送微博，并且把微博推送给我的用户

<?php
include('./lib.php');
include('./header.php');

/*
incr global:postid
set post:postid:$postid:time timestamp
set post:postid:$postid:userid $userid
set post:postid:$postid:content $ content

0：判断是否登录
1：接收post的内容
2：set redis
*/

if (($user = isLogin()) == false) {
    header('location: index.php');
    exit;
}

$content = P('status');
if (!$content) {
    error('请填写内容');
}

$r = connredis();
$postid = $r->incr('global:postid');
/*
$r->set('post:postid'.$postid.':userid', $user['userid']);
$r->set('post:postid'.$postid.':time', time());
$r->set('post:postid'.$postid.':content', $content);
*/
这里存储的时候，如果多存储一个字段的话，虽然会多存储一个字段，浪费了一点空间
但是查询的时候，就不用左连接查询了，用的是mysql中的说法。
也就是我们在发布微博的时候，多加一个冗余字段，将会给你的查询带来极大的方便
比如说：userid，很多场合需要知道username，虽然也能查出来，但是比较麻烦
所以在存的时候，完全可以把username存进去

当发布一条微博的时候，就把用户id、用户名、发布时间、内容都写到hash结构中去。
$r->hmset('post:postid:'.$postid, array('userid'=>$user['userid'], 'username'=>$user['username'], 'time'=>time(), 'content'=>$content));
// 现在用hash结构来搞定它

// 回到home.php

// 把微博推给自己的粉丝
// follower表：用户是别人的粉丝
// following表：别人是用户的粉丝
$fans = $r->smembers('follower:'.$user['userid']);
//print_r($fans);
//exit;

// 还要为每个人创建一张表，表示当前接收的微博表，因为微博固然是多，但是人们能看的微博无非就是前两页
// 我们完全可以给它维持前1000条
// 如果它要再翻再多的微博，那我们调用数据库，1000条往后的，都写到数据库中
// 所以我们查到自己的粉丝之后，挨个的给它们推送微博
// 自己的微博也要推送给自己一份
$fans[] = $user['userid'];

// 微博应该保持最新的100条或者1000条
foreach ($fans as $fansid) {
    $r->lpush('receivepost:'.$fansid, $postid);
    这里是你发布一个微博，就把微博推给你的所有粉丝，
    如果说你是像林志玲那样的，都好几百万，上千万粉丝，那这样的话，这些人发一条微博，那你的redis里面就迅速增加了上千万微博
    而这上千万条微博，内容几乎都一样，所以这就牵扯到你关注的人发的微博和他的粉丝之间，信息如何来到达它的粉丝这里，到底是主动的推送给它的粉丝
    还是由它的粉丝主动去获取。

    其实在微博早期的设计里面，它其实用的是推模型，这样的话，就会出现一个问题，当你的粉丝过多的时候，将会产生一些障碍。
    你发一条微博，一下子推给上千万的人，redis肯定受不了，做了很多无用功，所以微博由推改成拉。

    用户登录，就会把自己关注的那些人的最新信息给拽出来，所以你在微博里面可能会关注很多人，之前新浪微博设计的是关注的人上限是2000，你最多只能关注2000个人
    这样的话，你就算去拉这个数据，也无非是循环两千次。

    所以，要更换它的模型。
}

header('location: home.php');
exit();

include('./footer.php');?>