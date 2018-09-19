$(function() {
    var result = new Vue({
        el: '#jpyy',
        data: {
            area: [],
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
        methods: {
            getarea: function() {
                console.log("xxx", utily.manage_url);
            },
            change: function(key,val){
                console.log(key,val);
            }

        },
        mounted: function() {
            this.getarea();
            $("#area,#city").select2({
                width: "300px",
            })
            $(".selectcity .city_item").eq(0).addClass("active")
        }
    });
    var config = {
        reset: true,
        delay: 'always',
        mobile: true,
    }
    window.sr = new scrollReveal(config);
})