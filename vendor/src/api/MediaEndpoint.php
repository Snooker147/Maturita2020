<?php

class MediaEndpoint extends APIEndpoint
{

    public function getName(): string
    {
        return "media";
    }

    public function getRegistry(): array
    {
        return [
            "upload" => [ function(MediaEndpoint $self, APIArguments $args, DatabaseManager $man) 
            {
                $type = $args->getEnum("type", [ UploadedFile::TYPE_IMAGE, UploadedFile::TYPE_DOCS ]);
                $name = $args->getString("name");
                
                $f = new UploadedFile("file", $type);
                $f->setMaxSize(25 * 1000000);
                $err = $f->process(empty($name) ? NULL : $name);

                if($err !== UploadedFile::ERROR_NONE)
                {
                    throw new APIException("ErrorUpload" . $err);
                }
            }, User::ROLE_ADMIN ],
            "delete" => [ function(MediaEndpoint $self, APIArguments $args, DatabaseManager $man) 
            {
                $type = $args->getEnum("type", [ UploadedFile::TYPE_IMAGE, UploadedFile::TYPE_DOCS ]);
                $name = $args->getString("name");
                
                $target = UploadedFile::getTargetFilePath($type, $name);
                
                if(!file_exists($target))
                {
                    throw new APIException("ErrorDeleteUploadedFileNotExist");
                }

                if(!unlink($target))
                {
                    throw new APIException("ErrorDeleteUploadedFileFailedToUnlink");
                }
            }, User::ROLE_ADMIN ]
        ];
    }
    
}