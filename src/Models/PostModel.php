<?php

namespace Jidaikobo\Kontiki\Models;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Jidaikobo\Kontiki\Services\AuthService;
use Jidaikobo\Kontiki\Services\ValidationService;

class PostModel extends BaseModel
{
    use Traits\SoftDeleteTrait;
    use Traits\PublishedTrait;
    use Traits\ExpiredTrait;

    protected string $table = 'posts';
    protected string $deleteType = 'softDelete';
    private AuthService $authService;

    public function __construct(Connection $db, ValidationService $validationService, AuthService $authService)
    {
        parent::__construct($db, $validationService);
        $this->authService = $authService;
    }

    public function getDisplayFields(): array
    {
        return ['id', 'title', 'slug', 'created_at'];
    }

    public function getFieldDefinitions(array $params = []): array
    {
        $userModel = new UserModel($this->db, $this->validationService);
        $userOptions = $userModel->getOptions('username');
        $user = $this->authService->getCurrentUser();

        $id = $params['id'] ?? null;

        return [
            'id' => [
                'label' => 'ID',
            ],
            'title' => [
                'label' => __('title'),
                'type' => 'text',
                'attributes' => ['class' => 'form-control'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => true,
                'rules' => ['required'],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'content' => [
                'label' => __('content'),
                'description' => '',
                'type' => 'textarea',
                'attributes' => [
                    'class' => 'form-control',
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
            ],
            'slug' => [
                'label' => __('slug'),
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
            ],
            'published_at' => [
                'label' => __('published_at'),
                'type' => 'datetime-local',
                'attributes' => ['class' => 'form-control'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => date('Y-m-d\TH:i', time()),
                'searchable' => true,
                'rules' => ['required'],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'expired_at' => [
                'label' => __('expired_at'),
                'type' => 'datetime-local',
                'attributes' => ['class' => 'form-control'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => true,
                'rules' => [],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'is_draft' => [
                'label' => __('draft'),
                'type' => 'select',
                'options' => [0 => 'published', 1 => 'draft'],
                'attributes' => ['class' => 'form-control form-select'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => true,
//                'rules' => ['required'], // violates the rules of HTML
                'rules' => [],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'creator_id' => [
                'label' => __('creator'),
                'type' => 'select',
                'options' => $userOptions,
                'attributes' => ['class' => 'form-control form-select'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => $user['id'],
                'searchable' => true,
//                'rules' => ['required'], // violates the rules of HTML
                'rules' => [],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'created_at' => [
                'label' => __('created_at', 'Created'),
            ],
        ];
    }

    public function getAdditionalConditions(Builder $query, string $context = 'normal'): Builder
    {
        if ($context === 'normal') {
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
        // jlog($query->getBindings());

        return $query;
    }

    private function applyDraftConditions(Builder $query): Builder
    {
        return $query->where('is_draft', '=', 1);
    }

    private function applyNotDraftConditions(Builder $query): Builder
    {
        return $query->where('is_draft', '=', 0);
    }
}
