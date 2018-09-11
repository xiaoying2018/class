$(function() {
    var mallindex = new Vue({
        el: '#teacher',
        data: {},
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
        }
    })


})