<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
     "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>

	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?=$this->_getHtmlTitle();?></title>
	<base href="<?=$this->_getHtmlBaseHref();?>" />
	
	<meta name="robots" content="index, follow" />
	<meta name="keywords" />
	<meta name="author" content="Yuriy Novikov" />
	<meta name="description" content="description words" />
	<meta name="generator" content="vik-off-CMF" />

<?=$this->_getHtmlLinkTags();?>

	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://scripts.vik-off.net/debug.js"></script>
	<script type="text/javascript">
		var WWW_ROOT = '<?= WWW_ROOT; ?>';
	</script>
</head>
<body>

<div>

	<?=$this->_getHtmlContent();?>
	
</div>

<div id="footer">
	<?=$this->_getClientStatisticsLoader();?>
</div>

<?= Debugger::get()->getPageStatisticsHtml();?>

</body>
</html>