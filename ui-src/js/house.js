/**************************************
 * 楼盘展示页面的大图
 *
 * 移动端只显示大图，需要显示方向按钮和页码
 *
 * 桌面端显示小图导航，隐藏方向按钮和页码
 *
 *************************************/

const topOption = {
  spaceBetween: 10,
};

if (window.innerWidth <= 575) {
  topOption.navigation = {
    nextEl: '.top-swiper-next',
    prevEl: '.top-swiper-prev',
  };
  topOption.pagination = {
    el: '.top-swiper-pagination',
  };
}
const galleryTop = new Swiper('.top-swiper', topOption);

if (window.innerWidth > 575) {
  const galleryThumbs = new Swiper('.thumb-swiper', {
    centeredSlides: true,
    slidesPerView: 'auto',
    touchRatio: 0.2,
    slideToClickedSlide: true,
    navigation: {
      nextEl: '.thumb-swiper-next',
      prevEl: '.thumb-swiper-prev',
    },
  });
  galleryTop.controller.control = galleryThumbs;
  galleryThumbs.controller.control = galleryTop;
}

if ('wx' in window) {
  const classList = document.body.classList;
  for (const klass of classList) {
    const result = /^postid-(\d+)$/.exec(klass);
    if (result) {
      const postId = result[1];
      console.log('Post ID: ', postId);
      wx.miniProgram.postMessage({data: {id: postId}});
      break;
    }
  }
}
