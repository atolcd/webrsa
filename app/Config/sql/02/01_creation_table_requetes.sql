SET NAMES 'utf8';
CREATE TABLE referentiel.requetes
(
  id serial NOT NULL,
  nom character varying(100) NOT NULL,
  typereq character varying(10) NOT NULL,
  description character varying(100) NOT NULL,
  sql_select text    NOT NULL,
  sql_condition text NOT NULL,
  sql_option text    NOT NULL,
  isactif boolean DEFAULT true,
  created timestamp without time zone,
  modified timestamp without time zone,

  CONSTRAINT requetes_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE referentiel.requetes OWNER TO webrsa;

