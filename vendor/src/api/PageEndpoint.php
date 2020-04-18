<?php

class PageEndpoint extends APIEndpoint
{

    public function getName(): string
    {
        return "pages";
    }

    public function getRegistry(): array
    {
        return [
            "create-page" => [ function(PageEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $usage = $args->getEnum("usage", [
                    APIEndpoint::CREATE,
                    APIEndpoint::EDIT
                ]);

                $isEdit = $usage === APIEndpoint::EDIT;

                $page = new Page();
                
                if($isEdit)
                {
                    $page->ID = $args->getInt("id");
                }

                $page->UniqueName = $args->getString("unique");
                $page->HeaderText = $args->getString("heading");
                $page->HTML = $args->getString("html");
                $page->LanguageID = $args->getInt("languageid");

                $other = $man->getPageByUnique($page->UniqueName, $page->LanguageID);

                if($other !== NULL)
                {
                    if(!$isEdit)
                    {
                        throw new APIException("PageUniqueAlreadyTaken");
                    }

                    if($other->ID !== $page->ID)
                    {
                        throw new APIException("PageUniqueAlreadyTaken");
                    }
                }

                if($isEdit)
                {
                    $man->updatePage($page);
                }
                else
                {
                    $man->insertPage($page);
                }

                return $isEdit ? "PageEdited" : "PageCreated";
            }, User::ROLE_TEACHER ],
            "remove-page" => [ function(PageEndpoint $self, APIArguments $args, DatabaseManager $man)
            {   
                $page = $man->getPageByID($args->getInt("id"));

                if($page === NULL)
                {
                    throw new APIException("PageInvalidID");
                }

                $man->deletePage($page);
            }, User::ROLE_TEACHER ],
            "get-pages" => [ function(PageEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $page = $args->getInt("page", 0, NULL, 0);
                
                $ret = $man->paginatePages($page, 5);

                Lang::init();
                foreach ($ret->items as $page) 
                {
                    $page->LanguageID = Lang::getInstance()->languageIdToName($page->LanguageID);
                }

                return $ret;
            }, User::ROLE_TEACHER, ]
        ];
    }
}