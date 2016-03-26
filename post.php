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
$r->set('post:postid'.$postid.':userid', $user['userid']);
$r->set('post:postid'.$postid.':time', time());
$r->set('post:postid'.$postid.':content', $content);

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
}

header('location: home.php');
exit();

include('./footer.php');?>