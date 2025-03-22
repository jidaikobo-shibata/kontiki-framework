<?php

/**
  * @var string $publishing
  * @var string $reserved
  * @var string $expired
  * @var string $do_publish
  * @var string $do_reserve
  * @var string $do_save_as_pending
  * @var string $do_save_as_draft
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
     * open details and forcus input
     */
    $('details > summary > label').on('click', function (e) {
      e.preventDefault();

      const $label = $(this);
      const $details = $label.closest('details');

      const isOpen = $details.prop('open');

      if (isOpen) {
        $details.prop('open', false);
      } else {
        $details.prop('open', true);

        const $input = $details.find('input, textarea, select').first();
        if ($input.length) {
          setTimeout(() => $input.trigger('focus'), 50);
        }
      }
    });

    /**
     * update status (place bottom of this file)
     */
    let publishedAtInput = $("input[name=published_at]");
    let expiredAtInput = $("input[name=expired_at]");
    let mainSubmitBtn = $("button#mainSubmitBtn");
    let publishedDetails = publishedAtInput.closest("details");
    let expiredDetails = expiredAtInput.closest("details");
    let statusSelect = $("select[name=status]");

    if (publishedAtInput.length === 0 && expiredAtInput.length === 0) {
        return;
    }

    function updateStatusOption() {
        let publishedAt = publishedAtInput.val();
        let expiredAt = expiredAtInput.val();
        let publishedOption = $("select[name=status] option[value=published]");
        let currentStatus = statusSelect.val();

        let publishedDate = publishedAt ? new Date(publishedAt) : null;
        let expiredDate = expiredAt ? new Date(expiredAt) : null;
        let now = new Date();

        if (currentStatus === "draft") {
            mainSubmitBtn.text("<?= $do_save_as_draft ?>");
            return;
        } else if (currentStatus === "pending") {
            mainSubmitBtn.text("<?= $do_save_as_pending ?>");
            return;
        }

        if (expiredDate && !isNaN(expiredDate.getTime()) && expiredDate <= now) {
            publishedOption.text("<?= $expired ?>");
            mainSubmitBtn.text("<?= $do_save_as_pending ?>");
        } else if (publishedDate && !isNaN(publishedDate.getTime()) && publishedDate > now) {
            publishedOption.text("<?= $reserved ?>");
            mainSubmitBtn.text("<?= $do_reserve ?>");
        } else {
            publishedOption.text("<?= $publishing ?>");
            mainSubmitBtn.text("<?= $do_publish ?>");
        }
    }

    function updateDetailsState() {
        let publishedAt = publishedAtInput.val();
        let expiredAt = expiredAtInput.val();
        let publishedDate = publishedAt ? new Date(publishedAt) : null;
        let now = new Date();
        let currentStatus = statusSelect.val();

        if (currentStatus === "draft") {
            mainSubmitBtn.text("<?= $do_save_as_draft ?>");
            return;
        } else if (currentStatus === "pending") {
            mainSubmitBtn.text("<?= $do_save_as_pending ?>");
            return;
        }

        if (expiredAt) {
            expiredDetails.attr("open", true);
            mainSubmitBtn.text("<?= $do_save_as_pending ?>");
        } else {
            expiredDetails.removeAttr("open");
        }

        if (publishedDate && !isNaN(publishedDate.getTime()) && publishedDate > now) {
            publishedDetails.attr("open", true);
            mainSubmitBtn.text("<?= $do_reserve ?>");
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

    statusSelect.on("change", function () {
        updateStatusOption();
    });
});
