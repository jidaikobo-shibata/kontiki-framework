<?php

namespace Jidaikobo\Kontiki\Models\BaseModelTraits;

use Jidaikobo\Kontiki\Services\ValidationService;

trait ValidateTrait
{
    /**
     * Validate the given data against the field definitions.
     *
     * @param  array $data The data to validate.
     * @param  array $fieldDefinitions The field definitions.
     *
     * @return array An array with 'valid' (bool) and 'errors' (array of errors).
     */
    public function validateByFields(array $data, array $fieldDefinitions): array
    {
        $validationService = new ValidationService($this->db);
        return $validationService->validate($data, $fieldDefinitions);
    }
}
