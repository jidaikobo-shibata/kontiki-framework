<?php

namespace Jidaikobo\Kontiki\Models;

interface ModelInterface
{
    public function getTableName(): string;
    public function getFieldDefinitions(): array;
}
