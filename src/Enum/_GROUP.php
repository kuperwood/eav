<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Enum;

use Kuperwood\Eav\Interfaces\DefineTableInterface;

class _GROUP implements DefineTableInterface
{

    public const ID = 'group_id';
    public const SET_ID = 'set_id';
    public const NAME = 'name';

    public static function table() : string
    {
        return 'eav_attribute_groups';
    }
}
