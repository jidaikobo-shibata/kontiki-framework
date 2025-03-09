<?php

namespace Jidaikobo\Kontiki\Models\BaseModelTraits;

/**
 * Trait FieldDefinitionTrait
 *
 * Provides methods for defining and managing field definitions in models.
 */
trait FieldDefinitionTrait
{
    protected ?array $fieldDefinitions = null;

    abstract function setFieldDefinitions(array $params = []): void;

    /**
     * Get the field definitions.
     *
     * This method must be implemented by the class using this trait.
     *
     * @param array $params Optional parameters for dynamic adjustments.
     * @return array Field definitions where each key represents a field name
     *               and its value contains field metadata.
     */
    public function getFieldDefinitions(array $params = []): array
    {
        if ($this->fieldDefinitions === null) {
            $this->setFieldDefinitions($params);
        }
        return $this->fieldDefinitions;
    }

    /**
     * Get the metadata field definitions.
     *
     * This method allows models to define additional metadata fields.
     *
     * @param array $params Optional parameters for dynamic adjustments.
     * @return array Metadata field definitions.
     */
    public function getMetaDataFieldDefinitions(array $params = []): array
    {
        return [];
    }

    /**
     * Get field definitions with default values.
     *
     * This method populates field definitions with default values
     * extracted from the provided data.
     *
     * @param array $data Associative array where keys are field names
     *                    and values are the default values to be set.
     * @return array Updated field definitions with default values applied.
     */
    public function getFieldDefinitionsWithDefaults(array $data): array
    {
        $fields = $this->getFieldDefinitions();

        foreach ($fields as $fieldName => &$field) {
            if (isset($data[$fieldName])) {
                $field['default'] = $data[$fieldName];
            }
        }

        return $fields;
    }

    /**
     * Process field definitions before saving.
     *
     * This method allows dynamic modifications of field definitions
     * based on the provided context before saving.
     *
     * @param string $context The context in which the data is being saved.
     * @param array  $fieldDefinitions The current field definitions.
     * @return array Processed field definitions ready for saving.
     */
    public function processFieldDefinitionsForSave(
        string $context,
        array $fieldDefinitions
    ): array {
        return $fieldDefinitions;
    }


    /**
     * Get the definition for Id field.
     *
     * wrapper of read-only field.
     *
     */
    protected function getIdField(): array
    {
        return $this->getReadOnlyField(
            __('ID'),
            [
                'display_in_list' => true
            ]
        );
    }

    /**
     * Get the definition for a read-only field.
     *
     * This method defines the structure of a field that is displayed in lists
     * but not included in form inputs.
     *
     * @param string $label The label to be used for the field.
     * @param array $options Optional settings for the field.
     * @return array The read-only field definition.
     */
    protected function getReadOnlyField(string $label, array $options = []): array
    {
        return [
            'label' => $label,
            'display_in_list' => $options['display_in_list'] ?? false,
            'save_as_utc' => $options['save_as_utc'] ?? false,
        ];
    }

    /**
     * Generate a text field definition.
     *
     * @param string $label The field name.
     * @param array $options Optional settings for the field.
     * @return array Field definition.
     */
    protected function getField(string $label, array $options = []): array
    {
        return [
            'label' => $options['label'] ?? __($label),
            'type' => $options['type'] ?? 'text',
            'description' => $options['description'] ?? '',
            'attributes' => $options['attributes'] ?? ['class' => 'form-control'],
            'label_attributes' => $options['label_attributes'] ?? ['class' => 'form-label'],
            'options' => $options['options'] ?? [],
            'default' => $options['default'] ?? '',
            'searchable' => $options['searchable'] ?? true,
            'rules' => $options['rules'] ?? [],
            'filter' => $options['filter'] ?? (defined('FILTER_SANITIZE_FULL_SPECIAL_CHARS')
                ? FILTER_SANITIZE_FULL_SPECIAL_CHARS
                : FILTER_SANITIZE_SPECIAL_CHARS),
            'template' => $options['template'] ?? 'default',
            'group' => $options['group'] ?? 'main',
            'fieldset_template' => $options['fieldset_template'] ?? 'forms/fieldset/flat.php',
            'display_in_list' => $options['display_in_list'] ?? false,
            'save_as_utc' => $options['save_as_utc'] ?? false
        ];
    }
}
