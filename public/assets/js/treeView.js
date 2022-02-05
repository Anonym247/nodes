!function ($) {
    $(document).on("click","#left ul.nav li.parent > a > span.arrow", function(){
        $(this).find('i:first').toggleClass("rotated");
    });

    $("#left ul.nav li.parent.active > a > span.arrow").find('i:first').addClass("rotated");
    $("#left ul.nav li.current").parents('ul.children').addClass("in");

}(window.jQuery);