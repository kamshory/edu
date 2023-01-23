<?php
include_once dirname(dirname(__FILE__))."/lib.inc/auth.php";

$mlid = $member_login->member_id;

$json = array('registered'=>0);

if(isset($_POST['username']))
{
	$username = ltrim(kh_filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING_NEW), " \r\n\t0 ");
	$username = getvalidusername($username);
	if($username != '')
	{
		$sql = "select `member_id`, `email`, `username`
		from `member`
		where `username` like '$username'
		and `member_id` != '$mlid'
		";
		$res = mysql_query($sql);
		$json = array('registered'=>0, 'corrected'=>$username, 'valid'=>true);
		if(mysql_num_rows($res))
		{
			$data = mysql_fetch_assoc($res);
			$json = array('registered'=>1, 'corrected'=>$username, 'valid'=>false);
		}
	}
	else
	{
			$json = array('registered'=>0, 'corrected'=>$username, 'valid'=>false);
	}
	echo json_encode($json);
}
?>