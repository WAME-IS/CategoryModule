<?php

namespace App\CategoryModule\Presenters;

use App\Core\Presenters\BasePresenter;
use Wame\CategoryModule\Components\ICategoryControlFactory;
use Wame\CategoryModule\Components\ICategoryListControlFactory;
use Wame\CategoryModule\Repositories\CategoryRepository;

class CategoryPresenter extends BasePresenter
{
    /** @var CategoryRepository @inject */
    public $categoryRepository;

    /** @var ICategoryListControlFactory @inject */
    public $ICategoryListControlFactory;

    /** @var ICategoryControlFactory @inject */
    public $ICategoryControlFactory;

    
    /** handles ***************************************************************/
    
    public function handleGen()
    {
        //TODO remove
        $categories = $this->categoryRepository->find();

        foreach ($categories as $category) {
            $parent = $this->categoryRepository->getParent($category);

            if ($parent) {
                $category->setParent($parent);
            }
        }
    }
    

    /** actions ***************************************************************/
    
    public function actionShow($id)
    {
        $categoryControl = $this->ICategoryControlFactory->create();
        $categoryControl->setEntityId($id);
        $this->addComponent($categoryControl, 'category');
    }
    

    /** components ************************************************************/
    
    protected function createComponentCategoryList()
    {
        $component = $this->ICategoryListControlFactory->create();
        return $component;
    }
    
}
