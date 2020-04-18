<?php

class APIArguments
{

    /**
     * Argument name (for error handleing)
     * @var string
     */
    private $name;

    /**
     * Argument array
     */
    private $args = array();

    public function __construct(string $endName, array $arguments)
    {
        $this->name = $endName;

        foreach ($arguments as $name => $value) 
        {
            $this->args[$name] = $value;
        }
    }

    /**
     * Returns whether this argument list contains a argument
     */
    public function has(string $name)
    {
        return array_key_exists($name, $this->args);
    }

    private function get(string $name, $def)
    {
        if(!isset($this->args[$name]))
        {
            if($def === NULL)
            {
                $this->errorRequired($name);
            }

            return $def;
        }

        return $this->args[$name];
    }

    /**
     * Returns an integer variant of an arguent.
     * If default is set to anything other than NULL an error is thrown
     * when the argument doesnt exists. If you wish to use NULL as optional value
     * use logical statement with APIArguments::has method 
     * @return int
     */
    public function getInt(string $name, int $min = NULL, int $max = NULL, $def = NULL)
    {
        $val = $this->get($name, $def);

        if(is_numeric($val))
        {
            $val = intval($val);
        }
        else
        {
            $this->errorTypeMismatch($name, "integer");
        }

        if($min !== NULL && $val < $min)
        {
            $this->errorSmaller($name, $min);
        }
        else if($max !== NULL && $val > $max)
        {
            $this->errorLarger($name, $max);
        }

        return $val;
    }

    /**
     * Returns a float variant of an arguent
     * If default is set to anything other than NULL an error is thrown
     * when the argument doesnt exists. If you wish to use NULL as optional value
     * use logical statement with APIArguments::has method 
     * @return float
     */
    public function getFloat(string $name, float $min = NULL, float $max = NULL, $def = NULL)
    {
        $val = $this->get($name, $def);

        if(is_numeric($val))
        {
            $val = floatval($val);
        }
        else
        {
            $this->errorTypeMismatch($name, "float");
        }

        if($min !== NULL && $val < $min)
        {
            $this->errorSmaller($name, $min);
        }
        else if($max !== NULL && $val > $max)
        {
            $this->errorLarger($name, $max);
        }

        return $val;
    }

    /**
     * Returns a string variant of an arguent
     * If default is set to anything other than NULL an error is thrown
     * when the argument doesnt exists. If you wish to use NULL as optional value
     * use logical statement with APIArguments::has method 
     * @return string
     */
    public function getString(string $name, int $min = NULL, int $max = NULL, $def = NULL)
    {
        $val = trim($this->get($name, $def));

        if(is_numeric($val))
        {
            $val = strval($val);
        }
        else if(!is_string($val))
        {
            $this->errorTypeMismatch($name, "string");
        }

        $len = strlen($val);

        if($len === 0)
        {
            if($def !== NULL)
            {
                return $def;
            }
            
            $this->errorRequired($name);
        }

        if($min !== NULL && $len < $min)
        {
            $this->errorSmaller($name, $min);
        }
        else if($max !== NULL && $len > $max)
        {
            $this->errorLarger($name, $max);
        }

        return $val;
    }

    /**
     * Returns a unix timestemp from stringified field 
     */
    public function getDate(string $name, string $def = NULL)
    {
        $vl = $this->getString($name, NULL, nULL, $def);

        if($vl === NULL)
        {
            $this->errorRequired($name);
        }

        $time = strtotime($vl);

        if($time === FALSE)
        {
            $this->errorNotValidDate($name);
        }

        return $time;
    }

    /**
     * Gets pseudo-enum variant of an argument.
     * If def is set
     * @return string
     */
    public function getEnum(string $name, array $values, string $def = NULL)
    {
        $str = $this->getString($name, NULL, NULL, $def);

        $index = -1;

        if($str !== NULL)
        {
            $index = Utils::indexOf(strtolower($str), $values);
        }
        
        if($index === -1)
        {
            if($def !== NULL)
            {
                return $def;
            }

            $this->errorRequired($name);
        }

        return $values[$index];
    }

    /**
     * Returns an email variant of an arguent.
     * It requires a @ and "domain" at the end
     * @return string or NULL if optional
     */
    public function getEmail(string $name, bool $optional = FALSE)
    {
        $val = $this->getString($name, NULL, NULL, $optional ? NULL : "");

        if($optional && empty($val))
        {
            return NULL;
        }

        // check if contains spaces
        // email address cannot contain spaces
        if(preg_match('/\s/', $val))
        {
            $this->errorNotValidEmail($name);
        }

        $len = strlen($val);
        $atPos = strpos($val, "@");

        if($atPos === FALSE || ($atPos === 0 || $atPos >= $len))
        {
            $this->errorNotValidEmail($name);
        }

        $parts = explode("@", $val, 2);

        if(count($parts) <= 1)
        {
            $this->errorNotValidEmail($name);
        }

        $emailname = $parts[0];
        $domain = $parts[1];
        
        // probably uses as split should prevent it but hey
        if(strlen($emailname) === 0)
        {
            $this->errorNotValidEmail($name);
        }
        
        $dotPos = strpos($domain, ".");

        if($dotPos === FALSE || ($dotPos === 0 || $dotPos >= strlen($domain)))
        {
            $this->errorNotValidEmail($name);
        }

        return "$emailname@$domain";
    }
    
    private function errorNotValidEmail(string $name)
    {
        $this->error("${name}InvalidEmail");
    }

    private function errorNotValidDate(string $name)
    {
        $this->error("${name}InvalidDate");
    }

    private function errorRequired(string $name)
    {
        $this->error("${name}Required");
    }

    private function errorLarger(string $name, int $required)
    {
        $this->error("${name}Larger", $required);
    }

    private function errorSmaller(string $name, int $required)
    {
        $this->error("${name}Smaller", $required);
    }

    private function errorTypeMismatch(string $name, string $requestedType)
    {
        $this->error("${name}TypeMismatch", $requestedType);
    }
    
    private function error(string $name, $info = "")
    {
        $end = ucfirst($this->name);
        $argName = ucfirst($name);
        throw new APIException("Error${end}Arg$argName", $info);
    }

}