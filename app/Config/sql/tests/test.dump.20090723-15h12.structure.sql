--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: accoemplois; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE accoemplois (
    id integer NOT NULL,
    code character(4),
    name character varying(100)
);


ALTER TABLE public.accoemplois OWNER TO webrsa;

--
-- Name: accoemplois_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE accoemplois_id_seq
    START WITH 4
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.accoemplois_id_seq OWNER TO webrsa;

--
-- Name: accoemplois_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE accoemplois_id_seq OWNED BY accoemplois.id;


--
-- Name: acos; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE acos (
    id integer NOT NULL,
    parent_id integer NOT NULL,
    model character varying(255) DEFAULT ''::character varying,
    foreign_key integer,
    alias character varying(255) DEFAULT ''::character varying,
    lft integer,
    rght integer
);


ALTER TABLE public.acos OWNER TO webrsa;

--
-- Name: acos_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE acos_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.acos_id_seq OWNER TO webrsa;

--
-- Name: acos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE acos_id_seq OWNED BY acos.id;


--
-- Name: actions; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE actions (
    id integer NOT NULL,
    typeaction_id integer NOT NULL,
    code character(2),
    libelle character varying(250)
);


ALTER TABLE public.actions OWNER TO webrsa;

--
-- Name: actions_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE actions_id_seq
    START WITH 34
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.actions_id_seq OWNER TO webrsa;

--
-- Name: actions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE actions_id_seq OWNED BY actions.id;


--
-- Name: actionsinsertion; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE actionsinsertion (
    id integer NOT NULL,
    contratinsertion_id integer NOT NULL,
    dd_action date,
    df_action date,
    lib_action character(1)
);


ALTER TABLE public.actionsinsertion OWNER TO webrsa;

--
-- Name: actionsinsertion_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE actionsinsertion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.actionsinsertion_id_seq OWNER TO webrsa;

--
-- Name: actionsinsertion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE actionsinsertion_id_seq OWNED BY actionsinsertion.id;


--
-- Name: activites; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE activites (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    reg character(2),
    act character(3),
    paysact character(3),
    ddact date,
    dfact date,
    natcontrtra character(3),
    topcondadmeti boolean,
    hauremuscmic character(1)
);


ALTER TABLE public.activites OWNER TO webrsa;

--
-- Name: activites_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE activites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.activites_id_seq OWNER TO webrsa;

--
-- Name: activites_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE activites_id_seq OWNED BY activites.id;


--
-- Name: adresses; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE adresses (
    id integer NOT NULL,
    numvoie character varying(6),
    typevoie character varying(4),
    nomvoie character varying(25),
    complideadr character varying(38),
    compladr character varying(26),
    lieudist character varying(32),
    numcomrat character(5),
    numcomptt character(5),
    codepos character(5),
    locaadr character varying(26),
    pays character varying(3),
    canton character varying(20)
);


ALTER TABLE public.adresses OWNER TO webrsa;

--
-- Name: adresses_foyers; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE adresses_foyers (
    id integer NOT NULL,
    adresse_id integer NOT NULL,
    foyer_id integer NOT NULL,
    rgadr character(2),
    dtemm date,
    typeadr character(1)
);


ALTER TABLE public.adresses_foyers OWNER TO webrsa;

--
-- Name: adresses_foyers_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE adresses_foyers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.adresses_foyers_id_seq OWNER TO webrsa;

--
-- Name: adresses_foyers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE adresses_foyers_id_seq OWNED BY adresses_foyers.id;


--
-- Name: adresses_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE adresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.adresses_id_seq OWNER TO webrsa;

--
-- Name: adresses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE adresses_id_seq OWNED BY adresses.id;


--
-- Name: aidesagricoles; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE aidesagricoles (
    id integer NOT NULL,
    infoagricole_id integer NOT NULL,
    annrefaideagri character(4),
    libnataideagri character varying(30),
    mtaideagri numeric(9,2)
);


ALTER TABLE public.aidesagricoles OWNER TO webrsa;

--
-- Name: aidesagricoles_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE aidesagricoles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.aidesagricoles_id_seq OWNER TO webrsa;

--
-- Name: aidesagricoles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE aidesagricoles_id_seq OWNED BY aidesagricoles.id;


--
-- Name: aidesdirectes; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE aidesdirectes (
    id integer NOT NULL,
    actioninsertion_id integer NOT NULL,
    lib_aide character varying(32),
    typo_aide character varying(32),
    date_aide date
);


ALTER TABLE public.aidesdirectes OWNER TO webrsa;

--
-- Name: aidesdirectes_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE aidesdirectes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.aidesdirectes_id_seq OWNER TO webrsa;

--
-- Name: aidesdirectes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE aidesdirectes_id_seq OWNED BY aidesdirectes.id;


--
-- Name: allocationssoutienfamilial; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE allocationssoutienfamilial (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    sitasf character(2),
    parassoasf character(1),
    ddasf date,
    dfasf date
);


ALTER TABLE public.allocationssoutienfamilial OWNER TO webrsa;

--
-- Name: allocationssoutienfamilial_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE allocationssoutienfamilial_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.allocationssoutienfamilial_id_seq OWNER TO webrsa;

--
-- Name: allocationssoutienfamilial_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE allocationssoutienfamilial_id_seq OWNED BY allocationssoutienfamilial.id;


--
-- Name: aros; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE aros (
    id integer NOT NULL,
    parent_id integer,
    model character varying(255) DEFAULT ''::character varying,
    foreign_key integer,
    alias character varying(255) DEFAULT ''::character varying,
    lft integer,
    rght integer
);


ALTER TABLE public.aros OWNER TO webrsa;

--
-- Name: aros_acos; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE aros_acos (
    id integer NOT NULL,
    aro_id integer NOT NULL,
    aco_id integer NOT NULL,
    _create character(2) DEFAULT 0 NOT NULL,
    _read character(2) DEFAULT 0 NOT NULL,
    _update character(2) DEFAULT 0 NOT NULL,
    _delete character(2) DEFAULT 0 NOT NULL
);


ALTER TABLE public.aros_acos OWNER TO webrsa;

--
-- Name: aros_acos_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE aros_acos_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.aros_acos_id_seq OWNER TO webrsa;

--
-- Name: aros_acos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE aros_acos_id_seq OWNED BY aros_acos.id;


--
-- Name: aros_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE aros_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.aros_id_seq OWNER TO webrsa;

--
-- Name: aros_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE aros_id_seq OWNED BY aros.id;


--
-- Name: avispcgdroitrsa; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE avispcgdroitrsa (
    id integer NOT NULL,
    dossier_rsa_id integer NOT NULL,
    avisdestpairsa character(1),
    dtavisdestpairsa date,
    nomtie character varying(64),
    typeperstie character(1)
);


ALTER TABLE public.avispcgdroitrsa OWNER TO webrsa;

--
-- Name: avispcgdroitrsa_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE avispcgdroitrsa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.avispcgdroitrsa_id_seq OWNER TO webrsa;

--
-- Name: avispcgdroitrsa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE avispcgdroitrsa_id_seq OWNED BY avispcgdroitrsa.id;


--
-- Name: avispcgpersonnes; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE avispcgpersonnes (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    avisevaressnonsal character(1),
    dtsouressnonsal date,
    dtevaressnonsal date,
    mtevalressnonsal numeric(9,2),
    excl character(1),
    ddexcl date,
    dfexcl date
);


ALTER TABLE public.avispcgpersonnes OWNER TO webrsa;

--
-- Name: avispcgpersonnes_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE avispcgpersonnes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.avispcgpersonnes_id_seq OWNER TO webrsa;

--
-- Name: avispcgpersonnes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE avispcgpersonnes_id_seq OWNED BY avispcgpersonnes.id;


--
-- Name: condsadmins; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE condsadmins (
    id integer NOT NULL,
    avispcgdroitrsa_id integer NOT NULL,
    aviscondadmrsa character(1),
    moticondadmrsa character(2),
    comm1condadmrsa character varying(60),
    comm2condadmrsa character varying(60),
    dteffaviscondadmrsa date
);


ALTER TABLE public.condsadmins OWNER TO webrsa;

--
-- Name: condsadmins_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE condsadmins_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.condsadmins_id_seq OWNER TO webrsa;

--
-- Name: condsadmins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE condsadmins_id_seq OWNED BY condsadmins.id;


--
-- Name: connections; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE connections (
    id integer NOT NULL,
    user_id integer NOT NULL,
    php_sid character(32) DEFAULT NULL::bpchar,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.connections OWNER TO webrsa;

--
-- Name: connections_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE connections_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.connections_id_seq OWNER TO webrsa;

--
-- Name: connections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE connections_id_seq OWNED BY connections.id;


--
-- Name: contratsinsertion; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE contratsinsertion (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    structurereferente_id integer NOT NULL,
    typocontrat_id integer NOT NULL,
    dd_ci date,
    df_ci date,
    diplomes text,
    form_compl character varying(60),
    expr_prof text,
    aut_expr_prof character varying(120),
    rg_ci integer,
    actions_prev character(1),
    obsta_renc character varying(120),
    service_soutien character varying(120),
    pers_charg_suivi character varying(50),
    objectifs_fixes text,
    engag_object text,
    sect_acti_emp character varying(20),
    emp_occupe character varying(30),
    duree_hebdo_emp character varying(20),
    nat_cont_trav character(4),
    duree_cdd character varying(20),
    duree_engag integer,
    nature_projet text,
    observ_ci text,
    decision_ci character(1) DEFAULT 'E'::bpchar,
    datevalidation_ci date,
    date_saisi_ci date,
    lieu_saisi_ci character varying(30),
    emp_trouv boolean
);


ALTER TABLE public.contratsinsertion OWNER TO webrsa;

--
-- Name: contratsinsertion_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE contratsinsertion_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.contratsinsertion_id_seq OWNER TO webrsa;

--
-- Name: contratsinsertion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE contratsinsertion_id_seq OWNED BY contratsinsertion.id;


--
-- Name: creances; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE creances (
    id integer NOT NULL,
    dtimplcre date,
    natcre character(3),
    rgcre character(3),
    motiindu character(2),
    oriindu character(2),
    respindu character(2),
    ddregucre date,
    dfregucre date,
    dtdercredcretrans date,
    mtsolreelcretrans numeric(9,2),
    mtinicre numeric(9,2)
);


ALTER TABLE public.creances OWNER TO webrsa;

--
-- Name: creances_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE creances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.creances_id_seq OWNER TO webrsa;

--
-- Name: creances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE creances_id_seq OWNED BY creances.id;


--
-- Name: creancesalimentaires; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE creancesalimentaires (
    id integer NOT NULL,
    etatcrealim character(2),
    ddcrealim date,
    dfcrealim date,
    orioblalim character(3),
    motidiscrealim character(3),
    commcrealim character varying(50),
    mtsancrealim numeric(9,2),
    topdemdisproccrealim boolean,
    engproccrealim character(1),
    verspa character(1),
    topjugpa boolean,
    personne_id integer
);


ALTER TABLE public.creancesalimentaires OWNER TO webrsa;

--
-- Name: creancesalimentaires_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE creancesalimentaires_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.creancesalimentaires_id_seq OWNER TO webrsa;

--
-- Name: creancesalimentaires_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE creancesalimentaires_id_seq OWNED BY creancesalimentaires.id;


--
-- Name: creancesalimentaires_personnes; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE creancesalimentaires_personnes (
    personne_id integer NOT NULL,
    creancealimentaire_id integer NOT NULL
);


ALTER TABLE public.creancesalimentaires_personnes OWNER TO webrsa;

--
-- Name: derogations; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE derogations (
    id integer NOT NULL,
    avispcgpersonne_id integer NOT NULL,
    typdero character(3),
    avisdero character(1),
    ddavisdero date,
    dfavisdero date
);


ALTER TABLE public.derogations OWNER TO webrsa;

--
-- Name: derogations_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE derogations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.derogations_id_seq OWNER TO webrsa;

--
-- Name: derogations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE derogations_id_seq OWNED BY derogations.id;


--
-- Name: detailscalculsdroitsrsa; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE detailscalculsdroitsrsa (
    id integer NOT NULL,
    detaildroitrsa_id integer NOT NULL,
    natpf character(3),
    sousnatpf character(5),
    ddnatdro date,
    dfnatdro date,
    mtrsavers numeric(9,2),
    dtderrsavers date
);


ALTER TABLE public.detailscalculsdroitsrsa OWNER TO webrsa;

--
-- Name: detailscalculsdroitsrsa_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE detailscalculsdroitsrsa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.detailscalculsdroitsrsa_id_seq OWNER TO webrsa;

--
-- Name: detailscalculsdroitsrsa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE detailscalculsdroitsrsa_id_seq OWNED BY detailscalculsdroitsrsa.id;


--
-- Name: detailsdroitsrsa; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE detailsdroitsrsa (
    id integer NOT NULL,
    dossier_rsa_id integer NOT NULL,
    topsansdomfixe boolean,
    nbenfautcha integer,
    oridemrsa character(3),
    dtoridemrsa date,
    topfoydrodevorsa boolean,
    ddelecal date,
    dfelecal date,
    mtrevminigararsa numeric(9,2),
    mtpentrsa numeric(9,2),
    mtlocalrsa numeric(9,2),
    mtrevgararsa numeric(9,2),
    mtpfrsa numeric(9,2),
    mtalrsa numeric(9,2),
    mtressmenrsa numeric(9,2),
    mtsanoblalimrsa numeric(9,2),
    mtredhosrsa numeric(9,2),
    mtredcgrsa numeric(9,2),
    mtcumintegrsa numeric(9,2),
    mtabaneursa numeric(9,2),
    mttotdrorsa numeric(9,2)
);


ALTER TABLE public.detailsdroitsrsa OWNER TO webrsa;

--
-- Name: detailsdroitsrsa_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE detailsdroitsrsa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.detailsdroitsrsa_id_seq OWNER TO webrsa;

--
-- Name: detailsdroitsrsa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE detailsdroitsrsa_id_seq OWNED BY detailsdroitsrsa.id;


--
-- Name: detailsressourcesmensuelles; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE detailsressourcesmensuelles (
    id integer NOT NULL,
    ressourcemensuelle_id integer NOT NULL,
    natress character(3),
    mtnatressmen numeric(10,2),
    abaneu character(1),
    dfpercress date,
    topprevsubsress boolean
);


ALTER TABLE public.detailsressourcesmensuelles OWNER TO webrsa;

--
-- Name: detailsressourcesmensuelles_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE detailsressourcesmensuelles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.detailsressourcesmensuelles_id_seq OWNER TO webrsa;

--
-- Name: detailsressourcesmensuelles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE detailsressourcesmensuelles_id_seq OWNED BY detailsressourcesmensuelles.id;


--
-- Name: difdisps; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE difdisps (
    id integer NOT NULL,
    code character(4),
    name character varying(100)
);


ALTER TABLE public.difdisps OWNER TO webrsa;

--
-- Name: difdisps_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE difdisps_id_seq
    START WITH 6
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.difdisps_id_seq OWNER TO webrsa;

--
-- Name: difdisps_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE difdisps_id_seq OWNED BY difdisps.id;


--
-- Name: diflogs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE diflogs (
    id integer NOT NULL,
    code character(4),
    name character varying(100)
);


ALTER TABLE public.diflogs OWNER TO webrsa;

--
-- Name: diflogs_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE diflogs_id_seq
    START WITH 10
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.diflogs_id_seq OWNER TO webrsa;

--
-- Name: diflogs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE diflogs_id_seq OWNED BY diflogs.id;


--
-- Name: difsocs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE difsocs (
    id integer NOT NULL,
    code character(4),
    name character varying(100)
);


ALTER TABLE public.difsocs OWNER TO webrsa;

--
-- Name: difsocs_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE difsocs_id_seq
    START WITH 8
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.difsocs_id_seq OWNER TO webrsa;

--
-- Name: difsocs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE difsocs_id_seq OWNED BY difsocs.id;


--
-- Name: dossiers_rsa; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dossiers_rsa (
    id integer NOT NULL,
    numdemrsa character varying(11),
    dtdemrsa date,
    dtdemrmi date,
    numdepinsrmi character(3),
    typeinsrmi character(1),
    numcominsrmi integer,
    numagrinsrmi character(2),
    numdosinsrmi integer,
    numcli integer,
    numorg character(3),
    fonorg character(3),
    matricule character(15),
    statudemrsa character(1),
    typeparte character(4),
    ideparte character(3),
    fonorgcedmut character(3),
    numorgcedmut character(3),
    matriculeorgcedmut character(15),
    ddarrmut date,
    codeposanchab character(5),
    fonorgprenmut character(3),
    numorgprenmut character(3),
    dddepamut date,
    details_droits_rsa_id integer,
    avis_pcg_id integer,
    organisme_id integer,
    acompte_rsa_id integer
);


ALTER TABLE public.dossiers_rsa OWNER TO webrsa;

--
-- Name: dossiers_rsa_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE dossiers_rsa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dossiers_rsa_id_seq OWNER TO webrsa;

--
-- Name: dossiers_rsa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE dossiers_rsa_id_seq OWNED BY dossiers_rsa.id;


--
-- Name: dossierscaf; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dossierscaf (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    ddratdos date,
    dfratdos date,
    toprespdos boolean,
    numdemrsaprece character varying(11) DEFAULT NULL::character varying
);


ALTER TABLE public.dossierscaf OWNER TO webrsa;

--
-- Name: dossierscaf_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE dossierscaf_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dossierscaf_id_seq OWNER TO webrsa;

--
-- Name: dossierscaf_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE dossierscaf_id_seq OWNED BY dossierscaf.id;


--
-- Name: dspfs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspfs (
    id integer NOT NULL,
    foyer_id integer NOT NULL,
    motidemrsa character(4),
    accosocfam character(1),
    libautraccosocfam character varying(100),
    libcooraccosocfam character varying(250),
    natlog character(4),
    libautrdiflog character varying(100),
    demarlog character(4)
);


ALTER TABLE public.dspfs OWNER TO webrsa;

--
-- Name: dspfs_diflogs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspfs_diflogs (
    diflog_id integer NOT NULL,
    dspf_id integer NOT NULL
);


ALTER TABLE public.dspfs_diflogs OWNER TO webrsa;

--
-- Name: dspfs_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE dspfs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dspfs_id_seq OWNER TO webrsa;

--
-- Name: dspfs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE dspfs_id_seq OWNED BY dspfs.id;


--
-- Name: dspfs_nataccosocfams; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspfs_nataccosocfams (
    nataccosocfam_id integer NOT NULL,
    dspf_id integer NOT NULL
);


ALTER TABLE public.dspfs_nataccosocfams OWNER TO webrsa;

--
-- Name: dspps; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspps (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    drorsarmiant character(1),
    drorsarmianta2 character(1),
    couvsoc character(1),
    libautrdifsoc character varying(100),
    elopersdifdisp character(1),
    obstemploidifdisp character(1),
    soutdemarsoc character(1),
    libautraccosocindi character varying(100),
    libcooraccosocindi character varying(250),
    annderdipobt date,
    rappemploiquali boolean,
    rappemploiform boolean,
    libautrqualipro character varying(100),
    permicondub boolean,
    libautrpermicondu character varying(100),
    libcompeextrapro character varying(100),
    persisogrorechemploi boolean,
    libcooraccoemploi character varying(100),
    hispro character(4),
    libderact character varying(100),
    libsecactderact character varying(100),
    dfderact date,
    domideract character(1),
    libactdomi character varying(100),
    libsecactdomi character varying(100),
    duractdomi character(4),
    libemploirech character varying(100),
    libsecactrech character varying(100),
    creareprisentrrech character(1),
    moyloco boolean,
    diplomes text,
    dipfra character(1)
);


ALTER TABLE public.dspps OWNER TO webrsa;

--
-- Name: dspps_accoemplois; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspps_accoemplois (
    accoemploi_id integer NOT NULL,
    dspp_id integer NOT NULL
);


ALTER TABLE public.dspps_accoemplois OWNER TO webrsa;

--
-- Name: dspps_difdisps; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspps_difdisps (
    difdisp_id integer NOT NULL,
    dspp_id integer NOT NULL
);


ALTER TABLE public.dspps_difdisps OWNER TO webrsa;

--
-- Name: dspps_difsocs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspps_difsocs (
    difsoc_id integer NOT NULL,
    dspp_id integer NOT NULL
);


ALTER TABLE public.dspps_difsocs OWNER TO webrsa;

--
-- Name: dspps_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE dspps_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dspps_id_seq OWNER TO webrsa;

--
-- Name: dspps_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE dspps_id_seq OWNED BY dspps.id;


--
-- Name: dspps_nataccosocindis; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspps_nataccosocindis (
    nataccosocindi_id integer NOT NULL,
    dspp_id integer NOT NULL
);


ALTER TABLE public.dspps_nataccosocindis OWNER TO webrsa;

--
-- Name: dspps_natmobs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspps_natmobs (
    natmob_id integer NOT NULL,
    dspp_id integer NOT NULL
);


ALTER TABLE public.dspps_natmobs OWNER TO webrsa;

--
-- Name: dspps_nivetus; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE dspps_nivetus (
    nivetu_id integer NOT NULL,
    dspp_id integer NOT NULL
);


ALTER TABLE public.dspps_nivetus OWNER TO webrsa;

--
-- Name: evenements; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE evenements (
    id integer NOT NULL,
    dtliq date,
    heuliq date,
    fg character varying(30)
);


ALTER TABLE public.evenements OWNER TO webrsa;

--
-- Name: evenements_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE evenements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.evenements_id_seq OWNER TO webrsa;

--
-- Name: evenements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE evenements_id_seq OWNED BY evenements.id;


--
-- Name: foyers; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE foyers (
    id integer NOT NULL,
    dossier_rsa_id integer NOT NULL,
    sitfam character(3),
    ddsitfam date,
    typeocclog character(3),
    mtvallocterr numeric(9,2),
    mtvalloclog numeric(9,2),
    contefichliairsa text
);


ALTER TABLE public.foyers OWNER TO webrsa;

--
-- Name: foyers_creances; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE foyers_creances (
    foyer_id integer NOT NULL,
    creance_id integer NOT NULL
);


ALTER TABLE public.foyers_creances OWNER TO webrsa;

--
-- Name: foyers_evenements; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE foyers_evenements (
    foyer_id integer NOT NULL,
    evenement_id integer NOT NULL
);


ALTER TABLE public.foyers_evenements OWNER TO webrsa;

--
-- Name: foyers_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE foyers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.foyers_id_seq OWNER TO webrsa;

--
-- Name: foyers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE foyers_id_seq OWNED BY foyers.id;


--
-- Name: grossesses; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE grossesses (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    ddgro date,
    dfgro date,
    dtdeclgro date,
    natfingro character(1)
);


ALTER TABLE public.grossesses OWNER TO webrsa;

--
-- Name: grossesses_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE grossesses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.grossesses_id_seq OWNER TO webrsa;

--
-- Name: grossesses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE grossesses_id_seq OWNED BY grossesses.id;


--
-- Name: groups; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE groups (
    id integer NOT NULL,
    name character varying(50),
    parent_id integer
);


ALTER TABLE public.groups OWNER TO webrsa;

--
-- Name: groups_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE groups_id_seq
    START WITH 4
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.groups_id_seq OWNER TO webrsa;

--
-- Name: groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE groups_id_seq OWNED BY groups.id;


--
-- Name: identificationsflux; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE identificationsflux (
    id integer NOT NULL,
    applieme character(3),
    numversionapplieme character(4),
    typeflux character(1),
    natflux character(1),
    dtcreaflux date,
    heucreaflux date,
    dtref date
);


ALTER TABLE public.identificationsflux OWNER TO webrsa;

--
-- Name: identificationsflux_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE identificationsflux_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.identificationsflux_id_seq OWNER TO webrsa;

--
-- Name: identificationsflux_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE identificationsflux_id_seq OWNED BY identificationsflux.id;


--
-- Name: informationseti; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE informationseti (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    topcreaentre boolean,
    topaccre boolean,
    acteti character(1),
    topempl1ax boolean,
    topstag1ax boolean,
    topsansempl boolean,
    ddchiaffaeti date,
    dfchiaffaeti date,
    mtchiaffaeti numeric(9,2),
    regfiseti character(1),
    topbeneti boolean,
    regfisetia1 character(1),
    mtbenetia1 numeric(9,2),
    mtamoeti numeric(9,2),
    mtplusvalueti numeric(9,2),
    topevoreveti boolean,
    libevoreveti character varying(30),
    topressevaeti boolean
);


ALTER TABLE public.informationseti OWNER TO webrsa;

--
-- Name: informationseti_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE informationseti_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.informationseti_id_seq OWNER TO webrsa;

--
-- Name: informationseti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE informationseti_id_seq OWNED BY informationseti.id;


--
-- Name: infosagricoles; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE infosagricoles (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    mtbenagri numeric(10,2),
    dtbenagri date,
    regfisagri character(1)
);


ALTER TABLE public.infosagricoles OWNER TO webrsa;

--
-- Name: infosagricoles_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE infosagricoles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.infosagricoles_id_seq OWNER TO webrsa;

--
-- Name: infosagricoles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE infosagricoles_id_seq OWNED BY infosagricoles.id;


--
-- Name: infosfinancieres; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE infosfinancieres (
    id integer NOT NULL,
    dossier_rsa_id integer NOT NULL,
    moismoucompta date,
    type_allocation character varying(25),
    natpfcre character(3),
    rgcre integer,
    numintmoucompta integer,
    typeopecompta character(3),
    sensopecompta character(2),
    mtmoucompta numeric(11,2),
    ddregu date,
    dttraimoucompta date,
    heutraimoucompta date
);


ALTER TABLE public.infosfinancieres OWNER TO webrsa;

--
-- Name: infosfinancieres_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE infosfinancieres_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.infosfinancieres_id_seq OWNER TO webrsa;

--
-- Name: infosfinancieres_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE infosfinancieres_id_seq OWNED BY infosfinancieres.id;


--
-- Name: jetons; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE jetons (
    id integer NOT NULL,
    dossier_id integer NOT NULL,
    php_sid character(32) DEFAULT NULL::bpchar,
    user_id integer NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.jetons OWNER TO webrsa;

--
-- Name: jetons_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE jetons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.jetons_id_seq OWNER TO webrsa;

--
-- Name: jetons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE jetons_id_seq OWNED BY jetons.id;


--
-- Name: liberalites; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE liberalites (
    id integer NOT NULL,
    avispcgpersonne_id integer NOT NULL,
    mtlibernondecl numeric(9,2),
    dtabsdeclliber date
);


ALTER TABLE public.liberalites OWNER TO webrsa;

--
-- Name: liberalites_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE liberalites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.liberalites_id_seq OWNER TO webrsa;

--
-- Name: liberalites_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE liberalites_id_seq OWNED BY liberalites.id;


--
-- Name: modescontact; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE modescontact (
    id integer NOT NULL,
    foyer_id integer NOT NULL,
    numtel character varying(11),
    numposte character varying(4),
    nattel character(1),
    matetel character(3),
    autorutitel character(1),
    adrelec character varying(78),
    autorutiadrelec character(1)
);


ALTER TABLE public.modescontact OWNER TO webrsa;

--
-- Name: modescontact_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE modescontact_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.modescontact_id_seq OWNER TO webrsa;

--
-- Name: modescontact_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE modescontact_id_seq OWNED BY modescontact.id;


--
-- Name: nataccosocfams; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE nataccosocfams (
    id integer NOT NULL,
    code character(4),
    name character varying(100)
);


ALTER TABLE public.nataccosocfams OWNER TO webrsa;

--
-- Name: nataccosocfams_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE nataccosocfams_id_seq
    START WITH 5
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nataccosocfams_id_seq OWNER TO webrsa;

--
-- Name: nataccosocfams_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE nataccosocfams_id_seq OWNED BY nataccosocfams.id;


--
-- Name: nataccosocindis; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE nataccosocindis (
    id integer NOT NULL,
    code character(4),
    name character varying(100)
);


ALTER TABLE public.nataccosocindis OWNER TO webrsa;

--
-- Name: nataccosocindis_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE nataccosocindis_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nataccosocindis_id_seq OWNER TO webrsa;

--
-- Name: nataccosocindis_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE nataccosocindis_id_seq OWNED BY nataccosocindis.id;


--
-- Name: natmobs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE natmobs (
    id integer NOT NULL,
    code character(4),
    name character varying(100)
);


ALTER TABLE public.natmobs OWNER TO webrsa;

--
-- Name: natmobs_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE natmobs_id_seq
    START WITH 4
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.natmobs_id_seq OWNER TO webrsa;

--
-- Name: natmobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE natmobs_id_seq OWNED BY natmobs.id;


--
-- Name: nivetus; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE nivetus (
    id integer NOT NULL,
    code character(4),
    name character varying(100)
);


ALTER TABLE public.nivetus OWNER TO webrsa;

--
-- Name: nivetus_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE nivetus_id_seq
    START WITH 8
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.nivetus_id_seq OWNER TO webrsa;

--
-- Name: nivetus_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE nivetus_id_seq OWNED BY nivetus.id;


--
-- Name: orientsstructs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE orientsstructs (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    typeorient_id integer,
    structurereferente_id integer,
    propo_algo integer,
    valid_cg boolean,
    date_propo date,
    date_valid date,
    statut_orient character varying(15),
    date_impression date
);


ALTER TABLE public.orientsstructs OWNER TO webrsa;

--
-- Name: orientsstructs_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE orientsstructs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.orientsstructs_id_seq OWNER TO webrsa;

--
-- Name: orientsstructs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE orientsstructs_id_seq OWNED BY orientsstructs.id;


--
-- Name: orientsstructs_servicesinstructeurs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE orientsstructs_servicesinstructeurs (
    orientstruct_id integer NOT NULL,
    serviceinstructeur_id integer NOT NULL
);


ALTER TABLE public.orientsstructs_servicesinstructeurs OWNER TO webrsa;

--
-- Name: paiementsfoyers; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE paiementsfoyers (
    id integer NOT NULL,
    foyer_id integer NOT NULL,
    topverstie boolean,
    modepai character(2),
    topribconj boolean,
    titurib character(3),
    nomprenomtiturib character varying(24),
    etaban character(5),
    guiban character(5),
    numcomptban character(11),
    clerib smallint,
    comban character varying(24)
);


ALTER TABLE public.paiementsfoyers OWNER TO webrsa;

--
-- Name: paiementsfoyers_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE paiementsfoyers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.paiementsfoyers_id_seq OWNER TO webrsa;

--
-- Name: paiementsfoyers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE paiementsfoyers_id_seq OWNED BY paiementsfoyers.id;


--
-- Name: personnes; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE personnes (
    id integer NOT NULL,
    foyer_id integer NOT NULL,
    qual character varying(3),
    nom character varying(50),
    prenom character varying(50),
    nomnai character varying(50),
    prenom2 character varying(50),
    prenom3 character varying(50),
    nomcomnai character varying(26),
    dtnai date,
    rgnai integer,
    typedtnai character(1),
    nir character(15),
    topvalec boolean,
    sexe character(1),
    nati character(1),
    dtnati date,
    pieecpres character(1),
    idassedic character varying(8)
);


ALTER TABLE public.personnes OWNER TO webrsa;

--
-- Name: personnes_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE personnes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.personnes_id_seq OWNER TO webrsa;

--
-- Name: personnes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE personnes_id_seq OWNED BY personnes.id;


--
-- Name: prestations; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE prestations (
    personne_id integer,
    natprest character(3),
    rolepers character(3),
    topchapers boolean,
    toppersdrodevorsa boolean,
    id integer NOT NULL
);


ALTER TABLE public.prestations OWNER TO webrsa;

--
-- Name: prestations_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE prestations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.prestations_id_seq OWNER TO webrsa;

--
-- Name: prestations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE prestations_id_seq OWNED BY prestations.id;


--
-- Name: prestsform; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE prestsform (
    id integer NOT NULL,
    actioninsertion_id integer NOT NULL,
    refpresta_id integer NOT NULL,
    lib_presta character varying(32),
    date_presta date
);


ALTER TABLE public.prestsform OWNER TO webrsa;

--
-- Name: prestsform_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE prestsform_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.prestsform_id_seq OWNER TO webrsa;

--
-- Name: prestsform_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE prestsform_id_seq OWNED BY prestsform.id;


--
-- Name: rattachements; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE rattachements (
    personne_id integer NOT NULL,
    rattache_id integer NOT NULL,
    typepar character(3)
);


ALTER TABLE public.rattachements OWNER TO webrsa;

--
-- Name: reducsrsa; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE reducsrsa (
    id integer NOT NULL,
    avispcgdroitrsa_id integer NOT NULL,
    mtredrsa numeric(9,2),
    ddredrsa date,
    dfredrsa date
);


ALTER TABLE public.reducsrsa OWNER TO webrsa;

--
-- Name: reducsrsa_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE reducsrsa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.reducsrsa_id_seq OWNER TO webrsa;

--
-- Name: reducsrsa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE reducsrsa_id_seq OWNED BY reducsrsa.id;


--
-- Name: referents; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE referents (
    id integer NOT NULL,
    structurereferente_id integer NOT NULL,
    nom character varying(28),
    prenom character varying(32),
    numero_poste character varying(14),
    email character varying(78),
    qual character varying(3)
);


ALTER TABLE public.referents OWNER TO webrsa;

--
-- Name: referents_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE referents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.referents_id_seq OWNER TO webrsa;

--
-- Name: referents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE referents_id_seq OWNED BY referents.id;


--
-- Name: refsprestas; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE refsprestas (
    id integer NOT NULL,
    nomrefpresta character varying(28),
    prenomrefpresta character varying(32),
    emailrefpresta character varying(78),
    numero_posterefpresta character varying(4)
);


ALTER TABLE public.refsprestas OWNER TO webrsa;

--
-- Name: refsprestas_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE refsprestas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.refsprestas_id_seq OWNER TO webrsa;

--
-- Name: refsprestas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE refsprestas_id_seq OWNED BY refsprestas.id;


--
-- Name: regroupementszonesgeo; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE regroupementszonesgeo (
    id integer NOT NULL,
    lib_rgpt character varying(50)
);


ALTER TABLE public.regroupementszonesgeo OWNER TO webrsa;

--
-- Name: regroupementszonesgeo_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE regroupementszonesgeo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.regroupementszonesgeo_id_seq OWNER TO webrsa;

--
-- Name: regroupementszonesgeo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE regroupementszonesgeo_id_seq OWNED BY regroupementszonesgeo.id;


--
-- Name: ressources; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE ressources (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    topressnul boolean,
    mtpersressmenrsa numeric(10,2),
    ddress date,
    dfress date
);


ALTER TABLE public.ressources OWNER TO webrsa;

--
-- Name: ressources_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE ressources_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.ressources_id_seq OWNER TO webrsa;

--
-- Name: ressources_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE ressources_id_seq OWNED BY ressources.id;


--
-- Name: ressources_ressourcesmensuelles; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE ressources_ressourcesmensuelles (
    ressourcemensuelle_id integer NOT NULL,
    ressource_id integer NOT NULL
);


ALTER TABLE public.ressources_ressourcesmensuelles OWNER TO webrsa;

--
-- Name: ressourcesmensuelles; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE ressourcesmensuelles (
    id integer NOT NULL,
    ressource_id integer NOT NULL,
    moisress date,
    nbheumentra integer,
    mtabaneu numeric(9,2)
);


ALTER TABLE public.ressourcesmensuelles OWNER TO webrsa;

--
-- Name: ressourcesmensuelles_detailsressourcesmensuelles; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE ressourcesmensuelles_detailsressourcesmensuelles (
    detailressourcemensuelle_id integer NOT NULL,
    ressourcemensuelle_id integer NOT NULL
);


ALTER TABLE public.ressourcesmensuelles_detailsressourcesmensuelles OWNER TO webrsa;

--
-- Name: ressourcesmensuelles_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE ressourcesmensuelles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.ressourcesmensuelles_id_seq OWNER TO webrsa;

--
-- Name: ressourcesmensuelles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE ressourcesmensuelles_id_seq OWNED BY ressourcesmensuelles.id;


--
-- Name: servicesinstructeurs; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE servicesinstructeurs (
    id integer NOT NULL,
    lib_service character varying(100),
    num_rue character varying(6),
    nom_rue character varying(100),
    complement_adr character varying(38),
    code_insee character(5),
    code_postal character(5),
    ville character varying(26),
    numdepins character(3),
    typeserins character(1),
    numcomins character(3),
    numagrins integer,
    type_voie character varying(6)
);


ALTER TABLE public.servicesinstructeurs OWNER TO webrsa;

--
-- Name: servicesinstructeurs_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE servicesinstructeurs_id_seq
    START WITH 3
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.servicesinstructeurs_id_seq OWNER TO webrsa;

--
-- Name: servicesinstructeurs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE servicesinstructeurs_id_seq OWNED BY servicesinstructeurs.id;


--
-- Name: situationsdossiersrsa; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE situationsdossiersrsa (
    id integer NOT NULL,
    dossier_rsa_id integer NOT NULL,
    etatdosrsa character(1),
    dtrefursa date,
    moticlorsa character(3),
    dtclorsa date
);


ALTER TABLE public.situationsdossiersrsa OWNER TO webrsa;

--
-- Name: situationsdossiersrsa_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE situationsdossiersrsa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.situationsdossiersrsa_id_seq OWNER TO webrsa;

--
-- Name: situationsdossiersrsa_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE situationsdossiersrsa_id_seq OWNED BY situationsdossiersrsa.id;


--
-- Name: structuresreferentes; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE structuresreferentes (
    id integer NOT NULL,
    typeorient_id integer NOT NULL,
    lib_struc character varying(100) NOT NULL,
    num_voie character varying(6) NOT NULL,
    type_voie character varying(6) NOT NULL,
    nom_voie character varying(30) NOT NULL,
    code_postal character(5) NOT NULL,
    ville character varying(45) NOT NULL,
    code_insee character(5)
);


ALTER TABLE public.structuresreferentes OWNER TO webrsa;

--
-- Name: structuresreferentes_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE structuresreferentes_id_seq
    START WITH 6
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.structuresreferentes_id_seq OWNER TO webrsa;

--
-- Name: structuresreferentes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE structuresreferentes_id_seq OWNED BY structuresreferentes.id;


--
-- Name: structuresreferentes_zonesgeographiques; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE structuresreferentes_zonesgeographiques (
    structurereferente_id integer NOT NULL,
    zonegeographique_id integer NOT NULL
);


ALTER TABLE public.structuresreferentes_zonesgeographiques OWNER TO webrsa;

--
-- Name: suivisinstruction; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE suivisinstruction (
    id integer NOT NULL,
    dossier_rsa_id integer NOT NULL,
    etatirsa character(2),
    date_etat_instruction date,
    nomins character varying(28),
    prenomins character varying(32),
    numdepins character(3),
    typeserins character(1),
    numcomins character(3),
    numagrins integer
);


ALTER TABLE public.suivisinstruction OWNER TO webrsa;

--
-- Name: suivisinstruction_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE suivisinstruction_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.suivisinstruction_id_seq OWNER TO webrsa;

--
-- Name: suivisinstruction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE suivisinstruction_id_seq OWNED BY suivisinstruction.id;


--
-- Name: suspensionsdroits; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE suspensionsdroits (
    id integer NOT NULL,
    situationdossierrsa_id integer NOT NULL,
    motisusdrorsa character(2),
    ddsusdrorsa date
);


ALTER TABLE public.suspensionsdroits OWNER TO webrsa;

--
-- Name: suspensionsdroits_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE suspensionsdroits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.suspensionsdroits_id_seq OWNER TO webrsa;

--
-- Name: suspensionsdroits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE suspensionsdroits_id_seq OWNED BY suspensionsdroits.id;


--
-- Name: suspensionsversements; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE suspensionsversements (
    id integer NOT NULL,
    situationdossierrsa_id integer NOT NULL,
    motisusversrsa character(2),
    ddsusversrsa date
);


ALTER TABLE public.suspensionsversements OWNER TO webrsa;

--
-- Name: suspensionsversements_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE suspensionsversements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.suspensionsversements_id_seq OWNER TO webrsa;

--
-- Name: suspensionsversements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE suspensionsversements_id_seq OWNED BY suspensionsversements.id;


--
-- Name: titres_sejour; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE titres_sejour (
    id integer NOT NULL,
    personne_id integer NOT NULL,
    dtentfra date,
    nattitsej character(3),
    menttitsej character(2),
    ddtitsej date,
    dftitsej date,
    numtitsej character varying(10),
    numduptitsej integer
);


ALTER TABLE public.titres_sejour OWNER TO webrsa;

--
-- Name: titres_sejour_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE titres_sejour_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.titres_sejour_id_seq OWNER TO webrsa;

--
-- Name: titres_sejour_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE titres_sejour_id_seq OWNED BY titres_sejour.id;


--
-- Name: totalisationsacomptes; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE totalisationsacomptes (
    id integer NOT NULL,
    identificationflux_id integer NOT NULL,
    type_totalisation character varying(30),
    mttotsoclrsa numeric(12,2),
    mttotsoclmajorsa numeric(12,2),
    mttotlocalrsa numeric(12,2),
    mttotrsa numeric(12,2)
);


ALTER TABLE public.totalisationsacomptes OWNER TO webrsa;

--
-- Name: totalisationsacomptes_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE totalisationsacomptes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.totalisationsacomptes_id_seq OWNER TO webrsa;

--
-- Name: totalisationsacomptes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE totalisationsacomptes_id_seq OWNED BY totalisationsacomptes.id;


--
-- Name: typesactions; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE typesactions (
    id integer NOT NULL,
    libelle character varying(250)
);


ALTER TABLE public.typesactions OWNER TO webrsa;

--
-- Name: typesactions_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE typesactions_id_seq
    START WITH 6
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.typesactions_id_seq OWNER TO webrsa;

--
-- Name: typesactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE typesactions_id_seq OWNED BY typesactions.id;


--
-- Name: typesorients; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE typesorients (
    id integer NOT NULL,
    parentid integer,
    lib_type_orient character varying(30),
    modele_notif character varying(40)
);


ALTER TABLE public.typesorients OWNER TO webrsa;

--
-- Name: typesorients_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE typesorients_id_seq
    START WITH 11
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.typesorients_id_seq OWNER TO webrsa;

--
-- Name: typesorients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE typesorients_id_seq OWNED BY typesorients.id;


--
-- Name: typoscontrats; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE typoscontrats (
    id integer NOT NULL,
    lib_typo character varying(20)
);


ALTER TABLE public.typoscontrats OWNER TO webrsa;

--
-- Name: typoscontrats_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE typoscontrats_id_seq
    START WITH 4
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.typoscontrats_id_seq OWNER TO webrsa;

--
-- Name: typoscontrats_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE typoscontrats_id_seq OWNED BY typoscontrats.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE users (
    id integer NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    serviceinstructeur_id integer NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(50) NOT NULL,
    nom character varying(50),
    prenom character varying(50),
    date_naissance date,
    date_deb_hab date,
    date_fin_hab date,
    numtel character varying(15),
    filtre_zone_geo boolean DEFAULT true
);


ALTER TABLE public.users OWNER TO webrsa;

--
-- Name: users_contratsinsertion; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE users_contratsinsertion (
    user_id integer NOT NULL,
    contratinsertion_id integer NOT NULL
);


ALTER TABLE public.users_contratsinsertion OWNER TO webrsa;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE users_id_seq
    START WITH 7
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO webrsa;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: users_zonesgeographiques; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE users_zonesgeographiques (
    user_id integer NOT NULL,
    zonegeographique_id integer NOT NULL,
    id integer NOT NULL
);


ALTER TABLE public.users_zonesgeographiques OWNER TO webrsa;

--
-- Name: users_zonesgeographiques_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE users_zonesgeographiques_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_zonesgeographiques_id_seq OWNER TO webrsa;

--
-- Name: users_zonesgeographiques_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE users_zonesgeographiques_id_seq OWNED BY users_zonesgeographiques.id;


--
-- Name: zonesgeographiques; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE zonesgeographiques (
    id integer NOT NULL,
    codeinsee character(5) NOT NULL,
    libelle character varying(50) NOT NULL
);


ALTER TABLE public.zonesgeographiques OWNER TO webrsa;

--
-- Name: zonesgeographiques_id_seq; Type: SEQUENCE; Schema: public; Owner: webrsa
--

CREATE SEQUENCE zonesgeographiques_id_seq
    START WITH 4
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.zonesgeographiques_id_seq OWNER TO webrsa;

--
-- Name: zonesgeographiques_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: webrsa
--

ALTER SEQUENCE zonesgeographiques_id_seq OWNED BY zonesgeographiques.id;


--
-- Name: zonesgeographiques_regroupementszonesgeo; Type: TABLE; Schema: public; Owner: webrsa; Tablespace:
--

CREATE TABLE zonesgeographiques_regroupementszonesgeo (
    zonegeographique_id integer NOT NULL,
    regroupementzonegeo_id integer NOT NULL
);


ALTER TABLE public.zonesgeographiques_regroupementszonesgeo OWNER TO webrsa;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE accoemplois ALTER COLUMN id SET DEFAULT nextval('accoemplois_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE acos ALTER COLUMN id SET DEFAULT nextval('acos_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE actions ALTER COLUMN id SET DEFAULT nextval('actions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE actionsinsertion ALTER COLUMN id SET DEFAULT nextval('actionsinsertion_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE activites ALTER COLUMN id SET DEFAULT nextval('activites_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE adresses ALTER COLUMN id SET DEFAULT nextval('adresses_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE adresses_foyers ALTER COLUMN id SET DEFAULT nextval('adresses_foyers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE aidesagricoles ALTER COLUMN id SET DEFAULT nextval('aidesagricoles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE aidesdirectes ALTER COLUMN id SET DEFAULT nextval('aidesdirectes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE allocationssoutienfamilial ALTER COLUMN id SET DEFAULT nextval('allocationssoutienfamilial_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE aros ALTER COLUMN id SET DEFAULT nextval('aros_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE aros_acos ALTER COLUMN id SET DEFAULT nextval('aros_acos_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE avispcgdroitrsa ALTER COLUMN id SET DEFAULT nextval('avispcgdroitrsa_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE avispcgpersonnes ALTER COLUMN id SET DEFAULT nextval('avispcgpersonnes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE condsadmins ALTER COLUMN id SET DEFAULT nextval('condsadmins_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE connections ALTER COLUMN id SET DEFAULT nextval('connections_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE contratsinsertion ALTER COLUMN id SET DEFAULT nextval('contratsinsertion_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE creances ALTER COLUMN id SET DEFAULT nextval('creances_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE creancesalimentaires ALTER COLUMN id SET DEFAULT nextval('creancesalimentaires_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE derogations ALTER COLUMN id SET DEFAULT nextval('derogations_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE detailscalculsdroitsrsa ALTER COLUMN id SET DEFAULT nextval('detailscalculsdroitsrsa_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE detailsdroitsrsa ALTER COLUMN id SET DEFAULT nextval('detailsdroitsrsa_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE detailsressourcesmensuelles ALTER COLUMN id SET DEFAULT nextval('detailsressourcesmensuelles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE difdisps ALTER COLUMN id SET DEFAULT nextval('difdisps_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE diflogs ALTER COLUMN id SET DEFAULT nextval('diflogs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE difsocs ALTER COLUMN id SET DEFAULT nextval('difsocs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE dossiers_rsa ALTER COLUMN id SET DEFAULT nextval('dossiers_rsa_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE dossierscaf ALTER COLUMN id SET DEFAULT nextval('dossierscaf_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE dspfs ALTER COLUMN id SET DEFAULT nextval('dspfs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE dspps ALTER COLUMN id SET DEFAULT nextval('dspps_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE evenements ALTER COLUMN id SET DEFAULT nextval('evenements_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE foyers ALTER COLUMN id SET DEFAULT nextval('foyers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE grossesses ALTER COLUMN id SET DEFAULT nextval('grossesses_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE groups ALTER COLUMN id SET DEFAULT nextval('groups_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE identificationsflux ALTER COLUMN id SET DEFAULT nextval('identificationsflux_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE informationseti ALTER COLUMN id SET DEFAULT nextval('informationseti_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE infosagricoles ALTER COLUMN id SET DEFAULT nextval('infosagricoles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE infosfinancieres ALTER COLUMN id SET DEFAULT nextval('infosfinancieres_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE jetons ALTER COLUMN id SET DEFAULT nextval('jetons_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE liberalites ALTER COLUMN id SET DEFAULT nextval('liberalites_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE modescontact ALTER COLUMN id SET DEFAULT nextval('modescontact_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE nataccosocfams ALTER COLUMN id SET DEFAULT nextval('nataccosocfams_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE nataccosocindis ALTER COLUMN id SET DEFAULT nextval('nataccosocindis_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE natmobs ALTER COLUMN id SET DEFAULT nextval('natmobs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE nivetus ALTER COLUMN id SET DEFAULT nextval('nivetus_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE orientsstructs ALTER COLUMN id SET DEFAULT nextval('orientsstructs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE paiementsfoyers ALTER COLUMN id SET DEFAULT nextval('paiementsfoyers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE personnes ALTER COLUMN id SET DEFAULT nextval('personnes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE prestations ALTER COLUMN id SET DEFAULT nextval('prestations_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE prestsform ALTER COLUMN id SET DEFAULT nextval('prestsform_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE reducsrsa ALTER COLUMN id SET DEFAULT nextval('reducsrsa_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE referents ALTER COLUMN id SET DEFAULT nextval('referents_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE refsprestas ALTER COLUMN id SET DEFAULT nextval('refsprestas_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE regroupementszonesgeo ALTER COLUMN id SET DEFAULT nextval('regroupementszonesgeo_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE ressources ALTER COLUMN id SET DEFAULT nextval('ressources_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE ressourcesmensuelles ALTER COLUMN id SET DEFAULT nextval('ressourcesmensuelles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE servicesinstructeurs ALTER COLUMN id SET DEFAULT nextval('servicesinstructeurs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE situationsdossiersrsa ALTER COLUMN id SET DEFAULT nextval('situationsdossiersrsa_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE structuresreferentes ALTER COLUMN id SET DEFAULT nextval('structuresreferentes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE suivisinstruction ALTER COLUMN id SET DEFAULT nextval('suivisinstruction_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE suspensionsdroits ALTER COLUMN id SET DEFAULT nextval('suspensionsdroits_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE suspensionsversements ALTER COLUMN id SET DEFAULT nextval('suspensionsversements_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE titres_sejour ALTER COLUMN id SET DEFAULT nextval('titres_sejour_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE totalisationsacomptes ALTER COLUMN id SET DEFAULT nextval('totalisationsacomptes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE typesactions ALTER COLUMN id SET DEFAULT nextval('typesactions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE typesorients ALTER COLUMN id SET DEFAULT nextval('typesorients_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE typoscontrats ALTER COLUMN id SET DEFAULT nextval('typoscontrats_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE users_zonesgeographiques ALTER COLUMN id SET DEFAULT nextval('users_zonesgeographiques_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: webrsa
--

ALTER TABLE zonesgeographiques ALTER COLUMN id SET DEFAULT nextval('zonesgeographiques_id_seq'::regclass);


--
-- Name: accoemplois_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY accoemplois
    ADD CONSTRAINT accoemplois_pkey PRIMARY KEY (id);


--
-- Name: acos_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY acos
    ADD CONSTRAINT acos_pkey PRIMARY KEY (id);


--
-- Name: actions_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_pkey PRIMARY KEY (id);


--
-- Name: actionsinsertion_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY actionsinsertion
    ADD CONSTRAINT actionsinsertion_pkey PRIMARY KEY (id);


--
-- Name: activites_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY activites
    ADD CONSTRAINT activites_pkey PRIMARY KEY (id);


--
-- Name: adresses_foyers_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY adresses_foyers
    ADD CONSTRAINT adresses_foyers_pkey PRIMARY KEY (id);


--
-- Name: adresses_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY adresses
    ADD CONSTRAINT adresses_pkey PRIMARY KEY (id);


--
-- Name: aidesagricoles_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY aidesagricoles
    ADD CONSTRAINT aidesagricoles_pkey PRIMARY KEY (id);


--
-- Name: aidesdirectes_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY aidesdirectes
    ADD CONSTRAINT aidesdirectes_pkey PRIMARY KEY (id);


--
-- Name: allocationssoutienfamilial_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY allocationssoutienfamilial
    ADD CONSTRAINT allocationssoutienfamilial_pkey PRIMARY KEY (id);


--
-- Name: aros_acos_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY aros_acos
    ADD CONSTRAINT aros_acos_pkey PRIMARY KEY (id);


--
-- Name: aros_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY aros
    ADD CONSTRAINT aros_pkey PRIMARY KEY (id);


--
-- Name: avispcgdroitrsa_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY avispcgdroitrsa
    ADD CONSTRAINT avispcgdroitrsa_pkey PRIMARY KEY (id);


--
-- Name: avispcgpersonnes_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY avispcgpersonnes
    ADD CONSTRAINT avispcgpersonnes_pkey PRIMARY KEY (id);


--
-- Name: condsadmins_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY condsadmins
    ADD CONSTRAINT condsadmins_pkey PRIMARY KEY (id);


--
-- Name: connections_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY connections
    ADD CONSTRAINT connections_pkey PRIMARY KEY (id);


--
-- Name: contratsinsertion_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY contratsinsertion
    ADD CONSTRAINT contratsinsertion_pkey PRIMARY KEY (id);


--
-- Name: creances_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY creances
    ADD CONSTRAINT creances_pkey PRIMARY KEY (id);


--
-- Name: creancesalimentaires_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY creancesalimentaires
    ADD CONSTRAINT creancesalimentaires_pkey PRIMARY KEY (id);


--
-- Name: derogations_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY derogations
    ADD CONSTRAINT derogations_pkey PRIMARY KEY (id);


--
-- Name: detailscalculsdroitsrsa_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY detailscalculsdroitsrsa
    ADD CONSTRAINT detailscalculsdroitsrsa_pkey PRIMARY KEY (id);


--
-- Name: detailsdroitsrsa_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY detailsdroitsrsa
    ADD CONSTRAINT detailsdroitsrsa_pkey PRIMARY KEY (id);


--
-- Name: detailsressourcesmensuelles_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY detailsressourcesmensuelles
    ADD CONSTRAINT detailsressourcesmensuelles_pkey PRIMARY KEY (id);


--
-- Name: difdisps_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY difdisps
    ADD CONSTRAINT difdisps_pkey PRIMARY KEY (id);


--
-- Name: diflogs_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY diflogs
    ADD CONSTRAINT diflogs_pkey PRIMARY KEY (id);


--
-- Name: difsocs_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY difsocs
    ADD CONSTRAINT difsocs_pkey PRIMARY KEY (id);


--
-- Name: dossiers_rsa_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY dossiers_rsa
    ADD CONSTRAINT dossiers_rsa_pkey PRIMARY KEY (id);


--
-- Name: dossierscaf_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY dossierscaf
    ADD CONSTRAINT dossierscaf_pkey PRIMARY KEY (id);


--
-- Name: dspfs_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY dspfs
    ADD CONSTRAINT dspfs_pkey PRIMARY KEY (id);


--
-- Name: dspps_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY dspps
    ADD CONSTRAINT dspps_pkey PRIMARY KEY (id);


--
-- Name: evenements_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY evenements
    ADD CONSTRAINT evenements_pkey PRIMARY KEY (id);


--
-- Name: foyers_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY foyers
    ADD CONSTRAINT foyers_pkey PRIMARY KEY (id);


--
-- Name: grossesses_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY grossesses
    ADD CONSTRAINT grossesses_pkey PRIMARY KEY (id);


--
-- Name: groups_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY groups
    ADD CONSTRAINT groups_pkey PRIMARY KEY (id);


--
-- Name: identificationsflux_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY identificationsflux
    ADD CONSTRAINT identificationsflux_pkey PRIMARY KEY (id);


--
-- Name: informationseti_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY informationseti
    ADD CONSTRAINT informationseti_pkey PRIMARY KEY (id);


--
-- Name: infosagricoles_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY infosagricoles
    ADD CONSTRAINT infosagricoles_pkey PRIMARY KEY (id);


--
-- Name: infosfinancieres_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY infosfinancieres
    ADD CONSTRAINT infosfinancieres_pkey PRIMARY KEY (id);


--
-- Name: jetons_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY jetons
    ADD CONSTRAINT jetons_pkey PRIMARY KEY (id);


--
-- Name: liberalites_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY liberalites
    ADD CONSTRAINT liberalites_pkey PRIMARY KEY (id);


--
-- Name: modescontact_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY modescontact
    ADD CONSTRAINT modescontact_pkey PRIMARY KEY (id);


--
-- Name: nataccosocfams_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY nataccosocfams
    ADD CONSTRAINT nataccosocfams_pkey PRIMARY KEY (id);


--
-- Name: nataccosocindis_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY nataccosocindis
    ADD CONSTRAINT nataccosocindis_pkey PRIMARY KEY (id);


--
-- Name: natmobs_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY natmobs
    ADD CONSTRAINT natmobs_pkey PRIMARY KEY (id);


--
-- Name: nivetus_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY nivetus
    ADD CONSTRAINT nivetus_pkey PRIMARY KEY (id);


--
-- Name: orientsstructs_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY orientsstructs
    ADD CONSTRAINT orientsstructs_pkey PRIMARY KEY (id);


--
-- Name: orientsstructs_servicesinstructeurs_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY orientsstructs_servicesinstructeurs
    ADD CONSTRAINT orientsstructs_servicesinstructeurs_pkey PRIMARY KEY (orientstruct_id, serviceinstructeur_id);


--
-- Name: paiementsfoyers_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY paiementsfoyers
    ADD CONSTRAINT paiementsfoyers_pkey PRIMARY KEY (id);


--
-- Name: personnes_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY personnes
    ADD CONSTRAINT personnes_pkey PRIMARY KEY (id);


--
-- Name: prestations_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY prestations
    ADD CONSTRAINT prestations_pkey PRIMARY KEY (id);


--
-- Name: prestsform_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY prestsform
    ADD CONSTRAINT prestsform_pkey PRIMARY KEY (id);


--
-- Name: rattachements_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY rattachements
    ADD CONSTRAINT rattachements_pkey PRIMARY KEY (personne_id, rattache_id);


--
-- Name: reducsrsa_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY reducsrsa
    ADD CONSTRAINT reducsrsa_pkey PRIMARY KEY (id);


--
-- Name: referents_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY referents
    ADD CONSTRAINT referents_pkey PRIMARY KEY (id);


--
-- Name: refsprestas_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY refsprestas
    ADD CONSTRAINT refsprestas_pkey PRIMARY KEY (id);


--
-- Name: regroupementszonesgeo_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY regroupementszonesgeo
    ADD CONSTRAINT regroupementszonesgeo_pkey PRIMARY KEY (id);


--
-- Name: ressources_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY ressources
    ADD CONSTRAINT ressources_pkey PRIMARY KEY (id);


--
-- Name: ressourcesmensuelles_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY ressourcesmensuelles
    ADD CONSTRAINT ressourcesmensuelles_pkey PRIMARY KEY (id);


--
-- Name: servicesinstructeurs_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY servicesinstructeurs
    ADD CONSTRAINT servicesinstructeurs_pkey PRIMARY KEY (id);


--
-- Name: situationsdossiersrsa_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY situationsdossiersrsa
    ADD CONSTRAINT situationsdossiersrsa_pkey PRIMARY KEY (id);


--
-- Name: structuresreferentes_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY structuresreferentes
    ADD CONSTRAINT structuresreferentes_pkey PRIMARY KEY (id);


--
-- Name: structuresreferentes_zonesgeographiques_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY structuresreferentes_zonesgeographiques
    ADD CONSTRAINT structuresreferentes_zonesgeographiques_pkey PRIMARY KEY (structurereferente_id, zonegeographique_id);


--
-- Name: suivisinstruction_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY suivisinstruction
    ADD CONSTRAINT suivisinstruction_pkey PRIMARY KEY (id);


--
-- Name: suspensionsdroits_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY suspensionsdroits
    ADD CONSTRAINT suspensionsdroits_pkey PRIMARY KEY (id);


--
-- Name: suspensionsversements_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY suspensionsversements
    ADD CONSTRAINT suspensionsversements_pkey PRIMARY KEY (id);


--
-- Name: titres_sejour_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY titres_sejour
    ADD CONSTRAINT titres_sejour_pkey PRIMARY KEY (id);


--
-- Name: totalisationsacomptes_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY totalisationsacomptes
    ADD CONSTRAINT totalisationsacomptes_pkey PRIMARY KEY (id);


--
-- Name: typesactions_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY typesactions
    ADD CONSTRAINT typesactions_pkey PRIMARY KEY (id);


--
-- Name: typesorients_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY typesorients
    ADD CONSTRAINT typesorients_pkey PRIMARY KEY (id);


--
-- Name: typoscontrats_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY typoscontrats
    ADD CONSTRAINT typoscontrats_pkey PRIMARY KEY (id);


--
-- Name: users_contratsinsertion_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY users_contratsinsertion
    ADD CONSTRAINT users_contratsinsertion_pkey PRIMARY KEY (user_id, contratinsertion_id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users_username_key; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- Name: users_zonesgeographiques_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY users_zonesgeographiques
    ADD CONSTRAINT users_zonesgeographiques_pkey PRIMARY KEY (id);


--
-- Name: zonesgeographiques_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY zonesgeographiques
    ADD CONSTRAINT zonesgeographiques_pkey PRIMARY KEY (id);


--
-- Name: zonesgeographiques_regroupementszonesgeo_pkey; Type: CONSTRAINT; Schema: public; Owner: webrsa; Tablespace:
--

ALTER TABLE ONLY zonesgeographiques_regroupementszonesgeo
    ADD CONSTRAINT zonesgeographiques_regroupementszonesgeo_pkey PRIMARY KEY (zonegeographique_id, regroupementzonegeo_id);


--
-- Name: adresses_codepos_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX adresses_codepos_idx ON adresses USING btree (codepos);


--
-- Name: adresses_foyers_adresse_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX adresses_foyers_adresse_id_idx ON adresses_foyers USING btree (adresse_id);


--
-- Name: adresses_foyers_foyer_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX adresses_foyers_foyer_id_idx ON adresses_foyers USING btree (foyer_id);


--
-- Name: adresses_foyers_rgadr_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX adresses_foyers_rgadr_idx ON adresses_foyers USING btree (rgadr);


--
-- Name: adresses_locaadr_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX adresses_locaadr_idx ON adresses USING btree (locaadr);


--
-- Name: adresses_nomvoie_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX adresses_nomvoie_idx ON adresses USING btree (nomvoie);


--
-- Name: adresses_numcomptt_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX adresses_numcomptt_idx ON adresses USING btree (numcomptt);


--
-- Name: adresses_numcomrat_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX adresses_numcomrat_idx ON adresses USING btree (numcomrat);


--
-- Name: creancesalimentaires_etatcrealim_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX creancesalimentaires_etatcrealim_idx ON creancesalimentaires USING btree (etatcrealim);


--
-- Name: detailscalculsdroitsrsa_detaildroitrsa_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX detailscalculsdroitsrsa_detaildroitrsa_id_idx ON detailscalculsdroitsrsa USING btree (detaildroitrsa_id);


--
-- Name: detailscalculsdroitsrsa_natpf_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX detailscalculsdroitsrsa_natpf_idx ON detailscalculsdroitsrsa USING btree (natpf);


--
-- Name: detailsdroitsrsa_dossier_rsa_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX detailsdroitsrsa_dossier_rsa_id_idx ON detailsdroitsrsa USING btree (dossier_rsa_id);


--
-- Name: detailsdroitsrsa_topfoydrodevorsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX detailsdroitsrsa_topfoydrodevorsa_idx ON detailsdroitsrsa USING btree (topfoydrodevorsa);


--
-- Name: dossiers_rsa_dtdemrsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX dossiers_rsa_dtdemrsa_idx ON dossiers_rsa USING btree (dtdemrsa);


--
-- Name: dossiers_rsa_matricule_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX dossiers_rsa_matricule_idx ON dossiers_rsa USING btree (matricule);


--
-- Name: dossiers_rsa_numcli_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX dossiers_rsa_numcli_idx ON dossiers_rsa USING btree (numcli);


--
-- Name: dossiers_rsa_numdemrsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX dossiers_rsa_numdemrsa_idx ON dossiers_rsa USING btree (numdemrsa);


--
-- Name: dossiers_rsa_statudemrsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX dossiers_rsa_statudemrsa_idx ON dossiers_rsa USING btree (statudemrsa);


--
-- Name: foyers_dossier_rsa_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX foyers_dossier_rsa_id_idx ON foyers USING btree (dossier_rsa_id);


--
-- Name: foyers_sitfam_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX foyers_sitfam_idx ON foyers USING btree (sitfam);


--
-- Name: personnes_dtnai_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX personnes_dtnai_idx ON personnes USING btree (dtnai);


--
-- Name: personnes_foyer_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX personnes_foyer_id_idx ON personnes USING btree (foyer_id);


--
-- Name: personnes_nir_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX personnes_nir_idx ON personnes USING btree (nir);


--
-- Name: personnes_nom_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX personnes_nom_idx ON personnes USING btree (nom);


--
-- Name: personnes_nomnai_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX personnes_nomnai_idx ON personnes USING btree (nomnai);


--
-- Name: personnes_prenom_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX personnes_prenom_idx ON personnes USING btree (prenom);


--
-- Name: prestations_natprest_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX prestations_natprest_idx ON prestations USING btree (natprest);


--
-- Name: prestations_personne_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX prestations_personne_id_idx ON prestations USING btree (personne_id);


--
-- Name: prestations_rolepers_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX prestations_rolepers_idx ON prestations USING btree (rolepers);


--
-- Name: prestations_topchapers_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX prestations_topchapers_idx ON prestations USING btree (topchapers);


--
-- Name: prestations_toppersdrodevorsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX prestations_toppersdrodevorsa_idx ON prestations USING btree (toppersdrodevorsa);


--
-- Name: situationsdossiersrsa_dossier_rsa_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX situationsdossiersrsa_dossier_rsa_id_idx ON situationsdossiersrsa USING btree (dossier_rsa_id);


--
-- Name: situationsdossiersrsa_dtclorsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX situationsdossiersrsa_dtclorsa_idx ON situationsdossiersrsa USING btree (dtclorsa);


--
-- Name: situationsdossiersrsa_dtrefursa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX situationsdossiersrsa_dtrefursa_idx ON situationsdossiersrsa USING btree (dtrefursa);


--
-- Name: situationsdossiersrsa_etatdosrsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX situationsdossiersrsa_etatdosrsa_idx ON situationsdossiersrsa USING btree (etatdosrsa);


--
-- Name: situationsdossiersrsa_moticlorsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX situationsdossiersrsa_moticlorsa_idx ON situationsdossiersrsa USING btree (moticlorsa);


--
-- Name: suspensionsdroits_ddsusdrorsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX suspensionsdroits_ddsusdrorsa_idx ON suspensionsdroits USING btree (ddsusdrorsa);


--
-- Name: suspensionsdroits_motisusdrorsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX suspensionsdroits_motisusdrorsa_idx ON suspensionsdroits USING btree (motisusdrorsa);


--
-- Name: suspensionsdroits_situationdossierrsa_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX suspensionsdroits_situationdossierrsa_id_idx ON suspensionsdroits USING btree (situationdossierrsa_id);


--
-- Name: suspensionsversements_ddsusversrsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX suspensionsversements_ddsusversrsa_idx ON suspensionsversements USING btree (ddsusversrsa);


--
-- Name: suspensionsversements_motisusversrsa_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX suspensionsversements_motisusversrsa_idx ON suspensionsversements USING btree (motisusversrsa);


--
-- Name: suspensionsversements_situationdossierrsa_id_idx; Type: INDEX; Schema: public; Owner: webrsa; Tablespace:
--

CREATE INDEX suspensionsversements_situationdossierrsa_id_idx ON suspensionsversements USING btree (situationdossierrsa_id);


--
-- Name: actions_typeaction_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_typeaction_id_fkey FOREIGN KEY (typeaction_id) REFERENCES typesactions(id);


--
-- Name: actionsinsertion_contratinsertion_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY actionsinsertion
    ADD CONSTRAINT actionsinsertion_contratinsertion_id_fkey FOREIGN KEY (contratinsertion_id) REFERENCES contratsinsertion(id);


--
-- Name: activites_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY activites
    ADD CONSTRAINT activites_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: adresses_foyers_adresse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY adresses_foyers
    ADD CONSTRAINT adresses_foyers_adresse_id_fkey FOREIGN KEY (adresse_id) REFERENCES adresses(id);


--
-- Name: adresses_foyers_foyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY adresses_foyers
    ADD CONSTRAINT adresses_foyers_foyer_id_fkey FOREIGN KEY (foyer_id) REFERENCES foyers(id);


--
-- Name: aidesagricoles_infoagricole_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY aidesagricoles
    ADD CONSTRAINT aidesagricoles_infoagricole_id_fkey FOREIGN KEY (infoagricole_id) REFERENCES infosagricoles(id);


--
-- Name: aidesdirectes_actioninsertion_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY aidesdirectes
    ADD CONSTRAINT aidesdirectes_actioninsertion_id_fkey FOREIGN KEY (actioninsertion_id) REFERENCES actionsinsertion(id);


--
-- Name: allocationssoutienfamilial_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY allocationssoutienfamilial
    ADD CONSTRAINT allocationssoutienfamilial_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: avispcgdroitrsa_dossier_rsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY avispcgdroitrsa
    ADD CONSTRAINT avispcgdroitrsa_dossier_rsa_id_fkey FOREIGN KEY (dossier_rsa_id) REFERENCES dossiers_rsa(id);


--
-- Name: avispcgpersonnes_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY avispcgpersonnes
    ADD CONSTRAINT avispcgpersonnes_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: condsadmins_avispcgdroitrsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY condsadmins
    ADD CONSTRAINT condsadmins_avispcgdroitrsa_id_fkey FOREIGN KEY (avispcgdroitrsa_id) REFERENCES avispcgdroitrsa(id);


--
-- Name: connections_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY connections
    ADD CONSTRAINT connections_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: contratsinsertion_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY contratsinsertion
    ADD CONSTRAINT contratsinsertion_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: contratsinsertion_structurereferente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY contratsinsertion
    ADD CONSTRAINT contratsinsertion_structurereferente_id_fkey FOREIGN KEY (structurereferente_id) REFERENCES structuresreferentes(id);


--
-- Name: contratsinsertion_typocontrat_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY contratsinsertion
    ADD CONSTRAINT contratsinsertion_typocontrat_id_fkey FOREIGN KEY (typocontrat_id) REFERENCES typoscontrats(id);


--
-- Name: creancesalimentaires_personnes_creancealimentaire_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY creancesalimentaires_personnes
    ADD CONSTRAINT creancesalimentaires_personnes_creancealimentaire_id_fkey FOREIGN KEY (creancealimentaire_id) REFERENCES creancesalimentaires(id);


--
-- Name: creancesalimentaires_personnes_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY creancesalimentaires_personnes
    ADD CONSTRAINT creancesalimentaires_personnes_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: derogations_avispcgpersonne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY derogations
    ADD CONSTRAINT derogations_avispcgpersonne_id_fkey FOREIGN KEY (avispcgpersonne_id) REFERENCES avispcgpersonnes(id);


--
-- Name: detailscalculsdroitsrsa_detaildroitrsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY detailscalculsdroitsrsa
    ADD CONSTRAINT detailscalculsdroitsrsa_detaildroitrsa_id_fkey FOREIGN KEY (detaildroitrsa_id) REFERENCES detailsdroitsrsa(id);


--
-- Name: detailsdroitsrsa_dossier_rsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY detailsdroitsrsa
    ADD CONSTRAINT detailsdroitsrsa_dossier_rsa_id_fkey FOREIGN KEY (dossier_rsa_id) REFERENCES dossiers_rsa(id);


--
-- Name: detailsressourcesmensuelles_ressourcemensuelle_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY detailsressourcesmensuelles
    ADD CONSTRAINT detailsressourcesmensuelles_ressourcemensuelle_id_fkey FOREIGN KEY (ressourcemensuelle_id) REFERENCES ressourcesmensuelles(id);


--
-- Name: distfk; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY creancesalimentaires
    ADD CONSTRAINT distfk FOREIGN KEY (personne_id) REFERENCES personnes(id) MATCH FULL;


--
-- Name: dossierscaf_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dossierscaf
    ADD CONSTRAINT dossierscaf_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: dspfs_diflogs_diflog_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspfs_diflogs
    ADD CONSTRAINT dspfs_diflogs_diflog_id_fkey FOREIGN KEY (diflog_id) REFERENCES diflogs(id);


--
-- Name: dspfs_diflogs_dspf_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspfs_diflogs
    ADD CONSTRAINT dspfs_diflogs_dspf_id_fkey FOREIGN KEY (dspf_id) REFERENCES dspfs(id);


--
-- Name: dspfs_foyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspfs
    ADD CONSTRAINT dspfs_foyer_id_fkey FOREIGN KEY (foyer_id) REFERENCES foyers(id);


--
-- Name: dspfs_nataccosocfams_dspf_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspfs_nataccosocfams
    ADD CONSTRAINT dspfs_nataccosocfams_dspf_id_fkey FOREIGN KEY (dspf_id) REFERENCES dspfs(id);


--
-- Name: dspfs_nataccosocfams_nataccosocfam_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspfs_nataccosocfams
    ADD CONSTRAINT dspfs_nataccosocfams_nataccosocfam_id_fkey FOREIGN KEY (nataccosocfam_id) REFERENCES nataccosocfams(id);


--
-- Name: dspps_accoemplois_accoemploi_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_accoemplois
    ADD CONSTRAINT dspps_accoemplois_accoemploi_id_fkey FOREIGN KEY (accoemploi_id) REFERENCES accoemplois(id);


--
-- Name: dspps_accoemplois_dspp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_accoemplois
    ADD CONSTRAINT dspps_accoemplois_dspp_id_fkey FOREIGN KEY (dspp_id) REFERENCES dspps(id);


--
-- Name: dspps_difdisps_difdisp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_difdisps
    ADD CONSTRAINT dspps_difdisps_difdisp_id_fkey FOREIGN KEY (difdisp_id) REFERENCES difdisps(id);


--
-- Name: dspps_difdisps_dspp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_difdisps
    ADD CONSTRAINT dspps_difdisps_dspp_id_fkey FOREIGN KEY (dspp_id) REFERENCES dspps(id);


--
-- Name: dspps_difsocs_difsoc_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_difsocs
    ADD CONSTRAINT dspps_difsocs_difsoc_id_fkey FOREIGN KEY (difsoc_id) REFERENCES difsocs(id);


--
-- Name: dspps_difsocs_dspp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_difsocs
    ADD CONSTRAINT dspps_difsocs_dspp_id_fkey FOREIGN KEY (dspp_id) REFERENCES dspps(id);


--
-- Name: dspps_nataccosocindis_dspp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_nataccosocindis
    ADD CONSTRAINT dspps_nataccosocindis_dspp_id_fkey FOREIGN KEY (dspp_id) REFERENCES dspps(id);


--
-- Name: dspps_nataccosocindis_nataccosocindi_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_nataccosocindis
    ADD CONSTRAINT dspps_nataccosocindis_nataccosocindi_id_fkey FOREIGN KEY (nataccosocindi_id) REFERENCES nataccosocindis(id);


--
-- Name: dspps_natmobs_dspp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_natmobs
    ADD CONSTRAINT dspps_natmobs_dspp_id_fkey FOREIGN KEY (dspp_id) REFERENCES dspps(id);


--
-- Name: dspps_natmobs_natmob_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_natmobs
    ADD CONSTRAINT dspps_natmobs_natmob_id_fkey FOREIGN KEY (natmob_id) REFERENCES natmobs(id);


--
-- Name: dspps_nivetus_dspp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_nivetus
    ADD CONSTRAINT dspps_nivetus_dspp_id_fkey FOREIGN KEY (dspp_id) REFERENCES dspps(id);


--
-- Name: dspps_nivetus_nivetu_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps_nivetus
    ADD CONSTRAINT dspps_nivetus_nivetu_id_fkey FOREIGN KEY (nivetu_id) REFERENCES nivetus(id);


--
-- Name: dspps_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY dspps
    ADD CONSTRAINT dspps_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: foyers_creances_creance_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY foyers_creances
    ADD CONSTRAINT foyers_creances_creance_id_fkey FOREIGN KEY (creance_id) REFERENCES creances(id);


--
-- Name: foyers_creances_foyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY foyers_creances
    ADD CONSTRAINT foyers_creances_foyer_id_fkey FOREIGN KEY (foyer_id) REFERENCES foyers(id);


--
-- Name: foyers_dossier_rsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY foyers
    ADD CONSTRAINT foyers_dossier_rsa_id_fkey FOREIGN KEY (dossier_rsa_id) REFERENCES dossiers_rsa(id);


--
-- Name: foyers_evenements_evenement_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY foyers_evenements
    ADD CONSTRAINT foyers_evenements_evenement_id_fkey FOREIGN KEY (evenement_id) REFERENCES evenements(id);


--
-- Name: foyers_evenements_foyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY foyers_evenements
    ADD CONSTRAINT foyers_evenements_foyer_id_fkey FOREIGN KEY (foyer_id) REFERENCES foyers(id);


--
-- Name: grossesses_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY grossesses
    ADD CONSTRAINT grossesses_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: informationseti_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY informationseti
    ADD CONSTRAINT informationseti_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: infosagricoles_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY infosagricoles
    ADD CONSTRAINT infosagricoles_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: infosfinancieres_dossier_rsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY infosfinancieres
    ADD CONSTRAINT infosfinancieres_dossier_rsa_id_fkey FOREIGN KEY (dossier_rsa_id) REFERENCES dossiers_rsa(id);


--
-- Name: jetons_dossier_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY jetons
    ADD CONSTRAINT jetons_dossier_id_fkey FOREIGN KEY (dossier_id) REFERENCES dossiers_rsa(id);


--
-- Name: jetons_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY jetons
    ADD CONSTRAINT jetons_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: liberalites_avispcgpersonne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY liberalites
    ADD CONSTRAINT liberalites_avispcgpersonne_id_fkey FOREIGN KEY (avispcgpersonne_id) REFERENCES avispcgpersonnes(id);


--
-- Name: modescontact_foyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY modescontact
    ADD CONSTRAINT modescontact_foyer_id_fkey FOREIGN KEY (foyer_id) REFERENCES foyers(id);


--
-- Name: orientsstructs_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY orientsstructs
    ADD CONSTRAINT orientsstructs_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: orientsstructs_propo_algo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY orientsstructs
    ADD CONSTRAINT orientsstructs_propo_algo_fkey FOREIGN KEY (propo_algo) REFERENCES typesorients(id);


--
-- Name: orientsstructs_servicesinstructeurs_orientstruct_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY orientsstructs_servicesinstructeurs
    ADD CONSTRAINT orientsstructs_servicesinstructeurs_orientstruct_id_fkey FOREIGN KEY (orientstruct_id) REFERENCES orientsstructs(id);


--
-- Name: orientsstructs_servicesinstructeurs_serviceinstructeur_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY orientsstructs_servicesinstructeurs
    ADD CONSTRAINT orientsstructs_servicesinstructeurs_serviceinstructeur_id_fkey FOREIGN KEY (serviceinstructeur_id) REFERENCES servicesinstructeurs(id);


--
-- Name: orientsstructs_structurereferente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY orientsstructs
    ADD CONSTRAINT orientsstructs_structurereferente_id_fkey FOREIGN KEY (structurereferente_id) REFERENCES structuresreferentes(id);


--
-- Name: orientsstructs_typeorient_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY orientsstructs
    ADD CONSTRAINT orientsstructs_typeorient_id_fkey FOREIGN KEY (typeorient_id) REFERENCES typesorients(id);


--
-- Name: paiementsfoyers_foyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY paiementsfoyers
    ADD CONSTRAINT paiementsfoyers_foyer_id_fkey FOREIGN KEY (foyer_id) REFERENCES foyers(id);


--
-- Name: personnes_foyer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY personnes
    ADD CONSTRAINT personnes_foyer_id_fkey FOREIGN KEY (foyer_id) REFERENCES foyers(id);


--
-- Name: prestsform_actioninsertion_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY prestsform
    ADD CONSTRAINT prestsform_actioninsertion_id_fkey FOREIGN KEY (actioninsertion_id) REFERENCES actionsinsertion(id);


--
-- Name: prestsform_refpresta_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY prestsform
    ADD CONSTRAINT prestsform_refpresta_id_fkey FOREIGN KEY (refpresta_id) REFERENCES refsprestas(id);


--
-- Name: rattachements_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY rattachements
    ADD CONSTRAINT rattachements_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: rattachements_rattache_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY rattachements
    ADD CONSTRAINT rattachements_rattache_id_fkey FOREIGN KEY (rattache_id) REFERENCES personnes(id);


--
-- Name: reducsrsa_avispcgdroitrsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY reducsrsa
    ADD CONSTRAINT reducsrsa_avispcgdroitrsa_id_fkey FOREIGN KEY (avispcgdroitrsa_id) REFERENCES avispcgdroitrsa(id);


--
-- Name: referents_structurereferente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY referents
    ADD CONSTRAINT referents_structurereferente_id_fkey FOREIGN KEY (structurereferente_id) REFERENCES structuresreferentes(id);


--
-- Name: ressources_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY ressources
    ADD CONSTRAINT ressources_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: ressources_ressourcesmensuelles_ressource_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY ressources_ressourcesmensuelles
    ADD CONSTRAINT ressources_ressourcesmensuelles_ressource_id_fkey FOREIGN KEY (ressource_id) REFERENCES ressources(id);


--
-- Name: ressources_ressourcesmensuelles_ressourcemensuelle_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY ressources_ressourcesmensuelles
    ADD CONSTRAINT ressources_ressourcesmensuelles_ressourcemensuelle_id_fkey FOREIGN KEY (ressourcemensuelle_id) REFERENCES ressourcesmensuelles(id);


--
-- Name: ressourcesmensuelles_detailsre_detailressourcemensuelle_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY ressourcesmensuelles_detailsressourcesmensuelles
    ADD CONSTRAINT ressourcesmensuelles_detailsre_detailressourcemensuelle_id_fkey FOREIGN KEY (detailressourcemensuelle_id) REFERENCES detailsressourcesmensuelles(id);


--
-- Name: ressourcesmensuelles_detailsressourc_ressourcemensuelle_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY ressourcesmensuelles_detailsressourcesmensuelles
    ADD CONSTRAINT ressourcesmensuelles_detailsressourc_ressourcemensuelle_id_fkey FOREIGN KEY (ressourcemensuelle_id) REFERENCES ressourcesmensuelles(id);


--
-- Name: ressourcesmensuelles_ressource_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY ressourcesmensuelles
    ADD CONSTRAINT ressourcesmensuelles_ressource_id_fkey FOREIGN KEY (ressource_id) REFERENCES ressources(id);


--
-- Name: situationsdossiersrsa_dossier_rsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY situationsdossiersrsa
    ADD CONSTRAINT situationsdossiersrsa_dossier_rsa_id_fkey FOREIGN KEY (dossier_rsa_id) REFERENCES dossiers_rsa(id);


--
-- Name: structuresreferentes_typeorient_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY structuresreferentes
    ADD CONSTRAINT structuresreferentes_typeorient_id_fkey FOREIGN KEY (typeorient_id) REFERENCES typesorients(id);


--
-- Name: structuresreferentes_zonesgeographiq_structurereferente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY structuresreferentes_zonesgeographiques
    ADD CONSTRAINT structuresreferentes_zonesgeographiq_structurereferente_id_fkey FOREIGN KEY (structurereferente_id) REFERENCES structuresreferentes(id);


--
-- Name: structuresreferentes_zonesgeographique_zonegeographique_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY structuresreferentes_zonesgeographiques
    ADD CONSTRAINT structuresreferentes_zonesgeographique_zonegeographique_id_fkey FOREIGN KEY (zonegeographique_id) REFERENCES zonesgeographiques(id);


--
-- Name: suivisinstruction_dossier_rsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY suivisinstruction
    ADD CONSTRAINT suivisinstruction_dossier_rsa_id_fkey FOREIGN KEY (dossier_rsa_id) REFERENCES dossiers_rsa(id);


--
-- Name: suspensionsdroits_situationdossierrsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY suspensionsdroits
    ADD CONSTRAINT suspensionsdroits_situationdossierrsa_id_fkey FOREIGN KEY (situationdossierrsa_id) REFERENCES situationsdossiersrsa(id);


--
-- Name: suspensionsversements_situationdossierrsa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY suspensionsversements
    ADD CONSTRAINT suspensionsversements_situationdossierrsa_id_fkey FOREIGN KEY (situationdossierrsa_id) REFERENCES situationsdossiersrsa(id);


--
-- Name: titres_sejour_personne_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY titres_sejour
    ADD CONSTRAINT titres_sejour_personne_id_fkey FOREIGN KEY (personne_id) REFERENCES personnes(id);


--
-- Name: totalisationsacomptes_identificationflux_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY totalisationsacomptes
    ADD CONSTRAINT totalisationsacomptes_identificationflux_id_fkey FOREIGN KEY (identificationflux_id) REFERENCES identificationsflux(id);


--
-- Name: users_contratsinsertion_contratinsertion_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY users_contratsinsertion
    ADD CONSTRAINT users_contratsinsertion_contratinsertion_id_fkey FOREIGN KEY (contratinsertion_id) REFERENCES contratsinsertion(id);


--
-- Name: users_contratsinsertion_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY users_contratsinsertion
    ADD CONSTRAINT users_contratsinsertion_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: users_group_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_group_id_fkey FOREIGN KEY (group_id) REFERENCES groups(id);


--
-- Name: users_serviceinstructeur_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_serviceinstructeur_id_fkey FOREIGN KEY (serviceinstructeur_id) REFERENCES servicesinstructeurs(id);


--
-- Name: users_zonesgeographiques_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY users_zonesgeographiques
    ADD CONSTRAINT users_zonesgeographiques_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: users_zonesgeographiques_zonegeographique_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY users_zonesgeographiques
    ADD CONSTRAINT users_zonesgeographiques_zonegeographique_id_fkey FOREIGN KEY (zonegeographique_id) REFERENCES zonesgeographiques(id);


--
-- Name: zonesgeographiques_regroupementszon_regroupementzonegeo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY zonesgeographiques_regroupementszonesgeo
    ADD CONSTRAINT zonesgeographiques_regroupementszon_regroupementzonegeo_id_fkey FOREIGN KEY (regroupementzonegeo_id) REFERENCES regroupementszonesgeo(id);


--
-- Name: zonesgeographiques_regroupementszonesg_zonegeographique_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: webrsa
--

ALTER TABLE ONLY zonesgeographiques_regroupementszonesgeo
    ADD CONSTRAINT zonesgeographiques_regroupementszonesg_zonegeographique_id_fkey FOREIGN KEY (zonegeographique_id) REFERENCES zonesgeographiques(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

