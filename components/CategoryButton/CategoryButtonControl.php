<?php

namespace Wame\CategoryModule\Components;

use Nette\DI\Container;
use Wame\CategoryModule\Repositories\CategoryItemRepository;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\Core\Components\BaseControl;

interface ICategoryButtonControlFactory
{

    /** @return CategoryButtonControl */
    public function create();
}

class CategoryButtonControl extends BaseControl
{

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var CategoryItemRepository */
    private $categoryItemRepository;

    /** string */
    private $lang;

    /** @var string */
    private $type;

    /** @var integer */
    private $depth = 2;

    public function __construct(
    Container $container, CategoryRepository $categoryRepository, CategoryItemRepository $categoryItemRepository
    )
    {
        parent::__construct($container);

        $this->categoryRepository = $categoryRepository;
        $this->categoryItemRepository = $categoryItemRepository;

        $this->lang = $this->categoryRepository->lang;
    }

    public function render()
    {
        $this->setComponent();

        $criteria = [
            'type' => $this->type,
            'depth' => $this->depth
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
    private function setComponent()
    {
        if ($this->componentInPosition) {
            $this->type = $this->getComponentParameter('type');
//			$this->depth = $this->getComponentParameter('depth');
        }
    }
}
