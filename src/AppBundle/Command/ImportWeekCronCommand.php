<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportWeekCronCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('cron:importKpiWeek')
			->setDescription('Lancement de l\'import des kpi hebdomadaire')
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
            $filename1 = "D:\wamp64_3.1.0\www\LpTdbV3\web\imports\TABLEAU_DE_BORD_hebdo_lp_rq.csv";
        }
        else{
			$filename1 = "/data/ftp/imports/TABLEAU_DE_BORD_hebdo_lp_rq.csv";
        }

		$output->writeln($filename1);
		if ( file_exists($filename1) ) {

			$text = $this->getDescription();
			$output->writeln($text);

			$import = $this->getContainer()->get('cron.import');

			$output->writeln("Configuration du separateur");
			$import->setSeparator($input->getArgument('separator'));

			$output->writeln("Import des Kpi Capture");
			$import->importKpiCaptureSemaineCSVFile($input, $output, $filename1);

			$output->writeln("Update nb Transac User");
			$import->updateUserTransac($input, $output);

			$output->writeln("Delete histo > 30 jours");
			$import->deleteHistoDays($input, $output, 30);

			//$import->setUserforKpiLp();

			$output->writeln("Archivage du fichier");
			$import->renameLastImportWeek();
		} else {
		    $output->writeln("Aucun fichier, annulation de l'import");
		}


		$date2 = new \DateTime();
		$date2 = $date2->format('H:i:s');
		$output->writeln("debut : ".$date1." | fin : ".$date2);
	}
}
