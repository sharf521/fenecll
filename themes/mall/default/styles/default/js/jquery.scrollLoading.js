(function($) {  
    $.fn.scrollLoading = function(options) {  
        var defaults = {  
            attr: "data-url"  
        };  
        var params = $.extend({}, defaults, options || {});  
        params.cache = [];  
        $(this).each(function() {  
            var node = this.nodeName.toLowerCase(), url = $(this).attr(params["attr"]);  
            if (!url) { return; }  
            //����   
            var data = {  
                obj: $(this),  
                tag: node,  
                url: url  
            };  
            params.cache.push(data);  
        });  
  
        //��̬��ʾ����   
        var loading = function() {  
            var st = $(window).scrollTop(), sth = st + $(window).height();  
            $.each(params.cache, function(i, data) {  
                var o = data.obj, tag = data.tag, url = data.url;  
                if (o) {  
                    post = o.offset().top; posb = post + o.height();  
                    if ((post > st && post < sth) || (posb > st && posb < sth)) {  
                        //�������������   
                        if (tag === "img") {  
                            //ͼƬ���ı�src   
                            o.load(function() {  
                                o.removeClass("wimg");  
                            });  
                            o.attr("src", url);  
                        } else {  
                            o.load(url);  
                        }  
                        data.obj = null;  
                    }  
                }  
            });  
            return false;  
        };  
  
        //�¼�����   
        //������ϼ�ִ��   
        loading();  
        //����ִ��   
        $(window).bind("scroll", loading);  
    };  
})(jQuery);  
