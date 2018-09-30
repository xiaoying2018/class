$(function() {
    var mallindex = new Vue({
        el: '#teacher',
        data: {
            academic: [
            // {
            //     img: "http://vip.xiaoying.net/img/teacher/small/cyy.jpg",
            //     name: "陈远英",
            //     job: "精英首席学术研究顾问",
            //     desc: "东京大学金融工学。师从野村证券首席战略分析师新井富雄老师。8年私塾讲师经手案例近万人，擅长TOP10顶尖名校升学。"
            // }, 
            {
                img: "http://vip.xiaoying.net/img/teacher/small/jym.jpg",
                name: "简依梦",
                job: "精英计划高级学术顾问",
                desc: "御茶水女子大学社会学，私塾社会学大学院课程主讲，行业经验丰富，深受学员喜爱。擅长SGU英文项目/院生直考课程/研究生申请等项目，顶级院校升学辅导案例众多。"
            }, {
                img: "http://vip.xiaoying.net/img/teacher/small/zxm.jpg",
                name: "赵旭鸣",
                job: "精英计划高级学术顾问",
                desc: "早稻田大学经济学。4年日本留学经历。3年日资银行从业经验。擅长名校升学以及职业生涯全面规划。辅导学员过百人。"
            },  {
                img: "http://vip.xiaoying.net/img/teacher/small/shq.jpg",
                name: "尚海秋",
                job: "精英首席签证材料顾问",
                desc: "28年的日本文化与习惯的交流经验，作为20年赴日日本留学工作资深业内人士，深谙日本各大使馆及入管局对留学签证材料的要诣，有过多批签证率百分百的记录，还有多次拒签再获签的成功经验，特别擅长对于疑难复杂材料的修整和完善，近20年来得到无数赴日留学生和家长的安心委托与感谢！也赢得日本校方的高度信任与长期合作……"
            }, 
            // {
            //     img: "http://vip.xiaoying.net/img/teacher/small/yszj.jpg",
            //     name: "远山智久",
            //     job: "精英计划高级学术顾问",
            //     desc: "东京大学博士。20年以上教学经验，擅长东京大学京都大学等顶级名校升学指导，指导学员东京大学录取率常年保持在50%以上。"
            // },
            {
                img: "http://vip.xiaoying.net/img/teacher/small/ywy.jpg",
                name: "杨文颖",
                job: "精英计划高级学术顾问",
                desc: "广岛大学教育学，5年日本留学经历。曾于広島国際センター、広島YMCA専門学校等地任教实习，也曾在国内某大型教育机构担任日语老师。擅长研究生申请，文科类专业院校的的升学指导"
            }
            ]
        },
        filters: {},
        methods: {

        },
        mounted: function() {
            window.setTimeout(function() {
                var galleryTop = new Swiper('.gallery-top', {
                    spaceBetween: 0,
                    loop: true,
                    loopedSlides: 5, //looped slides should be the same
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });
                var galleryThumbs = new Swiper('.gallery-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 7,
                    touchRatio: 0.2,
                    loop: true,
                    loopedSlides: 5, //looped slides should be the same
                    slideToClickedSlide: true,
                });
                galleryTop.controller.control = galleryThumbs;
                galleryThumbs.controller.control = galleryTop;
            }, 1000)
            $(".s_nav li").eq(2).addClass('active');
        }
    })


})