<?php
include_once dirname(dirname(__FILE__))."/lib.inc/auth-admin.php";
if($admin_login->admin_level != 1)
{
	include_once dirname(__FILE__)."/bukan-super-admin.php";
	exit();
}

?><!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo $cfg->base_url;?>">
<link rel="shortcut icon" type="image/jpeg" href="<?php echo $cfg->base_assets;?>lib.assets/theme/default/css/images/favicon.png" />
<title>Username dan Password Guru - <?php echo $cfg->app_name;?></title>
<style type="text/css">
body{
	margin:0;
	padding:0;
}
.all{
	padding:10px;
}
.main-table{
	border-collapse:collapse;
}
.main-table td{
	padding:4px 5px;
}
.header{
	margin-bottom:10px;
}
h1, h2, h3{
	text-align:center;
	margin:0;
	padding:4px 0;
	text-transform:uppercase;
}
h1{
	font-size:18px;
}
h2{
	font-size:16px;
}
h3{
	font-size:14px;
}
.user-item{
	margin:15px 0;
}
.cut-here {
  height: 0px;
  border-bottom: 1px dashed #333333;
  margin: 18px 15px 18px 15px;
  display: block;
  position:relative;
}
.cut-here::before {
  content: '\2702';
  font-size: 12px;
  position: absolute;
  top: -9px;
  left: -15px;
}
.cut-here::after {
  transform:rotate(180deg);
  content: '\2702';
  font-size: 12px;
  position: absolute;
  top: -7px;
  right: -15px;
}
</style>
</head>

<body>
<div class="all">
<div class="header">
<h1>Username dan Password Guru</h1>
</div>
<div class="main">
<?php
    $sql = "SELECT `edu_teacher`.* 
    from `edu_teacher` 
    where `edu_teacher`.`active` = true
    order by `edu_teacher`.`name` asc 
    ";
    $stmt = $database->executeQuery($sql);
    if ($stmt->rowCount() > 0) {
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($rows as $data) {
        ?>

<div class="cut-here"></div>

<div class="user-item">
<table width="100%" border="1" cellspacing="0" cellpadding="0" class="main-table">
  <tr>
    <td width="18%">URL</td>
    <td width="15%">Nomor Induk</td>
    <td width="32%">Nama</td>
    <td width="20%">Username</td>
    <td width="15%">Password</td>
  </tr>
  <tr>
    <td><?php echo trim($cfg->base_url, "/");?></td>
    <td><?php echo $data['reg_number'];?></td>
    <td><?php echo $data['name'];?></td>
    <td><?php echo $data['username'];?></td>
    <td><?php echo $data['password_initial'];?></td>
  </tr>
</table>
</div>
<?php
      }
      ?>
</div>
</div>
</body>
</html>
<?php
    }

?>