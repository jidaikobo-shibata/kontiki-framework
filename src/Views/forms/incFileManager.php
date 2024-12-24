<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="uploadModalLabel">ファイルの管理</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
      </div>
      <div class="modal-body">
        <!-- Tab navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="upload-tab" data-bs-toggle="tab" href="#upload" role="tab" aria-controls="upload" aria-selected="true">ファイルアップロード</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="view-tab" data-bs-toggle="tab" href="#view" role="tab" aria-controls="view" aria-selected="false">ファイル一覧</a>
          </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="myTabContent">
          <!-- Tab pane 1 -->
          <div class="tab-pane fade show active" id="upload" role="tabpanel" aria-labelledby="upload-tab">
            <form class="pt-4 px-4 w-100" id="uploadForm" enctype="multipart/form-data">
              <div class="mt-4 mb-3 row">
                <label for="attachment" class="col-sm-3 col-form-label text-end">ファイル</label>
                <div class="col-sm-8">
                  <input type="file" name="attachment" id="attachment" required class="form-control fs-5" aria-describedby="attachmentHelp">
                  <small id="attachmentHelp" class="form-text text-muted">全角文字は変更か、削除されます。</small>
                </div>
              </div>
              <div class="mb-3 row">
                <label for="description" class="col-sm-3 col-form-label text-end">説明</label>
                <div class="col-sm-8">
                  <input type="text" name="description" id="description" class="form-control fs-5" aria-describedby="textHelp">
                  <small id="textHelp" class="form-text text-muted">画像の場合は<code>alt属性値</code>、PDF等の場合は<code>リンクテキスト</code>として用いられます。</small>
                </div>
              </div>
              <input type="hidden" name="csrf_token" id="file_manager_csrf_token" value="">
              <div class="text-center">
                <button type="submit" class="btn btn-info">送信する</button>
              </div>
            </form>
            <!-- Upload status -->
            <div id="uploadStatus" class="mt-3"></div>
          </div><!-- /Tab pane 1 -->

          <!-- Tab pane 2 -->
          <div class="tab-pane fade" id="view" role="tabpanel" aria-labelledby="view-tab">
            <div id="file-list">
              <p role="status">ファイルの一覧を準備中です。</p>
            </div>
          </div><!-- /Tab pane 2 -->
        </div><!-- /Tab content -->
      </div><!-- /modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
      </div>
    </div><!-- /modal-content -->
  </div><!-- /modal-dialog -->
</div>
