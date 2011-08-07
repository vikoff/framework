
function enableFloatingSubmits(){
	var numEnabledSubmitButtons = 0;
	$('#submit-box').children().each(function(){
		var submit = $(this);
		var act = submit.attr('id').replace('submit-', '');
		if(!act)
			return;
		var isLink = this.tagName.toLowerCase() == 'a';
		var btn = $('<a href="" class="btn-' + act + '" title="' + submit.attr('title') + '">' + (isLink ? submit.text() : submit.val()) + '</a>');
		if(isLink)
			btn.attr('href', submit.attr('href'));
		else
			btn.click(function(){submit.click(); return false;});
		$('<li></li>')
			.append(btn)
			.appendTo('#submit-box-floating');
			
		numEnabledSubmitButtons++;
	});
	if(numEnabledSubmitButtons){
		$('#submit-box').hide();
		$('#submit-box-floating')
			.css({'display': 'block'})
			.floatblock();
	}
}

