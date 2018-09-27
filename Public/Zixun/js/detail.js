var app = new Vue({
	el: '#zixundetail',
	data () {
		return {
			nav: 'news',
			newsNav: [],
			newsDetailInfo: {},
			vote: 0, //点赞数
			reads: 0, //阅读量
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
	
	methods: {
		getNewsNavData () {
			var _this = this;
			console.log('localStorage.cateid',localStorage.cateid,localStorage.cateidSec);

			$.ajax({
				url: "http://manages.com/article/catesearch",
				type:"GET",
				success: function(res) {
					_this.newsNav = res;
				}
			})
		},
		clickNewsNav(event) {
			var _this= this;
			var el =  event.currentTarget;
			if ($(el).data('cateid') != 'newsNavAll') {
				localStorage.cateid = $(el).data('cateid');
			} else {
				localStorage.cateid = '';
			}
			localStorage.cateidSec = '';
		},
		
		getData() {
			var _this = this;
			var id = _this.getQueryString('id');
			console.log(id)
			$.ajax({
				url:"http://manages.com/article/detailsearch?id=" + id,
				type:"GET",
				success: function (res) {
					if (res.status) {
						var obj = res.data;
						obj.source = obj.source?obj.source:'网络';
						obj.publishedtime = _this.timestampToTime(obj.publishedtime);
						_this.newsDetailInfo = obj;
						_this.vote = obj.upsnum;
						_this.reads = obj.hits;
					}
				}
			})
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
	    },
	    clickThumb () {
	    	var _this = this;
			var id = _this.getQueryString('id');
	    	$.ajax({
				url:"http://manages.com/vote?id=" + id,
				type:"GET",
				success: function (res) {
					if (res.status) {
						_this.vote++
					}else {
						console.log(res.msg);
					}
				},
				error: function (res) {
					console.log(res.msg);
				}
			})
	    },
	    setReads() {
	    	var _this = this;
			var id = _this.getQueryString('id');
			$.ajax({
				url:"http://manages.com/reads",
				type:"POST",
				data: {
					id: id
				},
				dataType: 'json',
				success: function (res) {
					if (res.status) {
						_this.reads++
					}else {
						console.log(res.msg);
					}
				},
				error: function (res) {
					console.log(res.msg);
				}
			})
	    },
	    getQueryString (name, needdecoed) {
		    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
		    var lh = window.location.search;
		    if (needdecoed) {
		        lh = decodeURI(window.location.search)
		    }
		    var r = lh.substr(1).match(reg);
		    if (r != null) return unescape(r[2]);
		    return "";
		}
	},
	mounted () {
		var _this = this;
		_this.getNewsNavData();
		_this.getData();

		// _this.setReads();

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
		
	}
})