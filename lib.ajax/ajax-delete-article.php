<?php
include_once dirname(dirname(__FILE__))."/lib.inc/auth-guru.php";
if(@$school_id == 0)
{
exit();
}
$article_id = kh_filter_input(INPUT_POST, 'article_id', FILTER_SANITIZE_NUMBER_UINT);
if(@$_POST['option'] == 'delete')
{
	$sql = "DELETE FROM `edu_article` where `article_id` = '$article_id' and `school_id` = '$school_id' and `member_create` = '$auth_teacher_id' ";
	$database->execute($sql);
}
