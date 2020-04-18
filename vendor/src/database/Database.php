<?php

function getDatabaseTables()
{ 
    yield User::createTable();
    yield Page::createTable();
    yield Article::createTable();
    yield Event::createTable();
    yield Feedback::createTable();
}

/**
 * Handles database connections
 */
class Database implements IResource
{
    /**
     * Database handle
     * @var mysqli
     */
    private $handle = null;

    /**
     * Database tables array
     * @var DatabaseTable[]
     */
    private $tables = array();

    public function __construct()
    {
        $dbName = Config::shouldAttemptToCreateDatabase() ? "" : Config::getDatabaseName();

        $db = new mysqli(
            Config::getDatabaseIP(),
            Config::getDatabaseUser(),
            Config::getDatabasePassword(),
            $dbName,
            Config::getDatabasePort() 
        );

        if($db->connect_errno)
        {
            ErrorHandler::reportError("Could not establish database connection", $db->connect_error);
        }

        $this->handle = $db;

        ResourceManager::register($this);
    }

    public function close()
    {
        $this->handle->close();
    }

    public function query(string $query)
    {
        $res = $this->handle->query($query);
   
        if($res === FALSE)
        {
            throw new RuntimeException($this->handle->error);
        }

        if($res === TRUE)
        {
            return NULL;
        }

        $result = new DatabaseQueryResult($res);
    
        $res->close();

        return $result;
    }

    public function statement(string $query, array $items): DatabaseQueryResult
    {
        $stmt = $this->handle->prepare($query);
        
        if($stmt === FALSE)
        {
            throw new InvalidArgumentException($this->handle->error);
        }
        
        $types = "";
        $values = array();
        
        
        for($i = 0, $len = count($items); $i < $len; $i++)
        {
            $item = $items[$i];
            $type = gettype($item);
            
            $values[] = &$items[$i];
            
            if($type === "integer")
            {
                $types .= "i";
            }
            else if($type === "double")
            {
                $types .= "d";
            }
            else if($type === "string")
            {
                $types .= "s";
            }
            else
            {
                throw new RuntimeException("Unknown type $type");
            }
        }
        
        array_unshift($values, $types);
        $bindResult = call_user_func_array(array($stmt, "bind_param"), $values);
        
        if($bindResult === FALSE)
        {
            throw new RuntimeException($stmt->error);
        }
        
        if($stmt->execute() === FALSE)
        {
            throw new RuntimeException($stmt->error);
        }
        
        $mysqlResult = NULL;

        if(strpos($query, "SELECT") === 0)
        {
            $mysqlResult = $stmt->get_result();
    
            if($mysqlResult === FALSE)
            {
                throw new RuntimeException($stmt->error);
            }
        }

        $res = new DatabaseQueryResult($mysqlResult);

        if($mysqlResult !== NULL)
        {
            $mysqlResult->close();
        }
        
        $stmt->close();

        return $res;
    }
    
    public function build(): void
    {
        if(Config::shouldAttemptToCreateDatabase())
        {
            $this->query("CREATE DATABASE IF NOT EXISTS " . Config::getDatabaseName());
        }
    }

    public function finalizeBuild(bool $build): void
    {
        if(Config::shouldAttemptToCreateDatabase())
        {
            $this->handle->select_db(Config::getDatabaseName());
        }

        $tables = getDatabaseTables();

        foreach ($tables as $tbl) 
        {
            $tbl->setParent($this);
            $this->tables[] = $tbl;

            if($build)
            {
                $tbl->build($this);

                $ev = $tbl->getOnCreatedEvent();
                
                if(is_callable($ev))
                {
                    call_user_func($ev, $this);
                }
            }
        }

        if($build)
        {
            // insert dummy data
            $dummyData = Utils::readFile("src/data/dummy/data.sql");
    
            if(strlen($dummyData) === 0)
            {
                return;
            }
    
            $this->handle->multi_query($dummyData);
    
            do 
            {
                if($res = $this->handle->store_result(MYSQLI_ASSOC)) 
                {
                    $res->free();
                }
            } while ($this->handle->more_results() && $this->handle->next_result());
        }
    }

    public function getResourceName(): string
    {
        return "database";
    }

    public function getTable(string $name): DatabaseTable
    {
        for($i = 0, $len = count($this->tables); $i < $len; $i++)
        {
            $t = $this->tables[$i];
            
            if($t->getName() === $name)
            {
                return $t;
            }
        }

        throw new InvalidArgumentException("Table $name does not exist!");
    }
}

?>