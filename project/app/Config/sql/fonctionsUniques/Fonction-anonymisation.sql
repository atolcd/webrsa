SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
-- Fonction de génration d'un nom Aaaaa d'une longueur voulue
-- *****************************************************************************
Create or replace function random_name(length integer) returns text as
$$
declare
	--déclaration des variables
  chars text[] := '{a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z}';
  caps text[] := '{A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z}';
  result text := '';
  i integer := 1;
BEGIN
	--Début de la fonction
  IF length < 0 THEN
 	-- Si la longueur voulue est 0 on genre une erreure
    raise exception 'Given length cannot be less than 0';
  END IF;
  -- On génere une Majuscule
  result := caps[1+random()*(array_length(chars, 1)-1)];
  -- On boucle pour avoir assez de caracteres
  FOR i in 1..length LOOP
 	--Ajout d'un caractère aléatoir a la chaine
    result := result || chars[1+random()*(array_length(chars, 1)-1)];
   END LOOP;
  RETURN result;
END ;
$$ LANGUAGE plpgsql;

-- *****************************************************************************
-- Fonction de remplacement des noms de la personne
-- *****************************************************************************
CREATE OR REPLACE FUNCTION personnes_replace() RETURNS VOID AS
$$
DECLARE
	--
    perso RECORD;
    tmpName text := '';
 BEGIN
 FOR perso IN SELECT id FROM personnes LOOP
	-- génération de la chaine utilisée pour le nom et le nomnai
	tmpName := (SELECT random_name(8));
	-- Update des valeurs
	UPDATE personnes SET
		nom= tmpName,
		nomnai= tmpName,
		prenom= (SELECT random_name(8)),
		prenom2= (SELECT random_name(6)),
		prenom3='',
		email=tmpName||'@test.com'
	WHERE id = perso.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM personnes_replace();

-- Suppression de la fonction
DROP FUNCTION personnes_replace();

-- *****************************************************************************
-- Fonction de remplacement des rattachements
-- *****************************************************************************
CREATE OR REPLACE FUNCTION rattache_replace() RETURNS VOID AS
$$
DECLARE
    rattache RECORD;
    tmppersonnes RECORD;
 BEGIN
 FOR rattache IN SELECT id,personne_id FROM rattachements LOOP
	SELECT nomnai FROM personnes P WHERE rattache.personne_id = P.id LIMIT 1 INTO tmppersonnes ;
	UPDATE rattachements AS R SET
		nomnai = tmppersonnes.nomnai,
		prenom = (SELECT random_name(6))
	WHERE id = rattache.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM rattache_replace();

-- Suppression de la fonction
DROP FUNCTION rattache_replace();

-- *****************************************************************************
-- Fonction de remplacement des situationsallocataires
-- *****************************************************************************
CREATE OR REPLACE FUNCTION situationsallocataires_replace() RETURNS VOID AS
$$
DECLARE
    situationsalloc RECORD;
    tmppersonnes RECORD;
 BEGIN
 FOR situationsalloc IN SELECT id,personne_id FROM situationsallocataires LOOP
	SELECT nom, prenom FROM personnes P WHERE situationsalloc.personne_id = P.id LIMIT 1 INTO tmppersonnes ;
	UPDATE situationsallocataires SET
		nom = tmppersonnes.nom,
		nomnai = tmppersonnes.nom,
		prenom = tmppersonnes.prenom
	WHERE id = situationsalloc.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM situationsallocataires_replace();

-- Suppression de la fonction
DROP FUNCTION situationsallocataires_replace();

-- *****************************************************************************
-- Fonction de remplacement des histoaprecomplementaires
-- *****************************************************************************
CREATE OR REPLACE FUNCTION histoaprecomplementaires_replace() RETURNS VOID AS
$$
DECLARE
    histoaprecomple RECORD;
    tmppersonnes RECORD;
 BEGIN
 FOR histoaprecomple IN SELECT id,personne_id FROM histoaprecomplementaires LOOP
	SELECT nom, prenom FROM personnes P WHERE histoaprecomple.personne_id = P.id LIMIT 1 INTO tmppersonnes ;
	UPDATE histoaprecomplementaires SET
		nom = tmppersonnes.nom,
		nom_titulaire_rib = tmppersonnes.nom,
		nom_referent = tmppersonnes.nom,
		nom_agent = tmppersonnes.nom,
		prenom = tmppersonnes.prenom,
		prenom_titulaire_rib = tmppersonnes.prenom,
		prenom_referent = tmppersonnes.prenom,
		prenom_agent = tmppersonnes.prenom
	WHERE id = histoaprecomple.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM histoaprecomplementaires_replace();

-- Suppression de la fonction
DROP FUNCTION histoaprecomplementaires_replace();

-- *****************************************************************************
-- Fonction de remplacement des personnescuis, pour le CG66 (inutile au CG 93)
-- *****************************************************************************
CREATE OR REPLACE FUNCTION personscuis_replace() RETURNS VOID AS
$$
DECLARE
    personscuis RECORD;
    tmppersonnes RECORD;
 BEGIN
 FOR personscuis IN SELECT id,nir FROM personnescuis LOOP
	SELECT nom, prenom, prenom2 FROM personnes P WHERE personscuis.nir = P.nir LIMIT 1 INTO tmppersonnes ;
	UPDATE personnescuis SET
		nomfamille=tmppersonnes.nom,
		nomusage=tmppersonnes.prenom,
		prenom1=tmppersonnes.prenom,
		prenom2=tmppersonnes.prenom2,
		prenom3=''
	WHERE id = personscuis.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM personscuis_replace();

-- Suppression de la fonction
DROP FUNCTION personscuis_replace();


-- *****************************************************************************
-- Update des suivit instruction
-- ***************************************************************************** 
UPDATE suivisinstruction AS SI SET
	nomins = (
		SELECT P.nom
		FROM foyers  AS F
		INNER JOIN personnes AS P ON P.foyer_id = F.id
		INNER JOIN prestations AS Prest ON P.id = Prest.personne_id
		WHERE F.dossier_id = SI.dossier_id
		AND Prest.rolepers = 'DEM'
		LIMIT 1
	),
	prenomins = (
		SELECT P.prenom
		FROM foyers  AS F
		INNER JOIN personnes AS P ON P.foyer_id = F.id
		INNER JOIN prestations AS Prest ON P.id = Prest.personne_id
		WHERE F.dossier_id = SI.dossier_id
		AND Prest.rolepers = 'DEM'
		LIMIT 1
	)
WHERE true;

-- *****************************************************************************
-- Fonction de remplacement des noms de pcreprise
-- *****************************************************************************
CREATE OR REPLACE FUNCTION pcreprise_replace() RETURNS VOID AS
$$
DECLARE
	--
   tmppcreprise RECORD;
   tmppersonnes RECORD;
 BEGIN
 FOR tmppcreprise IN SELECT id, "Personne__id" FROM pcreprise LOOP
	SELECT nom, prenom, prenom2 FROM personnes P WHERE P.id =  tmppcreprise."Personne__id" LIMIT 1 INTO tmppersonnes ;
	-- Update des valeurs
	UPDATE pcreprise SET
		nom = tmppersonnes.nom,
		"Personne__nom" = tmppersonnes.nom,
		"Personne__prenom" = tmppersonnes.prenom,
		"Personne__prenom2" = tmppersonnes.prenom2,
		"Personne__prenom3" = ''
	WHERE id = tmppcreprise.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM pcreprise_replace();

-- Suppression de la fonction
DROP FUNCTION pcreprise_replace();

-- *****************************************************************************    
-- Fonction de remplacement des nom de users
-- *****************************************************************************
CREATE OR REPLACE FUNCTION users_replace() RETURNS VOID AS
$$
DECLARE
    user RECORD;
    tmpName text := '';
 BEGIN
 FOR user IN SELECT id FROM users LOOP
	tmpName := (SELECT random_name(8));
	UPDATE users SET
		 nom=tmpName,
		 prenom=(SELECT random_name(6)),
		 email=tmpName||'@test.com'
	WHERE id = user.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

 -- Lancement de la fonction 
SELECT * FROM users_replace();

-- Suppression de la fonction
DROP FUNCTION users_replace();

-- *****************************************************************************
-- Update de la table CERS93, Inutile au CG 66
-- *****************************************************************************
  UPDATE cers93 AS CER SET
	nom = (SELECT nom FROM personnes P
		INNER JOIN contratsinsertion AS CI ON CI.personne_id = P.id
		WHERE CI.id = CER.contratinsertion_id
		LIMIT 1),
	nomnai = (SELECT nomnai FROM personnes P
		INNER JOIN contratsinsertion AS CI ON CI.personne_id = P.id
		WHERE CI.id = CER.contratinsertion_id
		LIMIT 1),
	prenom = (SELECT prenom FROM personnes P
		INNER JOIN contratsinsertion AS CI ON CI.personne_id = P.id
		WHERE CI.id = CER.contratinsertion_id
		LIMIT 1),
	 nomutilisateur = (SELECT nom||' '||prenom FROM users AS U 
		INNER JOIN contratsinsertion_users AS CI_U ON CI_U.user_id = U.id
		INNER JOIN contratsinsertion AS CI ON CI.personne_id = CI_U.contratinsertion_id
		WHERE CI.id = CER.contratinsertion_id
		LIMIT 1)
WHERE true;

-- *****************************************************************************
-- Fonction de remplacement des referents
-- *****************************************************************************
CREATE OR REPLACE FUNCTION referents_replace() RETURNS VOID AS
$$
DECLARE
    ref RECORD;
    tmpName text := '';
 BEGIN
 FOR ref IN SELECT id FROM referents LOOP
	tmpName := (SELECT random_name(8));
	UPDATE referents SET
		nom= tmpName,
		prenom=(SELECT random_name(8)),
		email=tmpName||'@test.com'
	WHERE id = ref.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM referents_replace();

-- Suppression de la fonction
DROP FUNCTION referents_replace();

-- *****************************************************************************
-- Fonction de remplacement des nom de informationspe
-- *****************************************************************************
CREATE OR REPLACE FUNCTION infoPE_replace() RETURNS VOID AS
$$
DECLARE
    infoPE RECORD;
 BEGIN
 FOR infoPE IN SELECT id FROM informationspe LOOP
	UPDATE informationspe SET
		 nom=(SELECT random_name(8)),
		 prenom=(SELECT random_name(6))
	WHERE id = infoPE.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

 -- lancement de la fonction 
SELECT * FROM infoPE_replace();

-- Suppression de la fonction
DROP FUNCTION infoPE_replace();

-- *****************************************************************************
-- Update des Commissionseps
-- *****************************************************************************
UPDATE commissionseps SET
	chargesuivi= (SELECT random_name(6)||' '||random_name(8) ),
	gestionnairebat= (SELECT random_name(6)||' '||random_name(8)),
	gestionnairebada= (SELECT random_name(6)||' '||random_name(8));

-- *****************************************************************************
-- Update des Membres des Commissionseps
-- *****************************************************************************
-- Fonction de remplacement de membreseps
CREATE OR REPLACE FUNCTION membreseps_replace() RETURNS VOID AS
$$
DECLARE
    membreseEP RECORD;
    tmpName text := '';
 BEGIN
 FOR membreseEP IN SELECT id FROM membreseps LOOP
	tmpName := (SELECT random_name(8));
	UPDATE membreseps AS PCUI SET
		nom= tmpName,
		prenom= (SELECT random_name(6)),
		mail= tmpName||'@test.com'
	WHERE id = membreseEP.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM membreseps_replace();

-- Suppression de la fonction
DROP FUNCTION membreseps_replace();

-- *****************************************************************************    
-- Fonction de remplacement des nom de participantscomites
-- *****************************************************************************
CREATE OR REPLACE FUNCTION partComm_replace() RETURNS VOID AS
$$
DECLARE
    partComm RECORD;
    tmpName text := '';
 BEGIN
 FOR partComm IN SELECT id FROM participantscomites LOOP
	tmpName := (SELECT random_name(8));
	UPDATE participantscomites SET
		 nom=tmpName, 
		 prenom=(SELECT random_name(6)),
		mail=tmpName||'@test.com'
	WHERE id = partComm.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;
    
 -- lancement de la fonction      
SELECT * FROM partComm_replace();
	
-- Suppression de la fonction
DROP FUNCTION partComm_replace();

-- *****************************************************************************
-- Fonction de remplacement des noms de composfoyerscers93
-- *****************************************************************************
CREATE OR REPLACE FUNCTION ComFoyCER93_replace() RETURNS VOID AS
$$
DECLARE
	--
    ComFoyCER93 RECORD;
 BEGIN
 FOR ComFoyCER93 IN SELECT id FROM composfoyerscers93 LOOP
	-- Update des valeurs
	UPDATE composfoyerscers93 SET
		nom= (SELECT random_name(8)),
		prenom= (SELECT random_name(6))
	WHERE id = ComFoyCER93.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM ComFoyCER93_replace();

-- Suppression de la fonction
DROP FUNCTION ComFoyCER93_replace();

-- *****************************************************************************
-- Fonction de remplacement des noms de contactspartenaires
-- *****************************************************************************
CREATE OR REPLACE FUNCTION contrapart_replace() RETURNS VOID AS
$$
DECLARE
	--
    contrapart RECORD;
    tmpName text := '';
 BEGIN
 FOR contrapart IN SELECT id FROM contactspartenaires LOOP
 	tmpName := (SELECT random_name(6));
	-- Update des valeurs
		UPDATE contactspartenaires SET
		nom= tmpName,
		prenom= (SELECT random_name(6)),
		email= tmpName||'@test.com'
	WHERE id = contrapart.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM contrapart_replace();

-- Suppression de la fonction
DROP FUNCTION contrapart_replace();

-- *****************************************************************************
-- Fonction de remplacement des noms de pc
-- *****************************************************************************
CREATE OR REPLACE FUNCTION pc_replace() RETURNS VOID AS
$$
DECLARE
	--
    tmppc RECORD;
 BEGIN
 FOR tmppc IN SELECT id FROM pc LOOP
	-- Update des valeurs
	UPDATE pc SET
		nom = (SELECT random_name(6))
	WHERE id = tmppc.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM pc_replace();

-- Suppression de la fonction
DROP FUNCTION pc_replace();

-- *****************************************************************************
-- Update des refsprestas
-- *****************************************************************************
UPDATE refsprestas SET
	nomrefpresta= (SELECT random_name(8) ),
	prenomrefpresta= (SELECT random_name(6)),
	emailrefpresta= (SELECT random_name(6)||'@test.com');

-- *****************************************************************************
-- Fonction de remplacement des noms de suivisaidesapres
-- *****************************************************************************
CREATE OR REPLACE FUNCTION suivisaidesapres_replace() RETURNS VOID AS
$$
DECLARE
	--
    tmpsuivisaidesapres RECORD;
 BEGIN
 FOR tmpsuivisaidesapres IN SELECT id FROM suivisaidesapres LOOP
	-- Update des valeurs
	UPDATE suivisaidesapres SET
		nom = (SELECT random_name(6)),
		prenom = (SELECT random_name(4))
	WHERE id = tmpsuivisaidesapres.id ;
 END LOOP;
END ;

$$ LANGUAGE 'plpgsql' ;

-- lancement de la fonction
SELECT * FROM suivisaidesapres_replace();

-- Suppression de la fonction
DROP FUNCTION suivisaidesapres_replace();

-- *****************************************************************************
