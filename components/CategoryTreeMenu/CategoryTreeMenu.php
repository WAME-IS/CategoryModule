<?php

namespace Wame\CategoryModule\Controls;

use Nette;
use Nette\Forms;
use Nette\Utils\Html;

use Wame\CategoryModule\Controls\BaseControl;

class CategoryTreeMenu extends BaseControl
{

	public function __construct() {
		parent::__construct();
	}
	
	public function render()
	{
		$items = $this->categoryRepository->getTree(['status' => 1]);
		$this->template->tree = $this->generate($items);
		$this->template->render(__DIR__ . '/category_tree_menu.latte');
	}
	
	public function getControl()
	{
		dump('test'); exit;
		
		return 'aaa';
	}
	
	public function generate($items)
	{
		$ul = Html::el('ul');
		
		if($items) {
			$li = Html::el('li');
				$body = null;
				$body .= Html::el('a')
						->href('/article-category/article/show/' . $items->item->id)
						->setText($items->item->langs[$this->categoryRepository->lang]->title);

				if(sizeof($items->child_nodes) > 0) {
					foreach($items->child_nodes as $child) {
						if($child->status == 1) $body .= Html::el('li')->setHtml($this->generate($child));
					}
				}
			$li->setHtml($body);
			$ul->setHtml($li);
		}
		
		return $ul;
	}
	
}