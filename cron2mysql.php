<? php
	include('./lib.php');
	$r = connredis();
	// 连接mysql，并把旧微博写入数据库中
	$conn = mysql_connect('127.0.0.1', 'root', '');
	mysql_query('use test', $conn);
	mysql_query('set names utf8', $conn);

	while ($r->llen('global:store') >= 1000) {

		$sql = 'insert into post(postid, userid, username, time, content) values ';

		// 要把链表最右侧的1000个单元拿出来，根据这一千条微博id，再去查微博的真正内容
		// 查出来再写入数据库，这就是我们的目标
		$i = 0;
		while($i++<1000) {
			$postid = $r -> rpop('global:store');
			$post = $r->hmget('post:postid:'.$postid, array('userid', 'username', 'time', 'content'));

			$sql .= "($postid, '" . $post['userid'] . "','" . $post['username'] . "','" . $post['time'] . "','" . $post['content'] . "'),");
		}
		$sql = substr($sql, 0, -1);
	    mysql_query($sql, $conn);
    }
    echo 'ok';
?>