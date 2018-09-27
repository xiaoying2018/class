$(function() {
    var mallindex = new Vue({
        el: '#xiaoying',
        data: {
            shouquan: [
                { "name": "日本帝京大学", "imgUrl": "jz4" },
                { "name": "日本工业大学", "imgUrl": "jz2" },
                { "name": "早稻田EDU学校", "imgUrl": "jz1" },
                { "name": "千驮谷日本语学校", "imgUrl": "jz2" },
                { "name": "ARC日本语学校", "imgUrl": "jz3" },
                { "name": "MANABI学校", "imgUrl": "jz4" },
                { "name": "KCP日本语学校", "imgUrl": "jz5" },
                { "name": "京东国际文化学院", "imgUrl": "jz6" },
                { "name": "早稻田EDU学校", "imgUrl": "jz1" },
                { "name": "千驮谷日本语学校", "imgUrl": "jz2" }
            ],
            caselist: [{
                    img: "stu1.jpg",
                    name: "王小青同学",
                    major: "经济学",
                    tuofu: "93",
                    desc: "小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,"
                },
                {
                    img: "stu2.jpg",
                    name: "王小青同学",
                    major: "经济学",
                    tuofu: "93",
                    desc: "小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,"
                },
                {
                    img: "stu3.jpg",
                    name: "王小青同学",
                    major: "经济学",
                    tuofu: "93",
                    desc: "小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,"
                },
                {
                    img: "stu4.jpg",
                    name: "王小青同学",
                    major: "经济学",
                    tuofu: "93",
                    desc: "小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,"
                },
                {
                    img: "stu5.jpg",
                    name: "王小青同学",
                    major: "经济学",
                    tuofu: "93",
                    desc: "小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,"
                },
                {
                    img: "stu6.jpg",
                    name: "王小青同学",
                    major: "经济学",
                    tuofu: "93",
                    desc: "小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,"
                },
                {
                    img: "stu7.jpg",
                    name: "王小青同学",
                    major: "经济学",
                    tuofu: "93",
                    desc: "小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,"
                },
                {
                    img: "stu8.jpg",
                    name: "王小青同学",
                    major: "经济学",
                    tuofu: "93",
                    desc: "小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,小莺是一个很温暖的大家庭,"
                },
            ],
            teacher: [{
                    img: "cyy.jpg",
                    name: "陈远英",
                    job: "精英首席学术研究顾问",
                    desc: "东京大学金融工学。师从野村证券首席战略分析师新井富雄老师。8年私塾讲师经手案例近万人，擅长TOP10顶尖名校升学。"
                }, {
                    img: "jym.jpg",
                    name: "简依梦",
                    job: "精英计划高级学术顾问",
                    desc: "御茶水女子大学社会学，私塾社会学大学院课程主讲，行业经验丰富，深受学员喜爱。擅长SGU英文项目/院生直考课程/研究生申请等项目，顶级院校升学辅导案例众多。"
                }, {
                    img: "zxm.jpg",
                    name: "赵旭鸣",
                    job: "精英计划高级学术顾问",
                    desc: "早稻田大学经济学。4年日本留学经历。3年日资银行从业经验。擅长名校升学以及职业生涯全面规划。辅导学员过百人。"
                }, {
                    img: "shq.jpg",
                    name: "尚海秋",
                    job: "精英首席签证材料顾问",
                    desc: "28年的日本文化与习惯的交流经验，作为20年赴日日本留学工作资深业内人士，深谙日本各大使馆及入管局对留学签证材料的要诣，有过多批签证率百分百的记录，还有多次拒签再获签的成功经验，特别擅长对于疑难复杂材料的修整和完善，近20年来得到无数赴日留学生和家长的安心委托与感谢！也赢得日本校方的高度信任与长期合作……"
                }, {
                    img: "yszj.jpg",
                    name: "远山智久",
                    job: "精英计划高级学术顾问",
                    desc: "东京大学博士。20年以上教学经验，擅长东京大学京都大学等顶级名校升学指导，指导学员东京大学录取率常年保持在50%以上。"
                }, {
                    img: "ywy.jpg",
                    name: "杨文颖",
                    job: "精英计划高级学术顾问",
                    desc: "广岛大学教育学，5年日本留学经历。曾于広島国際センター、広島YMCA専門学校等地任教实习，也曾在国内某大型教育机构担任日语老师。擅长研究生申请，文科类专业院校的的升学指导"
                }, {
                    img: 'zzf.jpg',
                    name: '赵梓帆',
                    job: '精英计划首席客户维护顾问',
                    desc: "三年0投诉精英顾问。18小时在线，零时差秒回答疑。2+2九州交换项目 ，3年在日经历。行业经验丰富，办理辅导学员至今已超500人，帝大167人。擅长直考/学部/研究生/语言学校多项目规划/精通撰写各项文书材料"
                },
                {
                    img: 'cmq.jpg',
                    name: '陈铭菁',
                    job: '精英计划高级留学顾问',
                    desc: "5年留日背景，不仅熟知日本学部学院考学情况，并且实时掌握当下日本就业发展情况。擅长通过与学员接触、聊天发现学员长处，并给于合理的建议及指导，被学生家长评价为“孩子留学之路上的指明灯”。"
                },
                {
                    img: 'wxm.jpg',
                    name: '汪晓敏',
                    job: '精英计划高级留学顾问',
                    desc: "日本留学行业经验丰富，熟悉日本高中，本科和研究生留学全程申请和专业设置。擅长挖掘学生的背景制定个性化的留学方案。工作责任心强，服务细致，客户满意度极高。在工作期间，成功地帮助大量学生拿到日本名校的offer。"
                },
                {
                    img: 'xxj.jpg',
                    name: '徐晓洁',
                    job: '精英计划高级留学顾问',
                    desc: "在日9年经验。日本大学本科加贸易公司就职在日9年，有丰富的日本升学指导和职业规划经验，辅导学员超百人。擅长学部、院生直考规划和日语、英语项目等各类申请，耐心迅速答疑解惑，好评不断。"
                },
                {
                    img: 'cyx.jpg',
                    name: '曹玉霞',
                    job: '精英计划高级留学顾问',
                    desc: "主要负责语言学校，EJU，研究生申请和修士直考，行业经验丰富，擅长分析学生性格，帮助每位学生制定最合适自己的留学规划。"
                },
                {
                    img: 'lx.jpg',
                    name: '李璇',
                    job: '精英计划高级留学顾问',
                    desc: "日语科班出身，研究生翻译第一，熟知日本各项目留学规划，擅长语言学校/研究生申请等项目, 7*18小时在线答疑， “专业”、“贴心”为本，深受学员喜爱。"
                },
                {
                    img: 'oyhx.jpg',
                    name: '欧阳含笑',
                    job: '精英计划高级留学顾问',
                    desc: "985高校日语专业出身，日本知名院校交换留学。留学行业经验丰富，多年日本学习生活经验，熟知日本各项目规划及申请。擅长eju留考/修士直考/研究生/sgu等多项目规划指导，耐心细心，学员好评金牌导师。"
                }
            ]
        },
        methods: {
            initevent: function() {
                $(".xy_form_a").xiaoyingForm({
                    parameter: [
                        { errorMes: "请选择出国时间", id: "XY_b09", rename: "出国时间", reg: "required", placeholder: "出国时间" },
                        { errorMes: "请选择目前学历", id: "XY_b19", rename: "目前学历", reg: "required", placeholder: "目前学历" },
                        { errorMes: "姓名格式不正确", id: "XY_a01", rename: "姓名", reg: "required", placeholder: "姓名" },
                    ],
                    submitBtn: ".xy_submit_a",
                    beforeSendData: function(_data, cb) {
                        utily.setStore('xy_logined_href', window.location.href);
                        var _m = utily.getStore('xy_tel');
                        if (_m == "" || _m == null) {
                            var _t = {
                                'XY_a01': _data['XY_a01'],
                                'XY_b09': _data['XY_b09'],
                                'XY_b19': _data['XY_b19'],
                                'XY_c01': _data['XY_c01'],
                                'city': $("#nationselect").val(),
                                'serviceselect': $("#serviceselect").select2("data")[0].id
                            }
                            utily.setStore('xy_banenr_form', JSON.stringify(_t));
                            alert('请登录或注册您的账户，方便顾问老师更加及时的跟进您的信息。')
                            location.href = '/user/#/login?a=register';
                            return false
                        } else {
                            _data['XY_b20'] = $("#nationselect").select2("data")[0].text;
                            _data['XY_b21'] = '该客户选择的服务是:' + $("#serviceselect").select2("data")[0].text;
                            _data['XY_a02'] = _m;
                            cb(_data);
                        }
                    },
                    beforeLoadField: function(res) {
                        return res;
                    },
                    afterLoadField: function() {
                        $(".xy_form_a").prepend('<div class="lineDv"><select name="" id="nationselect"><option value="">意向国家</option><option value="japan" title="japan">日本</option><option value="korea" title="korea">韩国</option><option value="singapore" title="singapore">新加坡</option><option value="aus" title="aus">澳洲</option><option value="canada" title="canada">加拿大</option></select></div>')
                        $(".xy_form_a").append('<div class="lineDv full"><select name="" id="serviceselect"><option value="">请选择我们的服务</option><option value="1">在线评估预约</option><option value="2">本科学校申请</option><option value="3">硕士申请</option><option value="4">签证办理服务</option><option value="5">申请文书写作服务</option><option value="6">海外保险服务</option></select></div>')
                        $("#nationselect").select2({
                            minimumResultsForSearch: -1,
                            templateResult: formatState
                        });

                        $("#serviceselect").select2({
                            minimumResultsForSearch: -1,
                        });

                        $('[name="XY_b09"],[name="XY_b19"]').select2({
                            minimumResultsForSearch: -1,
                        });


                        function formatState(state) {
                            if (!state.title) {
                                return state.text;
                            }
                            var baseUrl = "/Public/Common/img/nation";
                            var $state = $(
                                '<span><img src="' + baseUrl + '/' + state.element.title + '.png" class="img-flag" /> ' + state.text + '</span>'
                            );
                            return $state;
                        }

                        var _temp = JSON.parse(utily.getStore('xy_banenr_form'));
                        if (_temp) {
                            $("#nationselect").val(_temp['city']).trigger("change");
                            $("[name='XY_b09']").val(_temp['XY_b09']).trigger("change");
                            $("[name='XY_b19']").val(_temp['XY_b19']).trigger("change");
                            $("#serviceselect").val(_temp['serviceselect']).trigger("change");
                            $("[name='XY_a01']").val(_temp['XY_a01']);
                        }

                    },
                    validatorError: function(mes, node) {
                        alert(mes);
                    },
                    submitSuccess: function() {
                        alert("申请成功！请等到老师与您联系");
                    },
                    submitError: function() {}
                });

                new Swiper('.swiper-banner', {
                    paginationClickable: true,
                });
                new Swiper('.swiper-ad', {
                    pagination: '.swiper-pagination',
                    slidesPerView: 5,
                    paginationClickable: true,
                    spaceBetween: 20
                });
                new Swiper('.swiper-team', {
                    pagination: '.team-swiper-pagination',
                    slidesPerView: 4,
                    paginationClickable: true,
                    spaceBetween: 20
                });
                var swiper_case = new Swiper('.swiper-case', {
                    loop: true,
                    slidesPerView: 5,
                    paginationClickable: true,
                    spaceBetween: 20,
                    centeredSlides: true,
                    navigation: {
                        nextEl: '.swiper-case .swiper-button-next',
                        prevEl: '.swiper-case .swiper-button-prev',
                    },
                });

                $(".case .swiper-button-prev").click(function() {
                    swiper_case.slidePrev();
                });
                $(".case .swiper-button-next").click(function() {
                    swiper_case.slideNext();
                });


                new Swiper('.swiper-shouquan', {
                    pagination: '.team-swiper-pagination',
                    slidesPerView: 4,
                    paginationClickable: true,
                    spaceBetween: 20,
                    autoplay:3000
                });


            }
        },
        mounted: function() {
            this.initevent();
        }
    })
    // api/mall
});