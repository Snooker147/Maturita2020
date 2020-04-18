<?php

class Page extends DatabaseDocument
{

    public const TABLE_NAME = "Pages";

    /** @var int */
    public $ID;

    /** @var string */
    public $UniqueName;

    /** @var string */
    public $HeaderText;

    /** @var string */
    public $HTML;

    /** @var int */
    public $LanguageID;

    public static function createTable(): DatabaseTable
    {
        return DatabaseTable::create(Page::TABLE_NAME)->add(
            DatabaseField::createString("UniqueName", 64),
            DatabaseField::createString("HeaderText", 64),
            DatabaseField::createString("HTML", 0),
            DatabaseField::createInteger("LanguageID")
        );
    }

}

?>