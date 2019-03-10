<?php

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
    public static function defaultConnectionName()
    {
        return static::$connectionName;
    }
}
