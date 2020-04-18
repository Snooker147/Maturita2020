<?php

class PageHandler
{
    /**
     * @var DatabaseManager
     */
    private $man;

    public function __construct(DatabaseManager $man)
    {
        $this->man = $man;
    }

    public function getPage(Lang $lang, string $pageName)
    {
        if(strlen($pageName) === 0)
        {
            $pageName = "index";
        }

        $providedName = "Page" . str_replace(" ", "", ucwords(str_replace("-", " ", $pageName)));
        
        // check if developer provided page exists
        $flName = Utils::getPathRelativeToBase("src/frontend/pages/$providedName.twig");
        
        if(file_exists($flName))
        {
            Render::print("pages/$providedName.twig", [ 
                "database" => $this->man 
            ]);
        }
        else
        {
            $currentLangId = $lang->getLanguage();
            $defaultLangId = $lang->getDefaultLanguageID();

            $page = $this->man->getPageByUnique($pageName, $currentLangId); 
            
            if($page === NULL)
            {
                $page = $this->man->getPageByUnique($pageName, $defaultLangId);
            }

            if($page === NULL)
            {
                Render::print("InvalidPage.twig", [ 
                    "page" => $pageName
                ]);
            }
            else
            {
                MetaData::getInstance()->set("title", $page->HeaderText);
                
                Render::print("Page.twig", [
                    "page" => $page
                ]);
            }
        }
    }

}