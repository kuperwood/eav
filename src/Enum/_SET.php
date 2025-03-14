<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Enum;

use Drobotik\Eav\Interfaces\DefineTableInterface;

class _SET implements DefineTableInterface
{
    public const ID = 'set_id';
    public const DOMAIN_ID = 'domain_id';
    public const NAME = 'name';

    public static function table() : string
    {
        return 'eav_attribute_sets';
    }
}
