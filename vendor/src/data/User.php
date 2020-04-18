<?php

class User extends DatabaseDocument
{
    public const TABLE_NAME = "Users";

    public const ROLE_INVALID = -1;
    public const ROLE_DEFAULT = 0;
    public const ROLE_TEACHER = 1;
    public const ROLE_ADMIN =   10;

    /** Last ROLE ID */
    public const ROLE_LAST =    User::ROLE_ADMIN;

    /** @var int */
    public $ID;

    /** @var string */
    public $Email;

    /** @var string */
    public $Username;

    /** @var string */
    public $Password;

    /** @var string */
    public $Telephone;

    /** @var int */
    public $Role;

    /** @var int */
    public $CreatedAt;
    
    public static function createTable(): DatabaseTable
    {
        $tbl = DatabaseTable::create(User::TABLE_NAME)->add(
            DatabaseField::createString("Email", 64),
            DatabaseField::createString("Username", 64),
            DatabaseField::createString("Password", 64),
            DatabaseField::createString("Telephone", 16),
            DatabaseField::createInteger("Role")->setDefaultValue(User::ROLE_DEFAULT),
            DatabaseField::createTimestamp("CreatedAt")->setDefaultValue("CURRENT_TIMESTAMP")
        );

        $tbl->setOnCreatedEvent("User::onCreated");
        return $tbl;
    }

    public static function onCreated(Database $db)
    {
        if(!$db->query("SELECT ID FROM " . User::TABLE_NAME . " WHERE Username = '" . Config::getRootUsername() . "'")->isEmpty())
        {
            return;
        }

        $doc = new User();
        $doc->Email = Config::getRootUsername() . "@" . Config::getSitename();
        $doc->Username = Config::getRootUsername();
        $doc->Password = password_hash(Config::getRootPassword(), PASSWORD_BCRYPT);
        $doc->Telephone = "";
        $doc->Role = User::ROLE_ADMIN;

        $doc->insert(User::TABLE_NAME, $db);
    } 

    public static function hasPermission(?User $user, int $perm)
    {
        if($user === NULL)
        {
            return $perm == User::ROLE_DEFAULT;
        }

        return $user->Role >= $perm;
    }

}