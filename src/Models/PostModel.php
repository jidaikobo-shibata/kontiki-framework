<?php

namespace Jidaikobo\Kontiki\Models;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Database\DatabaseHandler;
use Jidaikobo\Kontiki\Services\ValidationService;

class PostModel extends BaseModel
{
    protected string $table = 'posts';
    private Session $session;

    public function __construct(DatabaseHandler $db, ValidationService $validationService, Session $session)
    {
        parent::__construct($db, $validationService);
        $this->session = $session;
    }

    public function getDisplayFields(): array
    {
        return ['id', 'title', 'slug', 'created_at'];
    }

    public function getFieldDefinitions(array $params = []): array
    {
        $userModel = new UserModel($this->db, $this->validationService);
        $segment = $this->session->getSegment('jidaikobo\kontiki\auth');
        $user = $segment->get('user');

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
                  ['lengthMin', 3]
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
                'options' => $userModel->getOptions('username'),
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
}
