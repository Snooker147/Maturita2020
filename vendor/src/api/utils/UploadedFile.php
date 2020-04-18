<?php

class UploadedFile
{
    public const TYPE_IMAGE = "images";
    public const TYPE_DOCS = "docs";

    public const ERROR_NONE = 0;
    public const ERROR_FILE_NOT_FOUND = 1;
    public const ERROR_FILE_NOT_TYPE = 2;
    public const ERROR_FILE_ALREADY_EXISTS = 3;
    public const ERROR_FILE_SIZE_EXCEEDED = 4;
    public const ERROR_FILE_INVALID_EXTENSION = 5;
    public const ERROR_FILE_UNKNOWN_ERROR = 6;

    /**
     *  $_FILES superglobal index
     *  @var string 
     */
    private $superName;

    /**
     * File type according to UploadedFile::TYPE_XXXX 
     * @var string 
     */
    private $type;

    /**
     * Maximal file size, 0 = any size
     * @var int
     */
    private $maxsize = 0;

    /**
     * Allowed extensions to be uploaded for this file.
     * If empty all files can be uploaded
     * @var string[]
     */
    private $allowedExtensions = NULL;

    public function __construct(string $superglobalname, string $type)
    {
        $this->superName = $superglobalname;
        $this->type = $type;

        if($type === UploadedFile::TYPE_IMAGE)
        {
            $this->allowedExtensions = [ "png", "jpg", "jpeg", "gif" ];
        }
        else if($type === UploadedFile::TYPE_DOCS)
        {
            $this->allowedExtensions = [ "docx", "doc", "pdf", "txt", "ppt" ];
        }
    }

    /**
     * Processes this file and copies it to the target file based on type
     * Upload is always set to assets/uploads/<TYPE>/<TARGET>.<SUPPLIED_EXTENSION>
     * If target is ommited (null), basename of the file is used instead
     */
    public function process(string $target = null)
    {
        if(!isset($_FILES[$this->superName]) || !is_array($_FILES[$this->superName]))
        {
            return UploadedFile::ERROR_FILE_NOT_FOUND;
        }

        $f = $_FILES[$this->superName];
        $name = $f["name"];
        $tmpName = $f["tmp_name"];

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $size = 0;

        if($this->type === UploadedFile::TYPE_IMAGE)
        {
            if(getimagesize($tmpName) === FALSE)
            {
                return UploadedFile::ERROR_FILE_NOT_TYPE;
            }
        }

        $size = filesize($tmpName);

        if($this->allowedExtensions !== NULL)
        {
            if(!Utils::find($this->allowedExtensions, $ext))
            {
                return UploadedFile::ERROR_FILE_INVALID_EXTENSION;
            }
        }

        if($this->maxsize > 0 && $size > $this->maxsize)
        {
            return UploadedFile::ERROR_FILE_SIZE_EXCEEDED;
        }

        if($target === NULL || empty($target))
        {
            $target = basename($name);
        }
        else
        {
            $target .= ".$ext";
        }

        $targetFile = UploadedFile::getTargetFilePath($this->type, $target);
        
        if(file_exists($targetFile))
        {
            return UploadedFile::ERROR_FILE_ALREADY_EXISTS;
        }

        if(!move_uploaded_file($tmpName, $targetFile))
        {
            return UploadedFile::ERROR_FILE_UNKNOWN_ERROR;
        }

        return UploadedFile::ERROR_NONE;
    }

    /**
     * Sets maximal size (in bytes) of this file ot be uploaded
     * when 0, any file size is allowed.
     * Note that this must always be below php.ini upload_size variable.
     */
    public function setMaxSize(int $max)
    {
        $this->maxsize = $max;
        return $this;
    }

    /**
     * Sets allowed extensions (without dot) for this file.
     * When set to NULL, any file can be set
     * @param string[] $exts Array of allowed extensions, or null if any
     */
    public function setAllowedExtensions($exts)
    {
        $this->allowedExtensions = $exts;
    }

    public static function getTargetFilePath(string $type, string $target)
    {
        return "uploads/$type/$target";
    }

}