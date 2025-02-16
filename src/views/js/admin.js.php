$(document).ready(function () {
    /**
     * open sidebar menu items
     */
    var currentUrl = window.location.pathname;
    $(".nav-item").each(function () {
        var menuPath = $(this).attr("data-path");

        if (menuPath && currentUrl.startsWith(menuPath)) {
            $(this).addClass("menu-is-opening menu-open");
            $(this).find(".nav-treeview").first().css("display", "block");
            $(this).find('> a.nav-link').attr('aria-expanded', 'true');
        } else {
            $(this).find('> a.nav-link').attr('aria-expanded', 'false');
        }
    });

});
