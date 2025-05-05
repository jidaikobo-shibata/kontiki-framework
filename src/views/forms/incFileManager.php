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
            <a class="nav-link active" id="upload-tab" data-bs-toggle="tab" href="#upload-tab-panel" role="tab" aria-controls="upload" aria-selected="true"><?= __('file_choose_upload') ?></a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="view-tab" data-bs-toggle="tab" href="#view-tab-panel" role="tab" aria-controls="view" aria-selected="false"><?= __('file_choose_from_uploaded') ?></a>
          </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="myTabContent">
          <!-- Tab pane 1 -->
          <div class="tab-pane fade show active" id="upload-tab-panel" role="tabpanel" aria-labelledby="upload-tab">
            <!-- upload -->
            <form class="pt-4 px-4 w-100" id="uploadForm" enctype="multipart/form-data">
              <div class="mt-4 mb-3 row">
                <label for="fileAttachment" class="visually-hidden"><?= __('file') ?></label>
                <div class="col-sm-8 mx-auto">
                  <input type="file" name="attachment" id="fileAttachment" required class="form-control fs-5" aria-describedby="attachmentHelp">
                  <small id="attachmentHelp" class="form-text text-muted text-center"><?= __('multi_bytes_warn', 'Multi-Bytes characters will be changed or deleted.') ?></small>
                </div>
              </div>
              <input type="hidden" name="_csrf_value" id="file_manager_csrf_token" value="">
              <div class="text-center">
                <button type="submit" id="fileUploadButton" disabled class="btn btn-light"><?= __('upload') ?></button>
              </div>
            </form>

            <div id="fileUploadStatus" class="mt-3"></div>

            <!-- save data by ajax and insert to field -->
            <form class="pt-4 px-4 w-100 d-none" id="insertUploadedFile" class="fileEdit">
              <input type="hidden" name="_csrf_value" id="file_manager_csrf_token" value="">
              <label for="uploadedDescription">画像説明</label>
              <textarea name="uploadedDescription" id="uploadedDescription" class="form-control" aria-describedby="uploadedDescriptionHelp" data-file-id="" data-file-path="" data-csrf_token=""></textarea>
              <small id="uploadedDescriptionHelp" class="form-text text-muted">画像説明を入力の上、挿入ボタンを押してください。画像に特に意味がない、装飾目的の画像の場合は、空のまま挿入してください。</small>
              <div id="insertStatusMsg"></div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary">挿入</button>
              </div>
            </form>
          </div><!-- /Tab pane 1 -->

          <!-- Tab pane 2 -->
          <div class="tab-pane fade" id="view-tab-panel" role="tabpanel" aria-labelledby="view-tab">
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
