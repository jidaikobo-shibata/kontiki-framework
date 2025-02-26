<?php

namespace Jidaikobo\Kontiki\Models;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class CategoryModel extends BaseModel
{
    use Traits\CRUDTrait;
    use Traits\MetaDataTrait;
    use Traits\IndexTrait;

    protected string $table = 'terms';

    public function getDisplayFields(): array
    {
        return ['term_id', 'name', 'slug'];
    }

    public function getFieldDefinitions(array $params = []): array
    {
        $id = 1;
        $fields = [
            'id' => $this->getIdField(),
            'name' => $this->getTextField('name', ['required']),
            'slug' => $this->getSlugField($id),
            'parent_id' => $this->getSelectField('parent', []),
            'term_order' => $this->getTextField('order'),
        ];

        return array_merge($fields, $this->getMetaDataFieldDefinitions($params));
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
}
