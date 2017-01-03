<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteClientsDailyCronCommand extends ContainerAwareCommand 
{ 
	protected function configure() 
	{ 
		$this 
			->setName('cron:deleteClientsDaily') 
			->setDescription('Lancement de la tache cron:deleteClientsDaily')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{ 
		
		$text = $this->getDescription();

		$dateDiff = $this->getContainer()->get('cron.delete.daily');

		$result = $dateDiff->deleteJPlus15Clients();

		$output->writeln($text);

		$output->writeln($result);
		
	}
}