<?php

namespace Jidaikobo\Kontiki\Models;

interface ModelInterface
{
    public function getFieldDefinitions(): array;
    public function setFieldDefinitions(array $params = []): void;
}
