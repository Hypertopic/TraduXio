--
-- PostgreSQL database dump
--

-- Started on 2010-03-20 11:30:01 CET

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 323 (class 2612 OID 16691)
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres
--

CREATE PROCEDURAL LANGUAGE plpgsql;


ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO postgres;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1495 (class 1259 OID 16692)
-- Dependencies: 6
-- Name: genre; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE genre (
    id integer NOT NULL,
    name character varying,
    created timestamp with time zone
);


ALTER TABLE public.genre OWNER TO traduxio;

--
-- TOC entry 1496 (class 1259 OID 16698)
-- Dependencies: 1495 6
-- Name: genre_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE genre_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.genre_id_seq OWNER TO traduxio;

--
-- TOC entry 1831 (class 0 OID 0)
-- Dependencies: 1496
-- Name: genre_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE genre_id_seq OWNED BY genre.id;


--
-- TOC entry 1497 (class 1259 OID 16700)
-- Dependencies: 1777 1778 1779 6
-- Name: interpretation; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE interpretation (
    work_id integer NOT NULL,
    original_work_id integer NOT NULL,
    translation text,
    from_segment integer NOT NULL,
    to_segment integer,
    modified timestamp with time zone DEFAULT now(),
    created timestamp with time zone DEFAULT now() NOT NULL,
    CONSTRAINT sentence_order CHECK ((from_segment <= to_segment))
);


ALTER TABLE public.interpretation OWNER TO traduxio;

--
-- TOC entry 1498 (class 1259 OID 16709)
-- Dependencies: 6
-- Name: log; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE log (
    id integer NOT NULL,
    message character varying,
    host character varying,
    level character varying,
    created timestamp with time zone
);


ALTER TABLE public.log OWNER TO traduxio;

--
-- TOC entry 1499 (class 1259 OID 16715)
-- Dependencies: 6 1498
-- Name: log_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE log_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.log_id_seq OWNER TO traduxio;

--
-- TOC entry 1832 (class 0 OID 0)
-- Dependencies: 1499
-- Name: log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE log_id_seq OWNED BY log.id;


--
-- TOC entry 1509 (class 1259 OID 25152)
-- Dependencies: 1791 6
-- Name: privileges; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE privileges (
    id integer NOT NULL,
    privilege character varying,
    user_id character varying,
    work_id integer,
    created timestamp with time zone DEFAULT now(),
    visibility character varying(10)
);


ALTER TABLE public.privileges OWNER TO traduxio;

--
-- TOC entry 1508 (class 1259 OID 25150)
-- Dependencies: 6 1509
-- Name: privileges_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE privileges_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.privileges_id_seq OWNER TO traduxio;

--
-- TOC entry 1833 (class 0 OID 0)
-- Dependencies: 1508
-- Name: privileges_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE privileges_id_seq OWNED BY privileges.id;


--
-- TOC entry 1500 (class 1259 OID 16717)
-- Dependencies: 1781 1783 6
-- Name: role; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE role (
    id integer NOT NULL,
    role character varying DEFAULT 'member'::character varying NOT NULL,
    "user" character varying NOT NULL,
    created timestamp with time zone DEFAULT now()
);


ALTER TABLE public.role OWNER TO traduxio;

--
-- TOC entry 1501 (class 1259 OID 16724)
-- Dependencies: 6 1500
-- Name: role_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.role_id_seq OWNER TO traduxio;

--
-- TOC entry 1834 (class 0 OID 0)
-- Dependencies: 1501
-- Name: role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE role_id_seq OWNED BY role.id;


--
-- TOC entry 1502 (class 1259 OID 16726)
-- Dependencies: 6
-- Name: taggable; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE taggable (
    id integer NOT NULL
);


ALTER TABLE public.taggable OWNER TO traduxio;

--
-- TOC entry 1503 (class 1259 OID 16729)
-- Dependencies: 1502 6
-- Name: taggable_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE taggable_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.taggable_id_seq OWNER TO traduxio;

--
-- TOC entry 1835 (class 0 OID 0)
-- Dependencies: 1503
-- Name: taggable_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE taggable_id_seq OWNED BY taggable.id;


--
-- TOC entry 1504 (class 1259 OID 16731)
-- Dependencies: 1785 1502 6
-- Name: sentence; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE sentence (
    number integer,
    content character varying,
    work_id integer,
    created timestamp with time zone
)
INHERITS (taggable);


ALTER TABLE public.sentence OWNER TO traduxio;

--
-- TOC entry 1505 (class 1259 OID 16737)
-- Dependencies: 1786 6
-- Name: tag; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE tag (
    taggable integer NOT NULL,
    genre integer,
    "user" character varying NOT NULL,
    comment character varying NOT NULL,
    created timestamp with time zone NOT NULL,
    modified timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tag OWNER TO traduxio;

--
-- TOC entry 1507 (class 1259 OID 16832)
-- Dependencies: 1789 6
-- Name: user; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE "user" (
    name character varying NOT NULL,
    last_access timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public."user" OWNER TO traduxio;

--
-- TOC entry 1506 (class 1259 OID 16760)
-- Dependencies: 1787 1788 6 1502
-- Name: work; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE work (
    title character varying,
    author character varying,
    language character varying,
    created timestamp with time zone,
    creator character varying,
    visibility character varying(10) DEFAULT 'private'::character varying NOT NULL,
    modified timestamp with time zone
)
INHERITS (taggable);


ALTER TABLE public.work OWNER TO traduxio;

--
-- TOC entry 1776 (class 2604 OID 16766)
-- Dependencies: 1496 1495
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE genre ALTER COLUMN id SET DEFAULT nextval('genre_id_seq'::regclass);


--
-- TOC entry 1780 (class 2604 OID 16768)
-- Dependencies: 1499 1498
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE log ALTER COLUMN id SET DEFAULT nextval('log_id_seq'::regclass);


--
-- TOC entry 1790 (class 2604 OID 25155)
-- Dependencies: 1508 1509 1509
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE privileges ALTER COLUMN id SET DEFAULT nextval('privileges_id_seq'::regclass);


--
-- TOC entry 1782 (class 2604 OID 16769)
-- Dependencies: 1501 1500
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE role ALTER COLUMN id SET DEFAULT nextval('role_id_seq'::regclass);


--
-- TOC entry 1784 (class 2604 OID 16771)
-- Dependencies: 1503 1502
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE taggable ALTER COLUMN id SET DEFAULT nextval('taggable_id_seq'::regclass);


--
-- TOC entry 1793 (class 2606 OID 16777)
-- Dependencies: 1495 1495
-- Name: genre_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY genre
    ADD CONSTRAINT genre_pkey PRIMARY KEY (id);


--
-- TOC entry 1795 (class 2606 OID 33431)
-- Dependencies: 1497 1497 1497
-- Name: interpretation_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY interpretation
    ADD CONSTRAINT interpretation_pkey PRIMARY KEY (work_id, from_segment);


--
-- TOC entry 1797 (class 2606 OID 16781)
-- Dependencies: 1498 1498
-- Name: log_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY log
    ADD CONSTRAINT log_pkey PRIMARY KEY (id);


--
-- TOC entry 1816 (class 2606 OID 25161)
-- Dependencies: 1509 1509
-- Name: privileges_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY privileges
    ADD CONSTRAINT privileges_pkey PRIMARY KEY (id);


--
-- TOC entry 1799 (class 2606 OID 16783)
-- Dependencies: 1500 1500
-- Name: role_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY role
    ADD CONSTRAINT role_pkey PRIMARY KEY (id);


--
-- TOC entry 1805 (class 2606 OID 16785)
-- Dependencies: 1504 1504 1504
-- Name: sentence_number_key; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY sentence
    ADD CONSTRAINT sentence_number_key UNIQUE (number, work_id);


--
-- TOC entry 1807 (class 2606 OID 16787)
-- Dependencies: 1504 1504
-- Name: sentence_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY sentence
    ADD CONSTRAINT sentence_pkey PRIMARY KEY (id);


--
-- TOC entry 1809 (class 2606 OID 33425)
-- Dependencies: 1505 1505 1505 1505
-- Name: tag_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_pkey PRIMARY KEY (taggable, "user", comment);


--
-- TOC entry 1801 (class 2606 OID 16959)
-- Dependencies: 1502 1502
-- Name: taggable_id_key; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY taggable
    ADD CONSTRAINT taggable_id_key UNIQUE (id);


--
-- TOC entry 1803 (class 2606 OID 16791)
-- Dependencies: 1502 1502
-- Name: taggable_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY taggable
    ADD CONSTRAINT taggable_pkey PRIMARY KEY (id);


--
-- TOC entry 1814 (class 2606 OID 16840)
-- Dependencies: 1507 1507
-- Name: user_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (name);


--
-- TOC entry 1812 (class 2606 OID 16797)
-- Dependencies: 1506 1506
-- Name: work_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY work
    ADD CONSTRAINT work_pkey PRIMARY KEY (id);


--
-- TOC entry 1810 (class 1259 OID 16851)
-- Dependencies: 1506
-- Name: fki_; Type: INDEX; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE INDEX fki_ ON work USING btree (creator);


--
-- TOC entry 1817 (class 2606 OID 16798)
-- Dependencies: 1497 1804 1504 1504 1497
-- Name: from_sentence; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY interpretation
    ADD CONSTRAINT from_sentence FOREIGN KEY (original_work_id, from_segment) REFERENCES sentence(work_id, number);


--
-- TOC entry 1818 (class 2606 OID 16803)
-- Dependencies: 1497 1506 1811
-- Name: interpretation_work_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY interpretation
    ADD CONSTRAINT interpretation_work_id_fkey FOREIGN KEY (work_id) REFERENCES work(id);


--
-- TOC entry 1825 (class 2606 OID 25162)
-- Dependencies: 1509 1506 1811
-- Name: privileges_work_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY privileges
    ADD CONSTRAINT privileges_work_id_fkey FOREIGN KEY (work_id) REFERENCES work(id);


--
-- TOC entry 1820 (class 2606 OID 16808)
-- Dependencies: 1504 1506 1811
-- Name: sentence_workid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY sentence
    ADD CONSTRAINT sentence_workid_fkey FOREIGN KEY (work_id) REFERENCES work(id);


--
-- TOC entry 1822 (class 2606 OID 16861)
-- Dependencies: 1792 1495 1505
-- Name: tag_genre_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_genre_fkey FOREIGN KEY (genre) REFERENCES genre(id);


--
-- TOC entry 1823 (class 2606 OID 16947)
-- Dependencies: 1506 1811 1505
-- Name: tag_taggable_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_taggable_fkey FOREIGN KEY (taggable) REFERENCES work(id);


--
-- TOC entry 1821 (class 2606 OID 16856)
-- Dependencies: 1505 1813 1507
-- Name: tag_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_user_fkey FOREIGN KEY ("user") REFERENCES "user"(name);


--
-- TOC entry 1819 (class 2606 OID 16813)
-- Dependencies: 1504 1804 1504 1497 1497
-- Name: to_sentence; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY interpretation
    ADD CONSTRAINT to_sentence FOREIGN KEY (original_work_id, to_segment) REFERENCES sentence(work_id, number);


--
-- TOC entry 1824 (class 2606 OID 16846)
-- Dependencies: 1506 1813 1507
-- Name: work_creator_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY work
    ADD CONSTRAINT work_creator_fkey FOREIGN KEY (creator) REFERENCES "user"(name);


--
-- TOC entry 1830 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2010-03-20 11:30:02 CET

--
-- PostgreSQL database dump complete
--

