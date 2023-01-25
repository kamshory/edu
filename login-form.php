<?php
include_once dirname(__FILE__) . "/lib.inc/functions-pico.php";
include_once dirname(__FILE__) . "/lib.inc/sessions.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="msapplication-navbutton-color" content="#06568E">
  <meta name="theme-color" content="#06568E">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="#06568E">
  <title>Masuk ke <?php echo $cfg->app_name; ?></title>
  <link type="text/css" rel="stylesheet" href="<?php echo $cfg->base_assets; ?>lib.assets/theme/default/css/login.css">
  <link rel="shortcut icon" type="image/jpeg" href="<?php echo $cfg->base_assets; ?>lib.assets/theme/default/css/images/favicon.png" />
  <?php
  if (date('Y') < 2017) {
    if (!isset($_SESSION['set_time_token'])) {
      $setTimeToken = md5($_SERVER['REMOTE_ADDR'] . '-' . time() . '-' . mt_rand(111111, 999999));
      $_SESSION['set_time_token'] = $setTimeToken;
      // saveSessionManual($_SESSION);
    }
    $setTimeToken = $_SESSION['set_time_token'];
  ?>
    <script type="text/javascript">
      var setTimeToken = '<?php echo $setTimeToken; ?>';
    </script>
    <script type="text/javascript" src="lib.assets/script/time-sync.min.js">
    </script>
  <?php
  }
  ?>
</head>

<body>
  <div class="all">
    <div class="content">
      <div class="body">
        <div class="logo-180"></div>
        <h3>Masuk Sebagai Siswa</h3>
        <form name="form1" method="post" action="login.php">
          <div class="input-control"><input type="text" name="username" id="username" placeholder="Nomor Induk Siswa" required></div>
          <div class="input-control"><input type="password" name="password" id="password" placeholder="Password Siswa" required></div>
          <div class="input-control">
            <input class="button-gradient" type="submit" name="login" id="login" value="Login">
          </div>
        </form>
      </div>
      <div class="footer">
        &copy; <a href="http://edu.planetbiru.com/" target="_blank">Planet Edu</a> 2008-<?php echo date('Y'); ?>. All rights reserved.
      </div>
    </div>
  </div>
</body>

</html>