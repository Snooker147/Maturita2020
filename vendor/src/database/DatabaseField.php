<?php

class DatabaseField
{
    public const TYPE_INT =         0;
    public const TYPE_VARCHAR =     1;
    public const TYPE_TEXT =        2;
    public const TYPE_DATE =        3;
    public const TYPE_FLOAT =       4;
    public const TYPE_TIMESTAMP =   5;
    public const TYPE_DATETIME =    6;

    public const MOD_PRIMARY_KEY =  "PRIMARY KEY";
    public const MOD_UNSIGNED =     "UNSIGNED";
    public const MOD_INCREMENT =    "AUTO_INCREMENT";
    public const MOD_NOT_NULL =     "NOT NULL";

    /**
     * Field name
     */
    private $name;

    /**
     * Field type
     * @var string
     */
    private $type;

    /**
     * Internal type for declaration. Use TYPE constants
     * @var int
     */
    private $internalType;
    
    /**
     * Whether or not this field can be NULL
     * @var bool
     */
    private $cantBeNull;

    /**
     * Any valid SQL modifiers
     * @var array
     */
    private $modifiers;

    /**
     * Foreign key SQL string or null if not foreign key
     * @var string|null
     */
    private $foreignKey;

    private function __construct(string $name, string $type, int $iType)
    {
        $this->name = $name;
        $this->type = $type;
        $this->internalType = $iType;
        $this->modifiers = array(); 
        $this->cantBeNull = true;
    }

    public function setCanBeNull(): DatabaseField
    {
        $this->cantBeNull = false;
        return $this;
    }

    public function setDefaultValue($val): DatabaseField
    {
        $varDef = $val;

        if(
            $this->internalType === DatabaseField::TYPE_TEXT || 
            $this->internalType === DatabaseField::TYPE_VARCHAR ||
            $this->internalType === DatabaseField::TYPE_DATE ||
            $this->internalType === DatabaseField::TYPE_DATETIME
        )
        {
            $varDef = '"' . $val . '"';   
        }

        $this->addModifier("DEFAULT $varDef");
        return $this;
    }

    public function setPrimaryKey(): DatabaseField
    {
        $this->addModifier(DatabaseField::MOD_PRIMARY_KEY);
        $this->addModifier(DatabaseField::MOD_INCREMENT);
        
        $this->setCanBeNull();
        
        return $this;
    }

    public function setForeignKey(string $refTable, string $refField = "ID"): DatabaseField
    {
        $this->foreignKey = "FOREIGN KEY ($this->name) REFERENCES $refTable($refField)";
        return $this;
    }

    public function removeForeignKey(): DatabaseField
    {
        $this->foreignKey = NULL;
        return $this;
    }

    public function addModifier(string $mod): DatabaseField
    {
        $this->modifiers[] = $mod;
        return $this;
    }

    public function getSqlTypeDefinition(): string
    {
        $name = $this->name;
        $type = $this->type;
        
        if($this->cantBeNull)
        {
            $this->modifiers[] = DatabaseField::MOD_NOT_NULL;
        }

        $mods = join(" ", $this->modifiers);

        return "$name $type $mods"; 
    }

    public function getSqlAddonationalDefinitions()
    {
        return $this->foreignKey;
    }

    public function isPrimaryKey()
    {
        return Utils::findIndex($this->modifiers, DatabaseField::MOD_PRIMARY_KEY) !== -1;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getInternalType()
    {
        return $this->internalType;
    }

    public function getModifiers()
    {
        return $this->modifiers;
    }

    public function getForeignKeyDefinition()
    {
        return $this->foreignKey;
    }

    public static function createInteger(string $name)
    {
        return new DatabaseField($name, "INTEGER", DatabaseField::TYPE_INT);
    }

    public static function createFloat(string $name)
    {
        return new DatabaseField($name, "FLOAT", DatabaseField::TYPE_FLOAT);
    }

    /**
     * Creates a new field with string type
     * @param name Field name
     * @param maxChars How many characters this field can have (0 to infinite) 
     */
    public static function createString(string $name, int $maxChars = 32)
    {
        if($maxChars <= 0)
        {
            return new DatabaseField($name, "TEXT", DatabaseField::TYPE_TEXT);
        }
        else
        {
            return new DatabaseField($name, "VARCHAR($maxChars)", DatabaseField::TYPE_VARCHAR);
        }
    }

    public static function createDate(string $name)
    {
        return new DatabaseField($name, "DATE", DatabaseField::TYPE_DATE);
    }

    public static function createDatetime(string $name)
    {
        return new DatabaseField($name, "DATETIME", DatabaseField::TYPE_DATETIME);
    }

    public static function createTimestamp(string $name)
    {
        return new DatabaseField($name, "TIMESTAMP", DatabaseField::TYPE_TIMESTAMP);
    }

    public static function timestempToDateTimeString(int $timestemp)
    {
        return strftime("%Y-%m-%d %H:%M", $timestemp);
    }

}