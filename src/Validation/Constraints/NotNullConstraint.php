<?php

namespace Drobotik\Eav\Validation\Constraints;

use Drobotik\Eav\Interface\ConstraintInterface;

class NotNullConstraint implements ConstraintInterface
{
    public function validate($value): ?string
    {
        if ($value === null) {
            return "This value cannot be null.";
        }
        return null;
    }
}
