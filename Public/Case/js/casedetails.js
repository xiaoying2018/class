$(function () {

	var app = new Vue({
        el: '#casedetails',
        data: {
            lock: true,
            choose: [
                {
                    name: "全部",
                    type: []
                }, {
                    name: "学生学历",
                    type: [{
                        name: "初中"
                    }, {
                        name: "高中"
                    }, {
                        name: "大学/大专在读"
                    }, {
                        name: "SGU英语项目"
                    }, {
                        name: "毕业工作"
                    }]
                }, {
                    name: "服务项目",
                    type: []
                }, {
                    name: "日语水平",
                    type: [{
                        name: "N1"
                    }, {
                        name: "N2"
                    }, {
                        name: "N3"
                    }, {
                        name: "N4"
                    }, {
                        name: "N5"
                    }, {
                        name: "0基础"
                    }]
                }, {
                    name: "录取年份",
                    type: [{
                        name: "2000-2012"
                    }, {
                        name: "2013"
                    }, {
                        name: "2014"
                    }, {
                        name: "2015"
                    }, {
                        name: "2016"
                    }, {
                        name: "2017"
                    }]
                }, {
                    name: "名校报考经验",
                    type: [{
                        name: "东京大学"
                    }, {
                        name: "京都大学"
                    }, {
                        name: "大阪大学"
                    }, {
                        name: "庆应大学"
                    }, {
                        name: "早稻田大学"
                    }, {
                        name: "更多"
                    }]
                }
            ],
            s_choose: [],
            caseInfo: '',
            query:{
                education: '',
                programcate: '',
                japan_language: '',
                receive_year: '',
                receive_college: '',
                c: ''
            },
            // requet: {
            //     PageIndex: 1
            // },
            // ,
            // temp: 12
        },
        methods: {
            tabChoose: function(item,event) {
                var _this = this;
                var el = event.target;

                this.s_choose = item;
                $('.in_ul li').removeClass('active');
                $(el).addClass("active").siblings().removeClass("active");

                _this.query.c = $(el).text();
                if(item.name == "全部") {
                     window.location.href = "/case"
                }

            },
            search: function (item,event) {
                console.log("search",item);
                var _this = this;

                var el = event.target;
                $('.in_ul li').removeClass('active');
                $(el).addClass("active").siblings().removeClass("active");
                // console.log(item.name,item.type);

                if ($(el).data('type') == "学生学历") {
                    _this.query.education= item.name;
                }else if ($(el).data('type') == "服务项目") {
                    _this.query.programcate= item.name;
                }else if ($(el).data('type') == "日语水平") {
                    _this.query.japan_language= item.name;
                }else if ($(el).data('type') == "录取年份") {
                    _this.query.receive_year= item.name;
                }else if ($(el).data('type') == "名校报考经验") {
                    _this.query.receive_college= item.name;
                }
                
                window.location.href = "/case?education="+_this.query.education+'&programcate='+_this.query.programcate+'&japan_language='+_this.query.japan_language+'&receive_year='+_this.query.receive_year+'&receive_college='+_this.query.receive_college+'&cat='+_this.query.c;
            },
            getData: function(id){
                var _this = this;
                console.log(_this.getQueryString("id"));
                $.ajax({
                    url:"http://crm.xiaoying.net?m=case&a=search",
                    type:"POST",
                    dataType:"json",
                    success:function(res){
                        if (res.status) {
                            for (var i = 0; i < res.data.list.length; i++) {

                                if(res.data.list[i].id == _this.getQueryString("id")) {

                                    if(res.data.list[i].headfile) {
                                        _this.caseInfo.head_file_path = res.data.list[i].headfile.file_path

                                    } else {
                                        _this.caseInfo.head_file_path = '';
                                    }
                                    _this.caseInfo= res.data.list[i];
                                    return false;
                                }    
                            }
                        }
                    }
                })
            },
            getallprogramcate: function () {
                var _this = this;
                //获取服务项目
                $.ajax({
                    url:"http://crm.xiaoying.net?m=case&a=getallprogramcate",
                    type:"POST",
                    dataType:"json",
                    success:function(res){
                        var obj = res;
                        // console.log('getallprogramcate',res);
                        var array = [];
                        for (var i = 0; i < _this.choose.length; i++) {
                            if(_this.choose[i].name == '服务项目') {
                                for(let i in obj) {
                                    var o = {}
                                    o.name = obj[i].name;
                                    
                                    array.push(o);
                                }
                                _this.choose[i].type = array;
                                return false
                            }   
                            
                        }
                    }
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
        mounted: function() {
            var _this = this;
            //获取服务项目
            _this.getallprogramcate();
            
            _this.getData(_this.getQueryString("id"));

        	// 多行溢出兼容IE
            var body=document.getElementsByTagName("body")[0];
            if(typeof body.style.WebkitAnimation!="undefined") {
                // alert("当前浏览器是Webkit内核！");
                // $('.item_li a h2').removeClass('ie');
            }else {
                // alert("当前浏览器不是Webkit内核！");
                $('.item_li a h2').addClass('ie');
            }
            //热门活动轮播初始化
        	var mySwiper = new Swiper('.swiper-container',{
			    pagination: '.pagination',
			    loop:true,
			    // autoplay: 3000,
			    autoplayDisableOnInteraction: false,
			    grabCursor: true,
			    paginationClickable: true
			})
		  	$('.arrow-left').on('click', function(e){
			    e.preventDefault()
			    mySwiper.swipePrev()
		    });
			$('.arrow-right').on('click', function(e){
			    e.preventDefault()
			    mySwiper.swipeNext()
			});



        }
    });
})