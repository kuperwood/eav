<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Interface;

use Drobotik\Eav\Result\Result;

interface TransportDriverInterface
{
    public function getHeader() : array;
    public function getChunk() : array|null;
    public function readAll() : array;
    public function writeAll(array $data): Result;
}