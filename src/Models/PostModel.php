<?php

namespace Jidaikobo\Kontiki\Models;

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

use Jidaikobo\Kontiki\Core\Auth;
use Jidaikobo\Kontiki\Core\Database;

class PostModel extends BaseModel
{
    use Traits\CRUDTrait;
    use Traits\MetaDataTrait;
    use Traits\IndexTrait;
    use Traits\SoftDeleteTrait;
    use Traits\PublishedTrait;
    use Traits\DraftTrait;
    use Traits\ExpiredTrait;
    use Traits\TaxonomyTrait;

    protected string $table = 'posts';
    protected string $postType = 'post';
    protected string $deleteType = 'softDelete';

    private UserModel $userModel;
    private Auth $auth;

    public function __construct(
        Database $db,
        Auth $auth,
        UserModel $userModel
    ) {
        $this->db = $db->getConnection();
        $this->userModel = $userModel;
        $this->auth = $auth;
    }

    public function setFieldDefinitions(array $params = []): void
    {
        // defaults
        $id = $params['id'] ?? null;

        $fields = [
            'id' => $this->getIdField(),
            'title' => $this->getTitleField(),
            'content' => $this->getContentField(__('content')),
            'slug' => $this->getSlugField($id),
            'parent_id' => $this->getParentIdField($id),
            'published_at' => $this->getPublishedAtField(),
            'expired_at' => $this->getExpiredAtField(),
            'status' => $this->getStatusField(),
            'creator_id' => $this->getCreatorIdField(),
            'created_at' => $this->getCreatedAtField(),
            'updated_at' => $this->getUpdatedAtField(),
        ];
        $MetaData = $this->getMetaDataFieldDefinitions($params);
        $this->fieldDefinitions = array_merge($fields, $MetaData);
    }

    private function getTitleField(): array
    {
        return $this->getField(
            __('title'),
            [
                'rules' => ['required'],
                'display_in_list' => true
            ]
        );
    }

    private function getContentField(string $label): array
    {
        $content_exp = __('content_exp', 'Please enter the content in <a href="' . env('BASEPATH') . '/posts/markdown-help" target="markdown-help">Markdown format</a>. You can add files using "File Upload".');
        return $this->getField(
            $label,
            [
                'type' => 'textarea',
                'description' => $content_exp,
                'attributes' => [
                    'class' => 'form-control font-monospace kontiki-file-upload',
                    'data-button-class' => 'mt-2',
                    'rows' => '10'
                ]
            ]
        );
    }

    private function getSlugField(?int $id): array
    {
        $slug_exp = __('slug_exp', 'The "slug" is used as the URL. It can contain alphanumeric characters and hyphens.');

        return $this->getField(
            __('slug'),
            [
                'description' => $slug_exp,
                'rules' => [
                    'required',
                    'slug',
                    ['lengthMin', 3],
                    ['unique', $this->table, 'slug', $id]
                ],
            ]
        );
    }

    private function getParentIdField(?int $id): array
    {
        $parentOptions = $this->getOptions('title', true, '', $id);
        return $this->getField(
            'parent',
            [
                'type' => env('POST_HIDE_PARENT', false) ? 'hidden' : 'select',
                'options' => $parentOptions,
                'attributes' => [
                    'class' => 'form-control form-select'
                ],
                'group' => 'meta',
            ]
        );
    }

    private function getPublishedAtField(): array
    {
        $now = Carbon::now(env('TIMEZONE', 'UTC'))->format('Y-m-d H:i');
        return $this->getField(
            'published_at',
            [
                'type' => 'datetime-local',
                'description' => __('published_at_exp'),
                'default' => $now,
                'group' => 'meta',
                'save_as_utc' => true
            ]
        );
    }

    private function getExpiredAtField(): array
    {
        return $this->getField(
            'expired_at',
            [
                'type' => 'datetime-local',
                'description' => __('expired_at_exp'),
                'group' => 'meta',
                'save_as_utc' => true
            ]
        );
    }

    private function getStatusField(): array
    {
        return $this->getField(
            'status',
            [
                'type' => 'select',
                'options' => [
                    'draft' => __('draft'),
                    'published' => __('published'),
                    'pending' => __('pending'),
                ],
                'attributes' => [
                    'class' => 'form-control form-select'
                ],
                'display_in_list' => true,
                'group' => 'meta',
            ]
        );
    }

    private function getCreatorIdField(): array
    {
        $userOptions = $this->userModel->getOptions('username');
        $user = $this->auth->getCurrentUser();
        return $this->getField(
            'creator',
            [
                    'type' => env('POST_HIDE_AUTHOR', false) ? 'hidden' : 'select',
                    'options' => $userOptions,
                    'default' => $user['id'],
                    'attributes' => [
                        'class' => 'form-control form-select'
                    ],
                    'group' => 'meta',
                ]
        );
    }

    private function getCreatedAtField(): array
    {
        return $this->getReadOnlyField(
            __('created_at', 'Created'),
            [
                'display_in_list' => true,
                'save_as_utc' => true
            ]
        );
    }

    private function getUpdatedAtField(): array
    {
        return $this->getReadOnlyField(
            __('updated_at', 'Updated'),
            [
                'save_as_utc' => true
            ]
        );
    }

    public function getMetaDataFieldDefinitions(array $params = []): array
    {
        $fields = [];

        $hide_excerpt = env('POST_HIDE_METADATA_EXCERPT', false);
        $hide_eyecatch = env('POST_HIDE_METADATA_EYECATCH', false);

        if (!$hide_excerpt) {
            $fields['excerpt'] = $this->getField(
                __('excerpt'),
                [
                    'type' => 'textarea',
                    'attributes' => [
                        'class' => 'form-control',
                        'row' => 3,
                    ]
                ]
            );
        }

        if (!$hide_eyecatch) {
            $fields['eyecatch'] = $this->getField(
                __('eyecatch'),
                [
                    'attributes' => [
                        'class' => 'form-control font-monospace kontiki-file-upload',
                    ],
                    'fieldset_template' => 'forms/fieldset/input-group.php',
                ]
            );
        }

        return $fields;
    }

    public function getTaxonomyDefinitions(array $params = []): array
    {
        $taxonomies = [
            'category' => [
                'label' => __('category'),
                'Model' => 'CategoryModel',
            ],
        ];
        return $taxonomies;
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
