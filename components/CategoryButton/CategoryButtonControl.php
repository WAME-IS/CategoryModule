<?php

namespace Wame\CategoryModule\Components;

use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Repositories\CategoryItemRepository;

interface ICategoryButtonControlFactory {

    /** @return CategoryButtonControl */
    public function create();
}

class CategoryButtonControl extends \Wame\Core\Components\BaseControl {

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var CategoryItemRepository */
    private $categoryItemRepository;

    /** string */
    private $lang;
    
    /** @var string */
    private $type;

    public function __construct(
        CategoryRepository $categoryRepository, CategoryItemRepository $categoryItemRepository
    ) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->categoryItemRepository = $categoryItemRepository;
        
        $this->lang = $this->categoryRepository->lang;
    }

    public function render() {
        $this->setComponent();

        $criteria = [
            'type' => $this->type
        ];

        $categories = $this->categoryRepository->find($criteria);

        $this->template->lang = $this->lang;
        $this->template->categories = $categories;

        $this->getTemplateFile();
        $this->template->render();
    }
    

    /**
     * Set component
     */
    private function setComponent() {
        if ($this->componentInPosition) {
            $this->type = $this->getComponentParameter('type');
        }
    }

}
