<?php
declare(strict_types=1);

namespace User\Model\Table;

use Cake\ORM\Table;

abstract class UserBaseTable extends Table
{
    /**
     * @var string Default User connection name
     */
    public static $connectionName = 'default';

    /**
     * {@inheritDoc}
     */
    public static function defaultConnectionName(): string
    {
        return static::$connectionName;
    }
}
