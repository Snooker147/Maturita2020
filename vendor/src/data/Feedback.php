<?php

class Feedback extends DatabaseDocument
{

    public const TABLE_NAME = "Feedbacks";

    /** @var int */
    public $ID;

    /** @var int */
    public $DateIssued;
    
    /** @var string */
    public $FirstNameAndSurname;

    /** @var string */
    public $EMail;

    /** @var string */
    public $PhoneNumber;

    /** @var string */
    public $Message;

    public static function createTable(): DatabaseTable
    {
        return DatabaseTable::create(Feedback::TABLE_NAME)->add(
            DatabaseField::createTimestamp("DateIssued")->setDefaultValue("CURRENT_TIMESTAMP"),
            DatabaseField::createString("FirstNameAndSurname", 64),
            DatabaseField::createString("EMail", 64),
            DatabaseField::createString("PhoneNumber", 32),
            DatabaseField::createString("Message", 0)
        );
    }

}

?>