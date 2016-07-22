<?php

namespace Wame\CategoryModule\Components;

use Nette\DI\Container;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\Core\Components\BaseControl;

interface ICategoryButtonControlFactory
{

    /** @return CategoryButtonControl */
    public function create();
}

class CategoryButtonControl extends BaseControl
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
