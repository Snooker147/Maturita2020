<?php

class Session
{
    private const SESSION_USER_ID_NAME = "UserID";

    public const INVALID_USER_ID = 0;

    /**
     * Current logged user (may be null when firstly getting the user)
     * @var User
     */
    private static $USER = NULL;

    /**
     * Currently logged user id. Can never be null
     * @var int
     */
    private static $USER_ID = Session::INVALID_USER_ID;

    public static function login(User $user)
    {
        $_SESSION[Session::SESSION_USER_ID_NAME] = $user->ID;
        
        Session::setUser($user);
    }

    public static function logout()
    {
        Session::setUser(NULL);
        
        if(session_destroy() === FALSE)
        {
            throw new RuntimeException("ErrorSessionFailedToDestroyed");
        }
    }

    /** Returns whether or not the user is currently logged in */
    public static function isLoggedIn()
    {
        return Session::$USER_ID !== Session::INVALID_USER_ID;
    }

    /**
     * Returns the current logged in user or NULL if there is none
     * @return User
     */
    public static function getUser()
    {
        if(!Session::isLoggedIn())
        {
            return NULL;
        }

        if(Session::$USER === NULL)
        {
            $man = new DatabaseManager(NULL);
            $user = $man->getUserByID(Session::$USER_ID);
            Session::setUser($user);
        }

        return Session::$USER;
    }

    private static function setUser(?User $user)
    {
        if($user !== NULL)
        {
            Session::$USER = $user;
            Session::$USER_ID = $user->ID;
        }
        else
        {
            Session::$USER = NULL;
            Session::$USER_ID = Session::INVALID_USER_ID;
        }
    }

    public static function init()
    {
        if(session_start() === FALSE)
        {
            throw new RuntimeException("ErrorFailedToCreateSession");
        }

        // session is not set
        if(!isset($_SESSION[Session::SESSION_USER_ID_NAME]))
        {
            return;
        }

        $uid = intval($_SESSION[Session::SESSION_USER_ID_NAME]);

        if($uid === Session::INVALID_USER_ID)
        {
            return;
        }

        Session::$USER_ID = $uid;
    }

}