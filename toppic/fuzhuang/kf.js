$(document).ready(function(){
	//��ʼ�����Ƿ���DIV�������ڹ���
	//0 ��ʾ����; 1 ��ʾ������;
	var popupStatus = 0;
	//ʹ��Jquery���ص��� 
	function loadPopup(){   
	//���ڿ�����־popupStatusΪ0������¼���  
	if(popupStatus==0){   
		$("#backgroundPopup").css({   
			"opacity": "0.7"  
		});   
		$("#backgroundPopup").fadeIn("slow");   
		$("#popupContact").fadeIn("slow");   
		popupStatus = 1;   
		}   
	}  
	//ʹ��Jqueryȥ������Ч�� 
	function disablePopup(){   
	//���ڿ�����־popupStatusΪ1�������ȥ��
		if(popupStatus==1){   
				$("#backgroundPopup").fadeOut("slow");   
				$("#popupContact").fadeOut("slow");   
				popupStatus = 0;   
			}   
	} 
	//���������ڶ�λ����Ļ������
	function centerPopup(){   
	//��ȡϵͳ����
		var windowWidth = document.documentElement.clientWidth;   
		var windowHeight = document.documentElement.clientHeight;   
		var popupHeight = $("#popupContact").height();   
		var popupWidth = $("#popupContact").width();   
		//��������   
		$("#popupContact").css({   
			"position": "absolute",   
			"top": windowHeight/2-popupHeight/2,   
			"left": windowWidth/2-popupWidth/2   
		});   
		//���´������IE6����Ч
		  
		$("#backgroundPopup").css({   
			"height": windowHeight   
		});   
	}
	
	//�򿪵�������   
	//��ť����¼�!
	$("#button").click(function(){   
		//���ú������д���
		centerPopup();   
		//���ú������ش���
		loadPopup();   
	});
	//�رյ�������   
	//���"X"���������¼�
	$("#popupContactClose").click(function(){   
			disablePopup();   
	});   
	//����������ⱳ���������Ĺرմ����¼�!
	$("#backgroundPopup").click(function(){   
		disablePopup();   
	});   
	//���̰���ESCʱ�رմ���!
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
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=2650956634&Site=��ǰ��ѯ1&Menu=yes\">��ǰ��ѯ1<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("        <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=2629654231&Site=��ǰ��ѯ2&Menu=yes\">��ǰ��ѯ2<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("		 <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=2511932699&Site=������ѯ1&Menu=yes\">������ѯ1<\/a><\/SPAN><\/TD><\/TR><TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=229577061&Site=������ѯ2&Menu=yes\">������ѯ2<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("        <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=2353137061&Site=Ͷ�߷���1&Menu=yes\">Ͷ�߷���1<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("		 <TR>");
document.writeln("          <TD height=30 align=middle class=\"kfqq\"><SPAN><a href=\"tencent://message/?uin=1354958767&Site=Ͷ�߷���2&Menu=yes\">Ͷ�߷���2<\/a><\/SPAN><\/TD><\/TR>");
document.writeln("        <\/TBODY><\/TABLE><\/TD><\/TR>");
document.writeln("  <TR>");
document.writeln("    <TD class=down_kefu vAlign=top><\/TD><\/TR><\/TBODY><\/TABLE>");
document.writeln("<DIV class=Obtn><\/DIV><\/DIV>");

//��ʾ����
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
//24Сʱ��ʾһ��							
function goad(){
var Then = new Date() 
Then.setTime(Then.getTime() + 24*60*60*1000)
//Then.setTime(Then.getTime() + 0)//����ʱʹ��
var cookieString = new String(document.cookie)
var cookieHeader = "Cookie1=" 
var beginPosition = cookieString.indexOf(cookieHeader)
if (beginPosition != -1){ 
} else 
{ document.cookie = "Cookie1=Filter;expires="+ Then.toGMTString() 
window.onload=function() 
  {
    document.getElementById("button").click();//�Զ�click 
 }
}
}
//goad();	

function tishi(){
	//document.getElementById("button").click();
}			