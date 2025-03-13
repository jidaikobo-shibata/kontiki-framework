<?php

/**
  * @var string $publishing
  * @var string $reserved
  * @var string $expired
  */
?>$(document).ready(function () {
    /**
     * move button
     */
    $("#create_button_in_index").insertAfter("#content-header h1");

    /**
     * open sidebar menu items
     */
    var currentUrl = window.location.pathname;
    $(".sidebar .nav-item").each(function () {
        var menuPath = $(this).attr("data-path");

        if (menuPath && currentUrl.startsWith(menuPath)) {
            $(this).addClass("menu-is-opening menu-open");
            $(this).find(".nav-treeview").first().css("display", "block");
            $(this).find('> a.nav-link').attr('aria-expanded', 'true');
        } else {
            $(this).find('> a.nav-link').attr('aria-expanded', 'false');
        }
    });

    /**
     * update status (place bottom of this file)
     */
    let publishedAtInput = $("input[name=published_at]");
    let expiredAtInput = $("input[name=expired_at]");

    if (publishedAtInput.length === 0 && expiredAtInput.length === 0) {
        return;
    }

    function updateStatusOption() {
        let publishedAt = publishedAtInput.val();
        let expiredAt = expiredAtInput.val();
        let publishedOption = $("select[name=status] option[value=published]");

        let publishedDate = publishedAt ? new Date(publishedAt) : null;
        let expiredDate = expiredAt ? new Date(expiredAt) : null;
        let now = new Date();

        if (expiredDate && !isNaN(expiredDate.getTime()) && expiredDate <= now) {
            publishedOption.text("<?= $expired ?>");
        } else if (publishedDate && !isNaN(publishedDate.getTime()) && publishedDate > now) {
            publishedOption.text("<?= $reserved ?>");
        } else {
            publishedOption.text("<?= $publishing ?>");
        }
    }

    updateStatusOption();

    $("input[name=published_at], input[name=expired_at]").on("input change", function () {
        updateStatusOption();
    });

});
