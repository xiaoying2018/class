$(function() {
    var result = new Vue({
        el: '#kr',
        data: {
            area: [],
            city: [],
            request:{
                page:1,
                limit:10,
                schoolname:"",
                address:""
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
            change: function(key,val){
                this.request[key] = val;
                this.request.page = 1;
                this.getdata();
            },
            getdata: function(){
                var _this = this;
                $.ajax({
                    url:"http://manage.com/getkoreaschool",
                    type:"get",
                    data: _this.request,
                    success:function(res){
                        if (res.status) {
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
                    }
                })
            }
        },
        mounted: function() {
            var _this = this;
            this.getdata();
            $(".choose .item ").click(function(){
                $(this).addClass("active").siblings().removeClass("active");
            })
            $("#area").select2({
                width: "300px",
            })
            $(".selectcity .city_item").eq(2).addClass("active")

            $("#area").on("select2:select",function(e){
                _this.change('address',$("#area").val());
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