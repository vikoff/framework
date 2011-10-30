<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">
<head>

	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?=$this->_getTitleHTML();?></title>
	<base href="<?=$this->_getBaseHrefHTML();?>" />
	
	<meta name="robots" content="index, follow" />
	<meta name="keywords" content="" />
	<meta name="author" content="Yuriy Novikov" />
	<meta name="description" content="description words" />
	<meta name="generator" content="vik-off-CMF" />

<?=$this->_getLinkTagsHTML();?>

	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/backend.css" type="text/css" />
	<!-- <link rel="icon" type="image/png" href="favicon.ico" /> -->
	
	<script type="text/javascript">
		var WWW_ROOT = '<?= WWW_ROOT; ?>';
	</script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://scripts.vik-off.net/debug.js"></script>
	
	<script type="text/javascript" src="js/jquery.validate.pack.js"></script>
	<script type="text/javascript" src="js/jquery.ctrlentersend.min.js"></script>
	<script type="text/javascript" src="js/jquery.floatblock.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/backend.js"></script>
</head>
<body>

<div id="site-container">

	<div id="top">
	
		<a href="<?=href('admin');?>" style="display: block; color: black; text-decoration: none; float: left; padding: 10px; background-color: #FFD46D;"><strong><?=CFG_SITE_NAME;?></strong></a>
		
		<div style="text-align: right;">
			<a href="<?= href(''); ?>">На сайт</a>
			<form action="" method="post" class="inline" onsubmit="return confirm('Уверены?');">
				<?= FORMCODE; ?>
				<input type="submit" class="button" name="action[user/profile/logout]" value="Выход" />
			</form>
		</div>
		
		<div id="top-menu-list">
			<?= $this->_getTopMenuHTML(); ?>
		</div>
		
		<div class="clear"></div>
	</div>
	
	<table id="body-frame">
	<tbody>
	<tr>
		<td id="body-left">
		
			<div id="left-menu-container">
				<div id="left-menu-list">
					<?= $this->_getLeftMenuHTML(); ?>
				</div>
			</div>
			
		</td>
		<td id="body-right">
			
			<?=$this->_getBreadcrumbsHTML(); ?>
			
			<?=$this->_getUserMessagesHTML();?>
			
			<?=$this->_getContentHTML();?>
	
		</td>
	</tr>
	</tbody>
	</table>
	
	<div class="clear"></div>
	
	<div id="footer-container"></div>
	
</div>

<div id="footer">
	<?=CFG_SITE_NAME;?>
</div>

<?= Debugger::get()->getPageInfoHTML();?>

</body>
</html>