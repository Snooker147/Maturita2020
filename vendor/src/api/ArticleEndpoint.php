<?php

class ArticleEndpoint extends APIEndpoint
{

    public function getName(): string
    {
        return "articles";
    }

    public function getRegistry(): array
    {
        return [
            "create-article" => [ function(ArticleEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $usage = $args->getEnum("usage", [
                    APIEndpoint::CREATE,
                    APIEndpoint::EDIT
                ]);

                $isEdit = $usage === APIEndpoint::EDIT;

                $article = new Article();
                
                if($isEdit)
                {
                    $article->ID = $args->getInt("id");
                }

                $article->UniqueName = $args->getString("unique");
                $article->HeaderText = $args->getString("heading");
                $article->HTML = $args->getString("html");
                $article->LanguageID = $args->getInt("languageid");

                $other = $man->getArticleByUnique($article->UniqueName, $article->LanguageID);

                if($other !== NULL)
                {
                    if(!$isEdit)
                    {
                        throw new APIException("ArticleUniqueAlreadyTaken");
                    }

                    if($other->ID !== $article->ID)
                    {
                        throw new APIException("ArticleUniqueAlreadyTaken");
                    }
                }

                if($isEdit)
                {
                    $man->updateArticle($article);
                }
                else
                {
                    $man->insertArticle($article);
                }

                return $isEdit ? "ArticleEdited" : "ArticleCreated";

            }, User::ROLE_TEACHER],
            "remove-article" => [ function(ArticleEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $id = $args->getInt("id");

                $article = $man->getArticleByID($id);

                if($article === NULL)
                {
                    throw new APIException("InvalidArticle");
                }

                $man->deleteArticle($article);
            }, User::ROLE_TEACHER],
            "get-articles" => [ function(ArticleEndpoint $self, APIArguments $args, DatabaseManager $man) 
            {
                $page = $args->getInt("page", 0, NULL, 0);
                $itemsPerPage = $args->getInt("itemsPerPage", 0, NULL, 5); 

                $ret = $man->paginateArticles($page, $itemsPerPage);

                Lang::init();
                foreach ($ret->items as $article) 
                {
                    $article->LanguageID = Lang::getInstance()->languageIdToName($article->LanguageID);
                }

                return $ret;
            }, User::ROLE_INVALID],
            "get-articles-preview" => [ function(ArticleEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $page = $args->getInt("page", 0, NULL, 0);
                $itemsPerPage = $args->getInt("itemsPerPage", 0, NULL, 5); 
                
                return $man->paginateArticlesPreview(Lang::getInstance()->getLanguage(), $page, $itemsPerPage);
            }, User::ROLE_INVALID]
        ];
    }
    
}