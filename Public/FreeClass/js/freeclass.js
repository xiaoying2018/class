$(function() {

    var freeclasss = new Vue({
        el: '#freeclasss',
        data: {
            lists:[],
            request:{
                page:1,
                limit_num:12,
                type: 'free',
                sort_by_people_num:'1'
            },
            lock: true
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
            },
            filterFace: function(val) {
                if (val != null) {
                    return val
                } else {
                    return '/Public/random/student/' + Math.floor(Math.random() * 10 + 1) + '.jpg'
                }
            }
        },
        methods: {
            condit: function(key,val){
                this.request.page = 1;
                this.lists = [];
                this.request[key] = val;
                this.getdata();
            },  
            getdata: function(){
                var _this = this;
                $.ajax({
                    url:"/api/opencourse",
                    type:"post",
                    data:_this.request,
                    success:function(_res){
                        if (_res.result) {
                            if (_this.request.page > _res.data.page_count) {
                                return false
                            }
                            _this.lists = _this.lists.concat(_res.data.open_course);
                            _this.request.page = _this.request.page + 1;
                            if (_res.data.open_course.length > 0) {
                                _this.lock = true;
                            }
                        } else {
                            _this.lock = false;
                        }
                    }
                })
            }
        },
        mounted: function() {
            var _this = this;
            this.getdata();
            $(".s_nav li").eq(3).addClass('active');
            $(".topcondit li,.paixu li,.typeul li").click(function(){
                $(this).addClass("active").siblings().removeClass("active");
            })
            $(window).scroll(function() {
                var scrollTop = parseInt($(this).scrollTop());
                var scrollHeight = $(document).height();
                var windowHeight = parseInt($(this).height());
                if (parseInt(scrollTop + windowHeight) >= parseInt(scrollHeight-200) && _this.lock) {
                    _this.lock = false;
                    _this.getdata();
                }
            });
        }
    })
});