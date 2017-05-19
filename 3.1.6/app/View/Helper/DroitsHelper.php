<?php
	/**
	 * Fichier source de la classe DroitsHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe DroitsHelper ...
	 *
	 * @package app.View.Helper
	 */
	class DroitsHelper extends Helper
	{

		public $helpers = array('Xhtml', 'Html');

		/* Affiche l'ntête du tableau avec les éléments du menu et les actions des controleurs */
		public function afficheEnTeteTableau($menuTree, $nbLigneTable) {
			$nbLigneEntete = $this->_profondeur($menuTree);
			$enTeteTab = array();
			$this->_construitLigneEnTete($enTeteTab, $menuTree, $nbLigneEntete, $nbLigneTable);
			foreach($enTeteTab as $enTeteTr) {
				echo '<tr>';
				// On ajoute une cellule d'entete vide qui correspond à la colonne d'affichage des profils et des utilisateurs
				echo '<th></th>';
				foreach($enTeteTr as $enTeteTh) {

					echo $enTeteTh;
				}
				echo '</tr>';
			}
		}

		/* Construit le tableau $enTeteTab en vu de l'affichage de l'entête du tableau */
		protected function _construitLigneEnTete(&$enTeteTab, $menuTree, $nbLigneEnTete, $nbLigneTable, $iLigne=1, $celIdSup='') {
			// Initialisation
			static $iCol = 1;

			// Parcours des éléments du menu
			foreach($menuTree as $menu) {
				$celId = ($celIdSup ? $celIdSup : 'C') . sprintf("%02d", $iCol);

				// Construction du html de la cellule
				$htmlCel = '<th id=\''. $celId . '\'' . ($menu['nbElement']>0 ? ' colspan='.($menu['nbElement']+1):'') . ' class="droits" >';
				if ($menu['nbElement']>0) {
					$idTogColImgId = $celId . 'Img';
					$htmlCel .=  "<a href=\"javascript:toggleCol('$celId', ".$menu['nbElement'].", $nbLigneTable, '$idTogColImgId');\">";
					$htmlCel .=  $this->Html->image('/img/icons/replier.png', array('id'=>$idTogColImgId));
					$htmlCel .=  "</a> ";
				};
				$htmlCel .= $menu['title'] . '</th>';
				$enTeteTab[$iLigne-1][$iCol-1] = $htmlCel;

				// Affichage du caractère | sur les cellules des lignes inférieures
				$celIdInf = $celId;
				for ($i=$iLigne; $i<$nbLigneEnTete; $i++) {
					$celIdInf .= sprintf("%02d", $iCol);
					$enTeteTab[$i][$iCol-1]='<th id=\''.$celIdInf.'\' class="droits"  >|</th>';
				}

				// On passe à la colonne suivante
				$iCol++;

				// Traitement du sous-menu
				if (is_array($menu))
								if (array_key_exists('subMenu', $menu)) {
					$this->_construitLigneEnTete($enTeteTab, $menu['subMenu'], $nbLigneEnTete, $nbLigneTable, $iLigne+1, $celId);
					}
			}
		}

		/* retourne le nombre de lignes de l'entête du tableau */
		protected function _profondeur($menuTree, $prof=1) {
			static $profMax=0;

			if ($prof>$profMax) $profMax = $prof;
			foreach($menuTree as $menu) {
				if (is_array($menu))
							if (array_key_exists('subMenu', $menu)) $this->_profondeur($menu['subMenu'], $prof+1);
			}
			return $profMax;
		}

		/* Affiche les lignes du tableau */
		public function afficheCorpsTableau($profilsUsersTree, $structColonnes, $nbCol, $tabDroits) {
			$this->_afficheLigneTableau($profilsUsersTree, $structColonnes, $nbCol, $tabDroits);
		}

		/* Affiche les lignes du tableau (récursive) */
		protected function _afficheLigneTableau($profilsTree, $structColonnes, $nbCol, $tabDroits, &$iLigne=0, $prof=0) {

			// Initialisations
			static $altProfil = false;
			$altUser = false;
			$margeProf = str_repeat('&nbsp;&nbsp;',$prof);
			$margeUser = str_repeat('&nbsp;&nbsp;',$prof+1);

			// Parcours des profils
			foreach($profilsTree as $profil) {
				// Nouvelle ligne du tableau
				$iLigne++;
				if ($prof==0) $altProfil = !$altProfil;
				echo '<tr id=\'l'.$iLigne.'\' class=\''.($altProfil ? 'altLigneProfil':'ligneProfil').'\'>';
					// Affichage du profil dans première céllule
					echo '<td>';
						echo $margeProf;

						if ($profil['nbElement']>0) {
							$togLigImgId = 'togLigImg' . $iLigne;
							echo "<a href=\"javascript:toggleLigne(" . ($iLigne+1) . ", " . ($iLigne+$profil['nbElement']) . ", '$togLigImgId');\">";
							echo $this->Html->image('/img/icons/replier.png', array('id'=>$togLigImgId));
							echo "</a>";
						} else echo $this->Html->image('/img/icons/tiret.png');

						if ($profil['name'] == 'Sans profil') echo ' Utilisateurs sans profil';
						else echo 'Profil : ' . $profil['name'];
					echo '</td>';

					// Affichage des cellules du profil
					for($i=1; $i<=$nbCol; $i++) {
						echo '<td id=\'l'.$iLigne.'c'.$i.'\'>';
						echo "<input type='checkbox' " . ($tabDroits[$iLigne-1][$i-1]=='1' ? "checked " : "") . "onclick='toggleCheckBox($i, ".($i+$structColonnes[$i-1]).", $iLigne, " . ($iLigne+$profil['nbElement']) . ")'></input>";
						echo '</td>';
					}
				echo '</tr>';

				// Affichage des utilisateurs associés
				if (array_key_exists('users', $profil)) {
					foreach($profil['users'] as $user) {
						// Nouvelle ligne du tableau
						$iLigne++;
						$altUser = !$altUser;
						echo '<tr id=\'l'.$iLigne.'\' class=\''.($altUser ? 'altLigneUtilisateur':'ligneUtilisateur').'\'>';
							// Affichage de l'utilisateur
							echo '<td>';
								echo $margeUser . $user['username'];
							echo '</td>';

							// Affichage des celulles de l'utilisateur
							for($i=1; $i<=$nbCol; $i++) {
								echo '<td id=\'l'.$iLigne.'c'.$i.'\'>';
									echo "<input type='checkbox' " . ($tabDroits[$iLigne-1][$i-1]=='1' ? "checked " : "") . "onclick='toggleCheckBox($i, ".($i+$structColonnes[$i-1]).", $iLigne, $iLigne)'></input>";
								echo '</td>';
							}
						echo '</tr>';
					}
				}

				// Traitement des sous-profils
				if (array_key_exists('sousProfils', $profil))
					$this->_afficheLigneTableau($profil['sousProfils'], $structColonnes, $nbCol, $tabDroits, $iLigne, $prof+1);
			}
		}

	}
?>