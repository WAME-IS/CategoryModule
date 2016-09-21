<?php

namespace Wame\CategoryModule\Grids\GroupActions;

use Wame\DataGridControl\BaseGridItem;
use Doctrine\Common\Collections\Criteria;
use Wame\CategoryModule\Repositories\CategoryRepository;

class AddCategorySelected extends BaseGridItem
{
    /** @var CategoryRepository */
    private $categoryRepository;
    
    
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    
    
    /** {@inheritDoc} */
	public function render($grid)
    {
        $grid->addGroupTextAction(_('Add category'))
                ->setAttribute('data-wame-autocomplete', 'category')
                ->onSelect[] = [$this, 'addCategory'];
        
		return $grid;
	}
    
    /**
     * Add category
     * 
     * @param array $ids
     * @param type $categoryId
     * @throws \Exception
     */
    public function addCategory(array $ids, $categoryId)
    {
        $category = $this->categoryRepository->get(['id' => $categoryId]);
        
        if($category instanceof \Wame\CategoryModule\Entities\CategoryEntity) {
            $collection = new \Doctrine\Common\Collections\ArrayCollection($this->getParent()->getEntities());
            $criteria = Criteria::create()->where(Criteria::expr()->in('id', $ids));
            $selectedEntities = $collection->matching($criteria);

            foreach($selectedEntities as $entity) {
                $this->categoryRepository->attach($entity, $category);
            }
        } else {
            throw new \Exception('Category shoud be instance of CategoryEntity.');
        }
        
        if ($this->getParent()->getPresenter()->isAjax()) {
            $this->getParent()->reload();
        } else {
            $this->redirect('this');
        }
    }
    
}