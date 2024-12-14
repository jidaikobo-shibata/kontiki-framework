<?php

namespace jidaikobo\kontiki\Models;

interface ModelInterface
{
    /**
     * Get field definitions for the model.
     *
     * @return array The field definitions.
     */
    public function getFieldDefinitions(): array;
}
