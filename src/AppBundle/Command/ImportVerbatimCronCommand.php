<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportVerbatimCronCommand extends ContainerAwareCommand 
{ 	
	protected function configure() 
	{ 
		$this 
			->setName('cron:importVerbatim') 
			->setDescription('Lancement de l\'import des verbatims')
			->addArgument('separator', InputArgument::REQUIRED, 'CSV separator?')
			//->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{ 
		$ip = $this->getContainer()->getParameter('local_ip');

		
		$date1 = new \DateTime();
		$date1 = $date1->format('H:i:s');

		$date = new \DateTime();
        $date = $date->format("Ymd");

        if($ip == "127.0.0.1")
        {
            $filename1 = "D:\wamp64\www\LpTdbV3\web\imports\Verbatim_Mois.csv";
        }
        else{
			$filename1 = "/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/Verbatim_Mois.csv";            
        }

		if ( file_exists($filename1) ) {
		    $text = $this->getDescription();
			$output->writeln($text);

			$import = $this->getContainer()->get('cron.import');

			$output->writeln("Configuration du separateur");
			$import->setSeparator($input->getArgument('separator'));

			$output->writeln("Import des Verbatims");
			$import->importVerbatim($input, $output, $filename1);

			//$import->setUserforKpiLp();
			
			$output->writeln("Archivage du fichier");		
			$import->renameLastImportVerbatim();
		} else {
		    $output->writeln("Aucun fichier, annulation de l'import");
		}

		$date2 = new \DateTime();
		$date2 = $date2->format('H:i:s');
		$output->writeln("debut : ".$date1." | fin : ".$date2);
	}
}