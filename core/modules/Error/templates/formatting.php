
<style type="text/css">
.error-all{
	padding: 10px 15px;
	margin: 1em 5px;
}
.error-all.handler-mode{
	border: solid 2px red;
	background-color:#FFF7F7;
}
.error-all.display-mode{
	border: solid 2px #CACFCF;
	background-color:#F0F0F1;
}
.error-level{
	font-weight: bold;
}
.error-all.handler-mode .error-level{
	color: red;
}
.error-managment{
	text-align: right;
	margin-bottom: 2px;
}
.error-stack-trace{
	margin-top: 10px;
	font-family: monospace;
	font-size: 13px;
	line-height: 16px;
}
.error-args-item{
	position: relative;
}
.error-args-detail{
	position: absolute;
	display: none;
	background-color: #FAFAFA;
	border: solid 2px #AAAAAA;
	left: -5px;
	top: -5px;
	color: #000;
	padding: 5px 10px;
	white-space: pre;
	z-index: 1000;
	font-size: 11px;
}
.error-args-short{
	color: #7A94CA;
	font-weight: bold;
	font-size: 13px;
}
.error-meta-info{
	margin-top: 10px;
	font-size: 11px;
	color: #999;
}
.error-meta-info a{
	color: #999;
}
</style>
<script type="text/javascript">

var Error = {
	movedElm: false,
	startTop: 0,
	startLeft: 0,
	startX: 0,
	startY: 0,
	showDetail: function(elm){
		var trg = elm.firstChild;
		trg.style.display='inline';
		
		var elmLeftOrigin = this.getElmPosition(trg).left;
		var elmLeft = elmLeftOrigin;
		
		if(elmLeft + trg.offsetWidth > document.body.clientWidth){
			elmLeft = document.body.clientWidth - trg.offsetWidth - 10;
			elmLeft = elmLeft < 0 ? 0 : elmLeft;
		}
		trg.style.left = (elmLeft - elmLeftOrigin) + "px";
	},
	hideDetail: function(elm){
		elm.firstChild.style.display='none';
	},
	getElmPosition: function(elm){
		var l = 0;
		var t = 0;
		while(elm){
			l += elm.offsetLeft;
			t += elm.offsetTop;
			elm = elm.offsetParent;
		}
		return {left: l, top: t};
	}
}
</script>
