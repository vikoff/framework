<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
     "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>

	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?=$this->_getTitleHTML();?></title>
	<base href="<?=$this->_getBaseHrefHTML();?>" />
	
	<meta name="robots" content="index, follow" />
	<meta name="keywords" />
	<meta name="author" content="Yuriy Novikov" />
	<meta name="description" content="description words" />
	<meta name="generator" content="vik-off-CMF" />

<?=$this->_getLinkTagsHTML();?>

	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/frontend.css" type="text/css" />
	
	<script type="text/javascript">
		var WWW_ROOT = '<?= WWW_ROOT; ?>';
	</script>
	<script type="text/javascript" src="<?= WWW_ROOT; ?>js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="<?= WWW_ROOT; ?>js/jquery.browser.min.js"></script>
	<script type="text/javascript" src="<?= WWW_ROOT; ?>js/jquery.ctrlentersend.min.js"></script>
	<script type="text/javascript" src="<?= WWW_ROOT; ?>js/common.js"></script>
	<script type="text/javascript" src="<?= WWW_ROOT; ?>js/frontend.js"></script>
	<script type="text/javascript" src="<?= WWW_ROOT; ?>js/debug.js"></script>
	<script type="text/javascript">
		
		// Layout.asyncEnable();
	</script>
</head>
<body>

<div id="top-menu">
	<?= $this->_getTopMenuHTML(); ?>
</div>

<div class="paragraph">
	<?= $this->_getLoginBlockHTML(); ?>
</div>

<div id="page-content" style="text-align: center;">

	<?=$this->_getContentHTML();?>
	
</div>

<div id="footer">
	<?=$this->_getClientStatisticsLoaderHTML();?>
</div>

<?= Debugger::get()->getPageInfoHTML();?>

</body>
</html>