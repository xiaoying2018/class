$(function(){
	var app = new Vue({
        el: '#openclassDetail',
        data: {
            crm_domain:"",
            classdata: [],
            request:{
                id:""
            },
            reco:[],
            reco_request:{
                is_reco:1,
                limit:2,
                page:1,
                order:"add_ts desc"
            },
            total:"",
        },
        methods:{
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
            getData:function(_id){
                var _this = this;
                $.ajax({
                    url:"/api/open/detail",
                    type:"POST",
                    data:this.request,
                    datatype:"json",
                    success:function(res){
                        if (res.result) {
                            if (res.info.playback_addr === 'NEED_AUTH') {
                                utily.setStore('xy_logined_href',location.href)
                                res.info.playback_addr = "/user/#/login"
                            }
                            res.info.t_img_url = res.crm_domain + res.info.t_img_url
                            _this.classdata = res.info;

                            var _body = $(".tabcontent").width();
                            // _this.$nextTick(function(){
                            //     $(".tabDv img").each(function(){
                            //         if ($(this).width() > _body) {
                            //             $(this).attr("width", "100%").attr("height", "auto");
                            //         }
                            //     })
                            // })
                        }
                    }
                })
            },
            addBowser:function(){
                var _json = {
                    id:this.request.id
                }
                $.ajax({
                    url:"/api/open/snumIncre",
                    type:"POST",
                    data:_json,
                    datatype:"json",
                    success:function(res){}
                })
            },
            getQueryString: function(name, needdecoed) {
	            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	            var lh = window.location.search;
	            if (needdecoed) {
	                lh = decodeURI(window.location.search)
	            }
	            var r = lh.substr(1).match(reg);
	            if (r != null) return unescape(r[2]);
	            return null;
	        }
        },
        mounted:function(){
            this.request.id = this.getQueryString('id')
            this.getreco();
            this.getData();
            this.addBowser();
            $(".tabHeader li").click(function(){
                var _index = $(this).index();
                $(this).addClass("active").siblings().removeClass("active");
                $(".tabDv .tabcontent").eq(_index).addClass("active").siblings().removeClass("active");
            })
        }
    });
})