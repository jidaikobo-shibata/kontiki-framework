<?php

namespace Jidaikobo\Kontiki\Models;

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Jidaikobo\Kontiki\Services\AuthService;

class PostModel extends BaseModel
{
    use Traits\CRUDTrait;
    use Traits\PostMetaTrait;
    use Traits\IndexTrait;
    use Traits\SoftDeleteTrait;
    use Traits\PublishedTrait;
    use Traits\DraftTrait;
    use Traits\ExpiredTrait;

    protected string $table = 'posts';
    protected string $postType = 'post';
    protected string $deleteType = 'softDelete';

    public function getDisplayFields(): array
    {
        return ['id', 'title', 'slug', 'created_at', 'status'];
    }

    public function getUtcFields(): array
    {
        return ['published_at', 'expired_at', 'created_at', 'updated_at'];
    }

    public function getFieldDefinitions(array $params = []): array
    {
        // defaults
        $userModel = new UserModel($this->db);
        $userOptions = $userModel->getOptions('username');
//        $user = $this->authService->getCurrentUser();
        $user = ['id' => 1];
        $id = $params['id'] ?? null;
        $parentOptions = $this->getOptions('title', true, '', $id);
        $now = Carbon::now(env('TIMEZONE', 'UTC'))->format('Y-m-d H:i');

        // env
        $hide_parent = env('POST_HIDE_PARENT', false);
        $hide_author = env('POST_HIDE_AUTHOR', false);

        $content_exp = __('content_exp', 'Please enter the content in <a href="' . env('BASEPATH') . '/admin/posts/markdown-help" target="markdown-help">Markdown format</a>. You can add files using "File Upload".');

        $fields = [
            'id' => $this->getIdField(),
            'title' => $this->getTextField('title', ['required']),
            'content' => $this->getContentField(
                __('content'),
                $content_exp,
                [
                    'class' => 'form-control font-monospace kontiki-file-upload',
                    'data-button-class' => 'mt-2',
                    'rows' => '10'
                ]
            ),
            'slug' => $this->getSlugField($id),
            'parent_id' => $this->getSelectField('parent', $parentOptions, '', $hide_parent),
            'published_at' => $this->getDateTimeField('published_at', 'published_at_exp', $now),
            'expired_at' => $this->getDateTimeField('expired_at', 'expired_at_exp'),
            'status' => $this->getStatusField(),
            'creator_id' => $this->getSelectField('creator', $userOptions, $user['id'], $hide_author),
            'created_at' => $this->getIdField(__('created_at', 'Created')),
        ];

        return array_merge($fields, $this->getPostMetaFieldDefinitions($params));
    }

    public function getPostMetaFieldDefinitions(array $params = []): array
    {
        $fields = [];

        $hide_excerpt = env('POST_HIDE_POSTMETA_EXCERPT', false);
        $hide_eyecatch = env('POST_HIDE_POSTMETA_EYECATCH', false);

        if (!$hide_excerpt) {
            $fields['excerpt'] = $this->getContentField(
                __('excerpt'),
                '',
                [
                    'class' => 'form-control font-monospace',
                    'data-button-class' => 'mt-2',
                    'rows' => '3'
                ]
            );
        }

        if (!$hide_eyecatch) {
            $fields['eyecatch'] = $this->getContentField(
                __('excerpt'),
                '',
                [
                    'class' => 'form-control font-monospace',
                    'data-button-class' => 'mt-2',
                    'rows' => '3'
                ]
            );
        }

        return $fields;
    }

    private function getIdField(string $label = 'ID'): array
    {
        return ['label' => $label];
    }

    private function getTextField(
        string $name,
        array $rules = [],
        array $attributes = ['class' => 'form-control'],
        string $fieldset_template = 'forms/fieldset/flat.php',
    ): array {
        return [
            'label' => __($name),
            'type' => 'text',
            'attributes' => $attributes,
            'label_attributes' => ['class' => 'form-label'],
            'default' => '',
            'searchable' => true,
            'rules' => $rules,
            'filter' => defined('FILTER_SANITIZE_FULL_SPECIAL_CHARS')
                ? FILTER_SANITIZE_FULL_SPECIAL_CHARS
                : FILTER_SANITIZE_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'main',
            'fieldset_template' => $fieldset_template,
        ];
    }

    private function getContentField($label, $description = '', $attributes = []): array
    {
        return [
            'label' => $label,
            'description' => $description,
            'type' => 'textarea',
            'attributes' => $attributes,
            'label_attributes' => ['class' => 'form-label'],
            'default' => '',
            'searchable' => true,
            'rules' => [],
            'filter' => FILTER_UNSAFE_RAW,
            'template' => 'default',
            'group' => 'main',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    private function getSlugField(?int $id): array
    {
        return [
            'label' => __('slug'),
            'description' => __('slug_exp', 'The "slug" is used as the URL. It can contain alphanumeric characters and hyphens.'),
            'type' => 'text',
            'attributes' => ['class' => 'form-control'],
            'label_attributes' => ['class' => 'form-label'],
            'default' => '',
            'searchable' => true,
            'rules' => [
                'required',
                'slug',
                ['lengthMin', 3],
                ['unique', $this->table, 'slug', $id]
            ],
            'filter' => defined('FILTER_SANITIZE_FULL_SPECIAL_CHARS')
                ? FILTER_SANITIZE_FULL_SPECIAL_CHARS
                : FILTER_SANITIZE_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'main',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    private function getSelectField(string $name, array $options, $default = '', $hide = false): array
    {
        $type = $hide ? 'hidden' : 'select';
        return [
            'label' => __($name),
            'type' => $type,
            'options' => $options,
            'attributes' => ['class' => 'form-control form-select'],
            'label_attributes' => ['class' => 'form-label'],
            'default' => $default,
            'searchable' => true,
            'rules' => [],
            'filter' => defined('FILTER_SANITIZE_FULL_SPECIAL_CHARS')
                ? FILTER_SANITIZE_FULL_SPECIAL_CHARS
                : FILTER_SANITIZE_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'meta',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    private function getDateTimeField(string $name, string $description = '', $default = '', array $rules = []): array
    {
        return [
            'label' => __($name),
            'description' => __($description),
            'type' => 'datetime-local',
            'attributes' => ['class' => 'form-control'],
            'label_attributes' => ['class' => 'form-label'],
            'default' => $default,
            'searchable' => true,
            'rules' => $rules,
            'filter' => defined('FILTER_SANITIZE_FULL_SPECIAL_CHARS')
                ? FILTER_SANITIZE_FULL_SPECIAL_CHARS
                : FILTER_SANITIZE_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'meta',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    private function getStatusField(): array
    {
        return [
            'label' => __('status'),
            'type' => 'select',
            'options' => [
                'draft' => __('draft'),
                'published' => __('published'),
                'pending' => __('pending'),
            ],
            'attributes' => ['class' => 'form-control form-select'],
            'label_attributes' => ['class' => 'form-label'],
            'default' => '',
            'searchable' => true,
            'rules' => [],
            'filter' => defined('FILTER_SANITIZE_FULL_SPECIAL_CHARS')
                ? FILTER_SANITIZE_FULL_SPECIAL_CHARS
                : FILTER_SANITIZE_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'meta',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    public function getAdditionalConditions(Builder $query, string $context = 'all'): Builder
    {
        $contextConditions = [
            'all'       => ['applyNotSoftDeletedConditions'],
            'published' => [
                'applyNotSoftDeletedConditions',
                'applyNotExpiredConditions',
                'applyPublisedConditions',
                'applyNotDraftConditions'
            ],
            'trash'     => ['applySoftDeletedConditions'],
            'reserved'  => ['applyNotPublisedConditions'],
            'expired'   => ['applyExpiredConditions'],
            'draft'     => ['applyDraftConditions'],
        ];

        if (isset($contextConditions[$context])) {
            foreach ($contextConditions[$context] as $method) {
                $query = $this->$method($query);
            }
        }

        // jlog($context);
        // jlog($query->toSql());
        // jlog($query->getBindings());

        return $query;
    }
}
