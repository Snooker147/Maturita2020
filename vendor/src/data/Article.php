<?php

class Article extends DatabaseDocument
{

    public const TABLE_NAME = "Articles";

    /** @var int */
    public $ID;

    /** @var int */
    public $DateIssued;

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
        return DatabaseTable::create(Article::TABLE_NAME)->add(
            DatabaseField::createTimestamp("DateIssued")->setDefaultValue("CURRENT_TIMESTAMP"),
            DatabaseField::createString("UniqueName", 64),
            DatabaseField::createString("HeaderText", 64),
            DatabaseField::createString("HTML", 0),
            DatabaseField::createInteger("LanguageID")
        );
    }

}

?>