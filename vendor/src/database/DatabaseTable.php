<?php

class DatabaseTable
{

    /**
     * Parent
     * @var Database
     */
    private $db;

    /**
     * Table name
     * @var string
     */
    private $name;

    /**
     * On created event
     * @var callback
     */
    private $onCreated = NULL;

    /**
     * Array of DatabaseField
     * @var DatabaseField[]
     */
    private $fields = array();

    public function __construct(string $name, bool $autoID = TRUE)
    {
        $this->name = $name;

        if($autoID)
        {
            $this->add(DatabaseField::createInteger("ID")->setPrimaryKey());
        }
    }

    public function build(Database $db)
    {
        if(Config::forceTableRecreation())
        {
            $name = $this->name;

            $check = "SHOW TABLES LIKE '$name'";
            $res = $db->query($check);

            if($res === NULL)
            {
                throw new RuntimeException("SELECT provided logical result");
            }

            // if the table exists, it must return a record
            // then just remove it so that it can be recreated
            if(!$res->isEmpty())
            {
                $db->query("DELETE TABLE $name");
            }
        }

        $db->query($this->getSqlCreateDefinition());
    }

    public function add(DatabaseField ...$fields): DatabaseTable
    {
        foreach($fields as $field)
        {
            $this->fields[] = $field;
        }

        return $this;
    }

    protected function get(int $index): DatabaseField
    {
        return $this->fields[$index];
    }

    public function getSqlCreateDefinition()
    {
        $types = [];
        $addons = [];

        foreach($this->fields as $field)
        {
            $types[] = $field->getSqlTypeDefinition();
            
            $addon = $field->getSqlAddonationalDefinitions();

            if($addon !== NULL)
            {
                $addons[] = $addon;
            }
        }
        
        $create = "CREATE TABLE IF NOT EXISTS $this->name (" . join(",", $types) . "\n" . join(",", $addons) . ")";
        return "$create CHARACTER SET utf8 COLLATE utf8_bin"; 
    }

    public function setParent(Database $parent)
    {
        $this->db = $parent;
    }

    public function setOnCreatedEvent(callable $clb)
    {
        $this->onCreated = $clb;
    }

    public function getOnCreatedEvent()
    {
        return $this->onCreated;
    }

    /**
     * Finds field by name
     * @return DatabaseField
     */
    public function getField(string $name)
    {
        foreach ($this->fields as $field) 
        {
            if($field->getName() === $name)
            {
                return $field;
            }
        }

        $tName = $this->name;
        throw new RuntimeException("Table $tName does not have field $name");
    }

    /**
     * Returns primary field
     * @return DatabaseField
     */
    public function getPrimaryKeyField()
    {
        foreach($this->fields as $field)
        {
            if($field->isPrimaryKey())
            {
                return $field;
            }
        }

        $tName = $this->name;
        throw new RuntimeException("Table $tName does not have a primary field");
    }

    public function getName()
    {
        return $this->name;
    }

    public static function create(string $name): DatabaseTable
    {
        return new DatabaseTable($name);
    }
}
