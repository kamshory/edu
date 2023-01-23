<?php
function rot13($data)
{
	$a = array ('A'=>'N','B'=>'O','C'=>'P','D'=>'Q','E'=>'R','F'=>'S','G'=>'T','H'=>'U','I'=>'V','J'=>'W','K'=>'X',
				'L'=>'Y','M'=>'Z','N'=>'A','O'=>'B','P'=>'C','Q'=>'D','R'=>'E','S'=>'F','T'=>'G','U'=>'H','V'=>'I',
				'W'=>'J','X'=>'K','Y'=>'L','Z'=>'M','a'=>'n','b'=>'o','c'=>'p','d'=>'q','e'=>'r','f'=>'s','g'=>'t',
				'h'=>'u','i'=>'v','j'=>'w','k'=>'x','l'=>'y','m'=>'z','n'=>'a','o'=>'b','p'=>'c','q'=>'d','r'=>'e',
				's'=>'f','t'=>'g','u'=>'h','v'=>'i','w'=>'j','x'=>'k','y'=>'l','z'=>'m');
	$data2='';
	for($i=0;$i<strlen($data);$i++)
	{
		$c = substr($data, $i, 1);
		if(in_array($c, $a))
		{
			$data2 .= $a[$c];
		}
		else
		{
			$data2 .= $c;
		}
	}
	return $data2;
}

class KamsSession
{
	private $ks_db_connection;
	private $ks_session;
	
	var $ks_cfg_usedatabase = false;
	
	var $ks_db_host;
	var $ks_db_user;
	var $ks_db_password;
	var $ks_db_name;
	var $ks_db_table = "sessions";
	
	var $ks_cookie_name = "kscookie";
	var $ks_cookie_lifetime = 8640000;
	var $ks_cookie_path = "/";
	var $ks_cookie_domain = "";
	var $ks_cookie_secure = false;
	var $ks_cookie_httponly = false;
	
	var $ks_session_id;
	var $ks_session_name = "kssession";
	var $ks_session_gc_probability = 50;
	var $ks_session_save_handler = "user";
	var $ks_session_method = 2;
	
	function __construct()
	{
	}
	
	function session_set_cookie_params($lifetime=0, $path="/", 
	$domain="", $secure=false, $httponly=false)
	{
		$this->ks_cookie_lifetime = $lifetime;
		$this->ks_cookie_path = $path;
		$this->ks_cookie_domain = $domain;
		$this->ks_cookie_secure = $secure;
		$this->ks_cookie_httponly = $httponly;
	}
	function session_set_database_params($host, $database, 
	$user, $password, $table="sessions")
	{
		$this->ks_db_host = $host;
		$this->ks_db_user = $user;
		$this->ks_db_password = $user;
		$this->ks_db_name = $password;
		$this->ks_db_table = $table;
	}
	function start()
	{
		if($this->ks_cfg_usedatabase)
		{
			$this->ks_session_id = $this->getmycookie($this->ks_cookie_name);
			
			ini_set('session.gc_probability', $this->ks_session_gc_probability);
			ini_set('session.save_handler',   $this->ks_session_save_handler);
			ini_set('session.gc_maxlifetime', $this->ks_cookie_lifetime);
			ini_set('session.save_path',      $this->ks_cookie_path);
			ini_set('session.cookie_domain',  $this->ks_cookie_domain);
			
			session_set_save_handler(
				array($this, 'open'),
				array($this, 'close'),
				array($this, 'read'),
				array($this, 'write'),
				array($this, 'destroy'),
				array($this, 'gc'));
			session_id(strrev(md5($this->ks_session_id)));
			session_name($this->ks_session_name);
			session_start();

			$this->setmycookie($this->ks_cookie_name, $this->ks_session_id, 
				time()+$this->ks_cookie_lifetime, $this->ks_cookie_path, 
				$this->ks_cookie_domain, $this->ks_cookie_secure, 
				$this->ks_cookie_httponly);
			setcookie($this->ks_session_name, md5(time().mt_rand(1,999999)), 
				time()+$this->ks_cookie_lifetime, $this->ks_cookie_path, 
				$this->ks_cookie_domain, $this->ks_cookie_secure, 
				$this->ks_cookie_httponly);
			
			if($this->ks_session_method == 2)
			{
				if(is_array($this->ks_session) && count($this->ks_session))
				{
					foreach($this->ks_session as $key=>$gval)
					{
						$_SESSION[$key] = $this->ks_session[$key];
					}
				}
			}
		}
		else
		{
			session_start();
			$this->setmycookie($this->ks_cookie_name, $this->ks_session_id, 
				time()+$this->ks_cookie_lifetime, $this->ks_cookie_path, 
				$this->ks_cookie_domain, $this->ks_cookie_secure, 
				$this->ks_cookie_httponly);
			setcookie($this->ks_session_name, md5(time().mt_rand(1,999999)), 
				time()+$this->ks_cookie_lifetime, $this->ks_cookie_path, 
				$this->ks_cookie_domain, $this->ks_cookie_secure, 
				$this->ks_cookie_httponly);
		}
	}
	
	function setmycookie($name, $value="", $lifetime=0, $path="/", 
	$domain="", $secure=false, $httponly=false)
	{
		$value = str_rot13(strrev($value));
		setcookie($name."0", substr($value,24,8), 
		$lifetime, $path, $domain, $secure, $httponly);
		setcookie($name."1", substr($value,0 ,8), 
		$lifetime, $path, $domain, $secure, $httponly);
		setcookie($name."2", substr($value,16,8), 
		$lifetime, $path, $domain, $secure, $httponly);
		setcookie($name."3", substr($value,8 ,8), 
		$lifetime, $path, $domain, $secure, $httponly);
	}
	
	function getmycookie($name){
		$v0 = (isset($_COOKIE[$name."0"]))?($_COOKIE[$name."0"]):"";
		$v1 = (isset($_COOKIE[$name."1"]))?($_COOKIE[$name."1"]):"";
		$v2 = (isset($_COOKIE[$name."2"]))?($_COOKIE[$name."2"]):"";
		$v3 = (isset($_COOKIE[$name."3"]))?($_COOKIE[$name."3"]):"";
		$v  = strrev(str_rot13($v1.$v3.$v2.$v0));
		if($v=="")
		return md5(microtime().mt_rand(1,9999999));
		else 
		return $v;
	}
	
    public function open()
	{
		try{
			$this->ks_db_connection = @mysql_connect($this->ks_db_host, 
			$this->ks_db_user, $this->ks_db_password);
			if ($this->ks_db_connection) 
			{
				return mysql_select_db($this->ks_db_name, $this->ks_db_connection);
			}
		}
		catch(Exception $e)
		{
			return false;
		}
    }
	
    public function close()
	{
		if($this->ks_db_connection)
		{
        return mysql_close($this->ks_db_connection);
		}
		return true;
    }
	
    public function read($id)
	{
		if($this->ks_db_connection)
		{
			$id = mysql_real_escape_string($id);
			$sql = sprintf("SELECT `data`, `xdata` 
			FROM `".$this->ks_db_table."` WHERE `id` = '%s'", $id);
			if($result = mysql_query($sql, $this->ks_db_connection))
			{
				if(mysql_num_rows($result))
				{
					$record = mysql_fetch_assoc($result);
					if($this->ks_session_method == 2)
					{
						$xdata = str_rot13(stripslashes($record['xdata']));
						$this->ks_session = unserialize($xdata);
					}
					return $record['data'];
				}
			}
			return '';
		}
        return '';
    }
	
    public function write($id, $data)
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		if(count(@$_SESSION) == 0)
		{
			return false;
		}
		if($this->ks_session_method == 2)
		{
			$xdata = str_rot13(serialize(@$_SESSION));
			$data = "";
		}
		else
		{
			$xdata = "";
		}
		if($this->ks_db_connection)
		{
			$sql = sprintf("REPLACE INTO `".$this->ks_db_table."` 
						   VALUES('%s', '%s', '%s', '%s', '%s')",
						   mysql_real_escape_string($id),
						   mysql_real_escape_string($ip),
						   mysql_real_escape_string($data),
						   mysql_real_escape_string($xdata),
						   mysql_real_escape_string(time())
						   );
			return mysql_query($sql, $this->ks_db_connection);
		}
		else
		{
			return false;
		}

    }
    public function forcesave()
	{
		$id = session_id();
		$data = '';
		$ip = $_SERVER['REMOTE_ADDR'];
		if(count(@$_SESSION) == 0)
		{
			return false;
		}
		if($this->ks_session_method == 2)
		{
			$xdata = str_rot13(serialize(@$_SESSION));
			$data = "";
		}
		else
		{
			$xdata = "";
		}
		if($this->ks_db_connection)
		{
			$sql = sprintf("REPLACE INTO `".$this->ks_db_table."` 
						   VALUES('%s', '%s', '%s', '%s', '%s')",
						   mysql_real_escape_string($id),
						   mysql_real_escape_string($ip),
						   mysql_real_escape_string($data),
						   mysql_real_escape_string($xdata),
						   mysql_real_escape_string(time())
						   );
			return mysql_query($sql, $this->ks_db_connection);
		}
		else
		{
			return false;
		}
	}
    public function destroy($id)
	{
		if($this->ks_db_connection)
		{
			$sql = sprintf("DELETE FROM `".$this->ks_db_table."` 
						   WHERE `id` = '%s'", $id);
			return mysql_query($sql, $this->ks_db_connection);
		}
		else
		{
			return false;
		}
	}
	
    public function gc($max)
	{
		if($this->ks_db_connection)
		{
			$sql = sprintf("DELETE FROM `".$this->ks_db_table."` 
						   WHERE `timestamp` < '%s'",
						   mysql_real_escape_string(time() - $max));
			return mysql_query($sql, $this->ks_db_connection);
		}
		else
		{
			return false;
		}
    }	
}

function saveSessionManual($data)
{
	global $ksession;
	$table = $ksession->ks_db_table;
	$id = session_id();
	$xdata = addslashes(rot13(serialize($data)));
	$sql = "update `$table` set `xdata` = '$xdata' where `id` = '$id' ";
	$res = mysql_query($sql);
}

@ini_set('session.bug_compat_42',0);
@ini_set('session.bug_compat_warn',0);
$ksession = new KamsSession();


// database setting
$ksession->ks_db_host         = DB_HOST;
$ksession->ks_db_name         = DB_NAME;
$ksession->ks_db_user         = DB_USER;
$ksession->ks_db_password     = DB_PASS;
$ksession->ks_db_table        = "sessions";
// or session_set_database_params($host, 
// $database, $user, $password, $table);

// cookie setting
$ksession->ks_cookie_name     = "planetbiru";
$ksession->ks_cookie_lifetime = 632448000;
$ksession->ks_cookie_path     = "/";
if(stripos($_SERVER['SERVER_NAME'],'planetbiru.com')!==false)
	$ksession->ks_cookie_domain = ".planetbiru.com";
else
	$ksession->ks_cookie_domain = "";
	
$ksession->ks_cookie_secure   = false;
$ksession->ks_cookie_httponly = false;
// or session_set_cookie_params($lifetime, 
// $path, $domain, $secure, $httponly);

// session setting
$ksession->ks_session_name    = "xpos4";
$ksession->ks_session_method  = 2;
// set ks_session_method to 2 if session data 
// unreadable on classic method
// default value of ks_session_method is 2

if((isset($_POST['login']) && isset($_POST['letmeloggedin'])) || isset($_COOKIE['letmeloggedin']))
{
	$ksession->ks_cookie_lifetime = 632448000;
	setcookie('letmeloggedin', 'true', time()+632448000, $ksession->ks_cookie_path, 
	$ksession->ks_cookie_domain, $ksession->ks_cookie_secure, 
	$ksession->ks_cookie_httponly);
}
if((isset($_POST['login']) && !isset($_POST['letmeloggedin'])))
{
	setcookie('letmeloggedin', '', time(), $ksession->ks_cookie_path, 
	$ksession->ks_cookie_domain, $ksession->ks_cookie_secure, 
	$ksession->ks_cookie_httponly);
}

$ksession->ks_cfg_usedatabase = true;
$ksession->start();
// now, session is ready to be used
?>