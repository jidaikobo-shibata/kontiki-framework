<?php

namespace Jidaikobo\Kontiki\Models;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\BaseModelTraits;
use Jidaikobo\Kontiki\Validation\ValidatorInterface;

/**
 * BaseModel provides common CRUD operations for database interactions.
 * Extend this class to create specific models for different database tables.
 */
abstract class BaseModel implements ModelInterface
{
    use BaseModelTraits\FieldDefinitionTrait;
    use BaseModelTraits\SearchTrait;
    use BaseModelTraits\UtilsTrait;

    protected string $table;
    protected string $postType = '';
    protected string $deleteType = 'hardDelete';
    protected Connection $db;
    public ?ValidatorInterface $validator = null;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
        $this->initializeFields();
        $this->initializeMetaDataFields();
    }

    public function getQuery(): Builder
    {
        return $this->db->table($this->table);
    }

    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
        $this->validator->setModel($this);
    }

    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    public function getDeleteType(): string
    {
        return $this->deleteType;
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public function getPostType(): string
    {
        return $this->postType;
    }
}
