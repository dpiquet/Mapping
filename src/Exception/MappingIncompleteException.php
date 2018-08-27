<?php

namespace Dpiquet\Mapping\Exception;

use Exception;

/**
 * Exception de mapping non résolu
 *
 * @see \Dpiquet\Mapping\Mapping
 */
class MappingIncompleteException extends Exception
{
    private $columnName;

    public function __construct($columnName, $code = 0, Exception $previous = null)
    {
        $this->columnName = $columnName;
        parent::__construct(sprintf('Required column name %s not found', $columnName), $code, $previous);
    }

    public function getColumnName()
    {
        return $this->columnName;
    }
}
