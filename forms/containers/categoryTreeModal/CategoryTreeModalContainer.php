<?php

namespace Wame\CategoryModule\Forms\Containers;

use Wame\DynamicObject\Forms\Containers\BaseContainer;
use Wame\DynamicObject\Registers\Types\IBaseContainer;
use Wame\Core\Registers\StatusTypeRegister;
use Wame\CategoryModule\Forms\Groups\CategoryGroup;
use Wame\Utils\HttpRequest;


interface ICategoryTreeModalContainerFactory extends IBaseContainer
{
	/** @return CategoryTreeModalContainer */
	public function create();
}


class CategoryTreeModalContainer extends BaseContainer
{
    /** @var StatusTypeRegister */
    protected $statusTypeRegister;

    /** @var HttpRequest */
    private $httpRequest;

    /** @var string */
    protected $type;


    public function __construct(
        \Nette\DI\Container $container,
        StatusTypeRegister $statusTypeRegister,
        HttpRequest $httpRequest
    ) {
        parent::__construct($container);

        $this->statusTypeRegister = $statusTypeRegister;
        $this->httpRequest = $httpRequest;
    }


    /** {@inheritDoc} */
    public function configure()
	{
        $this->type = $this->httpRequest->getParameter('t');

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