<?php

class UploadedFileResource implements IResource
{

    public function __construct()
    {
        ResourceManager::register($this);
    }

    public function build(): void
    {
        Utils::copyDir("src/data/dummy/images", "../uploads/images");
        Utils::copyDir("src/data/dummy/docs", "../uploads/docs");
    }

    public function finalizeBuild(bool $build): void
    {
    }

    public function getResourceName(): string
    {
        return "uploads";
    }

    public static function init()
    {
        new UploadedFileResource();
    }

}