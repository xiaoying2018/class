$(function() {
    var mallindex = new Vue({
        el: '#malldetail',
        data: {
            detail: {},
            temp:[],
            tuijian:[],
            type:"",
            hotindex:0
        },
        filters: {
            filtertime: function(val){
                if (val) {
                    val = val.substring(0,10)
                    return val;
                }else{
                    return '2018-09-01';
                }
            },
            filtername: function(val){
                if (val) {
                    var s = val.split('@')[0];
                    return s;
                }else{
                    return '小莺出国官方教务';
                }
            },
            filterprice: function(val) {
                var s = val.split('.')[0];
                return s;
            }
        },
        methods: {
            sscroll:function(){
                $("body, html").animate({
                    scrollTop: $(".tabDv").offset().top-140
                }, 600)
                $("[data-flag='tab2']").click();
            },
            huanyihuan: function(){
                this.tuijian = [];
                if (this.hotindex > 9) {
                    this.hotindex = 0;
                }
                var _t = this.hotindex + 2; 
                for(var i = this.hotindex; i < _t; i++){
                    this.tuijian.push(this.temp[i])
                }
                this.hotindex = _t;
                // this.tuijian = getRandomArrayElements(this.temp, 2);
            },
            gettuijian: function(){
                var _this = this;
                $.ajax({
                    url:"/api/tuijian_banji",
                    type:"post",
                    success:function(res){
                        _this.temp = res.data;
                        _this.huanyihuan();
                    }
                })
            },
            caseData: function(_url,_id) {
                var _this = this;
                $.ajax({
                    url: _url,
                    type: "post",
                    data: { id: _id },
                    success: function(res) {
                        if (res.result) {
                        	if (res.data.detail) {
                        		res.data.detail = escapeStringHTML(res.data.detail);
                                for(var i = 0; i < res.data.sections.length; i++){
                                    res.data.sections[i].zhibo_path = '';
                                    if (res.data.sections[i].hasOwnProperty('live_path') && res.data.sections[i].live_path.length > 0 && res.data.sections[i].live_path[0].length > 0) {
                                        res.data.sections[i].zhibo_path = res.data.sections[i].live_path[0][0].playpath;
                                    }
                                }
                        	}
                            _this.detail = res.data;
                        } else {
                            alert(res.msg)
                        }
                    }
                })
            },
            play: function(n){
                if (utily.getStore('xy_nickname')) {
                    if (n == null) {
                        $("#video_part").hide();
                        alert('没有视频')
                    }
                    else if(n.indexOf('http://') > -1){
                        var _html = '<div class="inner"><img src="../../Public/Common/map/close.png" class="close"><iframe frameborder="0" width="100%;" height="100%" src="'+n+'" ></iframe></div><div class="coverBg"></div>';
                        $("#video_part").html(_html);
                        $("#video_part").show();
                    }else{
                        n = 'http://'+n;
                        var _html = '<div class="inner"><img src="../../Public/Common/map/close.png" class="close"><video width="100%;" controls height="100%" src="'+n+'" autoplay="autoplay"></video></div><div class="coverBg"></div>';
                        $("#video_part").html(_html);
                        $("#video_part").show();
                    }
                }else{
                    utily.setStore('xy_logined_href',location.href)
                    window.location.href = '/user/#/login?a=login';
                }
            }
        },
        mounted: function() {
            this.type = getQueryString('type');
            if (this.type == 'class') {
                this.caseData('/api/banji_detail',getQueryString('id'));
            }else{
                this.caseData('/api/course_detail',getQueryString('id'));
            }
            this.gettuijian();
            $(document).on('click', '.tabHeader li', function() {
                $(this).addClass("active").siblings().removeClass("active");
                var _flag = $(this).attr("data-flag");
                $("body, html").animate({
                    scrollTop: $(".tabcontent."+_flag).offset().top - 150
                }, 600)
            })
            $(document).on('click', '#video_part .close', function() {
                $("#video_part").html("").hide();
            })
            fixDiv($(".tabHeader"), "fixed_pc", 450);

            // $(window).bind("scroll", function() {
            //     var e = $(window).scrollTop();
            //     e < Math.floor($("#tab1").offset().top) ? ($(".tabHeader li:eq(0)").removeClass("active"),
            //     $(".tabHeader li:eq(0)").addClass("active")) : e < Math.floor($("#tab1").offset().top) ? ($(".tabHeader li").removeClass("active"),
            //     $(".tabHeader li:eq(1)").addClass("active")) : e < Math.floor($("#tab2").offset().top) ? ($(".tabHeader li").removeClass("active"),
            //     $(".tabHeader li:eq(2)").addClass("active")) : e < Math.floor($("#tab3").offset().top) + 475 && ($(".tabHeader li").removeClass("active"),
               
            // })
        }
    })
    function fixDiv(t, e, o) {
        var n = 0;
        $(window).scroll(function() {
            n = document.documentElement.scrollTop > 0 ? document.documentElement.scrollTop : document.body.scrollTop,
            n >= o ? t.addClass(e) : o > n && t.removeClass(e)
        })
    }
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }


    function escapeStringHTML(str) {
        str = str.replace(/&lt;/g, '<');
        str = str.replace(/&gt;/g, '>');
        str = str.replace(/&amp;/g, '"');
        str = str.replace(/&quot;/g, '"');
        str = str.replace(/&#039;/g, "'");
        return str;
    }

    function getRandomArrayElements(arr, count) {
        var shuffled = arr.slice(0), i = arr.length, min = i - count, temp, index;
        while (i-- > min) {
            index = Math.floor((i + 1) * Math.random());
            temp = shuffled[index];
            shuffled[index] = shuffled[i];
            shuffled[i] = temp;
        }
        return shuffled.slice(min);
    }
});