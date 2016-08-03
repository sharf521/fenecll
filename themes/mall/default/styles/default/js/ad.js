 var time = 500;
    var h = 0;
    function addCount()
    {
        if(time>0)
        {
            time--;
            h = h+5;
        }
        else
        {
            return;
        }
        if(h>456)  //高度
        {
            return;
        }
        document.getElementById("ads").style.display = "";
        document.getElementById("ads").style.height = h+"px";
        setTimeout("addCount()",30); 
    }
    
    window.onload = function showAds()
    {
        addCount();
        setTimeout("noneAds()",10000); //停留时间自己适当调整
    }

    var T = 456;
    var N = 456; //高度
    function noneAds()
    {
        if(T>0)
        {
            T--;
            N = N-5;
        }
        else
        {
            return;
        }
		if (N<87)
		{
		document.getElementById("adimg").style.display = "none";
		document.getElementById("adimg2").style.display = "block";
			
		return;
		
		}
        if(N<0)
        {
            document.getElementById("ads").style.display = "none";
            return;
        }
        
        document.getElementById("ads").style.height = N+"px";
        setTimeout("noneAds()",30); 
    }