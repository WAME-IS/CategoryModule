<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\Core\Registers\StatusTypeRegister;
use Wame\CategoryModule\Forms\Groups\CategoryGroup;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\CategoryModule\Entities\CategoryItemEntity;
use Wame\CategoryModule\Repositories\CategoryItemRepository;


interface ICategorySelectedContainerFactory extends IBaseContainer
{
	/** @return CategorySelectedContainer */
	public function create();
}


class CategorySelectedContainer extends BaseContainer
{
    /** @var StatusTypeRegister */
    protected $statusTypeRegister;

    /** @var CategoryRepository */
    protected $categoryRepository;

    /** @var CategoryItemRepository */
    protected $categoryItemRepository;

    /** @var string */
    protected $type;

    /** @var CategoryItemEntity[] */
    protected $categories;

    /** @var array */
    protected $categoryItems = [];


    public function __construct(
        \Nette\DI\Container $container,
        StatusTypeRegister $statusTypeRegister,
        CategoryRepository $categoryRepository,
        CategoryItemRepository $categoryItemRepository
    ) {
        parent::__construct($container);

        $this->statusTypeRegister = $statusTypeRegister;
        $this->categoryRepository = $categoryRepository;
        $this->categoryItemRepository = $categoryItemRepository;
    }


    /** {@inheritDoc} */
    public function configure()
	{
        $entity = $this->getForm()->getEntity();

        $this->type = $this->getEntityAlias($this->statusTypeRegister, $entity);

        // Todo: zbavit sa toho nejak
        if ($this->type == 'shopProductVariant') {
            $this->type = 'shopProduct';
        }

        $group = new CategoryGroup();
        $group->addButton(_("Add category"), ":Admin:CategoryItem:create", ['id' => $entity->getId(), 't' => $this->type], 'add_circle_outline');

        $this->getForm()->addBaseGroup($group, 'CategoryGroup');

		$this->addHidden('category', _('Category'))
                ->setAttribute('data-tree', $this->getName());
    }


    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
        $this->categories = $this->categoryItemRepository->findByType($this->type, $entity->getId());

        foreach ($this->categories as $categoryItem) {
            $this->categoryItems[$categoryItem->getCategory()->getId()] = $categoryItem->getCategory()->getTitle();
        }

        $this['category']->setDefaultValue(implode(',', array_keys($this->categoryItems)));
	}


    /** {@inheritDoc} */
    public function compose($template)
    {
        $itemId = $this->getForm()->getEntity()->getId();

        $template->categories = $this->categories;
        $template->categoryItems = $this->categoryItems;
        $template->type = $this->type;
        $template->itemId = $itemId;
    }


    /** {@inheritDoc} */
    public function create($form, $values)
    {

    }


    /** {@inheritDoc} */
    public function update($form, $values)
    {
//        Nieje potreba ukladá sa to cez AJAX
//        $entity = method_exists($form, 'getLangEntity') ? $form->getLangEntity(): $form->getEntity();
//
//        $categories = $this->categoryRepository->findAssoc(['id IN' => explode(',', $values['category'])], 'id');
//
//        $this->categoryItemRepository->setItemToCategory($this->type, $entity->getId(), $categories);
    }

}
