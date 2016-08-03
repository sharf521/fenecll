$(document).ready(function(){
	//初始化：是否开启DIV弹出窗口功能
	//0 表示开启; 1 表示不开启;
	var popupStatus = 0;
	//使用Jquery加载弹窗 
	function loadPopup(){   
	//仅在开启标志popupStatus为0的情况下加载  
	if(popupStatus==0){   
		$("#backgroundPopup").css({   
			"opacity": "0.7"  
		});   
		$("#backgroundPopup").fadeIn("slow");   
		$("#popupContact").fadeIn("slow");   
		popupStatus = 1;   
		}   
	}  
	//使用Jquery去除弹窗效果 
	function disablePopup(){   
	//仅在开启标志popupStatus为1的情况下去除
		if(popupStatus==1){   
				$("#backgroundPopup").fadeOut("slow");   
				$("#popupContact").fadeOut("slow");   
				popupStatus = 0;   
			}   
	} 
	//将弹出窗口定位在屏幕的中央
	function centerPopup(){   
	//获取系统变量
		var windowWidth = document.documentElement.clientWidth;   
		var windowHeight = document.documentElement.clientHeight;   
		var popupHeight = $("#popupContact").height();   
		var popupWidth = $("#popupContact").width();   
		//居中设置   
		$("#popupContact").css({   
			"position": "absolute",   
			"top": windowHeight/2-popupHeight/2,   
			"left": windowWidth/2-popupWidth/2   
		});   
		//以下代码仅在IE6下有效
		  
		$("#backgroundPopup").css({   
			"height": windowHeight   
		});   
	}
	
	//打开弹出窗口   
	//按钮点击事件!
	$("#button").click(function(){   
		//调用函数居中窗口
		centerPopup();   
		//调用函数加载窗口
		loadPopup();   
	});
	//关闭弹出窗口   
	//点击"X"所触发的事件
	$("#popupContactClose").click(function(){   
			disablePopup();   
	});   
	//点击窗口以外背景所触发的关闭窗口事件!
	$("#backgroundPopup").click(function(){   
		disablePopup();   
	});   
	//键盘按下ESC时关闭窗口!
	$(document).keypress(function(e){   
		if(e.keyCode==27 && popupStatus==1){   
			disablePopup();   
		}   
	});  


});
document.writeln("<style type=\"text\/css\">");
document.writeln("#xixi .main_head {");
document.writeln("	BACKGROUND: url(/images\/img3-5_2.png) no-repeat");
document.writeln("}");
document.writeln("#xixi .info {");
document.writeln("	PADDING-BOTTOM: 10px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; BACKGROUND: url(/images\/img3-5_3.png) repeat-y;PADDING-TOP: 5px");
document.writeln("}");
document.writeln("#xixi .down_kefu {");
document.writeln("	WIDTH: 157px; BACKGROUND: url(/images\/img3-5_4.png) no-repeat;_FILTER: progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\"/images\/img3-5_4.png\",sizingMethod=\'crop\'); HEIGHT: 8px");
document.writeln("}");
document.writeln("#xixi .Obtn {");
document.writeln("	MARGIN-TOP: 40px; WIDTH: 32px; BACKGROUND: url(/images\/img3-5_1.png) no-repeat; FLOAT: left; HEIGHT: 139px; MARGIN-LEFT: -5px;");
document.writeln("}");
document.writeln("#xixi .qqtable SPAN {");
document.writeln("	PADDING-BOTTOM: 5px; LINE-HEIGHT: 20px; PADDING-LEFT: 0px; WIDTH: 100px; PADDING-RIGHT: 0px; COLOR: #ff6600; FONT-SIZE: 13px; FONT-WEIGHT: bold; PADDING-TOP: 5px");
document.writeln("}");
document.writeln("#xixi .qqtable A {");
document.writeln("	TEXT-DECORATION: none");
document.writeln("}");
document.writeln("#xixi .qqtable A:hover {");
document.writeln("	TEXT-DECORATION: none");
document.writeln("}");
document.writeln("#xixi .qun {");
document.writeln("	BORDER-BOTTOM: #ffd2bf 1px solid; BORDER-LEFT: #ffd2bf 1px solid; PADDING-BOTTOM: 5px; LINE-HEIGHT: 20px; BACKGROUND-COLOR: #ffffff; PADDING-LEFT: 0px; WIDTH: 100px; PADDING-RIGHT: 0px; FONT-SIZE: 12px; BORDER-TOP: #ffd2bf 1px solid; BORDER-RIGHT: #ffd2bf 1px solid; PADDING-TOP: 5px");
document.writeln("}");
document.writeln("#xixi .qun SPAN {");
document.writeln("	COLOR: #ff6600; FONT-SIZE: 13px; FONT-WEIGHT: bold");
document.writeln("}");
document.writeln("#xixi{position:absolute; z-index:9999; top:250px; left:-152px;}");
document.writeln("#xixi .kfqq{padding-left:18px}");
document.writeln("#xixi .kfqq span{background:url(/images\/img3-5-btn1.gif) no-repeat left; width:120px; height:25px; line-height:25px; display:block; text-align:center; padding-left:8px}");
document.writeln("#xixi .kfqq2{padding-left:11px}");
document.writeln("#xixi .kfqq2 span{background:url(/images\/iimg3-5-btn1.gif) no-repeat left; width:120px; height:25px; line-height:25px; display:block; text-align:center;}");
document.writeln("<\/style>");
document.writeln("<DIV id=\"xixi\" onmouseover=toBig() onmouseout=toSmall() style=\"position:absolute; z-index:9999; top:250px; left:-152px;\">");
document.writeln("<TABLE style=\"FLOAT: left\" border=0 cellSpacing=0 cellPadding=0 width=157>");
document.writeln("  <TBODY>");
document.writeln("  <TR>");
document.writeln("    <TD class=main_head height=39 vAlign=top>&nbsp;<\/TD><\/TR>");
document.writeln("  <TR>");
document.writeln("    <TD class=info vAlign=top>");
document.writeln("      <TABLE class=qqtable border=0 cellSpacing=0 cellPadding=0 width=130 ");
document.writeln("      align=left>");
document.writeln("        <TBODY>");
document.writeln("        <TR>");
document.writeln("          <TD height=5><\/TD><\/TR>");
document.writeln("        <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=2650956634&Site=售前咨询1&Menu=yes\">售前咨询1<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("        <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=2629654231&Site=售前咨询2&Menu=yes\">售前咨询2<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("		 <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=2511932699&Site=技术咨询1&Menu=yes\">技术咨询1<\/a><\/SPAN><\/TD><\/TR><TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=229577061&Site=技术咨询2&Menu=yes\">技术咨询2<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("        <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=2353137061&Site=投诉反馈1&Menu=yes\">投诉反馈1<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("		 <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=1354958767&Site=投诉反馈2&Menu=yes\">投诉反馈2<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("        <\/TBODY><\/TABLE><\/TD><\/TR>");
document.writeln("  <TR>");
document.writeln("    <TD class=down_kefu vAlign=top><\/TD><\/TR><\/TBODY><\/TABLE>");
document.writeln("<DIV class=Obtn><\/DIV><\/DIV>");

//提示内容
document.writeln("    <div id=\"button\"><\/div>  ");
document.writeln("    <div id=\"popupContact\">  ");
document.writeln("        <p id=\"contactArea\">");
document.writeln("<img src=\"/down/images/fxb.png\" width=\"408\" height=\"346\" usemap=\"#Map\" border=\"0\" \/>");
document.writeln("<map name=\"Map\" id=\"Map\">");
document.writeln("  <area shape=\"rect\" coords=\"7,272,251,332\" href=\"/down/setup.exe\" \/>");
document.writeln("  <area shape=\"rect\" coords=\"273,304,383,332\" href=\"/down/\" \/>");
document.writeln("<\/map>");
document.writeln("        <\/p>  ");
document.writeln("    <\/div>  ");
document.writeln("    <div id=\"backgroundPopup\"><\/div>");

		kfjs=function (id,_top,_left){
		var me=id.charAt?document.getElementById(id):id, d1=document.body, d2=document.documentElement;
		d1.style.height=d2.style.height='100%';me.style.top=_top?_top+'px':0;me.style.left=_left+"px";//[(_left>0?'left':'left')]=_left?Math.abs(_left)+'px':0;
		me.style.position='absolute';
		setInterval(function (){me.style.top=parseInt(me.style.top)+(Math.max(d1.scrollTop,d2.scrollTop)+_top-parseInt(me.style.top))*0.1+'px';},10+parseInt(Math.random()*20));
		return arguments.callee;
		};
		window.onload=function (){
		kfjs
		('xixi',250,-152)
		}

			lastScrollY=0; 
			
			var InterTime = 1;
			var maxWidth=-1;
			var minWidth=-152;
			var numInter = 8;
			
			var BigInter ;
			var SmallInter ;
			
			var o =  document.getElementById("xixi");
				var i = parseInt(o.style.left);
				function Big()
				{
					if(parseInt(o.style.left)<maxWidth)
					{
						i = parseInt(o.style.left);
						i += numInter;	
						o.style.left=i+"px";	
						if(i==maxWidth)
							clearInterval(BigInter);
					}
				}
				function toBig()
				{
					clearInterval(SmallInter);
					clearInterval(BigInter);
						BigInter = setInterval(Big,InterTime);
				}
				function Small()
				{
					if(parseInt(o.style.left)>minWidth)
					{
						i = parseInt(o.style.left);
						i -= numInter;
						o.style.left=i+"px";
						
						if(i==minWidth)
							clearInterval(SmallInter);
					}
				}
				function toSmall()
				{
					clearInterval(SmallInter);
					clearInterval(BigInter);
					SmallInter = setInterval(Small,InterTime);
					
				}
//24小时提示一次							
function goad(){
var Then = new Date() 
Then.setTime(Then.getTime() + 24*60*60*1000)
//Then.setTime(Then.getTime() + 0)//调试时使用
var cookieString = new String(document.cookie)
var cookieHeader = "Cookie1=" 
var beginPosition = cookieString.indexOf(cookieHeader)
if (beginPosition != -1){ 
} else 
{ document.cookie = "Cookie1=Filter;expires="+ Then.toGMTString() 
window.onload=function() 
  {
    document.getElementById("button").click();//自动click 
 }
}
}
//goad();	

function tishi(){
	//document.getElementById("button").click();
}			