<?php

namespace Jidaikobo\Kontiki\Models\BaseModelTraits;

trait FieldDefinitionTrait
{
    public function getDisplayFields(): array
    {
        return [];
    }

    public function getUtcFields(): array
    {
        return [];
    }

    /**
     * Get the field definitions.
     *
     * @param array $params Optional parameters for dynamic adjustments.
     * @return array Field definitions.
     */
    abstract public function getFieldDefinitions(array $params = []): array;

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

    public function processFieldDefinitionsForSave(string $context, array $fieldDefinitions): array
    {
        return $fieldDefinitions;
    }
}
