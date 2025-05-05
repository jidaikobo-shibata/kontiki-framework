<?php

/**
  * @var string $basepath
  */
?>$(document).ready(function() {
    /**
     * add button when input||textarea has `kontiki-file-upload`
     */
    $("input.kontiki-file-upload, textarea.kontiki-file-upload").each(function () {
        const $element = $(this);
        const buttonClass = $(this).data("button-class") || "";
        const targetComponentId = $(this).attr("id") || "";
        const buttonHtml = `
            <button type="button" class="btn btn-secondary ${buttonClass}" data-bs-toggle="modal" data-bs-target="#uploadModal" data-target-component-id="${targetComponentId}" data-tab-target="view"><?= __('file_manage_upload', 'File Manage / Upload') ?></button>
        `;
        $element.after(buttonHtml);

        if ($element.is("textarea")) {
            const extraButtonHtml = `
            <button type="button" class="btn btn-secondary ${buttonClass}" data-bs-toggle="modal" data-bs-target="#uploadModal" data-target-component-id="${targetComponentId}"><?= __('image_insert', 'Insert Image') ?></button>
        `;
            $element.after(extraButtonHtml);
        }
    });

    /**
     * Listening to events using the Bootstrap modal JavaScript API
     */
    let fileManager; // keep instance `KontikiFileManager`
    $("#uploadModal").on("show.bs.modal", function (event) {
        // Get which button opened the modal
        const button = event.relatedTarget;

        if (button) {
            const targetTargetComponentId = $(button).data("target-component-id") || "content";

            // Destroys any existing `fileManager` instance (if necessary)
            if (fileManager) {
                delete fileManager;
            }

            fileManager = new KontikiFileManager('<?= $basepath ?>/', targetTargetComponentId);
        }
    });

});
