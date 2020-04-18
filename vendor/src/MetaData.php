<?php

class MetaData
{

    /** @var MetaData */
    private static $INSTANCE = NULL;

    /** @var array */
    private $values;

    private function __construct()
    {
        $this->values = [];
    }

    public function invokeStaticMethod(string $class, string $method)
    {
        return call_user_func(array($class, $method));
    }

    public function set(string $key, string $val)
    {
        $this->values[$key] = $val;
    }

    /**
     * @return string
     */
    public function get(string $key, string $def = NULL)
    {
        if(array_key_exists($key, $this->values))
        {
            return $this->values[$key];
        }

        return $def;
    }

    // helper methods for twig classes
    public function getIndexCardsCfg()
    {
        return Utils::getYaml("usercfg/index-cards.yml");
    }

    public function getIndexOfferCfg()
    {
        return Utils::getYaml("usercfg/index-we-offer.yml");
    }

    public function computeEventTimestempArray(int $month, int $year)
    {
        return Event::computeTimestempArray($month, $year);
    }

    /**
     * Gets files uploaded by type (UploadedFile::TYPE_XXX)
     */
    public function getUploaded(string $type)
    {
        $target = UploadedFile::getTargetFilePath($type, "");
        return array_diff(scandir($target), array("..", ".", "_README"));
    }

    /**
     * Get event in which is the date currently in
     * @param Event[] $events
     * @param int $dt Datetime to find event
     * @return Event[] events
     */
    public function getEventsIn($events, int $dt)
    {
        $ret = [];
        
        foreach ($events as $e) 
        {
            $begin = strtotime($e->DateBegin);
            $beginDay = strftime("%d", $begin);
            $beginMon = strftime("%m", $begin);
            $beginYear = strftime("%Y", $begin);
            $begin = strtotime("$beginDay.$beginMon.$beginYear");

            $end = strtotime($e->DateEnd);
            $endDay = strftime("%d", $end);
            $endMon = strftime("%m", $end);
            $endYear = strftime("%Y", $end);
            $end = strtotime("$endDay.$endMon.$endYear");

            if($dt >= $begin && $dt <= $end)
            {
                $ret[] = $e;
            }
        }
        
        return empty($ret) ? NULL : $ret;
    }

    public static function init()
    {
        if(MetaData::$INSTANCE === NULL)
        {
            MetaData::$INSTANCE = new MetaData();
        }
    }

    public static function getInstance()
    {
        return MetaData::$INSTANCE;
    }

}