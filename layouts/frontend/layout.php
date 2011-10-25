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
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://scripts.vik-off.net/debug.js"></script>
	<script type="text/javascript" src="js/jquery.address-1.4.min.js"></script>
	<script type="text/javascript" src="js/jquery.ctrlentersend.min.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/frontend.js"></script>
	<script type="text/javascript">
		
		// инициализация хеша
		/*
		if (!location.hash)
			location.hash = '/' + location.href.replace($('base').attr('href'), '').split('#')[0];
		*/
	
		$(function(){
			
			/*
			$('a').click(function(){
				var t = $(this);
				if (t.hasClass('external-link') || t.hasClass('jlink'))
					return true;
				var uri = t.attr('href') || '';
				location.hash = '/' + uri;
				return false;
			});
			
			$.address.change(function(addr){
				var uri = addr.value.substr(1);
				// alert(uri + '|' + location.hash.substr(2));
				// if (uri != location.hash.substr(2)){
					// alert('send');
					$.get(href(uri), function(response){ Layout.fill(response); }, 'json');
				// }
			});
			*/
		});
	</script>
</head>
<body>

<div id="top-menu">
	<?= $this->_getTopMenuHTML(); ?>
</div>

<div id="page-content">

	<?=$this->_getContentHTML();?>
	
</div>

<div id="footer">
	<?=$this->_getClientStatisticsLoader();?>
</div>

<?= Debugger::get()->getPageStatisticsHtml();?>

</body>
</html>