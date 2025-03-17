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
     * notice message updated
     */
    let $flashMessage = $('[role="status"]');
    if ($flashMessage.length) {
        $flashMessage.hide();
        setTimeout(function () {
            $flashMessage.slideDown(500);
        }, 100);
    }

    /**
     * update status (place bottom of this file)
     */
    let publishedAtInput = $("input[name=published_at]");
    let expiredAtInput = $("input[name=expired_at]");
    let publishedDetails = publishedAtInput.closest("details");
    let expiredDetails = expiredAtInput.closest("details");

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

    function updateDetailsState() {
        let publishedAt = publishedAtInput.val();
        let expiredAt = expiredAtInput.val();
        let publishedDate = publishedAt ? new Date(publishedAt) : null;
        let now = new Date();

        if (expiredAt) {
            expiredDetails.attr("open", true);
        } else {
            expiredDetails.removeAttr("open");
        }

        if (publishedDate && !isNaN(publishedDate.getTime()) && publishedDate > now) {
            publishedDetails.attr("open", true);
        } else {
            publishedDetails.removeAttr("open");
        }
    }

    updateStatusOption();
    updateDetailsState();

    $("input[name=published_at], input[name=expired_at]").on("input change", function () {
        updateStatusOption();
        updateDetailsState();
    });

});
