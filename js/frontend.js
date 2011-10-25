
var Layout = {
	
	fill: function(data){
		
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