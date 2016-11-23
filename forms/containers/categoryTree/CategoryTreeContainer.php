<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\Core\Registers\StatusTypeRegister;
use Wame\CategoryModule\Forms\Groups\CategoryGroup;

interface ICategoryTreeContainerFactory extends IBaseContainer
{
	/** @return CategoryTreeContainer */
	public function create();
}

class CategoryTreeContainer extends BaseContainer
{
    /** @var StatusTypeRegister */
    protected $statusTypeRegister;

    /** @var string */
    protected $type;


    public function __construct(\Nette\DI\Container $container, StatusTypeRegister $statusTypeRegister)
    {
        parent::__construct($container);

        $this->statusTypeRegister = $statusTypeRegister;
    }


    /** {@inheritDoc} */
    public function configure()
	{
        $this->type = $this->getEntityAlias($this->statusTypeRegister, $this->getForm()->getEntity());

        $group = new CategoryGroup();
        $group->addButton(_("Add new category"), ":Admin:Category:create", ['id' => $this->type], 'add_circle_outline');

        $this->getForm()->addBaseGroup($group, 'CategoryGroup');

		$this->addHidden('category', _('Category'))
				->setRequired(_('Please select category'));
    }

    /** {@inheritDoc} */
    public function compose($template)
    {
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