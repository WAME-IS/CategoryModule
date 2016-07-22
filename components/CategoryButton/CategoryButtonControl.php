<?php

namespace Wame\CategoryModule\Components;

use Wame\CategoryModule\Repositories\CategoryRepository;

interface ICategoryButtonControlFactory
{
    /** @return CategoryButtonControl */
    public function create();
}

class CategoryButtonControl extends \Wame\Core\Components\BaseControl
{
    /** @var string */
    private $lang;
    
    /** @var CategoryRepository */
    private $categoryRepository;
    

    public function __construct(Container $container, CategoryRepository $categoryRepository)
    {
        parent::__construct($container);
        
        $this->categoryRepository = $categoryRepository;
        
        $this->lang = $this->categoryRepository->lang;
    }

    public function render()
    {
        $this->template->lang = $this->lang;

        $this->getTemplateFile();
        $this->template->render();
    }

}
