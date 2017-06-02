SET NAMES 'utf8';

UPDATE referentiel.requetes SET sql_entete =  'CACHE,SEARCH,SEARCH,SEARCH' where id = 1 ;

UPDATE referentiel.requetes SET sql_select =  'select  id as "Identifiant",nom as "Nom", prenom as "Prenom", dtnai as "Date de naissance" from (select id,nom,prenom,to_char(dtnai,''YYYY-MM-DD'') as "dtnai" from public.personnes) as Requetes' where id = 1 ;

