(function($){
	$.fn.ctrlentersend = function(options){
	
		options = $.extend({
		}, options);
		
		this.each(function(){
		
			$(this).keypress(function(e){
				if(e.ctrlKey && (e.keyCode == 10 || e.keyCode == 13)){
					if(this.form){
						$(this.form).submit();
					}
				}
			})
		});
		return this;
	}
})(jQuery);