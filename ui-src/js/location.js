const params = {
  spaceBetween: 12,
  pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
};

// 適配響應式
if (innerWidth >= 576) {
  params.slidesPerView = 3;
  params.slidesPerGroup = 3;
  params.loop = true;
  params.loopFillGroupWithBlank = true;
} else {
  params.slidesPerColumn = 2;
}

const swiper = new Swiper('.swiper-container', params);
