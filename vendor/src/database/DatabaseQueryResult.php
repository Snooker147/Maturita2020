<?php

/**
 * Handles result handleing
 */
class DatabaseQueryResult
{

    /**
     * Number of rows
     * @var int
     */
    private $count;

    /**
     * The actual data
     * @var array
     */
    private $rows;

    /**
     * Creates a new resutl instance
     * @param mysqli_result $res Result or NULL (empty result) 
     */
    public function __construct($res)
    {
        if($res !== NULL)
        {
            $this->count = $res->num_rows;
            $this->rows = $res->fetch_all(MYSQLI_NUM);
        }
        else
        {
            $this->count = 0;
            $this->rows = array();
        }
    }

    /**
     * Gets the row or NULL
     */
    public function get(int $index = 0)
    {
        if($index < 0)
        {
            throw new InvalidArgumentException("index must be higher than 0");
        }

        if($index >= $this->getLength())
        {
            return NULL;
        }

        return $this->rows[$index];
    }

    public function getLength(): int
    {
        return $this->count;
    }

    public function getArray(): array
    {
        return $this->rows;
    }

    public function isEmpty(): bool
    {
        return $this->count === 0;
    }
}

?>