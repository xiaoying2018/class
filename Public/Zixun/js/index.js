// const router = new VueRouter({
//   routes 
// })


var app = new Vue({
	el: '#zixun',
	data () {
		return {
			nav: 'news',
			newsNav: [],
			newsSecNav: [],
			newsList: [],
			request: {
				page: 1,
				limit: 2,
				cateidSecNav: '',
				cateid: ''
			},
			newsTotal: 0 ,//资讯总数,
			hotActivities: [
				{
					id: 1,
					imgSrc: "../../Public/Zixun/img/hotActivities/zhanhui2018.jpg",
					url: "http://www.xiaoying.net/zhanhui2018/"
				},
				{
					id: 2,
					imgSrc: "../../Public/Zixun/img/hotActivities/evaluation.jpg",
					url: "http://www.xiaoying.net/evaluation?q=web"
				},
				{
					id: 3,
					imgSrc: "../../Public/Zixun/img/hotActivities/duyan.jpg",
					url: "http://www.xiaoying.net/newdy/"
				},
				{
					id: 4,
					imgSrc: "../../Public/Zixun/img/hotActivities/yuyan.jpg",
					url: "http://www.xiaoying.net/yuyan/"
				}
			],
			parentsCare: [
				{
					id: 1,
					title: "为何选择小莺",
					imgSrc: "../../Public/Zixun/img/why_choose_icon.png"
				},
				{
					id: 2,
					title: "赴日留学费用",
					imgSrc: "../../Public/Zixun/img/liuxue_free_icon.png"
				},
				{
					id: 3,
					title: "在日如何工作",
					imgSrc: "../../Public/Zixun/img/how_work_icon.png"
				},
				{
					id: 4,
					title: "日本升学攻略",
					imgSrc: "../../Public/Zixun/img/highter_school_icon.png"
				},
				{
					id: 5,
					title: "赴日调研要求",
					imgSrc: "../../Public/Zixun/img/survey_require_icon.png"
				},
				{
					id: 6,
					title: "语言学校要求",
					imgSrc: "../../Public/Zixun/img/language_school_icon.png"
				},
				{
					id: 7,
					title: "日常生活常识",
					imgSrc: "../../Public/Zixun/img/life_icon.png"
				},
				{
					id: 8,
					title: "出国行前准备",
					imgSrc: "../../Public/Zixun/img/befor_prepare_icon.png"
				}

			]
		}
	},
	// router,
	methods: {
		getNewsNavData () {
			var _this = this;
			console.log('localStorage.cateid',localStorage.cateid,localStorage.cateidSec);

			$.ajax({
				url: "http://manages.com/article/catesearch",
				type:"GET",
				success: function(res) {
					_this.newsNav = res;
					_this.$nextTick(function () {
						if (localStorage.cateid) {
							$('.newsNavUl li').each(function () {
								if($(this).data('cateid') == localStorage.cateid) {
									$(this).addClass('active').siblings().removeClass('active');
								}
							})
							_this.request.cateidSecNav = localStorage.cateid;
							_this.getSecNavData();
						} else if (localStorage.cateid == '') {
							$('.newsNavUl li').eq(0).addClass('active').siblings().removeClass('active');
							$('.secNavList li').eq(0).addClass('active').siblings().removeClass('active');
						}
					})
				}
			})
		},
		getSecNavData () {
			var _this = this;
			$.ajax({
				url: "http://manages.com/article/catesearch?cateid="+ _this.request.cateidSecNav,
				type:"GET",
				success: function(res) {
					_this.newsSecNav= res;
					_this.$nextTick(function () {
						if (localStorage.cateidSec) {
							$('.secNavList li').each(function () {
								if($(this).data('cateid') == localStorage.cateidSec) {
									$(this).addClass('active').siblings().removeClass('active');
								}
							})
							_this.request.cateid = localStorage.cateidSec;

						} else if(localStorage.cateidSec == '') {
							$('.secNavList li').eq(0).addClass('active').siblings().removeClass('active');
							_this.request.cateid = localStorage.cateid;
						}
						_this.getNewListData();
					})
				}
			})
		},
		getNewListData () {
			var _this = this;
			$.ajax({
				url: "http://manages.com/article/contentssearch?page="+_this.request.page + "&limit=" +_this.request.limit+"&cateid="+ _this.request.cateid,
				type:"GET",
				success: function(res) {
					var array = res.data;
					// 'http://xiaoying.net'+item.picture
					for (var i = 0; i < array.length; i++) {
						if(array[i].picture.indexOf('http') > -1) {
							array[i].picture = array[i].picture;
						}else if(array[i].picture == '') {
							array[i].picture = '../../../static/images/news/img.jpg'
						} else {
							array[i].picture = 'http://xiaoying.net'+ array[i].picture;
						}
						array[i].publishedtime = _this.timestampToTime(array[i].publishedtime);
					}
					_this.newsList = array;

					_this.newsTotal= res.count;
					_this.pageCount =  _this.newsTotal%_this.request.limit?(parseInt(_this.newsTotal/_this.request.limit) + 1 ):parseInt(_this.newsTotal/_this.request.limit);

					$('.pageBox').pagination({
						pageCount: _this.pageCount,
						showData: _this.request.limit,
						current:  _this.request.page,
						homePage: '首页',
						endPage: '末页',
						isHide: true,
						count: 2,
						activeCls: 'active',
						coping: true,
						callback: function (api) {
							var getCurrent = api.getCurrent();
							var total = api.getPageCount();
							if (getCurrent>=1 && getCurrent <= total) {
								localStorage.page = api.getCurrent();
								_this.request.page = localStorage.page;
								console.log(_this.request.page);
								_this.getNewListData();
							}
						}
					});
				}
			})
		},
		tabNewsNav (event) {
			var _this= this;
			var el =  event.currentTarget;

			$(el).addClass('active').siblings().removeClass('active');
			_this.request.page = 1;
			localStorage.page = 1;
			if ($(el).data('cateid') != 'newsNavAll') {
				_this.request.cateidSecNav = $(el).data('cateid');

				_this.request.cateid = $(el).data('cateid');
				$('.secNavList li').eq(0).addClass('active').siblings().removeClass('active');

				localStorage.cateidSec = '';
				localStorage.cateid = $(el).data('cateid');

				_this.getSecNavData();


			} else {
				
				_this.request.cateid = '';
				localStorage.cateid = '';
				_this.newsSecNav = '';
				_this.getNewListData();
			}
			
		},
		tabsecNav(event) {
			var _this= this;
			var el =  event.currentTarget;
			$(el).addClass('active').siblings().removeClass('active');
			_this.request.page = 1;
			localStorage.page = 1;
			if($(el).data('cateid') != 'secNavAll') {
				_this.request.cateid = $(el).data('cateid');
				localStorage.cateidSec = $(el).data('cateid');
				_this.getNewListData();
			} else {
				//点击全部
				console.log(2)
				_this.request.cateid = localStorage.cateid;
				localStorage.cateidSec = '';
				_this.getNewListData();
			}

		},
		timestampToTime (timestamp) {
	        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
	        var Y = date.getFullYear() + '-';
	        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
	        var D = date.getDate() + ' ';
	        var h = date.getHours() + ':';
	        var m = date.getMinutes() + ':';
	        var s = date.getSeconds();
	        return Y+M+D;
	        // return Y+M+D+h+m+s;
	    }
	},
	mounted () {
		var _this = this;
		_this.request.page = localStorage.page?localStorage.page:1
		//初始化一级分类
		_this.getNewsNavData();
		// 初始化课程列表
		_this.getNewListData();


		//热门活动轮播初始化
		var mySwiper = new Swiper('.swiper-container',{
            // pagination: '.pagination',
            loop:true,
            autoplay: 5000,
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
	},
	beforeRouteLeave (to, from, next) {
	    // 导航离开该组件的对应路由时调用
	    // 可以访问组件实例 `this`
	    localStorage.cateid = '';
	    localStorage.cateidSec = '';
	    localStorage.page = '';
	    next();
	}
})
