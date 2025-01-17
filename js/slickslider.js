$(".slickcard").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    speed: 300,
    arrows: true,
    dots: true,
    responsive: [{
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      }, {
        breakpoint: 520,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1
        }
      }]
  });
  