$(window).load(function () {
    let menuItems   = $('.top-menu .nav1 li');
    let activeIndex = 2;

    if (activeIndex >= 0 && activeIndex < menuItems.length)
        menuItems[activeIndex].classList.add('active');
});
