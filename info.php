<?php
include_once dirname(__FILE__)."/lib.inc/functions-pico.php";
include_once dirname(__FILE__)."/lib.inc/sessions.php";
include_once dirname(__FILE__)."/lib.inc/dom.php";
$cfg->page_title = "Infomasi";
if(isset($_GET['info_id']))
{
	$info_id = kh_filter_input(INPUT_GET, 'info_id', FILTER_SANITIZE_STRING_NEW);
	$sql_filter_info = " and `edu_info`.`info_id` = '$info_id' ";

	$sql = "SELECT `edu_info`.*, `member`.`name` as `creator`
	from `edu_info` 
	left join(`member`) on(`member`.`member_id` = `edu_info`.`admin_create`) 
	where `edu_info`.`active` = '1' $sql_filter_info ";
	$stmt = $database->executeQuery($sql);
	if($stmt->rowCount() > 0)
	{
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		$cfg->page_title = $data['name'];

		$obj = parsehtmldata('<html><body>'.($data['content']).'</body></html>');
		$arrparno = array();
		$arrparlen = array();
		$cntmax = ""; // do not remove
		$content = ""; // do not remove
		$i = 0;
		$minlen = 300;
		
		if(isset($obj->p) && count($obj->p)>0)
		{
			$max = 0;
			foreach($obj->p as $parno=>$par)
			{
				$arrparlen[$i] = strlen($par);
				if($arrparlen[$i]>$max)
				{
					$max = $arrparlen[$i];
					$cntmax = $par;
				}
				if($arrparlen[$i] >= $minlen)
				{
					$content = $par;
					break;
				}
			}
			if(!$content)
			{
				
				$content = $cntmax;
			}
		}
		if(!$content)
		{
			$content = "&nbsp;";
		}
		$maxlen = 300;
		if(strlen($content)>$maxlen)
		{
			$content.=" ";
			$pos = stripos($content, ". ", $maxlen);
			if($pos===false){
			$pos = stripos($content, ".", $maxlen);
			}
			if($pos===false){
			$pos = stripos($content, " ", $maxlen);
			}
			if($pos===false) $pos = $maxlen;
			$content = substr($content, 0, $pos+1);
			$content = tidyHTML($content);
		}
	
		$cfg->meta_description = htmlspecialchars(strip_tags($content));
		include_once dirname(__FILE__)."/lib.assets/theme/default/header-home.php";
		?>
		<script type="text/javascript" src="<?php echo $cfg->base_assets;?>lib.assets/script/FileSaver.js"></script>
        <script type="text/javascript" src="<?php echo $cfg->base_assets;?>lib.assets/theme/default/js/info.min.js"></script>

        <link rel="stylesheet" type="text/css" href="<?php echo $cfg->base_assets;?>lib.assets/fonts/roboto/font.css">
        <div class="main-content">
            <div class="main-content-wrapper">
            <div class="article-title"><h1><?php echo $data['name'];?></h1></div>
            <div class="article-content"><?php echo $data['content'];?></div>
            <div class="article-time">Dibuat <?php echo translateDate(date('j F Y H:i:s', strtotime($data['time_create'])));?></div>
            <div class="article-creator">Oleh <?php echo $data['creator'];?></div>
            <div class="article-link">
                <a href="javascript:;" class="download-word">Download</a>
                <a href="info.php">Semua</a>
            </div>
        </div>
        </div>
		<?php
		include_once dirname(__FILE__)."/lib.assets/theme/default/footer-home.php";
	}
	else
	{
		include_once dirname(__FILE__)."/lib.assets/theme/default/header-home.php";
		include_once dirname(__FILE__)."/lib.assets/theme/default/footer-home.php";
	}
}
else
{
include_once dirname(__FILE__)."/lib.assets/theme/default/header-home.php";
$sql_filter_info = "";
if(isset($_GET['period']))
{
$period = kh_filter_input(INPUT_GET, 'period', FILTER_SANITIZE_STRING_NEW);
$sql = "select `edu_info`.* 
from `edu_info` 
where `edu_info`.`active` = '1' and `edu_info`.`time_create` like '$period%' $sql_filter_info 
order by `edu_info`.`info_id` desc
";
}
else
{
$sql = "select `edu_info`.* 
from `edu_info` 
where `edu_info`.`active` = '1' $sql_filter_info 
order by `edu_info`.`info_id` desc
limit 0,20
";
}
$stmt = $database->executeQuery($sql);
if($stmt->rowCount() > 0)
{
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	?>
    <div class="main-content">
    	<div class="main-content-wrapper">
            <h1>Informasi <?php echo $cfg->app_name;?></h1>
	<link rel="stylesheet" type="text/css" href="<?php echo $cfg->base_assets;?>lib.assets/fonts/roboto/font.css">
	<div class="article-list">
	<?php
	foreach($rows as $data)
	{

		$obj = parsehtmldata('<html><body>'.($data['content']).'</body></html>');
		$arrparno = array();
		$arrparlen = array();
		$cntmax = ""; // do not remove
		$content = ""; // do not remove
		$i = 0;
		$minlen = 300;
		
		if(isset($obj->p) && count($obj->p)>0)
		{
			$max = 0;
			foreach($obj->p as $parno=>$par)
			{
				$arrparlen[$i] = strlen($par);
				if($arrparlen[$i]>$max)
				{
					$max = $arrparlen[$i];
					$cntmax = $par;
				}
				if($arrparlen[$i] >= $minlen)
				{
					$content = $par;
					break;
				}
			}
			if(!$content)
			{
				
				$content = $cntmax;
			}
		}
		if(!$content)
		{
			$content = "&nbsp;";
		}
		$maxlen = 300;
		if(strlen($content)>$maxlen)
		{
			$content.=" ";
			$pos = stripos($content, ". ", $maxlen);
			if($pos===false){
				$pos = stripos($content, ".", $maxlen);
			}
			if($pos===false){
				$pos = stripos($content, " ", $maxlen);
			}
			if ($pos === false) {
				$pos = $maxlen;
			}
			$content = substr($content, 0, $pos+1);
			$content = tidyHTML($content);
		}
	
		?>
		<div class="article-item">
			<div class="article-title"><h3><?php echo $data['name'];?></h3></div>
			<div class="article-content"><?php echo $content;?></div>
			<div class="article-link">
				<a href="info.php?option=detail&info_id=<?php echo $data['info_id'];?>">Baca</a>
			</div>
		</div>
		<?php
	}
	?>
    </div>
    </div>
</div>
	<?php	
}

include_once dirname(__FILE__)."/lib.assets/theme/default/footer-home.php";
}
?>