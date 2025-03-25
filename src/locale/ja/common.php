<?php

return [
    // common
    'login'    => 'ログイン',
    'logout'   => 'ログアウト',
    'username' => 'ユーザ名',
    'password' => 'パスワード',
    'account' => 'アカウント',
    'account_settings' => 'アカウント設定',

    'submit' => '送信する',
    'create' => '作成する',
    'edit' => '編集する',
    'preview' => 'プレビューする',
    'published_url' => '公開URL',
    'reserved_url' => '公開予定URL',
    'banned_url' => '無効なURL',
    'edit_this_content' => 'このページを編集する',
    'update' => '更新する',
    'trash' => 'ごみ箱',
    'to_trash' => 'ごみ箱へ',
    'restore' => '復活する',
    'delete' => '削除する',
    'close'  => '閉じる',
    'open'   => '開く',
    'do_publish' => '公開する',
    'do_reserve' => '公開予約する',
    'do_save_as_pending' => '非公開状態で保存する',
    'do_save_as_draft' => '下書き状態で保存する',

    'value' => '値',
    'title' => 'タイトル',
    'content' => '内容',
    'required' => '必須',
    'description' => '説明',
    'search_str' => '検索文字',
    'search' => '検索する',
    'actions' => '操作',
    'creator' => '作成者',
    'created_at' => '作成日時',
    'updated_at' => '更新日時',
    'deleted_at' => '削除日時',
    'excerpt' => '抜粋',
    'eyecatch' => '代表画像',
    'open_in_new_window' => '新しいウィンドウ/タブで開く',

    'reserved_at' => '公開予約日時',
    'published_at' => '公開日時',
    'published_at_exp' => '未来の日時を入れると予約投稿になります。',
    'expired_at' => '公開終了日時',
    'expired_at_exp' => '未設定の場合は永続的に公開されます。',
    'status' => 'ステータス',
    'index' => 'すべて',
    'draft' => '下書き',
    'reserved' => '予約',
    'expired' => '公開終了',
    'publishing' => '公開',
    'published' => '公開中',
    'pending' => '非公開',
    'parent' => '親',
    'publishing_settings' => '公開設定',
    'publishing_settings_exp' => '「公開終了日時」と「公開予約日時」で、記事の表示終了日と、公開予約日が設定できます。',
    'display_filter' => '表示フィルタ',
    'management_portal' => '管理項目一覧',

    // placeholders
    'x_management' => ':nameの管理',
    'x_index' => ':nameの一覧',
    'x_index_all' => ':nameの一覧',
    'x_index_published' => '公開中の:name',
    'x_index_pending' => '非公開の:name',
    'x_index_draft' => '下書き中の:name',
    'x_index_reserved' => '予約中の:name',
    'x_index_expired' => '期限切れした:name',
    'x_index_trash' => 'ごみ箱（:name）',
    'x_index_taxonomy' => ':nameの分類',
    'x_create' => ':nameの作成',
    'x_edit' => ':nameの編集',
    'x_save' => ':nameを保存する',
    'x_save_success' => ':nameを保存しました',
    'x_save_success_and_redirect' => ':nameを保存しました。[一覧を表示する](:url)',
    'x_delete' => ':nameを完全に削除',
    "x_delete_confirm" => "本当に:nameを完全に削除していいですか？",
    "x_delete_success" => ":nameを完全に削除しました",
    "x_delete_failed" => ":nameの完全な削除に失敗しました",
    'x_trash' => ':nameをごみ箱に',
    "x_trash_confirm" => "本当に:nameをごみ箱に入れていいですか？",
    "x_trash_success" => ":nameをごみ箱に入れました",
    "x_trash_failed" => ":nameをごみ箱に入れることに失敗しました",
    'x_restore' => ':nameを復元',
    "x_restore_confirm" => "本当に:nameを復元していいですか？",
    "x_restore_success" => ":nameを復元しました",
    "x_restore_failed" => ":nameの復元に失敗しました",
    'error_at_x' => '「:name」にエラーがあります',

    // messages
    'found_the_problem' => '問題が見つかりました',
    'is_already_exists' => 'はすでに存在します',
    'wrong_username_or_password' => 'ユーザ名かパスワードが間違っています',
    'database_error' => 'データベースでエラーがありました',
    'confirm_delete_message' => '項目は完全に削除されます。本当にこの項目を削除してもいいですか？',
    '404_text' => 'ページが見つかりませんでした。',
    'csrf_invalid' => 'セッションの有効期限が切れた可能性があります。再度操作を行ってください',
    'cannot_preview_title' => 'プレビューできません',
    'cannot_preview_desc' => 'プレビューは再読込できません。プレビューウィンドウを閉じて、再度、プレビューしてください。',
];
