<?php

class UserEndpoint extends APIEndpoint
{
    
    public function getName(): string
    {
        return "users";
    }

    public function getRegistry(): array
    {
        return [
            "set-user-language" => [ function(UserEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $lang = $args->getString("lang");

                Lang::init();
                Lang::getInstance()->setLanguage($lang);
            }],
            "login" => [ function(UserEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $username = $args->getString("username");
                $password = $args->getString("password");

                $user = $man->getUserByClause("Username = ?", [ $username ]);

                if($user === NULL)
                {
                    throw new APIException("ErrorUserNotFound");
                }

                if(!Utils::verifyPassword($password, $user->Password))
                {
                    throw new APIException("ErrorPasswordMismatch");
                }

                Session::login($user);
            }, User::ROLE_INVALID ],
            "logout" => [ function(UserEndpoint $self, APIArguments $args, DatabaseManager $man, User $user)
            {
                if($user === NULL)
                {
                    throw new APIException("ErrorNotLoggedIn");
                }
                
                Session::logout();
            }, User::ROLE_INVALID ],
            "create-user" => [ function(UserEndpoint $self, APIArguments $args, DatabaseManager $man) 
            {
                $usage = $args->getEnum("usage", [ 
                    APIEndpoint::CREATE,
                    APIEndpoint::EDIT 
                ]);
                
                $isEdit = $usage === APIEndpoint::EDIT;

                $user = new User();

                if($isEdit)
                {
                    $user->ID = $args->getInt("id");
                }

                $user->Email = $args->getEmail("email");
                $user->Username = $args->getString("username", 4, 32);

                if(!$isEdit)
                {
                    $user->Password = Utils::hashPassword($args->getString("password", 4, 32));
                }
                else
                {
                    $pwd = $args->getString("password", 4, 32, "");

                    if(isset($pwd) && !empty($pwd))
                    {
                        $user->Password = Utils::hashPassword($pwd);
                    }
                }
                
                $user->Telephone = $args->getString("telephone");
                $user->Role = $args->getInt("role", User::ROLE_INVALID, User::ROLE_ADMIN);

                $other = $man->getUserByClause("Username = ?", [ $user->Username ]);
                
                if($other !== NULL)
                {
                    if(($isEdit && $user->ID !== $other->ID) || !$isEdit)
                    {
                        throw new APIException("ErrorUsernameTaken");
                    }
                }

                if($isEdit)
                {
                    $man->updateUser($user);
                }
                else
                {
                    $man->insertUser($user);
                }

                return $isEdit ? "UserEdited" : "UserCreated";
            }, User::ROLE_ADMIN ],
            "delete-user" => [ function(UserEndpoint $self, APIArguments $args, DatabaseManager $man, User $user) {
                $id = $args->getInt("id");

                if($id === $user->ID)
                {
                    throw new APIException("ErrorDeleteUserUnableSelf");
                }
                
                $queriedUser = $man->getUserByID($id);

                if($queriedUser === NULL)
                {
                    throw new APIException("ErrorUserDoesNotExist");
                }

                if($queriedUser->Username === Config::getRootUsername())
                {
                    throw new APIException("ErrorCantDeleteRoot");
                }
                
                $man->deleteUser($queriedUser);
            }, User::ROLE_ADMIN ],
            "get-users" => [ function(UserEndpoint $self, APIArguments $args, DatabaseManager $man) 
            {
                $page = $args->getInt("page", 0, NULL, 0);
                
                return $man->paginateUsers($page, 5);
            }, User::ROLE_ADMIN ]
        ];
    }
    
}