<?php

abstract class APIEndpoint
{
    public const GET_RESPONSE_TYPE = "type";
    public const GET_RETURN_URL = "returnURL";
    public const GET_RETURN_URL_FAILED = "returnURLFailed";
    public const GET_RETURN_URL_SUCCEED = "returnURLSuccess";
    public const GET_METHOD_NAME = "method";
    public const GET_ENDPOINT_NAME = "endpoint";

    public const CREATE = "create";
    public const EDIT = "edit";

    /**
     * Database manager instance
     * @var DatabaseManager
     */
    private $db;

    /**
     * Function registry 
     * @var array
     */
    private $funcs;

    public function __construct()
    {
        $this->funcs = $this->getRegistry();
    }

    /** Returns the name of this endpoint (all lowercase) */
    public abstract function getName(): string;

    /**
     * Returns the method registry for this APIEndpoint.
     * The method signurate can be seen here:
     * #-> (APIEndpoint $self, APIArguments $args, DatabaseManager $db, User $user), ROLE   
     */
    public abstract function getRegistry(): array;

    public function invoke(array $definition, array $arguments)
    {
        $defLen = count($definition);

        $method = $definition[0];
        $perm = $defLen <= 1 ? User::ROLE_INVALID : $definition[1];

        $user = Session::getUser();

        if($perm > User::ROLE_INVALID && !User::hasPermission($user, $perm))
        {
            throw new RuntimeException("InsufficientPermission");
        }
        
        return $method($this, new APIArguments($this->getName(), $arguments), $this->db, $user);
    }

    /**
     * Returns a method by name
     * @return array
     */
    public function getMethod(string $name)
    {
        if(isset($this->funcs[$name]))
        {
            return $this->funcs[$name];
        }
        
        return NULL;
    }

    public function setDatabase(DatabaseManager $man)
    {
        $this->db = $man;
    }

    public function getDatabase()
    {
        return $this->db;
    }

}