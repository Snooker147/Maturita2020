<?php

class DatabaseDocument
{
    public const INVALID_ID = 0;
    public const INVALID_TIMESTAMP = 0;
    
    public function __construct($source = NULL)
    {
        if($source === NULL)
        {
            return;
        }

        $counter = 0;
        $maxIndex = count($source);

        foreach ($this as $key => $value) 
        {
            if($counter >= $maxIndex)
            {
                break;
            }
            
            $this->$key = $source[$counter++];
        }
    }

    public function createInsertOrDelete()
    {
        $typeQuery = [];
        $questions = [];
        $values = [];
        
        foreach ($this as $key => $value) 
        {
            // we ignore NULL variables as they auto probably auto
            // or have defautl value 
            if($value === NULL)
            {
                continue;
            }

            $typeQuery[] = $key;
            $questions[] = "?";
            $values[] = $value;
        }

        return new DbInsertUpdateInfo($typeQuery, $values, $questions);
    }

    public function insert(string $table, Database $db)
    {
        $info = $this->createInsertOrDelete();

        $header = join(",", $info->types);
        $marks = join(",", $info->questions);
        $query = "INSERT INTO $table ($header) VALUES ($marks)";
        
        $db->statement($query, $info->values);
    }

    public function update(string $table, Database $db, string $by)
    {
        $info = $this->createInsertOrDelete();
        
        $types = Utils::map($info->types, function(string $val) {
            return "$val = ?";
        });
        $head = implode(", ", $types);

        $query = "UPDATE $table SET $head WHERE $by = ?";

        $info->values[] = $this->getFieldValue($by);
        
        $db->statement($query, $info->values);
    }

    public function hasField(string $name)
    {
        return property_exists($this, $name);
    }

    public function getFieldValue(string $name)
    {
        return $this->$name;
    }

    public static function initializeFromSource(string $className, $source)
    {
        $refl = new ReflectionClass($className);
        return $refl->newInstanceArgs(array($source));
    }

    public static function initializeFromAssoc(string $className, array $assoc)
    {
        $refl = new ReflectionClass($className);
        $obj = $refl->newInstanceArgs([ NULL ]);
        return DatabaseDocument::createFromAssoc($obj, $assoc);
    }

    public static function createFromAssoc(DatabaseDocument $doc, array $assoc): DatabaseDocument
    {
        foreach ($assoc as $key => $value) 
        {
            if(!$doc->hasField($key))
            {
                throw new RuntimeException("Document does not contain the field $key");
            }

            $doc->$key = $value;
        }

        return $doc;
    }

}


/**
 * Helper class for insertion and updating string classes
 */
class DbInsertUpdateInfo
{
    /** @var string[] */
    public $types;

    /** @var array */
    public $values;

    /** @var string[] */
    public $questions;

    public function __construct(array $types, array $values, array $questions)
    {
        $this->types = $types;
        $this->values = $values;
        $this->questions = $questions;
    }
}
