<?php

namespace Jidaikobo\Kontiki\Models;

use Illuminate\Database\Connection;
use Jidaikobo\Kontiki\Services\ValidationService;
use Jidaikobo\Kontiki\Models\BaseModelTraits;

/**
 * BaseModel provides common CRUD operations for database interactions.
 * Extend this class to create specific models for different database tables.
 */
abstract class BaseModel implements ModelInterface
{
    use BaseModelTraits\CRUDTrait;
    use BaseModelTraits\FieldDefinitionTrait;
    use BaseModelTraits\SearchTrait;
    use BaseModelTraits\UtilsTrait;
    use BaseModelTraits\ValidateTrait;

    protected Connection $db;
    protected ValidationService $validationService;
    protected string $table;
    protected string $deleteType = 'hardDelete';

    public function __construct(Connection $db, ValidationService $validationService)
    {
        $this->db = $db;
        $this->validationService = $validationService;
    }

    public function getDeleteType(): string
    {
        return $this->deleteType;
    }

    public function getTableName(): string
    {
        return $this->table;
    }
}
