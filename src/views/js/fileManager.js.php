<?php

/**
  * @var string $uploading
  * @var string $couldnt_upload
  * @var string $get_file_list
  * @var string $couldnt_find_file
  * @var string $couldnt_get_file_list
  * @var string $copied
  * @var string $copy_failed
  * @var string $close
  * @var string $edit
  * @var string $confirm_delete_message
  * @var string $couldnt_delete_file
  * @var string $insert_success
  */
?>class KontikiFileManager {
    /**
     * Initializes the FileManager instance.
     * @param {string} ajaxUrl The base URL for AJAX requests.
     * @param {string} targetFieldId - The ID of the target targetField for file insertion.
     */
    constructor(ajaxUrl, targetFieldId) {
        this.ajaxUrl = ajaxUrl || '/'; // Default to '/admin/' if no URL is provided
        this.targetFieldId = targetFieldId || 'content'; // Default targetField ID
        this.init();
    }

    // Initialize events and functions
    init() {
        this.updateCsrfToken();
        this.prepareModalDialog();
        this.setupFileUploadButton();
        this.setupFileUpload();
        this.setupUpdateDescAndInsert();
        this.setupFileList();
        this.setupPagination();
        this.setupImageModal();
        this.setupCopyUrl();
        this.setupShowEdit();
        this.setupDeleteFile();
        this.setupFileEdit();
        this.setupInsertFile();
        this.switchTab();
    }

    /**
     * Sends an AJAX request to retrieve a new CSRF token from the server.
     * @returns {void}
     */
    updateCsrfToken() {
        $.ajax({
            url: `${this.ajaxUrl}get_csrf_token`,
            type: 'GET',
            success: (response) => {
                $('#file_manager_csrf_token').val(response.csrf_token);

                $('[data-csrf_token]').each(function() {
                    $(this).attr('data-csrf_token', response.csrf_token)
                });
            },
            error: () => {
                alert('Failed to obtain CSRF token.');
            }
        });
    }

    /**
     * prepare modal dialog.
     */
prepareModalDialog() {
        const parentModalId = '#uploadModal';
        let previousFocusBeforeUploadModal = null;
        let uploadModalInitialized = false;

        // 親モーダルのときだけ
        $(document).on('show.bs.modal', parentModalId, (event) => {
            if (event.target.id === 'uploadModal') {
                previousFocusBeforeUploadModal = document.activeElement;
            }

            if (uploadModalInitialized) return;
            uploadModalInitialized = true;

            const button = $(event.relatedTarget);
            const targetTab = button.data('tab-target');

            if (targetTab === 'view') {
                new bootstrap.Tab($('#view-tab')[0]).show();
                this.fetchFiles();
            } else {
                new bootstrap.Tab($('#upload-tab')[0]).show();
            }
        });

        // 親モーダルのときだけ、閉じたら元のフォーカス位置に戻す
        $(document).on('hidden.bs.modal', parentModalId, (event) => {
            if (event.target.id !== 'uploadModal') return;

            if (previousFocusBeforeUploadModal && typeof previousFocusBeforeUploadModal.focus === 'function') {
                setTimeout(() => {
                    previousFocusBeforeUploadModal.focus();
                }, 0);
            }
        });

        // Remove the current focus from within the modal.
        // because of accessibility error message of console
        $('#uploadModal').on('hide.bs.modal', function () {
            document.activeElement.blur();
        });
    }

    /**
     * Handles file upload button.
     */
    setupFileUploadButton() {
        $('#fileAttachment').on('change', function () {
            const hasFile = this.files.length > 0;
            const button = $('#fileUploadButton');

            button.prop('disabled', !hasFile);
            button.toggleClass('btn-light', !hasFile);
            button.toggleClass('btn-info', hasFile);
        });
    }

    /**
     * Handles the file upload process.
     * @param {Event} event - The event object from the submit event.
     */
    setupFileUpload() {
        $('#uploadForm').on('submit', (event) => {
            event.preventDefault(); // Prevent the default form submission
            // console.log("Form submitted!");

            // Show upload status
            var fileUploadStatus = document.getElementById('fileUploadStatus');
            fileUploadStatus.innerText = '<?= $uploading ?>';
            fileUploadStatus.setAttribute('role', 'status');

            var formData = new FormData(event.target); // Create FormData object

            // AJAX request
            $.ajax({
                url: `${this.ajaxUrl}upload`, // Specify the URL to upload the file
                type: 'POST',
                data: formData,
                contentType: false, // Prevent jQuery from setting content type
                processData: false, // Prevent jQuery from processing data
                success: (response) => {
                    // Handle successful upload
                    $('#fileUploadStatus').addClass('alert alert-success');
                    $('#fileUploadStatus').html(response.message);
                    $('#fileAttachment').val('');
                    $('#fileAttachment').focus();

                    // Set data-file-id on the textarea
                    $('#uploadedDescription').attr('data-file-id', response.data.id);
                    $('#uploadedDescription').attr('data-file-path', response.data.path);

                    // Optionally also clear the textarea for fresh input
                    $('#uploadedDescription').val('');

                    // Smooth transition: hide uploadForm, show insertUploadedFile
                    $('#uploadForm').fadeOut(300, function () {
                        $('#insertUploadedFile').fadeIn(300).removeClass('d-none');
                    });

                    // Optional: show a prompt or focus
                    $('#uploadedDescription').focus();

                    this.updateCsrfToken();
                },
                error: (xhr, status, error) => {
                    // Handle upload error
                    var response = xhr.responseJSON; // Get the JSON response

                    $('#fileUploadStatus').addClass('alert alert-danger');

                    // Check if the response contains a message
                    if (response && response.message) {
                        // Display the error message from response
                        $('#fileUploadStatus').html(response.message);
                    } else {
                        // Default error message
                        $('#fileUploadStatus').text('<?= $couldnt_upload ?>');
                    }
                    this.updateCsrfToken();
                }
            });
        });
    }

    /**
     * Save file description via ajax.
     * Insert markdown format/file path into input field.
     *
     * @event submit
     * @param {Event} e - The event object for the form submission.
     */
    setupUpdateDescAndInsert() {
        $(document).on('submit', '#insertUploadedFile', (e) => {
            e.preventDefault(); // Prevent the default form submission

            const form = $(e.target);

            const description = $('#uploadedDescription').val();
            const csrfToken = $('#uploadedDescription').attr('data-csrf_token');
            const fileId = $('#uploadedDescription').attr('data-file-id');
            const fileUrl = $('#uploadedDescription').attr('data-file-path');

            // Prepare the data to be sent
            const formData = {
                description: description,
                _csrf_value: csrfToken,
                id: fileId
            };

            // Make the AJAX request
            $.ajax({
                url: `${this.ajaxUrl}update`, // The URL to handle the request
                type: 'POST',
                data: formData,
                success: (response) => {
                    const markdown = `![${description}](${fileUrl})`;
                    this.insertTextAtCaret(markdown);
                    this.closeModal();
                },
                error: (xhr, status, error) => {
                    // Handle upload error
                    var response = xhr.responseJSON; // Get the JSON response

                    // reset
                    $('#uploadedDescription').removeAttr('aria-invalid');
                    $('#uploadedDescription').removeAttr('aria-errormessage');
                    $('#uploadedDescription').removeClass('is-invalid');

                    // Check if the response contains a message
                    if (response && response.message) {
                        // Add aria-invalid and aria-errormessage to input#eachDescription_<id>
                        if (response.message.includes('errormessage_eachDescription_' + fileId)) {
                            $('#uploadedDescription').attr('aria-invalid', 'true');
                            $('#uploadedDescription').attr('aria-errormessage', 'insertStatusMsg');
                            $('#uploadedDescription').addClass('is-invalid');
                        }
                        const replacedMessage = response.message.replace('#eachDescription_' + fileId, '#uploadedDescription');
                        $('#insertStatusMsg').html(replacedMessage);
                    } else {
                        $('#insertStatusMsg').text('Update failed.');
                    }
                    this.updateCsrfToken();
                }
            });
        });
    }

    /**
     * Event handler for the "View Files" tab click.
     * @param {Event} event - The focus event on the "View Files" tab.
     * @returns {void}
     */
    setupFileList() {
        let isInitialized = false;

        $('#view-tab').on('focus', () => {
            if (!isInitialized) {
                this.fetchFiles();
                isInitialized = true;
            }
            $('#uploadStatus').empty();
        });

        // reset
        $('#upload-tab').on('focus', () => {
            isInitialized = false;
        });
    }

    /**
     * Handles pagination link clicks and calls fetchFiles with the selected page.
     * @returns {void}
     */
    setupPagination() {
        // Listen for clicks on pagination links
        $(document).off('click', '.pagination .page-link-ajax'); // ensure reset click event
        $(document).on('click', '.pagination .page-link-ajax', (event) => {
            event.preventDefault(); // Prevent the default link behavior
            const page = $(event.target).data('page'); // Get the page number from data attribute
            this.fetchFiles(page); // Fetch files for the selected page
        });
    }

    /**
     * Fetch the list of uploaded files from the server.
     * @param {number} page - The page number to fetch.
     * @returns {void}
     */
    fetchFiles(page = 1) {
        // Find the file-list element where we'll append the files
        var fileListContainer = $('#file-list');

        fileListContainer.html('<p role="status"><?= $get_file_list ?></p>'); // Show loading message
        // clear upload status
        $('#fileUploadStatus').html('');

        // AJAX request to get the files
        $.ajax({
            url: `${this.ajaxUrl}filelist`,
            method: 'GET',
            data: { page: page },
            success: (response) => {
                // Successfully received the files list
                fileListContainer.empty(); // Clear the loading message

                // Check if there are any files
                if (response.length > 0) {
                    // Successfully received HTML content
                    fileListContainer.empty(); // Clear the loading message

                    // Directly insert the returned HTML into the file-list container
                    fileListContainer.html(response);

                    this.updateCsrfToken();
                } else {
                    // If no files are found, display a message
                    fileListContainer.html('<p role="status"><?= $couldnt_find_file ?></p>');
                }
            },
            error: () => {
                // Handle error
                fileListContainer.html('<p role="status"><?= $couldnt_get_file_list ?></p>');
            }
        });
    }

    /**
     * Initialize the setup for displaying images in a modal on click or keyboard focus.
     * Requires Bootstrap and jQuery.
     */
    setupImageModal() {
        // Handle click event
        $(document).off('click', '.clickable-image'); // ensure reset click event
        $(document).on('click', '.clickable-image', (e) => {
            this.showImageInModal(e.target);
        });

        // Handle keydown event (Enter or Space)
        $(document).off('keydown', '.clickable-image'); // ensure reset click event
        $(document).on('keydown', '.clickable-image', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault(); // Prevent default scroll behavior for Space key
                this.showImageInModal(e.target);
            }
        });

        // Attach custom behavior for nested modals
        this.setupNestedModal();
    }

    /**
     * Setup behavior for nested modals to ensure the proper handling of ESC key events
     * and background styling for the parent modal.
     */
    setupNestedModal() {
        const expandedModalId = '#expandedImageModal';
        const parentModalId = '#uploadModal';
        const overlayClass = 'modal-overlay';

        // Listen for the expanded modal's "shown" event
        $(document).on('shown.bs.modal', expandedModalId, () => {
            // Temporarily disable ESC key for the parent modal
            const parentModal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
            if (parentModal) {
                parentModal._config.keyboard = false;
            }

            // Add an overlay to darken the parent modal
            const overlay = $('<div>')
                .addClass(overlayClass)
                .css({
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: '100%',
                    backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    zIndex: 1050,
                });
            $(parentModalId).append(overlay);

            $('#expandedImageModal').css('z-index', 1055);
            $('#expandedImageModal .modal-dialog').css('z-index', 1056);

            // Add custom ESC key listener for the expanded modal
            $(document).on('keydown.expanded-modal', (e) => {
                if (e.key === 'Escape') {
                    $(expandedModalId).modal('hide');
                }
            });
        });

        // Listen for the expanded modal's "hidden" event
        $(document).on('hidden.bs.modal', expandedModalId, () => {
            // Remove the custom ESC key listener for the expanded modal
            $(document).off('keydown.expanded-modal');

            // Re-enable ESC key for the parent modal
            const parentModal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
            if (parentModal) {
                parentModal._config.keyboard = true;
            }

            // Remove the overlay from the parent modal
            $(parentModalId).find('.' + overlayClass).remove();

            // Return focus to the parent modal
            const parentModalElement = document.getElementById('uploadModal');
            if (parentModalElement) {
                setTimeout(() => {
                    const focusableElement = parentModalElement.querySelector(
                        'button:not([disabled]), a[href], input:not([disabled]), select, textarea, [tabindex]:not([tabindex="-1"])'
                    );
                    if (focusableElement && typeof focusableElement.focus === 'function') {
                        focusableElement.focus();
                    }
                }, 0);
            }
        });
    }

    /**
     * Show the image in the modal.
     * @param {HTMLElement} element The image element that triggered the event.
     */
    showImageInModal(element) {
        // Get the URL of the image from the 'src' attribute
        var imageUrl = $(element).attr('src');

        // Set the source of the modal image to the larger image URL
        $('#modalImage').attr('src', imageUrl);

        // Show the Bootstrap modal
        $('#expandedImageModal').modal('show');
    }

    /**
     * Handles the click event for copying the URL to the clipboard.
     * @param {Event} e - The click event triggered by clicking the 'copy url' link.
     * @returns {void}
     */
    setupCopyUrl() {
        $(document).off('click', '.fileCopyUrl'); // ensure reset click event
        $(document).on('click', '.fileCopyUrl', (e) => {
            e.preventDefault(); // Prevent default anchor behavior

            // Find the preceding <td> within the same <tr>
            const copyButton = $(e.target);
            const textField = copyButton.closest('td').prev('td').find('.fileUrl');
            const textToCopy = textField.text().trim(); // Extract the text to copy

            // Remove existing messages before adding a new one
            textField.siblings('.copy-status').remove();

            // Use the Clipboard API to copy the text
            navigator.clipboard.writeText(textToCopy).then(() => {
                // Append a success message
                textField.after('<span role="status" class="copy-status ms-2 text-success"><?= $copied ?></span>');
            }).catch((error) => {
                // Append an error message
                textField.after('<span role="status" class="copy-status ms-2 text-danger"><?= $copy_failed ?></span>');
            });
        });
    }

    /**
     * Toggles the visibility of an edit form within a table row.
     *
     * @returns {void}
     */
    setupShowEdit() {
        $(document).off('click', '.fileEditBtn'); // ensure reset click event
        $(document).on('click', '.fileEditBtn', function (e) {
            e.preventDefault(); // Prevent the default anchor behavior

            const editBtn = $(this);
            const formId = editBtn.attr('data-description-id');
            const form = $('#' + formId);

            if (form.hasClass('d-none')) {
                form.removeClass('d-none');
                editBtn.text('<?= $close ?>');
            } else {
                form.addClass('d-none');
                editBtn.text('<?= $edit ?>');
            }
        });
    }

    /**
     * Handles the click event on the delete link to remove a file.
     *
     * @param {Event} e - The event object representing the click event.
     */
    setupDeleteFile() {
        $(document).off('click', 'a.file-delete-link'); // ensure reset click event
        $(document).on('click', 'a.file-delete-link', (e) => {
            e.preventDefault(); // Prevent default anchor behavior

            const deleteId = $(e.target).data('delete-id');
            const csrfToken = $(e.target).attr('data-csrf_token'); // Use attr() to get the latest value
            if (!confirm("<?= $confirm_delete_message ?>")) {
                return;
            }

            // AJAX request to delete the file
            $.ajax({
                url: `${this.ajaxUrl}delete`,
                type: 'POST',
                data: {
                    id: deleteId,
                    _csrf_value: csrfToken
                },
                success: (response) => {
                    alert(response.message);
                    this.fetchFiles(); // Function to reload or refresh the table
                },
                error: (xhr, status, error) => {
                    var response = xhr.responseJSON; // Get the JSON response

                    // Check if the response contains a message
                    if (response && response.message) {
                        alert(response.message);
                    } else {
                        $('#uploadStatus').text('<?= $couldnt_delete_file ?>');
                    }
                    this.updateCsrfToken();
                }
            });
        });
    }

    /**
     * Handles form submission and sends the data via AJAX.
     * Prevents the default form submission, retrieves form data,
     * and sends it to the server using AJAX.
     *
     * @event submit
     * @param {Event} e - The event object for the form submission.
     */
    setupFileEdit() {
        $(document).on('submit', '.fileEdit', (e) => {
            e.preventDefault(); // Prevent the default form submission

            // Save the reference to the form element
            const form = $(e.target);

            // Get the textarea content and CSRF token
            const description = form.find('.eachDescription').val(); // Get the text from the textarea
            const csrfToken = form.find('.eachDescription').attr('data-csrf_token'); // Get the CSRF token from data attribute
            const fileId = form.find('.eachDescription').attr('data-file-id'); // Get the file ID from data attribute

            // Prepare the data to be sent
            const formData = {
                description: description,
                _csrf_value: csrfToken,
                id: fileId
            };

            // Make the AJAX request
            $.ajax({
                url: `${this.ajaxUrl}update`, // The URL to handle the request
                type: 'POST',
                data: formData,
                success: (response) => {
                    alert(response.message);
                    this.fetchFiles(); // Function to reload or refresh the table
                },
                error: (xhr, status, error) => {
                    // Handle upload error
                    var response = xhr.responseJSON; // Get the JSON response

                    // reset
                    form.find('.eachDescription').removeAttr('aria-invalid');
                    form.find('.eachDescription').removeAttr('aria-errormessage');
                    form.find('.eachDescription').removeClass('is-invalid');

                    // Check if the response contains a message
                    if (response && response.message) {
                        // Add aria-invalid and aria-errormessage to input#eachDescription_<id>
                        if (response.message.includes('errormessage_eachDescription_'+fileId)) {
                            form.find('.eachDescription').attr('aria-invalid', 'true');
                            form.find('.eachDescription').attr('aria-errormessage', 'errormessage_eachDescription_'+fileId);
                            form.find('.eachDescription').addClass('is-invalid');
                        }

                        form.find('.updateStatus').html(response.message); // Display the error message from response
                    } else {
                        form.find('.updateStatus').text('Update failed.'); // Default error message
                    }
                    this.updateCsrfToken();
                }
            });
        });
    }

    /**
     * Handles the "Insert" button click to insert a file reference
     * into the targetField and display success status.
     */
    setupInsertFile() {
        $(document).off('click', '.fileInsertBtn'); // ensure reset click event
        $(document).on('click', '.fileInsertBtn', (e) => {
            e.preventDefault(); // Prevent default anchor behavior

            // Find the <code> element in the same row
            const fileRow = $(e.target).closest('tr'); // The row containing the button
            const codeContent = fileRow.find('td.text-break code').text().trim(); // Get <code> content

            // Use the extracted method
            this.insertTextAtCaret(codeContent);

            // close modal
            this.closeModal();

            // Display success status
            // const codeElement = fileRow.find('td.text-break code');
            // fileRow.find('.insert-status').remove();
            // codeElement.after('<span role="status" class="insert-status ms-2 text-success"><?= $insert_success ?></span>');
        });
    }

    /**
     * close modal
     */
    closeModal() {
        const modalElement = document.getElementById('uploadModal');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    }

    /**
     * Insert the given text at the current caret position of the target textarea.
     * @param {string} textToInsert - The text to insert at the caret.
     */
    insertTextAtCaret(textToInsert) {
        const targetField = document.getElementById(this.targetFieldId);
        if (!targetField) return;

        const startPos = targetField.selectionStart;
        const endPos = targetField.selectionEnd;
        const textBefore = targetField.value.substring(0, startPos);
        const textAfter = targetField.value.substring(endPos);

        targetField.value = textBefore + textToInsert + textAfter;
        targetField.selectionStart = targetField.selectionEnd = startPos + textToInsert.length;
        targetField.focus();
    }

    /**
     * Switch Tab
     * @returns {void}
     */
    switchTab() {
        $(document).off('click', '#switchToViewTab'); // ensure reset click event
        $(document).on('click', '#switchToViewTab', (event) => {
            event.preventDefault();
            var viewTab = new bootstrap.Tab(document.getElementById("view-tab"));
            this.fetchFiles();
            viewTab.show();
        });
    }
}
