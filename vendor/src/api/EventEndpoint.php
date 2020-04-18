<?php

class EventEndpoint extends APIEndpoint
{
    public function getName(): string
    {
        return "events";
    }

    public function getRegistry(): array
    {
        return [
            "create-event" => [ function(EventEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $usage = $args->getEnum("usage", [
                    APIEndpoint::CREATE,
                    APIEndpoint::EDIT
                ]);

                $isEdit = $usage === APIEndpoint::EDIT;

                $event = new Event();
                
                if($isEdit)
                {
                    $id = $args->getInt("id");
                    $event = $man->getEventByID($id);

                    if($event == NULL)
                    {
                        throw new APIException("ErrorInvalidEvent");
                    }
                }

                $event->DateBegin = DatabaseField::timestempToDateTimeString($args->getDate("start"));
                $event->DateEnd = DatabaseField::timestempToDateTimeString($args->getDate("end"));
                $event->TitleText = $args->getString("titletext");
                $event->ArticleUniqueName = $args->getString("article-name");
                $event->Color = intval($args->getString("color"), 16);
                
                if($event->DateBegin > $event->DateEnd)
                {
                    throw new APIException("ErrorCalendarEventDateBoundsInvalid");
                }

                if($isEdit)
                {
                    $man->updateEvent($event);
                }
                else
                {
                    $man->insertEvent($event);
                }

                return $isEdit ? "EventEdited" : "EventCreated";

            }, User::ROLE_TEACHER],
            "delete-event" => [ function(EventEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $id = $args->getInt("id");
                $event = $man->getEventByID($id);

                if($event === NULL)
                {
                    throw new APIException("ErrorInvalidEvent");
                }

                $man->deleteEvent($event);
            }, User::ROLE_TEACHER],
            "get-events" => [ function(EventEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $page = $args->getInt("page", 0, NULL);

                return $man->paginateEvents($page, 5);
            }, User::ROLE_TEACHER],
            "get-event-detail" => [ function(EventEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $id = $args->getInt("id");

                $event = $man->getEventByID($id);

                if($event === NULL)
                {
                    throw new APIException("InvalidEvent");
                }
                
                Lang::init();
                $article = $man->getArticleByUnique($event->ArticleUniqueName, Lang::getInstance()->getLanguage());
                
                if($article === NULL)
                {
                    throw new APIException("InvalidArticle");
                }

                $event->TitleText = Lang::getInstance()->get($event->TitleText);
                $event->DateBegin = strftime("%d.%m.%Y %H:%M", strtotime($event->DateBegin));
                $event->DateEnd = strftime("%d.%m.%Y %H:%M", strtotime($event->DateEnd));

                return array(
                    "event" => $event,
                    "article" => $article,
                );
            }, User::ROLE_DEFAULT]
        ];
    }
    
}