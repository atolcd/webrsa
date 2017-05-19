SET NAMES 'utf8';
ALTER TABLE referentiel.requetes ADD COLUMN sql_entete text;

UPDATE referentiel.requetes 
SET sql_entete =  'CACHE,SEARCH,SEARCH,NULL' where id = 1 ;

UPDATE referentiel.requetes 
SET sql_select =  'select  id as "Identifiant",nom as "Nom", prenom as "Prenom", dtnai as "Date de naissance" from personnes as Requetes' where id = 1 ;

