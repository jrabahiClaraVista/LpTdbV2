<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportTrimHebdoCronCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('cron:importKpiTrimHebdo')
			->setDescription('Lancement de l\'import des kpi trimestrielle hebdomadaire')
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
            $filename1 = "E:\wamp64\www\LpTdbV3\web\imports\Desabo_Hardbounce_LP.csv";
        }
        else{
			$filename1 = "/data/ftp/imports/Desabo_Hardbounce_LP.csv";
        }

		$output->writeln($filename1);
		if ( file_exists($filename1) ) {

			$text = $this->getDescription();
			$output->writeln($text);

			$import = $this->getContainer()->get('cron.import');

			$output->writeln("Configuration du separateur");
			$import->setSeparator($input->getArgument('separator'));

			$output->writeln("Import des Kpi de Desabo");
			$import->importKpiCaptureTrimestreHebdoCSVFile($input, $output, $filename1);

			$output->writeln("Archivage du fichier");
			$import->renameLastImportTrimHebdo();
		} else {
		    $output->writeln("Aucun fichier, annulation de l'import");
		}


		$date2 = new \DateTime();
		$date2 = $date2->format('H:i:s');
		$output->writeln("debut : ".$date1." | fin : ".$date2);
	}
}
