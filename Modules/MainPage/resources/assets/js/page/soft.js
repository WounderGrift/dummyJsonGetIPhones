$(window).load(function () {
    let menuItems   = $('.top-menu .nav1 li');
    let activeIndex = 8;

    if (activeIndex >= 0 && activeIndex < menuItems.length)
        menuItems[activeIndex].classList.add('active');
});
