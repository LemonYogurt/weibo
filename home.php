<?php
include('./lib.php');
include('./header.php');
if (($user = isLogin()) == false) {
    header('location: index.php');
    exit;
}

$r = connredis();
/*
// 取出自己发的和粉主推过来的信息
$r->ltrim('receivepost:'.$user['userid'], 0, 49);
// 并不是想要别人推送给我的id，而是我想要它的内容
// 它循环的取出微博的id，然后循环的把id替换到*，再取出它的内容
// $newpost = $r->sort('receiveport:'.$user['userid'], array('sort'=>'desc', 'get'=>'post:postid:*:content'));

// 现在得到的是微博的主键
$newpost = $r->sort('receiveport:'.$user['userid'], array('sort'=>'desc'));
*/

// 首先要知道关注的那些人，获取我关注的人
//
$star = $r->smembers('following:'.user['userid']);
// 这里把自己的id也加进去
$star[] = $user['userid'];

// 在post中维护了最新20条微博的有序列表，但是我并非20条全要
// 得到上次抽取的内容
$lastpull = $r->get('lastpull:userid:'.$user['userid']);
if (!$lastpull) {
    $lastpull = 0;
}

// 拉取最新数据
$latest = array();
foreach($star as $s) {
    // 关注的人的userid都知道，把它们要满足什么条件的拉取过来
    // 1 << 32 - 1是php中的，在js中使用的是Math.pow()
    // 这里表示的是score
    //
    $latest = array_merge($latest, $r->zrangebyscore('starpost:userid:'.$s, $lastpull + 1, 1<<32-1));
}


sort($latest, SORT_NUMRIC);

// 更新lastpull
// 既然已经排序了，它的值是最后那个最大的值
// 这里需要注意：数组为0，把lastpull更新为0了，所以要做一个判断：
if (!empty($latest)) {
    $r->set('lastpull:userid'.$user['userid'], end($latest));
}
// 循环把latest放到自己主页应该收取的微博链表里
foreach($latest as $l) {
    $r->lpush('recivepost:'.$user['userid'], $l);
}
// 保持个人主页，最多收取1000条最新微博
$r->ltrim('recivepost:'.$user['userid'], 0, 999);

// 这一行是用hash结构存储微博
$newpost = $r->sort('recivepost:'.$user['userid'], array('sort'=>'desc'));

// 计算几个粉丝，几个关注
// 就是计算集合的元素个数
// 专门用来计算集合的个数
$myfans = $r->sCard('following:'.$user['userid']);
$mystar = $r->sCard('follower:'.$user['userid']);

?>

<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<div id="postform">
<form method="POST" action="post.php">
<?php echo $user['username']; ?>, 有啥感想?
<br>
<table>
<tr><td><textarea cols="70" rows="3" name="status"></textarea></td></tr>
<tr><td align="right"><input type="submit" name="doit" value="Update"></td></tr>
</table>
</form>
<div id="homeinfobox">
<?php echo $myfans; ?> 粉丝<br>
<?php echo $mystar; ?> 关注<br>
</div>
</div>
这里得到的微博，不仅要得到我自己发布的微博，还要得到我关注人的微博
有两种方式：
把关注的人循环一遍，把它们的微博取出来
还有一种办法，就是当一个人发微博的时候，直接推送给我的粉丝

<?php
// 通过关联数组得到微博的内容
foreach($newpost as $postid) {
    $p = $r->hmget('post:postid:'.$postid, array('userid', 'username', 'time', 'content'));
?>

<div class="post">
<a class="username" href="profile.php?u=<?php echo $p['username']; ?>"><?php echo $p['username']; ?></a> <?php echo $p['content']; }?><br>
<i><?php echo formattime($p['time']); ?>前 通过 web发布</i>
</div>
<?php }?>
<?php include('./footer.php');?>