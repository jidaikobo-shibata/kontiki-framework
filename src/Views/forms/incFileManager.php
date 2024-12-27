<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="uploadModalLabel"><?= __('x_management', ':name Management', ['name' => __('files')]) ?></h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= __('close') ?>"></button>
      </div>
      <div class="modal-body">
        <!-- Tab navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="upload-tab" data-bs-toggle="tab" href="#upload" role="tab" aria-controls="upload" aria-selected="true"><?= __('file_upload') ?></a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="view-tab" data-bs-toggle="tab" href="#view" role="tab" aria-controls="view" aria-selected="false"><?= __('x_index', ':name Index', ['name' => __('files')]) ?></a>
          </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="myTabContent">
          <!-- Tab pane 1 -->
          <div class="tab-pane fade show active" id="upload" role="tabpanel" aria-labelledby="upload-tab">
            <form class="pt-4 px-4 w-100" id="uploadForm" enctype="multipart/form-data">
              <div class="mt-4 mb-3 row">
                <label for="fileAttachment" class="col-sm-3 col-form-label text-end"><?= __('file') ?></label>
                <div class="col-sm-8">
                  <input type="file" name="attachment" id="fileAttachment" required class="form-control fs-5" aria-describedby="attachmentHelp">
                  <small id="attachmentHelp" class="form-text text-muted"><?= __('multi_bytes_warn', 'Multi-Bytes characters will be changed or deleted.') ?></small>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="fileDescription" class="col-sm-3 col-form-label text-end"><?= __('description') ?></label>
                <div class="col-sm-8">
                  <input type="text" name="description" id="description" class="form-control fs-5" aria-describedby="textHelp">
                  <small id="textHelp" class="form-text text-muted"><?= __('desc_for_description', 'For images, it is used as the <code>alt attribute value</code>, and for PDFs, etc., it is used as the <code>link text</code>.') ?></small>
                </div>
              </div>
              <input type="hidden" name="_csrf_token" id="file_manager_csrf_token" value="">
              <div class="text-center">
                <button type="submit" class="btn btn-info"><?= __('submit') ?></button>
              </div>
            </form>
            <!-- Upload status -->
            <div id="fileUploadStatus" class="mt-3"></div>
          </div><!-- /Tab pane 1 -->

          <!-- Tab pane 2 -->
          <div class="tab-pane fade" id="view" role="tabpanel" aria-labelledby="view-tab">
            <div id="file-list">
              <p role="status"><?= __('prepare_file_list', 'Preparing file list.') ?></p>
            </div>
          </div><!-- /Tab pane 2 -->
        </div><!-- /Tab content -->
      </div><!-- /modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('close') ?></button>
      </div>
    </div><!-- /modal-content -->
  </div><!-- /modal-dialog -->
</div>
