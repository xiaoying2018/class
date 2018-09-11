$(function() {

    var app = new Vue({
        el: '#openclass',
        data: {
            crm_domain:"",
            classdata: [],
            cate:[],
            request:{
                livecate:"",
                livecontent:"",
                page:"1",
                init:1,
                limit:5
            },
            livecontent:[],
            reco:[],
            reco_request:{
                is_reco:1,
                limit:2,
                page:1,
                order:"add_ts desc"
            },
            total:""
        },
        methods:{
            choose:function(_flag,_val){
                this.request.page = '1'
                if (_flag == 'livecontent') {
                    if (this.livecontent.indexOf(_val) == -1) {
                        this.livecontent.push(_val)
                    }else{
                        this.livecontent.splice(this.livecontent.indexOf(_val),1)
                    }

                    if (_val == '') {
                        this.livecontent = []
                    }
                    _val = this.livecontent;
                }
                this.classdata = [];
                this.request[_flag] = _val;
                this.getData();
            },
            getData:function(){
                var _this = this;
                if (Object.keys(this.cate).length > 0) {
                    delete this.request.init
                }
                $.ajax({
                    url:"/api/open",
                    type:"POST",
                    data:this.request,
                    datatype:"json",
                    success:function(res){
                        if (res.result) {
                            var _count = parseInt(res.count);
                            var currentPage = parseInt(res.params.page);
                            _this.crm_domain = res.crm_domain
                            if (_count > 0) {
                                $('#jqPaginator').jqPaginator({
                                    totalCounts: _count,
                                    pageSize:_this.request.limit,
                                    visiblePages: 7,
                                    currentPage: currentPage,
                                    first:"<a>首页</a>",
                                    last:"<a>末页</a>",
                                    prev:"<a>上一页</a>",
                                    page:"<a class='page'>{{page}}</a>",
                                    next:"<a>下一页</a>",
                                    onPageChange: function (num, type) {
                                        _this.request.page = num
                                        if (type == "change") {
                                            _this.getData();
                                        }
                                    }
                                })
                            }else{
                                $("#jqPaginator").html("")
                            }
                            _this.classdata = res.lists;
                            if (_this.cate.length == 0) {
                                _this.cate = res._init;
                            }
                        }
                    }
                })
            },
            getreco:function(){
                var _this = this;
                $.ajax({
                    url:"/api/open",
                    type:"POST",
                    data:this.reco_request,
                    datatype:"json",
                    success:function(res){
                        if (res.result) {
                            _this.crm_domain = res.crm_domain
                            _this.reco = res.lists
                            _this.total = res.count
                        }
                    }
                })
            },
            change:function(){
                var _page = this.reco_request.page;
                if (_page != parseInt(this.total/this.reco_request.limit)) {
                    this.reco_request.page = _page + 1
                }else{
                    this.reco_request.page = 1
                }
                this.getreco();
            },
        },
        mounted:function(){
            this.getData();
            this.getreco();

            $(document).on('click', '.chooseUl.a .item', function() {
                $(this).addClass("active").siblings().removeClass("active");
            })
            $(document).on('click', '.chooseUl.b .item', function() {
                $(".chooseUl.b li").eq(0).removeClass("active");
                if ($(this).hasClass("active")) {
                    $(this).removeClass("active");
                }else{
                    $(this).addClass("active");
                }
                if ($(this).text() == '全部') {
                    $(this).addClass("active").siblings().removeClass("active");
                }
            })
        }
    });      
});