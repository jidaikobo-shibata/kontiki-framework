<?php

namespace Jidaikobo\Kontiki\Models;

interface ModelInterface
{
    public function getFields(): array;
    public function getFieldDefinitions(): array;
    public function getMetaDataFieldDefinitions(): array;
}
