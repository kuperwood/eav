<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Doctrine\DBAL\Exception;
use Drobotik\Eav\Enum\_PIVOT;
use PDO;

class PivotModel extends Model
{
    public function __construct()
    {
        $this->setTable(_PIVOT::table());
        $this->setPrimaryKey(_PIVOT::ID->column());
    }

    /**
     * @throws Exception
     */
    public function create(array $data) : int
    {
        $conn = $this->db();
        $conn->createQueryBuilder()
            ->insert($this->getTable())
            ->values([
                _PIVOT::DOMAIN_ID->column() => '?',
                _PIVOT::SET_ID->column() => '?',
                _PIVOT::GROUP_ID->column() => '?',
                _PIVOT::ATTR_ID->column() => '?'
            ])
            ->setParameter(0, $data[_PIVOT::DOMAIN_ID->column()])
            ->setParameter(1, $data[_PIVOT::SET_ID->column()])
            ->setParameter(2, $data[_PIVOT::GROUP_ID->column()])
            ->setParameter(3, $data[_PIVOT::ATTR_ID->column()])
            ->executeQuery();
        return (int) $conn->lastInsertId();
    }

    public function findOne(int $domainKey, int $setKey, int $groupKey, int $attributeKey) : bool|array
    {
        return $this->db()->createQueryBuilder()
            ->select($this->getPrimaryKey())
            ->from($this->getTable())
            ->where(sprintf('%s = ?', _PIVOT::DOMAIN_ID->column()))
            ->andWhere(sprintf('%s = ?', _PIVOT::SET_ID->column()))
            ->andWhere(sprintf('%s = ?', _PIVOT::GROUP_ID->column()))
            ->andWhere(sprintf('%s = ?', _PIVOT::ATTR_ID->column()))
            ->setParameter(0, $domainKey, PDO::PARAM_INT)
            ->setParameter(1, $setKey, PDO::PARAM_INT)
            ->setParameter(2, $groupKey, PDO::PARAM_INT)
            ->setParameter(3, $attributeKey, PDO::PARAM_INT)
            ->executeQuery()
            ->fetchAssociative();
    }
}