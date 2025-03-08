<?php

namespace Jidaikobo\Kontiki\Models;

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Jidaikobo\Kontiki\Services\AuthService;

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
    protected AuthService $authService;

    public function __construct(Connection $db, AuthService $authService)
    {
        parent::__construct($db);
        $this->authService = $authService;
    }

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
        $user = $this->authService->getCurrentUser();
        $id = $params['id'] ?? null;
        $parentOptions = $this->getOptions('title', true, '', $id);
        $now = Carbon::now(env('TIMEZONE', 'UTC'))->format('Y-m-d H:i');

        // env
        $hide_parent = env('POST_HIDE_PARENT', false);
        $hide_author = env('POST_HIDE_AUTHOR', false);

        // description
        $content_exp = __('content_exp', 'Please enter the content in <a href="' . env('BASEPATH') . '/posts/markdown-help" target="markdown-help">Markdown format</a>. You can add files using "File Upload".');
        $slug_exp = __('slug_exp', 'The "slug" is used as the URL. It can contain alphanumeric characters and hyphens.');

        $fields = [
            'id' => $this->getReadOnlyField('ID'),

            'title' => $this->getField(
                'title',
                [
                    'rules' => ['required']
                ]
            ),

            'content' => $this->getField(
                __('content'),
                [
                    'type' => 'textarea',
                    'description' => $content_exp,
                    'attributes' => [
                        'class' => 'form-control font-monospace kontiki-file-upload',
                        'data-button-class' => 'mt-2',
                        'rows' => '10'
                    ]
                ]
            ),

            'slug' => $this->getField(
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
            ),

            'parent_id' => $this->getField(
                'parent',
                [
//                    'type' => 'select',
                    'type' => 'hidden',
                    'options' => $parentOptions,
                    'attributes' => [
                        'class' => 'form-control form-select'
                    ],
                    'group' => 'meta',
                ]
            ),

            'published_at' => $this->getField(
                'published_at',
                [
                    'type' => 'datetime-local',
                    'description' => __('published_at_exp'),
                    'default' => __($now),
                    'group' => 'meta',
                ]
            ),

            'expired_at' => $this->getField(
                'expired_at',
                [
                    'type' => 'datetime-local',
                    'description' => __('expired_at_exp'),
                    'group' => 'meta',
                ]
            ),

            'status' => $this->getField(
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
                    'group' => 'meta',
                ]
            ),

            'creator_id' => $this->getField(
                'creator',
                [
//                    'type' => 'select',
                    'type' => 'hidden',
                    'options' => $userOptions,
                    'default' => $user['id'],
                    'attributes' => [
                        'class' => 'form-control form-select'
                    ],
                    'group' => 'meta',
                ]
            ),

            'created_at' => $this->getReadOnlyField(__('created_at', 'Created')),
        ];

        return array_merge($fields, $this->getMetaDataFieldDefinitions($params));
    }

    public function getMetaDataFieldDefinitions(array $params = []): array
    {
        $fields = [];

        $hide_excerpt = env('POST_HIDE_METADATA_EXCERPT', false);
        $hide_eyecatch = env('POST_HIDE_METADATA_EYECATCH', false);

        if (!$hide_excerpt) {
            $fields['excerpt'] = $this->getContentField(
                __('excerpt'),
                [
                    'type' => 'textarea',
                    'description' => $content_exp,
                    'attributes' => [
                        'class' => 'form-control font-monospace',
                        'data-button-class' => 'mt-2',
                        'rows' => '3'
                    ]
                ]
            );
        }

        if (!$hide_eyecatch) {
            $fields['eyecatch'] = $this->getTextField(
                __('eyecatch'),
                [
                    'attributes' => [
                        'class' => 'form-control font-monospace kontiki-file-upload',
                    ]
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
