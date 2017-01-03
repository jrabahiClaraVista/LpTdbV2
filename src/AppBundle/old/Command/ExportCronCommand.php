<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCronCommand extends ContainerAwareCommand 
{ 
	protected function configure() 
	{ 
		$this 
			->setName('cron:export') 
			->setDescription('Lancement de la tache cron:export')			
			//->addArgument('host', InputArgument::REQUIRED, 'FTP Host?')
			//->addArgument('username', InputArgument::REQUIRED, 'Username?')
			//->addArgument('password', InputArgument::REQUIRED, 'Password?')
			//->addArgument('sourceFile', InputArgument::REQUIRED, 'sourceFile?')
			//->addArgument('destinationFile', InputArgument::REQUIRED, 'destinationFile?')
			//->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{ 
		
		$text = $this->getDescription();

		//$host = $input->getArgument('host');
		//$username = $input->getArgument('username');
		//$password = $input->getArgument('password');
		//$sourceFile = $input->getArgument('sourceFile');
		//$destinationFile = $input->getArgument('destinationFile');

		$import = $this->getContainer()->get('cron.export');

		$import->createExportClientCSVFile();
		//$import->exportCSVFileToFtp($host, $username, $password, $sourceFile, $destinationFile);


		$output->writeln($text);
	}
}