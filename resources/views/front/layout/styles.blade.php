<!-- All CSS -->
<link rel="stylesheet" href="{{ asset('dist-front/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/jquery-ui.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/magnific-popup.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/spacing.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/meanmenu.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/iziToast.min.css') }}">
<link rel="stylesheet" href="{{ asset('dist-front/css/style.css') }}">

<style>
    .main-nav nav .navbar-nav .nav-item a:hover,
    .main-nav nav .navbar-nav .nav-item:hover a,
    .slide-carousel.owl-carousel .owl-nav .owl-prev:hover, 
    .slide-carousel.owl-carousel .owl-nav .owl-next:hover,
    .home-feature .inner .icon i,
    .home-rooms .inner .text .price,
    .home-rooms .inner .text .button a,
    .blog-item .inner .text .button a,
    .room-detail-carousel.owl-carousel .owl-nav .owl-prev:hover, 
    .room-detail-carousel.owl-carousel .owl-nav .owl-next:hover {
        color: {{ $global_setting_data->theme_color_1 }};
    }

    .main-nav nav .navbar-nav .nav-item .dropdown-menu li a:hover,
    .primary-color {
        color: {{ $global_setting_data->theme_color_1 }}!important;
    }

    .testimonial-carousel .owl-dots .owl-dot,
    .footer ul.social li a,
    .footer input[type="submit"],
    .scroll-top,
    .room-detail .right .widget .book-now {
        background-color: {{ $global_setting_data->theme_color_1 }};
    }

    .slider .text .button a,
    .search-section button[type="submit"],
    .home-rooms .big-button a,
    .bg-website {
        background-color: {{ $global_setting_data->theme_color_1 }}!important;
    }

    .slider .text .button a,
    .slide-carousel.owl-carousel .owl-nav .owl-prev:hover, 
    .slide-carousel.owl-carousel .owl-nav .owl-next:hover,
    .search-section button[type="submit"],
    .room-detail-carousel.owl-carousel .owl-nav .owl-prev:hover, 
    .room-detail-carousel.owl-carousel .owl-nav .owl-next:hover,
    .room-detail .amenity .item {
        border-color: {{ $global_setting_data->theme_color_1 }}!important;
    }

    .home-feature .inner .icon i,
    .home-rooms .inner .text .button a,
    .blog-item .inner .text .button a,
    .room-detail .amenity .item,
    .cart .table-cart tr th {
        background-color: {{ $global_setting_data->theme_color_2 }}!important;
    }
</style>