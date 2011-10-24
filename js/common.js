function captcha_reload(){$("#captcha").attr("src",'libs/captcha/captcha.php?rnd='+Math.round(Math.random(0)*1000));}
		
function href(href){
	return WWW_ROOT + 'index.php' + (href ? '?r=' + href.replace('?', '&') : '');
}

// набор стандартных опций для tinymce
function getDefaultTinyMceSettings(){
	
	return {
	
		document_base_url: WWW_ROOT,
		script_url: WWW_ROOT + 'libs/tiny_mce/tiny_mce.js',
		popup_css: WWW_ROOT + 'libs/tiny_mce/themes/advanced/skins/default/dialog.css',
		
		language : 'ru',
		
		theme : "advanced",
		plugins : "pagebreak,emotions,inlinepopups,preview,media,contextmenu,paste,fullscreen,template,advlist",
		
		class_filter : function(cls, rule) {
			// trace(cls + ', ' + rule + '<br />');
			return cls;
		},
		
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,styleselect,formatselect,fontselect,fontsizeselect,|,undo,redo,|,preview,fullscreen",
		theme_advanced_buttons2 : "hr,removeformat,|,charmap,emotions,iespell,media,advhr,|,cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,image,cleanup,help,code,|,forecolor,backcolor,pagebreak",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		content_css : WWW_ROOT + 'css/common.css,'+WWW_ROOT + 'css/frontend.css'
	};
}

$(function(){
	
	VikDebug.init();
	
	$.ajaxSetup({
		error: function(xhr){VikDebug.print(xhr.responseText, 'ajax-error', {position: 'top'});}
	});
	
	$('.ctrlentersend').ctrlentersend();
	
	$('table.tr-highlight>tbody>tr').hover(
		function(){$(this).addClass("tr-hover")},
		function(){$(this).removeClass("tr-hover");}
	);					

});
