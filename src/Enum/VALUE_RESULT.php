<?php

namespace Kuperwood\Eav\Enum;

enum VALUE_RESULT
{
    case CREATED;
    case UPDATED;
    case DELETED;
    case NOT_DELETED;
    case FOUND;
    case NOT_FOUND;
    case NOT_ENOUGH_ARGS;
    case NOT_ALLOWED;
    case EMPTY;

    public function code(): int
    {
        return match ($this) {
            self::CREATED => 1,
            self::UPDATED => 2,
            self::FOUND => 3,
            self::NOT_FOUND => 4,
            self::NOT_ENOUGH_ARGS => 5,
            self::NOT_ALLOWED => 6,
            self::EMPTY => 7,
            self::DELETED => 8,
            self::NOT_DELETED => 9,
        };
    }

    public function message(): string
    {
        return match ($this) {
            self::CREATED => "Created",
            self::UPDATED => "Updated",
            self::FOUND => "Found",
            self::NOT_FOUND => "Not found",
            self::NOT_ENOUGH_ARGS => "Not enough arguments",
            self::NOT_ALLOWED => "Not allowed",
            self::EMPTY => "Nothing to perform",
            self::DELETED => "Deleted",
            self::NOT_DELETED => "Not deleted",
        };
    }
}