SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

UPDATE bilansparcours66 SET situationperso = '' WHERE situationperso IS NULL;
UPDATE bilansparcours66 SET situationpro = '' WHERE situationpro IS NULL;
UPDATE bilansparcours66 SET objinit = '' WHERE objinit IS NULL;
UPDATE bilansparcours66 SET objatteint = '' WHERE objatteint IS NULL;
UPDATE bilansparcours66 SET objnew = '' WHERE objnew IS NULL;

UPDATE bilansparcours66 SET situationperso = ('Situation personnelle et familiale (actualisation de la situation sociale) :
' || situationperso || '

Situation professionnelle (préciser les avancées réalisées) :
' || situationpro || '

Objectifs initiaux et actions prévues, capacités mobilisables, freins éventuels :
' || objinit || '

Atteinte des objectifs :
' || objatteint || '

Nouveaux objectifs: actions à mettre en place ou à poursuivre :
' || objnew );

UPDATE bilansparcours66 SET situationpro = NULL WHERE situationpro = '';
UPDATE bilansparcours66 SET objinit = NULL WHERE objinit = '';
UPDATE bilansparcours66 SET objatteint = NULL WHERE objatteint = '';
UPDATE bilansparcours66 SET objnew = NULL WHERE objnew = '';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
