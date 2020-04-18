<?php

class FeedbackEndpoint extends APIEndpoint
{

    public function getName(): string
    {
        return "feedback";
    }

    public function getRegistry(): array
    {
        return [
            "submit" => [ function(FeedbackEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $name = $args->getString("name", NULL, 32, "");
                $email = $args->getEmail("email");
                $phone = $args->getString("phone", NULL, 32, "N/A");
                $message = $args->getString("message");
                $checked = $args->getString("checked");

                $f = new Feedback();
                $f->FirstNameAndSurname = $name;
                $f->EMail = $email;
                $f->PhoneNumber = $phone;
                $f->Message = $message;

                $man->insertFeedback($f);
                
                return "yes";
            }, User::ROLE_INVALID],
            "delete-feedback" => [ function(FeedbackEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $id = $args->getInt("id");

                $f = $man->getFeedbackByID($id);

                if($f === NULL)
                {
                    throw new APIException("UnknownFeedback");
                }

                $man->deleteFeedback($f);
            }, User::ROLE_ADMIN ],
            "get-feedback" => [ function(FeedbackEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $id = $args->getInt("id");

                $f = $man->getFeedbackByID($id);
                
                if($f === NULL)
                {
                    throw new APIException("UnknownFeedback");
                }

                return $f;
            }, User::ROLE_ADMIN ],
            "get-feedbacks" => [ function(FeedbackEndpoint $self, APIArguments $args, DatabaseManager $man)
            {
                $page = $args->getInt("page", 0, 1000, 0);
                $items = $args->getInt("itemsPerPage", 1, 10, 5);

                return $man->paginateFeedbacks($page, $items);
            }, User::ROLE_ADMIN ]
        ];
    }
    
}