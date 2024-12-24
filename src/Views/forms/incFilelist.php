<ul class="mt-4">
	<li><code>URLをコピー</code>でURLをコピーできます</li>
	<li><code>編集する</code>で、「説明」を編集できます。</li>
	<li id="eachDescriptionHelp">「説明」は画像の場合はalt属性（画像の代替テキスト）、ファイルの場合はリンク文字列として使われます</li>
</ul>

<!-- table-responsive -->
<div class="table-responsive">
<?php echo $pagination; ?>
<table class="table table-bordered table-hover table-striped">
	<thead class="table-light">
		<tr class="table-dark">
			<th class="text-center">ID</th>
			<th class="w-25">ファイル</th>
			<th>値</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($items as $file): ?>
		<?php $fileId = intval($file['id']) ?>
			<tr>
				<th class="text-center"><?php echo $fileId; ?></th>
				<td>
					<div class="text-center">
					<?php
					echo $file['imageOrLink'];
					?>
	 			</div>
					<div class="text-center text-nowrap"><a href="#" class="text-danger file-delete-link" data-confirm="critical" data-delete-id="<?php echo $fileId ?>" data-csrf_token="">完全に削除する</a></div>
				</td>
				<td class="eachFile">

					<table class="table table-bordered m-0">
					<tr>
						<th class="align-middle" scope="row">URL</th>
						<td class="text-break"><span class="fileUrl"><?php echo htmlspecialchars($file['url']); ?></span></td>
						<td class="text-nowrap align-middle"><a href="#" class="fileCopyUrl">URLをコピー</a></td>
					</tr>
					<tr>
						<th class="align-middle" scope="row">説明</th>
						<td class="text-break"><?php echo htmlspecialchars($file['description']); ?></td>
						<td class="text-nowrap align-middle"><a href="#" class="fileEditBtn">編集する</a></td>
					</tr>
					<tr>
						<th class="text-nowrap align-middle" scope="row">コード</th>
						<td class="text-break"><code>![<?php echo htmlspecialchars($file['description']); ?>](<?php echo htmlspecialchars($file['url']); ?>)</code></td>
						<td class="text-nowrap align-middle"><a href="#" class="fileInsertBtn">挿入する</a></td>
					</tr>
					</table>

					<?php
						echo '<form class="fileEdit d-none border p-3">';
						echo '<div class="updateStatus"></div>';
						echo '<div class="mb-3">';
						echo '<label for="eachDescription_'.$fileId.'" class="form-label">説明</label>';
						echo '<textarea name="eachDescription_'.$fileId.'" id="eachDescription_'.$fileId.'" class="eachDescription form-control" aria-describedby="eachDescriptionHelp" data-file-id="'.$fileId.'" data-csrf_token="">'.htmlspecialchars($file['description']).'</textarea>';
						echo '</div>';
						echo '<div class="d-flex justify-content-end">';
						echo '<button type="submit" class="btn btn-primary">更新する</button>';
						echo '</form>';
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div> <!-- /table-responsive -->

<!-- Modal -->
<div class="modal fade" id="expandedImageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="imageModalLabel">画像の拡大</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body text-center">
				<img id="modalImage" src="" class="img-fluid" alt="Enlarged Image">
			</div>
		</div>
	</div>
</div>
