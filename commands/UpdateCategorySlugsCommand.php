<?php

namespace Wame\CategoryModule\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\CategoryModule\Repositories\CategoryRepository;
use Wame\Utils\Strings;

class UpdateCategorySlugsCommand extends Command
{
    /** @var CategoryRepository @inject */
    public $repository;
    

	protected function configure()
    {
		$this->setName('category:update-slugs')
				->setDescription('Updates category slugs');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		
		try {
            $categories = $this->repository->find();
            
            foreach($categories as $category) {
                $category->setSlug(Strings::webalize($category->getTitle()));
                $this->repository->update($category);
            }
            
            $this->repository->entityManager->flush();
            
			$output->writeLn("The category slugs (count: " . count($categories) . ") were successfully updated.");
			return 0; // zero return code means everything is ok
		} catch (SmtpException $e) {
			$output->writeLn('<error>' . $e->getMessage() . '</error>');
			return 1; // non-zero return code means error
		}
	}

}
