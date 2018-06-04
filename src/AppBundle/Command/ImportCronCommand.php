<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCronCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('cron:importKpi')
			->setDescription('Lancement de l\'import des kpi')
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
            $filename1 = "D:\wamp64_3.1.0\www\LpTdbV3\web\imports\TABLEAU_DE_BORD_lp_rq.csv";
        }
        else{
			$filename1 = "/data/ftp/imports/TABLEAU_DE_BORD_lp_rq.csv";
        }

		if ( file_exists($filename1) ) {
		    $text = $this->getDescription();
			$output->writeln($text);

			$import = $this->getContainer()->get('cron.import');

			$output->writeln("Configuration du separateur");
			$import->setSeparator($input->getArgument('separator'));

			$output->writeln("Import des Kpi Capture");
			$import->importKpiCaptureCSVFile($input, $output, $filename1);

			$output->writeln("Update nb Transac User");
			$import->updateUserTransac($input, $output);

			//$import->setUserforKpiLp();

			$output->writeln("Archivage du fichier");
			$import->renameLastImport();
		} else {
		    $output->writeln("Aucun fichier, annulation de l'import");
		}


	    /*$text = $this->getDescription();
		$output->writeln($text);

		$import = $this->getContainer()->get('cron.import');

		$output->writeln("Configuration du separateur");
		$import->setSeparator($input->getArgument('separator'));

		$output->writeln("Import des Kpi Capture");
		$files = $import->scanDir();
		$i = 1;
		foreach ($files as $csv) {
			if(substr($csv, -4) == ".csv" ){
				$output->writeln('Ouverture du fichier '.$i.' : '.$csv);
				$import->importKpiCaptureCSVFile($input, $output, "D:\wamp\www\LpTdbV3\web\imports\\".$csv);
				$i++;
			}
		}*/

		$date2 = new \DateTime();
		$date2 = $date2->format('H:i:s');
		$output->writeln("debut : ".$date1." | fin : ".$date2);
	}
}
