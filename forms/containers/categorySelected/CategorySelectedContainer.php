<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\Core\Registers\StatusTypeRegister;
use Wame\CategoryModule\Forms\Groups\CategoryGroup;
use Wame\CategoryModule\Repositories\CategoryRepository;
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
				->setRequired(_('Please select category'));
    }

    /** {@inheritDoc} */
    public function compose($template)
    {
        $itemId = $this->getForm()->getEntity()->getId();

        $categories = $this->categoryItemRepository->findByType($this->type, $itemId);

        $template->categories = $categories;
        $template->type = $this->type;
    }

    /** {@inheritDoc} */
	public function setDefaultValues($entity, $langEntity = null)
	{
        // TODO:
//        $this['category']->setDefaultValue($entity->getTitle());
	}

    /** {@inheritDoc} */
    public function create($form, $values)
    {
        // TODO:
//        $entity = method_exists($form, 'getLangEntity') ? $form->getLangEntity(): $form->getEntity();
//        $entity->setCategory($values['category']);
    }

    /** {@inheritDoc} */
    public function update($form, $values)
    {
        // TODO:
//        $entity = method_exists($form, 'getLangEntity') ? $form->getLangEntity(): $form->getEntity();
//        $entity->setCategory($values['category']);
    }

}
