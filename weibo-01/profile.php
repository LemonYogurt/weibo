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

$r = connredis();

// 获取用户名$user
// 在点击profile.php时，会跳转页面，此时跳转的url有一个u参数后面跟的就是用户的id
// 所以需要从地址栏上获取$u
$u = G('u');
$prouid = $r->get('user:username:'.$u.':userid');
if (!$prouid) {
	error('非法用户');
	exit;
}

// 集合里面有一个方法是ismember
// 判断这个集合里面是否有这个人
// 如果已经粉过这个人的话，
$isf = $r->sismember('following:'.$user['userid'], $prouid);
// 如果为真，表示已经关注过了，应该显示取消关注才对
$isfstatus = $isf? '0' : '1';
$isfword = $isf?'取消关注':'关注ta';
?>
<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<h2 class="username">test</h2>
这里要判断，我是不是他的粉丝，就要从我的following里面读取，
要想读取自己的following，就要知道自己的userid
<a href="follow.php?uid=<?php echo $prouid;?>&f=<?php echo $isfstatus;?>" class="button"><?php echo $isfword;?></a>

<div class="post">
<a class="username" href="profile.php?u=test">test</a> 
world<br>
<i>11 分钟前 通过 web发布</i>
</div>

<div class="post">
<a class="username" href="profile.php?u=test">test</a>
hello<br>
<i>22 分钟前 通过 web发布</i>
</div>

<div id="footer">redis版本的仿微博项目 <a href="http://redis.io">Redis key-value database</a></div>
</div>
</body>
</html>
