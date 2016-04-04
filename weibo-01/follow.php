<?php
include('./lib.php');
include('./header.php');

if (!($user = isLogin())) {
	header('location: index.php');
	exit;
}

// 点击关注后，key-value数据库将会发生什么变化
// 思路：
/*
每人有自己的粉丝记录 set，用集合来记录
每人有自己的关注的记录 set

aid 关注 bid，发生了什么：
following：我关注的人：aid(bid)，集合，aid，添加bid

粉丝表：
follower:bid(aid)

思路理清楚了，就好办了，设置两个集合：following、follower

0：获取用户名
1：查询id
2：查询此id，是否在我的following集合里
*/

$uid = G('uid');
$f = G('f');



/*
判断：uid，是否合法值
判断：uid是否是自己，不能关注自己
*/

$r = connredis();

// 如果是1，则表示要进行关注
// 否则的话，就认为你是要取消关注的
// 集合是没有顺序的，直接删除就可以了
// 关注我的人，再取出我所屏蔽的人，求一下差集，就是我想要的那些粉丝了。
// 对于redis来说完成这个功能是非常简单的
if ($f == 1) {
	// 此时uid对应的用户就已经被关注了
    $r->sadd('following'.$user['userid'], $uid);
    // 对方表里面应该填写我的id
    $r->sadd('follower:'.$uid, $user['userid']);
} else {
	$r->srem('following'.$user['userid'], $uid);
    // 对方表里面应该填写我的id
    $r->srem('follower:'.$uid, $user['userid']);
}


// 对方的uname
$uname = $r->get('user:userid:'.$uid.':username');

// 关注完毕了，就可以转到刚才的页面中：
header('Location: profile.php?u='.$uname);

<? php include('./footer.php');?>
