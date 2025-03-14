<?php

namespace Drobotik\Eav\Validation\Constraints;

use Drobotik\Eav\Interface\ConstraintInterface;

class NumericConstraint implements ConstraintInterface
{
    public function validate($value): ?string
    {
        if (!is_numeric($value)) {
            return "This value must be a number.";
        }
        return null;
    }
}