$(function() {
    var result = new Vue({
        el: '#jp',
        data: {
            area: [],
            city: [],
            request:{
                page:1,
                limit:10,
                school_type:"",
                xingzhi_name:"",
                rank:"",
                nowcid:"",
                schoolname:""
            },
            count:"",
            lists:[],
            tuition: [{
                name: "60万円以下",
                id: "1"
            }, {
                name: "60-70万円",
                id: "2"
            }, {
                name: "70-80万円",
                id: "3"
            }, {
                name: "80万円以上",
                id: "4"
            }]
        },
        filters:{
            filterfee: function(val){
                if (val) {
                    val = parseFloat(val);
                    val = val.toFixed(2);
                }
                return val;
            }
        },
        methods: {
            getarea: function(_id) {
                var _data = {};
                if (_id) {
                    _data = {
                        id : _id
                    }
                }
                var _this = this;
                $.ajax({
                    url:"http://manage.com/getarea",
                    type:"post",
                    data:_data,
                    success:function(res){
                        if (res.status) {
                            if (!_id) {
                                _this.area = res.data;
                            }else{
                                _this.city = res.data;
                            }
                            _this.$nextTick(function(){
                                $("#city").select2({
                                    width: "300px",
                                })
                            })
                        }
                    }
                })
            },
            change: function(key,val){
                this.request[key] = val;
                this.request.page = 1;
                this.getdata();
            },
            getdata: function(){
                var _this = this;
                $.ajax({
                    url:"http://manage.com/getjapschool",
                    type:"get",
                    data: _this.request,
                    success:function(res){
                        if (res.code == 0) {
                            _this.lists = res.data;
                            _this.count = res.count
                            $("body, html").animate({
                                scrollTop: $(".choosepart").offset().top - 130
                            }, 200)
                            $("#jqPaginator").html("");
                            if (res.count > 0) {
                                $('#jqPaginator').jqPaginator({
                                    totalCounts: parseInt(res.count),
                                    pageSize: _this.request.limit,
                                    visiblePages: 7,
                                    currentPage: _this.request.page,
                                    first: "<a>首页</a>",
                                    last: "<a>末页</a>",
                                    prev: "<a>上一页</a>",
                                    page: "<a class='page'>{{page}}</a>",
                                    next: "<a>下一页</a>",
                                    onPageChange: function(num, type) {
                                        if (type == "change") {
                                            _this.request.page = num;
                                            _this.getdata();
                                        }
                                    }
                                })
                            }
                        }
                        console.log("xxx",res);
                    }
                })
            }
        },
        mounted: function() {
            var _this = this;
            this.getarea();
            this.getdata();
            $(".choose .item ").click(function(){
                $(this).addClass("active").siblings().removeClass("active");
            })
            $("#area,#city").select2({
                width: "300px",
            })
            $(".selectcity .city_item").eq(1).addClass("active")

            $("#area").on("select2:select",function(e){
                _this.change('nowcid',$("#area").val());
                _this.getarea($("#area").val());
            });
            $("#city").on("select2:select",function(e){
                _this.change('nowcid',$("#city").val());
            });
        }
    });
    var config = {
        reset: true,
        delay: 'always',
        mobile: true,
    }
    window.sr = new scrollReveal(config);
})