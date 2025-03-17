<?php

namespace Jidaikobo\Kontiki\Validation;

use Valitron\Validator;

use Jidaikobo\Kontiki\Models\ModelInterface;

interface ValidatorInterface
{
    public function setModel(ModelInterface $model): void;
    public function validate(array $data, array $context = []): array;
    public function additionalvalidate(Validator $validator, array $data, array $context): Validator;
}
