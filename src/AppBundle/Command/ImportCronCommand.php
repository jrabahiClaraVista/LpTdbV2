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
			->setName('cron:import') 
			->setDescription('Lancement de la tache cron:import')
			->addArgument('separator', InputArgument::REQUIRED, 'CSV separator?')
			//->addOption('yell', null, InputOption::VALUE_NONE, 'Si définie, la tâche criera en majuscules')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{ 
		
		$text = $this->getDescription();
		$output->writeln($text);

        //$filename1 = "/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/TABLEAU_DE_BORD_lp_rq.csv";
        $filename1 = "D:\wamp\www\LpTdbV2\web\imports\TABLEAU_DE_BORD_lp_rq.csv";

		if ( file_exists($filename1) ) {

			$import = $this->getContainer()->get('cron.import');

			$output->writeln("Configuration du separateur");
			$import->setSeparator($input->getArgument('separator'));
			
			#Lncl
			# -> Set Import table clean for new update
			# -> import clients - update campaign
			# -> Delete users not in import file
			#$output->writeln("Nettoyage de la table d'import");
			#$import->deleteImportLncl();		
			#$output->writeln("Importation des clients et mise a jour des cibles");
			#$result = $import->importClientCSVFileLncl();
			#$import->deleteClientsNotInImport();
			# -> Delete Clients not in Import to do

			#Lp
			# -> Import/update Marque / Dr / Boutique Users
			$output->writeln("Mise a jour des utilisateurs");
			$result = $import->importClientCSVFileLp("User");
			# -> Import Kpi of the month
			$output->writeln("Import des Kpis");
			$result = $import->importClientCSVFileLp("KpiMonth");
			$result = $import->importClientCSVFileLp("KpiYearToDate");
			# -> Update User fields for Kpis
			$import->setUserforKpiLp();
			
			$output->writeln("Archivage du fichier");
			$import->renameLastImport();

			# -> Get import result and save it
			$output->writeln("Archivage de l'import");
			$importFile = $this->getContainer()->get('import.file.log');
			$importFile->AddImportFile($result);
		}
		else{
			$output->writeln("Aucun fichier, annulation de l'import");
		}

		$output->writeln("Tache terminee");	
	}
}