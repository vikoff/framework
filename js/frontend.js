
var Layout = {
	
	_prevHash: '',
	
	asyncEnable: function(){
		
		var syncPath = location.href.replace($('base').attr('href'), '').split('#')[0];
		
		if (!location.hash)
			location.hash = '/' + syncPath;
	
		var asyncPath = location.hash.substr(2);
		
		$(function(){
			
			if (asyncPath != syncPath){
				Layout._fetchData(asyncPath);
			} else {
				Layout._prevHash = asyncPath;
			}
			
			$('a').click(function(){
				var t = $(this);
				if (t.hasClass('external-link') || t.hasClass('jlink'))
					return true;
				location.hash = '/' + (t.attr('href') || '');
				return false;
			});
			
			$.address.change(function(addr){
				var uri = addr.value.substr(1);
				if (uri != Layout._prevHash){
					// alert(uri + '\n' + Layout._prevHash + '\n' + (uri == Layout._prevHash));
					Layout._fetchData(uri);
				}
			});
		});
	},
	
	_fetchData: function(uri){
		Layout._prevHash = uri;
		$.get(href(uri), function(response){ Layout._fillData(response); }, 'json');
	},
	
	_fillData: function(data){
		
		this.setTitle(data.title);
		this.setContent(data.content);
		this.setTopMenu(data.topMenuActiveIndex);
		// var_dump(data);
	},
	
	setTitle: function(html){
		
		$('title').text(html);
	},
	
	setContent: function(html){
		
		$('#page-content').html(html);
	},
	
	setTopMenu: function(activeIndex){
		
		$('#top-menu a').removeClass('active');
		$('#top-menu a:eq(' + activeIndex + ')').addClass('active');
	},
}

