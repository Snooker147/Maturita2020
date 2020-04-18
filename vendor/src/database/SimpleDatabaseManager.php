<?php

class SimpleDatabaseManagerPaginateResponse
{
    /** Items returned (any) */
    public $items = [];

    /** Number of total items */
    public $count = 0;

    /** Number of total pages */
    public $countPages = 0;

    /** Current page you are currently */
    public $currentPage = 0;
}

abstract class SimpleDatabaseManager
{
    public const ALL_FIELDS = [ "*" ];

    /**
     * Handle
     * @var Database
     */
    protected $db;

    /**
     * Whether we should autoclose on destruct
     * @var bool
     */
    private $autoClose = false;

    public function __construct(Database $db = NULL)
    {
        if($db === NULL)
        {
            $this->db = new Database();
            $this->autoClose = true; 
        }
        else
        {
            $this->db = $db;
        }
    }

    public function __destruct()
    {
        if($this->autoClose)
        {
            $this->db->close();
        }
    }

    protected function update(string $table, DatabaseDocument $doc, string $by)
    {
        $doc->update($table, $this->db, $by);
    }

    protected function insert(string $table, DatabaseDocument $doc)
    {
        $doc->insert($table, $this->db);
    }

    protected function delete(string $tableName, DatabaseDocument $doc)
    {
        $table = $this->db->getTable($tableName);
        $primary = $table->getPrimaryKeyField();
        $primaryName = $primary->getName();
        
        $this->db->statement("DELETE FROM $tableName WHERE $primaryName = ?", [ $doc->getFieldValue($primaryName) ]);
    }

    protected function getByID(string $className, string $table, int $id)
    {
        $res = $this->db->statement("SELECT * FROM $table WHERE ID = ?", [ $id ]);
        
        if($res->isEmpty())
        {
            return NULL;
        }

        return DatabaseDocument::initializeFromSource($className, $res->get());
    }
    
    protected function getPartialByID(string $className, string $table, array $fields, int $id)
    {
        $fields[] = "ID";
        $fieldsNames = implode(", ", $fields);

        $res = $this->db->statement("SELECT $fieldsNames FROM $table WHERE ID = ?", [ $id ]);

        if($res->isEmpty())
        {
            return nULL;
        }

        return DatabaseDocument::initializeFromSource($className, $res->get());
    }

    protected function getByClause(string $className, string $table, string $clause, array $prepare)
    {
        $res = $this->db->statement("SELECT * FROM $table WHERE $clause", $prepare);

        if($res->isEmpty())
        {
            return NULL;
        }

        return DatabaseDocument::initializeFromSource($className, $res->get());
    }

    protected function getPartialByClause(string $className, string $table, array $fields, string $clause, array $prepare)
    {
        $fieldsNames = implode(", ", $fields);

        $res = $this->db->statement("SELECT $fieldsNames FROM $table WHERE $clause", $prepare);

        if($res->isEmpty())
        {
            return NULL;
        }

        return DatabaseDocument::initializeFromSource($className, $res->get());
    }

    protected function getManyByClause(string $className, string $table, string $clause, array $prepare): array
    {
        $ret = [];

        $res = $this->db->statement("SELECT * FROM $table WHERE $clause", $prepare);
        $arr = $res->getArray();
        
        foreach ($arr as $row) 
        {
            $ret[] = DatabaseDocument::initializeFromSource($className, $row);
        }

        return $ret;
    }

    protected function getManyPartialsByClause(string $className, string $table, array $fields, string $clause, array $prepare): array
    {
        $fieldsNames = implode(", ", $fields);

        $ret = [];

        $res = $this->db->statement("SELECT $fieldsNames FROM $table WHERE $clause", $prepare);
        $arr = $res->getArray();
        
        foreach ($arr as $row) 
        {
            $ret[] = DatabaseDocument::initializeFromSource($className, $row);
        }

        return $ret;
    }

    protected function paginate(string $className, string $table, string $orderBy, int $page, int $itemsPerPage)
    {
        return $this->paginatePartials($className, $table, SimpleDatabaseManager::ALL_FIELDS, $orderBy, $page, $itemsPerPage);
    }

    protected function paginatePartials(
        string $className, 
        string $table, 
        array $fields, 
        string $orderBy, 
        int $page, 
        int $itemsPerPage,
        string $whereClause = NULL,
        string $having = NULL,
        string $orderByDirection = "DESC"
    )
    {
        $fieldsNames = implode(",", $fields);
        $ret = [];

        $limitOffset = $page * $itemsPerPage;

        $whInsert = $whereClause !== NULL ? "WHERE $whereClause" : "";
        $haInsert = $having !== NULL ? "HAVING $having" : "";

        $query = "SELECT $fieldsNames FROM $table $whInsert $haInsert ORDER BY $orderBy $orderByDirection LIMIT $limitOffset, $itemsPerPage";

        $res = $this->db->query($query);

        $arr = $res->getArray();

        foreach ($arr as $row) 
        {
            $ret[] = DatabaseDocument::initializeFromSource($className, $row);
        }

        $count = $this->count($table, $orderBy, $whereClause);

        $returnDoc = new SimpleDatabaseManagerPaginateResponse();
        $returnDoc->items = $ret;
        $returnDoc->count = $count;
        $returnDoc->countPages = ceil($count / $itemsPerPage);
        $returnDoc->currentPage = $page;

        return $returnDoc;
    }

    /**
     * Returns number of rows
     * @return int 
     */
    protected function count(string $table, string $field, string $whereClause = NULL)
    {
        $whInsert = $whereClause !== NULL ? "WHERE $whereClause" : "";

        $query = "SELECT COUNT($field) FROM $table $whInsert";

        $res = $this->db->query($query);

        $count = $res->get()[0];
        
        return $count;
    }

    public function getDatabase()
    {
        return $this->db;
    }

}