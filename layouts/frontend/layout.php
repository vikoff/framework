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

	<link rel="stylesheet" href="<?= WWW_ROOT; ?>css/common.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="<?= WWW_ROOT; ?>css/frontend.css" type="text/css" media="screen, projection" />
	<!--[if lte IE 6]><link rel="stylesheet" href="css/frontend_ie.css" type="text/css" media="screen, projection" /><![endif]-->
	
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

<div id="wrapper">

	<div id="header">
		
		<div id="top-menu">
			<?= $this->_getTopMenuHTML(); ?>
		</div>
		
	</div>

	<div id="middle">

		<div id="container">

			<div id="content">
			
				<?=$this->_getUserMessagesHTML();?>
				
				<?=$this->_getContentLinksHTML(' | ');?>
			
				<?=$this->_getContentHTML();?>
				
			</div>
		</div>

		<div class="sidebar" id="sideLeft">
			<?= $this->_getProfileBlockHTML(); ?>
		</div>

		<div class="sidebar" id="sideRight">
			vik-off framework
		</div>

	</div>

</div>

<div id="footer"></div>

<?= $this->_getClientStatisticsLoaderHTML(); ?>
<?= Debugger::get()->getPageInfoHTML(); ?>
<?= Debugger::get()->getLog(); ?>

</body>
</html>