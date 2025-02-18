<?php

namespace Jidaikobo\Kontiki\Models;

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Jidaikobo\Kontiki\Services\AuthService;
use Jidaikobo\Kontiki\Services\ValidationService;

class PostModel extends BaseModel
{
    use Traits\SoftDeleteTrait;
    use Traits\PublishedTrait;
    use Traits\DraftTrait;
    use Traits\ExpiredTrait;

    protected string $postType = 'post';
    protected string $deleteType = 'softDelete';
    private AuthService $authService;

    public function __construct(Connection $db, ValidationService $validationService, AuthService $authService)
    {
        parent::__construct($db, $validationService);
        $this->authService = $authService;
    }

    public function getDisplayFields(): array
    {
        return ['id', 'title', 'slug', 'created_at', 'status'];
    }

    protected function getUtcFields(): array
    {
        return ['published_at', 'expired_at', 'created_at', 'updated_at'];
    }

    public function getFieldDefinitions(array $params = []): array
    {
        // Author
        $userModel = new UserModel($this->db, $this->validationService);
        $userOptions = $userModel->getOptions('username');
        $user = $this->authService->getCurrentUser();

        // unique check
        $id = $params['id'] ?? null;

        // parent_id
        $parentOptions = $this->getOptions('title', TRUE, '', $id);

        // current
        $now = Carbon::now(env('TIMEZONE', 'UTC'));

        return [
            'id' => $this->getIdField(),
            'title' => $this->getTextField('title', ['required']),
            'content' => $this->getContentField(),
            'slug' => $this->getSlugField($id),
            'parent_id' => $this->getSelectField('parent', $parentOptions),
            'published_at' => $this->getDateTimeField('published_at', $now, ['required']),
            'expired_at' => $this->getDateTimeField('expired_at'),
            'status' => $this->getStatusField(),
            'creator_id' => $this->getSelectField('creator', $userOptions, $user['id']),
            'created_at' => $this->getIdField(__('created_at', 'Created')),
        ];
    }

    private function getIdField(string $label = 'ID'): array
    {
        return ['label' => $label];
    }

    private function getTextField(string $name, array $rules = []): array
    {
        return [
            'label' => __($name),
            'type' => 'text',
            'attributes' => ['class' => 'form-control'],
            'label_attributes' => ['class' => 'form-label'],
            'default' => '',
            'searchable' => true,
            'rules' => $rules,
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'main',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    private function getContentField(): array
    {
        return [
            'label' => __('content'),
            'description' => __('content_exp', 'Please enter the content in <a href="' . env('BASEPATH') . '/admin/posts/markdown-help" target="markdown-help">Markdown format</a>. You can add files using "File Upload".'),
            'type' => 'textarea',
            'attributes' => [
                'class' => 'form-control font-monospace kontiki-file-upload',
                'data-button-class' => 'mt-2',
                'rows' => '10'
            ],
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
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'main',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    private function getSelectField(string $name, array $options, $default = ''): array
    {
        return [
            'label' => __($name),
            'type' => 'select',
            'options' => $options,
            'attributes' => ['class' => 'form-control form-select'],
            'label_attributes' => ['class' => 'form-label'],
            'default' => $default,
            'searchable' => true,
            'rules' => [],
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'main',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    private function getDateTimeField(string $name, $default = '', array $rules = []): array
    {
        return [
            'label' => __($name),
            'type' => 'datetime-local',
            'attributes' => ['class' => 'form-control'],
            'label_attributes' => ['class' => 'form-label'],
            'default' => $default,
            'searchable' => true,
            'rules' => $rules,
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'main',
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
            'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'template' => 'default',
            'group' => 'main',
            'fieldset_template' => 'forms/fieldset/flat.php',
        ];
    }

    public function getAdditionalConditions(Builder $query, string $context = 'all'): Builder
    {
        if ($context === 'all') {
            $query = $this->applyNotSoftDeletedConditions($query);
        } elseif ($context === 'published') {
            $query = $this->applyNotSoftDeletedConditions($query);
            $query = $this->applyNotExpiredConditions($query);
            $query = $this->applyPublisedConditions($query);
            $query = $this->applyNotDraftConditions($query);
        } elseif ($context === 'trash') {
            $query = $this->applySoftDeletedConditions($query);
        } elseif ($context === 'reserved') {
            $query = $this->applyNotPublisedConditions($query);
        } elseif ($context === 'expired') {
            $query = $this->applyExpiredConditions($query);
        } elseif ($context === 'draft') {
            $query = $this->applyDraftConditions($query);
        }

        // jlog($context);
        // jlog($query->toSql());
        // jlog($query->getBindings());

        return $query;
    }
}
