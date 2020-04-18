<?php

/**
 * Helper class
 */
class DatabaseManager extends SimpleDatabaseManager
{
    
    /**
     * Updates user
     */
    public function updateUser(User $user)
    {
        $this->update(User::TABLE_NAME, $user, "ID");
    }
    
    /**
     * Updates page
     */
    public function updatePage(Page $page)
    {
        $this->update(Page::TABLE_NAME, $page, "ID");
    }
    
    /**
     * Updates article
     */
    public function updateArticle(Article $article)
    {
        $this->update(Article::TABLE_NAME, $article, "ID");
    }

    /**
     * Updates event
     */
    public function updateEvent(Event $event)
    {
        $this->update(Event::TABLE_NAME, $event, "ID");
    }

    /**
     * Inserts a user
     */
    public function insertUser(User $user)
    {
        $this->insert(User::TABLE_NAME, $user);
    }

    /**
     * Inserts a page
     */
    public function insertPage(Page $page)
    {
        $this->insert(Page::TABLE_NAME, $page);
    }

    /**
     * Inserts an article
     */
    public function insertArticle(Article $article)
    {
        $this->insert(Article::TABLE_NAME, $article);
    }

    /**
     * Inserts an event
     */
    public function insertEvent(Event $event)
    {
        $this->insert(Event::TABLE_NAME, $event);
    }

    /**
     * Inserts a feedback
     */
    public function insertFeedback(Feedback $feedback)
    {
        $this->insert(Feedback::TABLE_NAME, $feedback);
    }

    /**
     * Deletes a user
     */
    public function deleteUser(User $user)
    {
        $this->delete(User::TABLE_NAME, $user);
    }

    /**
     * Deletes a page
     */
    public function deletePage(Page $page)
    {
        $this->delete(Page::TABLE_NAME, $page);
    }

    /**
     * Deletes an article
     */
    public function deleteArticle(Article $article)
    {
        $this->delete(Article::TABLE_NAME, $article);
    }

    /**
     * Deletes an event
     */
    public function deleteEvent(Event $event)
    {
        $this->delete(Event::TABLE_NAME, $event);
    }

    /**
     * Deletes feedback
     */
    public function deleteFeedback(Feedback $feedback)
    {
        $this->delete(Feedback::TABLE_NAME, $feedback);
    }

    /**
     * Returns a user by ID
     * @return User
     */
    public function getUserByID(int $id)
    {
        return $this->getByID("User", User::TABLE_NAME, $id);
    }

    /**
     * Retunrs page by id
     * @return Page
     */
    public function getPageByID(int $id)
    {
        return $this->getByID("Page", Page::TABLE_NAME, $id);
    }

    /**
     * Retunrs article by id
     * @return Article
     */
    public function getArticleByID(int $id)
    {
        return $this->getByID("Article", Article::TABLE_NAME, $id);
    }

    /**
     * Retunrs event by id
     * @return Event
     */
    public function getEventByID(int $id)
    {
        return $this->getByID("Event", Event::TABLE_NAME, $id);
    }

    /**
     * Returns feedback by id
     * @return Feedback
     */
    public function getFeedbackByID(int $id)
    {
        return $this->getByID("Feedback", Feedback::TABLE_NAME, $id);
    }

    /**
     * Returns a user by clause
     * @return User
     */
    public function getUserByClause(string $clause, array $fields)
    {
        return $this->getByClause("User", User::TABLE_NAME, $clause, $fields);
    }

    /**
     * Returns a user by clause
     * @return Page
     */
    public function getPageByClause(string $clause, array $fields)
    {
        return $this->getByClause("Page", Page::TABLE_NAME, $clause, $fields);
    }
    
    /**
     * Returns a page by unqiue specification
     * @return Page
     */
    public function getPageByUnique(string $unique, int $lang)
    {
        return $this->getPageByClause("UniqueName = ? AND LanguageID = ?", [ $unique, $lang ]);
    }

    /**
     * Returns article by clause
     * @return Article
     */
    public function getArticleByClause(string $clause, array $fields)
    {
        return $this->getByClause("Article", Article::TABLE_NAME, $clause, $fields);
    }

    /**
     * Returns an article by unqiue specification
     * @return Article
     */
    public function getArticleByUnique(string $name, int $lang)
    {
        return $this->getArticleByClause("UniqueName LIKE ? AND LanguageID = ?", [ $name, $lang ]);
    }

    /**
     * Returns event by clause
     * @return Event
     */
    public function getEventByClause(string $clause, array $fields)
    {
        return $this->getByClause("Event", Event::TABLE_NAME, $clause, $fields);
    }

    /**
     * Returns events by month and year
     * @return Event
     */
    public function getEventsByDate(int $month, int $year)
    {
        return $this->getManyByClause(
            "Event", 
            Event::TABLE_NAME, 
            "(MONTH(DateBegin) BETWEEN ? AND ?) AND YEAR(DateBegin) = ?",
            [ $month - 1, $month + 1, $year ]
        );
    }

    /**
     * Returns an article by unqiue specification
     * @return Article
     */
    public function getArticleByLanguage(string $name)
    {
        $l = Lang::getInstance();

        $self = $this->getArticleByUnique($name, $l->getLanguage());
        
        if($self !== NULL)
        {
            return $self;
        }

        return $this->getArticleByUnique($name, $l->getDefaultLanguageID());
    }

    public function paginateUsers(int $page, int $itemsPerPage)
    {
        return $this->paginate("User", User::TABLE_NAME, "ID", $page, $itemsPerPage);
    }

    public function paginatePages(int $page, int $itemsPerPage)
    {
        return $this->paginate("Page", Page::TABLE_NAME, "ID", $page, $itemsPerPage);
    }
    
    public function paginateArticles(int $page, int $itemsPerPage)
    {
        return $this->paginate("Article", Article::TABLE_NAME, "DateIssued", $page, $itemsPerPage);
    }

    public function paginateArticlesByLanguage(int $lang, int $page, int $itemsPerPage)
    {
        return $this->paginatePartials(
            "Article", 
            Article::TABLE_NAME, 
            DatabaseManager::ALL_FIELDS,
            "DateIssued",
            $page,
            $itemsPerPage,
            "LanguageID = $lang"
        ); 
    }

    public function paginateArticlesPreview(int $lang, int $page, int $itemsPerPage)
    {
        return $this->paginatePartials(
            "Article", 
            Article::TABLE_NAME, 
            [ "ID", "DateIssued", "UniqueName", "HeaderText" ],
            "DateIssued",
            $page,
            $itemsPerPage,
            "LanguageID = $lang"
        );
    }

    public function paginateEvents(int $page, int $itemsPerPage)
    {
        return $this->paginate("Event", Event::TABLE_NAME, "ID", $page, $itemsPerPage);
    }

    public function paginateFeedbacks(int $page, int $itemsPerPage)
    {
        return $this->paginate("Feedback", Feedback::TABLE_NAME, "ID", $page, $itemsPerPage);
    }

    public static function create(): DatabaseManager
    {
        return new DatabaseManager();
    }

}