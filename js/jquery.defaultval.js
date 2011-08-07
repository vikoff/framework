(function($){
	$.fn.defaultVal = function(options){
	
		options = $.extend({
			focusColor: '#000000',		// цвет текста при фокусе
			blurColor: '#999999',		// цвет текста без фокуса
			emptyToDefaultOnBlur: true	// преобразовывать пустое значение в дефолтное
		}, options);
		
		this.each(function(){
		
			var trg = $(this);
			var startval = trg.val();
			
			if(!startval.length)
				return;
				
			trg.data('defaultVal', startval);
			trg.data('virginity', true);
			trg.css('color', options.blurColor);
			
			trg.focus(function(){
				$(this).css('color', options.focusColor);
				$(this).data('virginity', false);
				if($(this).val() == $(this).data('defaultVal'))
					$(this).val('');
			});
			
			if(options.emptyToDefaultOnBlur){
				trg.blur(function(){
					if($(this).val() == ''){
						$(this).data('virginity', true);
						$(this).val($(this).data('defaultVal'));
						$(this).css('color', options.blurColor);
					}
				});
			}
			
		});
		return this;
	}
})(jQuery);