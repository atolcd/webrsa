<?php

	/**
	 * Code source de la classe WebrsaPdfUtility.
	 *
	 * @package app.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe WebrsaPdfUtility permet de manipuler des groupes de PDFs
	 *
	 * @package app.Utility
	 */
	class WebrsaPdfUtility {
		/**
		 * Valeur pour ajouter des pages blanches entre les PDFs
		 * 
		 * @var boolean
		 */
		const ADD_BLANK_PAGES_BETWEEN_PDFS = true;
		
		/**
		 * Dossier racine où serons créer les fichiers.
		 * 
		 * @var string
		 */
		public $basePath;
		
		/**
		 * Chemin vers le fichier PDF blank
		 * 
		 * @var string
		 */
		public $blankPdfPath;
		
		/**
		 * Binary du fichier PDF blank
		 * 
		 * @var string
		 */
		public $binBlankPdf;
		
		/**
		 * Nombre d'erreurs, si tout s'est bien passé, doit être égal à 0
		 * 
		 * @var integer
		 */
		public $error = 0;
		
		/**
		 * Constructeur de classe, défini le basePath
		 */
		public function __construct() {
			$this->basePath = APP . 'tmp/files/';
			$this->blankPdfPath = APP . 'Vendor/modelesodt/Webrsa/blank.pdf';
			
			if (!is_file($this->blankPdfPath)) {
				throw new Exception('Blank PDF not found at: '.$this->blankPdfPath);
			}
			
			if (!file_exists($this->basePath)) {
				mkdir($this->basePath, 0777, true);
				chmod($this->basePath, 0777);
			}
			
			$File = fopen($this->blankPdfPath, "r");
			$this->binBlankPdf = fread($File, filesize($this->blankPdfPath));
			fclose($File);
		}
	
		/**
		 * Permet d'obtenir le nombre de pages d'un pdf
		 * 
		 * @param string $pdf Binaire du pdf
		 * @return integer Nombre de pages du pdf
		 */
		public function getBinaryPageCount( $pdf ) {
			$tmpFileName = $this->_generateTmpFile($pdf);
			exec("pdftk \"{$tmpFileName}\" dump_data", $output);
			unlink($tmpFileName);

			$pageCount = 0;
			foreach($output as $op)	{
				if(preg_match("/NumberOfPages:\s*(\d+)/i", $op, $matches) === 1) {
					$pageCount = (integer)$matches[1];
					break;
				}
			}
			
			if ( $pageCount === 0 ) {
				$this->error++;
			}
			
			return $pageCount;
		}
		
		/**
		 * Permet d'ajouter une page blanche si un fichier PDF possède un nombre 
		 * de page impaire pour chaques PDFs dans la liste.
		 * 
		 * @param array $pdfList Liste des PDFs à fusionner
		 * @param boolean $addBlankPagesBetweenPdfs Permet si vrai, d'ajouter une page vide entre chaques PDFs
		 * @return array Liste des fichiers PDFs prêt à fusionner
		 */
		public function preparePdfListForRectoVerso( array $pdfList, $addBlankPagesBetweenPdfs = false ) {
			$pdfListWithBlankPages = array();
			$pdfs = array_values($pdfList);

			for ($i=0; $i<count($pdfs); $i++) {
				if ( is_array($pdfs[$i]) ) {//debug("Exception");
					throw new Exception('Nested array are not allowed');
				}

				$nbPages = $this->getBinaryPageCount($pdfs[$i]);
				$pdfListWithBlankPages[] = $pdfs[$i];

				if ( $nbPages % 2 ) {
					$pdfListWithBlankPages[] = $this->binBlankPdf;
				}
				if ( $addBlankPagesBetweenPdfs ) {
					$pdfListWithBlankPages[] = $this->binBlankPdf;
					$pdfListWithBlankPages[] = $this->binBlankPdf;
				}
			}
			
			if ( $this->error > 0 ) {
				return false;
			}
			
			return $pdfListWithBlankPages;
		}
		
		/**
		 * Génère un fichier temporaire
		 * 
		 * @param string $data
		 * @return \WebrsaPdfUtility
		 */
		protected function _generateTmpFile( $data ) {
			$fileName = 'WebrsaPdfUtilityTmpFile-'.session_id().'.tmp';
			$fullPath = $this->basePath.$fileName;
			
			if (is_file($fullPath)) {
				unlink($fullPath);
			}
			
			$File = fopen($fullPath, "w+");
			
			if ( $File && chmod($fullPath, 0777) && fwrite($File, $data) ) {
				fclose($File);
				$this->_files[] = $fullPath;
			}
			else {
				$this->error++;
			}
			
			return $fullPath;
		}
	}
