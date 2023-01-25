<?php
include_once dirname(__FILE__)."/lib.inc/auth-siswa.php";
include_once dirname(__FILE__)."/lib.inc/mobile-detector.php";
if(@$auth_student_id && @$auth_school_id)
{

$test_id = addslashes(abs(@$_GET['test_id']));
if($test_id == 0)
{
	$test_id = addslashes(abs(@$_POST['test_id']));
}
$offset = addslashes(abs(@$_GET['offset']));
	
if(@$_GET['option'] == 'login')
{
	header("Location: ".$cfg->base_url."siswa/ujian/index.php?confirm-login-to-test=yes&test_id=$test_id");
	exit();
}

$school_id = @$school_id . '';

if(isset($_POST['save']) || strlen(@$_POST['submit_test']))
{
	$start = addslashes(@$_SESSION['session_test'][$student_id][$test_id]['start']);
	if($start == '' || $start == '0000-00-00 00:00:00')
	{
		$start = kh_filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING_NEW);
	}
	$end = $picoEdu->getLocalDateTime();
	
	$sql = "select * from `edu_test` where `test_id` = '$test_id' ";

	$data = $database->executeQuery($sql)->fetch(PDO::FETCH_ASSOC);
	$test_name = $data['name'];
	if($data['standard_score'] == 0)
	{
		$data['standard_score'] = 1;
	}
	foreach($_POST as $key=>$val)
	{
		if(stripos($key, 'answer_')===0)
		{
			$field = addslashes($key);
			$value = addslashes($val);
			$_SESSION['answer_tmp'][$student_id][$test_id][$field] = $value;
		}
	}
	
	// check score dari answer
	$arr = $_SESSION['answer_tmp'][$student_id][$test_id];
	$answer_arr = array();
	
	$true = 0;
	$false = 0;
	$score = 0;
	$jumlah_soal = 0;
	
	foreach($arr as $key=>$value)
	{
		$soal = addslashes(substr($key, strlen('answer_')));
		$answer = addslashes($value);
		$answer_arr[] = "[".$soal.",".$answer."]";
		
		$sql = "select * from `edu_option` where `option_id` = '$answer' ";
		$dt = $database->executeQuery($sql)->fetch(PDO::FETCH_ASSOC);
		
		if($dt['score']>0)
		{
			$true++;
			$score+=$dt['score'];
		}
		else
		{
			$false++;
		}
		$jumlah_soal++;
	}
	if($jumlah_soal == 0)
	{
		$jumlah_soal = 1;
	}
	$str_soal = @$_SESSION['session_test'][$student_id][$test_id]['soal'];
	$str_soal = trim(str_replace(array('[', ']'), array('', ','), $str_soal), ',');
		
	$penalty = $false*$data['penalty'];
	$final_score = $score-$penalty;
	$percent = 100*($final_score/($jumlah_soal*$data['standard_score']));
	$proses = false;
	$answer_str = addslashes(implode(",", $answer_arr)); // catatan answer
	if($data['has_limits'])
	{
		$sql = "select * from `edu_answer` where `student_id` = '$student_id' and `test_id` = '$test_id' ";
		$nujian = $database->executeQuery($sql)->rowCount();
		if($nujian < $data['trial_limits'])
		{
			$proses = true;
		}
		else
		{
			$proses = false;
		}
	}
	else
	{
		$proses = true;
	}
	$question_set = str_replace(array('[', ']'), array(',', ','), @$_SESSION['session_test'][$student_id][$test_id]['soal']);
	$question_set = trim(str_replace(",,", ",", $question_set), ",");
	$storage_key = md5($student_id."-".$test_id."|".$question_set); 
	$storage_key;
	if($proses)
	{
		$_SESSION['session_test'][$student_id][$test_id] = null;
		unset($_SESSION['session_test'][$student_id][$test_id]);
		$_SESSION['answer_tmp'][$student_id][$test_id] = null;
		unset($_SESSION['answer_tmp'][$student_id][$test_id]);
		$_SESSION['session_test'] = array();
		// simpan di tabel answer
		$sql = "insert into `edu_answer` 
		(`school_id`, `test_id`, `student_id`, `start`, `end`, `answer`, `true`, `false`, `initial_score`, `penalty`, `final_score`, `percent`, `active`) values
		('$school_id', '$test_id', '$student_id', '$start', '$end', '$answer_str', '$true', '$false', '$score', '$penalty', '$final_score', '$percent', '1') ";
		$database->execute($sql);
		$picoEdu->logoutTest($school_id, $student_id, $test_id, session_id(), $picoEdu->getLocalDateTime(), addslashes($_SERVER['REMOTE_ADDR']));
		include_once dirname(__FILE__)."/lib.inc/header.php";
		?>
        <div class="info">Jawaban berhasil dikirim.</div>
        <script type="text/javascript">
		var test = '<?php echo $test_id;?>';
		window.localStorage.removeItem('<?php echo $storage_key;?>-answer-set');
		window.localStorage.removeItem('<?php echo $storage_key;?>-current-index');
		window.localStorage.removeItem('jwb_'+test);
        window.location = '<?php echo "".$cfg->base_url."siswa/ujian/index.php?option=sent&test_id=$test_id";?>';
        </script>
        <?php
		include_once dirname(__FILE__)."/lib.inc/footer.php";
		exit();
	}
}

$sql_filter = "";
$sql_filter .= " 
	and (
	`edu_test`.`open` = '1'
	or (
		((`edu_test`.`class` = '' or `edu_test`.`class` = '||')))
	or (concat(',',`edu_test`.`class`,',') like '%,$class_id,%')
	)
";

$now = $picoEdu->getLocalDateTime();

$sql = "select `edu_test`.*  
from `edu_test` 
where `edu_test`.`active` = '1'
and `edu_test`.`test_id` = '$test_id' 
and (`edu_test`.`test_availability` = 'F' or `edu_test`.`available_to` >= '$now')
and `edu_test`.`school_id` = '$school_id'
$sql_filter
";
$stmt = $database->executeQuery($sql);
if($stmt->rowCount() > 0)
{
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$question_per_page = $data['question_per_page'];
$alert_message = $data['alert_message'];
$has_alert = $data['has_alert'];
$alert_time = $data['alert_time'];
$autosubmit = 0;
if(isset($data['autosubmit']))
{
	$autosubmit = $data['autosubmit'];
}
$curtime = date('Y-m-d H:is');
if($data['test_availability'] != 'F' && ($data['available_from'] > $curtime || $data['available_to'] < $curtime))
{
include_once dirname(__FILE__)."/lib.inc/header.php";
?>
<blockquote>
<p>Anda tidak dapat bisa mengikuti test <strong><?php echo stripslashes($data['name']);?></strong> karena tidak dalam masa ujian. <a href="../">Klik di sini untuk kembali</a>
</p>
</blockquote>
<?php	
include_once dirname(__FILE__)."/lib.inc/footer.php";
}
else
{
$guidance_text = $data['guidance'];
$test = $data['test_id'];

if(@$_GET['option']=='sent')
{
include_once dirname(__FILE__)."/lib.inc/header.php";
?>
<div class="info">
<?php
$sql = "select * from `edu_answer` where `student_id` = '$student_id' and `test_id` = '$test_id' order by `start` desc ";
$stmt = $database->executeQuery($sql);
$ntest = $stmt->rowCount();
if($ntest > 0)
{
?>
<p>Anda telah mengerjakan ujian <strong><?php echo stripslashes($data['name']);?></strong>. <a href="../siswa/ujian.php?option=history&test_id=<?php echo $test;?>">Klik di sini untuk kembali</a>
</p>
<script type="text/javascript">
window.localStorage.removeItem('jwb_<?php echo $test;?>');
</script>
<?php
}
else
{
?>
<p>Pengiriman jawaban gagal. <a href="javascipt:history.go(-1)">Klik di sini untuk kembali</a></p>
<?php
}
?>
</div>
<?php
include_once dirname(__FILE__)."/lib.inc/footer.php";
}
else if(@$_GET['login-to-test'])
{
$dur_obj = $picoEdu->secondsToTime($data['duration']);
if($data['has_limits'])
{
	$sql = "select * from `edu_answer` where `student_id` = '$student_id' and `test_id` = '$test_id' order by `start` desc ";
	$stmt = $database->executeQuery($sql);
	$ntest = $stmt->rowCount();
	if($ntest < $data['trial_limits'])
	{
		$proses = true;
	}
	else
	{
		$proses = false;
		$dt = $stmt->fetch(PDO::FETCH_ASSOC);
		$test_terakhir = $dt['start'];
	}
}
else
{
	$proses = true;
}
if($proses)
{
$question_package = @$_SESSION['session_test'][$student_id][$test]['soal'];
if(empty($question_package))
{
	$number_of_question = $data['number_of_question'];
	$duration = $data['duration'];
	$question_per_page = $data['question_per_page'];
	$due_time = time()+$duration;
	$_SESSION['session_test'][$student_id][$test]['start'] = $picoEdu->getLocalDateTime();
	$_SESSION['session_test'][$student_id][$test]['due_time'] = $due_time;
	$alert_message = $data['alert_message'];
	
	if($data['random'])
	{	
		$sql = "select `question_id` , rand() as `rand`
		from `edu_question` where `test_id` = '$test'
		order by `rand` asc
		limit 0, $number_of_question
		";
	}
	else
	{
		$sql = "select `question_id` , `order`
		from `edu_question` where `test_id` = '$test'
		order by `order` asc, `question_id` asc
		limit 0, $number_of_question
		";
	}
	$stmt = $database->executeQuery($sql);
	$arr = array();
						$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($rows as $dt)
	{
		$arr[] = $dt['question_id'];
	}
	$question_package = $str = '['.implode('][', $arr).']';
	$_SESSION['session_test'][$student_id][$test]['soal'] = $str;
	$picoEdu->loginTest($school_id, $student_id, $test, session_id(), $picoEdu->getLocalDateTime(), addslashes($_SERVER['REMOTE_ADDR']));
}

if(isset($_SESSION['session_test'][$student_id][$test]))
{
	$session_id = session_id();
	$key_username = rot13('s:'.strlen('student_username').':"student_username";s:'.strlen($student_id).':"'.$student_id.'";');

	$str = (serialize($_SESSION['session_test'][$student_id][$test]));
	$arr = explode('"start"', $str);
	$key_test = rot13("i:$test;".$arr[0].'"start";s:19:"');
	
	$sql = "select * from `sessions` where `xdata` like '%$key_username%' and `xdata` like '%$key_test%' and `id` != '$session_id' ";
	$stmt = $database->executeQuery($sql);
	if($stmt->rowCount() > 0)
	{
		// Do nothing
	}
}

if(@!$mobile_browser)
{
	include_once dirname(__FILE__)."/lib.inc/test-template-un.php";
}
else
{
	include_once dirname(__FILE__)."/lib.inc/test-template-us.php";
}
}
else
{
include_once dirname(__FILE__)."/lib.inc/header.php";
?>
<div class="warning">
<p>Anda telah melaksanakan ujian sebanyak <strong><?php echo $ntest;?></strong> kali. Ujian terahir pada tanggal<strong> <?php echo translateDate(date('j F Y', strtotime($test_terakhir)));?></strong> jam <strong><?php echo date('H:i:s', strtotime($test_terakhir));?></strong>. <a href="../siswa">Klik di sini untuk kembali</a>
</p>
</div>
<?php
include_once dirname(__FILE__)."/lib.inc/footer.php";
}
?>
<?php
}
else
{
include_once dirname(__FILE__)."/lib.inc/header.php";
?>
<div class="label">
Informasi Singkat Ujian
</div>
  <table width="100%" border="0" class="two-side-table responsive-tow-side-table" cellspacing="0" cellpadding="0">
    <tr>
    <td>Nama Ujian</td><td><?php echo $data['name'];?></td>
    </tr>
    <?php
	if($data['subject'])
	{
	?>
    <tr>
    <td>Mata Pelajaran</td><td><?php echo $data['subject'];?></td>
    </tr>
    <?php
	}
	?>
    <tr>
    <td>Jumlah Soal</td><td><?php echo $data['number_of_question'];?></td>
    </tr>
    <tr>
    <td>Jumlah Pilihan</td><td><?php echo $data['number_of_option'];?></td>
    </tr>
    <tr>
    <td>Nilai Standard</td>
    <td><?php echo $data['standard_score'];?></td>
    </tr>
    <tr>
    <td>Penalti
    </td><td><?php echo $data['penalty'];?></td>
    </tr>
    <tr>
    <td>Otomatis Kirim Jawaban</td>
    <td><?php echo ($data['autosubmit'])?'Ya':'Tidak';?></td>
    </tr>
</table>
<div class="button-area">
<input type="button" value="Masuk Ke Ujian" onclick="window.location='<?php echo $cfg->base_url;?>siswa/ujian/index.php?login-to-test=yes&test_id=<?php echo $data['test_id'];?>'">
<input type="button" value="Batal" onclick="window.location='<?php echo $cfg->base_url;?>siswa/ujian.php'">
</div>
<?php
include_once dirname(__FILE__)."/lib.inc/footer.php";
}
}
}
else
{
include_once dirname(__FILE__)."/lib.inc/header.php";
?>
<div class="info">
<p>Ujian ini tidak tersedia untuk Anda. <a href="ujian.php">Klik di sini untuk kembali</a></p>
</div>
<?php
include_once dirname(__FILE__)."/lib.inc/footer.php";
}
exit();
}
?>