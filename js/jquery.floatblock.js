(function($){
	$.fn.floatblock = function(options){
		
		if(options == 'stop'){
			this.each(stop);
			return this;
		}
		
		var o = $.extend({
			'topSpace': 5,
			/** тип плейсхолдера: клон элемента с visibility: hidden; или пустой div с visibility: hidden. */
			'placeholder': 'div' // div|clone
		}, options);
		
		this.each(function(){
			var $this = $(this);
			var index = $.floatblock._getIncrement();
			var scrollTopSpace = $this.offset().top;
			var topSub = parseFloat($this.css('marginTop').replace(/auto/, 0));
			var topOrigin = scrollTopSpace - topSub;
			var leftOrigin = $this.offset().left -  parseFloat($this.css('marginLeft').replace(/auto/, 0));
			var placeholder;
			
			if(o.placeholder == 'div'){
				placeholder = $('<div></div>')
					.css({
						'width': $this.width(),
						'height': $this.height(),
						'border': $this.css('border'),
						'margin': $this.css('margin'),
						'padding': $this.css('padding'),
						'float': $this.css('float'),
						'visibility': 'hidden'
					})
					.insertBefore($this);
			}else{
				placeholder = $this.clone()
					.css({
						'width': $this.width(),
						'height': $this.height(),
						'visibility': 'hidden'
					})
					.insertBefore($this);
			}
			
			// обработчик thead тега
			if(this.tagName.toLowerCase() == 'thead'){
				// зафиксируем ширину всех элементов шапки
				$this.find('tr').children().each(function(){
					$(this).css({
						'width': $(this).width(),
						'height': $(this).height()
					});
				});
				// зафиксируем ширину всех ячеек первой строки таблицы
				$this.next().find('tr:first').children().each(function(){
					$(this).css({
						'width': $(this).width(),
						'height': $(this).height()
					});
				});
				// поместим элемент в пустую таблицу в body
				var wrap = $('<table></table>')
					.attr('id', $this.parent().attr('id'))
					.attr('class', $this.parent().attr('class'))
					.append($this)
					.appendTo('body');
				
				o.topSpace = 0;
			}
			// другие элементы
			else{
				$this.appendTo('body');
			}
			
			// присваивание необходимых свойств текущему элементу
			$this
				.data('floatblock-index', index)
				.data('floatblock-enabled', true)
				.data('floatblock-placeholder', placeholder)
				.data('floatblock-origin-css', {
					'position': $this.css('position'),
					'top': $this.css('top'),
					'left': $this.css('left')
				})
				.css({
					'width': $this.width(),
					'height': $this.height(),
					'position': 'absolute',
					'left': leftOrigin,
					'top': topOrigin,
				})
			
			var fixed = false;
			
			function checkPosition(){
				if(scrollTopSpace - $(window).scrollTop() <= o.topSpace){
					if(!fixed){
						$this.css({'position': 'fixed', 'top': o.topSpace - topSub});
						fixed = true;
					}
				}
				else{
					if(fixed){
						$this.css({'position': 'absolute', 'top': topOrigin});
						fixed = false;
					}
				}
			}
			
			checkPosition();
			$(window).bind('scroll.floatblock-item-' + index, checkPosition);
				
		});
		return this;
	}
	
	function stop(){
		$this = $(this);
		if($this.data('floatblock-enabled')){
			var placeholder = $this.data('floatblock-placeholder');
			$this.insertAfter(placeholder)
			placeholder.remove();
			$(window).unbind('scroll.floatblock-item-' + $this.data('floatblock-index'));
			$this.css($this.data('floatblock-origin-css'));
			$this.data('floatblock-enabled', false);
		}
	}
	
	$.floatblock = {
		_increment: 0,
		_getIncrement: function(){
			return ++$.floatblock._increment;
		}
	}

})(jQuery);