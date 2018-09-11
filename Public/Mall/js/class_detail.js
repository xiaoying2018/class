$(function() {
    var mallindex = new Vue({
        el: '#malldetail',
        data: {
            detail: {},
            temp:[],
            tuijian:[],
            type:""
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
            huanyihuan: function(){
                this.tuijian = getRandomArrayElements(this.temp, 2);
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
                        	}
                            _this.detail = res.data;
                        } else {
                            alert(res.msg)
                        }
                    }
                })
            },
            play: function(n){
                if (n == null) {
                    $("#video_part").hide();
                    alert('没有视频')
                }else{
                    n = 'http://'+n;
                    var _html = '<div class="inner"><img src="../../public/common/map/close.png" class="close"><video width="100%;" controls height="100%" src="'+n+'" autoplay="autoplay"></video></div><div class="coverBg"></div>';
                    $("#video_part").html(_html);
                    $("#video_part").show();
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
                var _flag = $(this).attr("data-flag");
                $(this).addClass("active").siblings().removeClass("active");
                $(".tabcontent").hide();
                $(".tabcontent."+_flag).show();

            })
            $(document).on('click', '#video_part .close', function() {
                $("#video_part").html("").hide();
            })
        }
    })

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