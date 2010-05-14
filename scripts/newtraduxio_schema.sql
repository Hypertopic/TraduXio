--
-- PostgreSQL database dump
--

-- Started on 2010-05-14 20:07:52 CEST

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 367 (class 1255 OID 34713)
-- Dependencies: 7
-- Name: concat(text); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE concat(text) (
    SFUNC = textcat,
    STYPE = text,
    INITCOND = ''
);


ALTER AGGREGATE public.concat(text) OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1556 (class 1259 OID 34424)
-- Dependencies: 1844 7
-- Name: genre; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE genre (
    id integer NOT NULL,
    name character varying,
    created timestamp with time zone DEFAULT now()
);


ALTER TABLE public.genre OWNER TO traduxio;

--
-- TOC entry 1557 (class 1259 OID 34430)
-- Dependencies: 7 1556
-- Name: genre_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE genre_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.genre_id_seq OWNER TO traduxio;

--
-- TOC entry 1911 (class 0 OID 0)
-- Dependencies: 1557
-- Name: genre_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE genre_id_seq OWNED BY genre.id;


--
-- TOC entry 1575 (class 1259 OID 42995)
-- Dependencies: 1863 7
-- Name: history; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE history (
    id integer NOT NULL,
    "user" character varying NOT NULL,
    work_id integer NOT NULL,
    date timestamp with time zone DEFAULT now() NOT NULL,
    message character varying
);


ALTER TABLE public.history OWNER TO traduxio;

--
-- TOC entry 1574 (class 1259 OID 42993)
-- Dependencies: 1575 7
-- Name: history_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE history_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.history_id_seq OWNER TO traduxio;

--
-- TOC entry 1912 (class 0 OID 0)
-- Dependencies: 1574
-- Name: history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE history_id_seq OWNED BY history.id;


--
-- TOC entry 1558 (class 1259 OID 34432)
-- Dependencies: 1845 1846 1847 7
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
-- TOC entry 1565 (class 1259 OID 34468)
-- Dependencies: 7
-- Name: taggable; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE taggable (
    id integer NOT NULL
);


ALTER TABLE public.taggable OWNER TO traduxio;

--
-- TOC entry 1566 (class 1259 OID 34471)
-- Dependencies: 7 1565
-- Name: taggable_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE taggable_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.taggable_id_seq OWNER TO traduxio;

--
-- TOC entry 1913 (class 0 OID 0)
-- Dependencies: 1566
-- Name: taggable_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE taggable_id_seq OWNED BY taggable.id;


--
-- TOC entry 1567 (class 1259 OID 34473)
-- Dependencies: 1855 7 1565
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
-- TOC entry 1571 (class 1259 OID 34720)
-- Dependencies: 1651 7
-- Name: interpretation_sentence; Type: VIEW; Schema: public; Owner: traduxio
--

CREATE VIEW interpretation_sentence AS
    SELECT interpretation.work_id, interpretation.original_work_id, interpretation.from_segment, interpretation.to_segment, concat((sentence_order.content)::text) AS source, interpretation.translation FROM (interpretation JOIN (SELECT sentence.work_id, sentence.number, sentence.content FROM sentence ORDER BY sentence.work_id, sentence.number) sentence_order ON ((((sentence_order.work_id = interpretation.original_work_id) AND (sentence_order.number >= interpretation.from_segment)) AND (sentence_order.number <= interpretation.to_segment)))) GROUP BY interpretation.work_id, interpretation.from_segment, interpretation.original_work_id, interpretation.to_segment, interpretation.translation ORDER BY interpretation.work_id, interpretation.from_segment;


ALTER TABLE public.interpretation_sentence OWNER TO traduxio;

--
-- TOC entry 1572 (class 1259 OID 34754)
-- Dependencies: 1860 1861 7
-- Name: languages; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE languages (
    id character(3) NOT NULL,
    part2b character(3),
    part1 character(2),
    scope character(1) NOT NULL,
    type character(1) NOT NULL,
    ref_name character varying(150) NOT NULL,
    rtl boolean DEFAULT false,
    active boolean DEFAULT false
);


ALTER TABLE public.languages OWNER TO traduxio;

--
-- TOC entry 1559 (class 1259 OID 34441)
-- Dependencies: 7
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
-- TOC entry 1560 (class 1259 OID 34447)
-- Dependencies: 7 1559
-- Name: log_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE log_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.log_id_seq OWNER TO traduxio;

--
-- TOC entry 1914 (class 0 OID 0)
-- Dependencies: 1560
-- Name: log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE log_id_seq OWNED BY log.id;


--
-- TOC entry 1573 (class 1259 OID 34791)
-- Dependencies: 7
-- Name: privilege_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE privilege_id_seq
    START WITH 59
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.privilege_id_seq OWNER TO traduxio;

--
-- TOC entry 1561 (class 1259 OID 34449)
-- Dependencies: 1849 7
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
-- TOC entry 1562 (class 1259 OID 34456)
-- Dependencies: 7 1561
-- Name: privileges_id_seq; Type: SEQUENCE; Schema: public; Owner: traduxio
--

CREATE SEQUENCE privileges_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.privileges_id_seq OWNER TO traduxio;

--
-- TOC entry 1915 (class 0 OID 0)
-- Dependencies: 1562
-- Name: privileges_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE privileges_id_seq OWNED BY privileges.id;


--
-- TOC entry 1563 (class 1259 OID 34458)
-- Dependencies: 1851 1852 7
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
-- TOC entry 1564 (class 1259 OID 34466)
-- Dependencies: 7 1563
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
-- TOC entry 1916 (class 0 OID 0)
-- Dependencies: 1564
-- Name: role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: traduxio
--

ALTER SEQUENCE role_id_seq OWNED BY role.id;


--
-- TOC entry 1568 (class 1259 OID 34479)
-- Dependencies: 1856 7
-- Name: tag; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE tag (
    taggable integer NOT NULL,
    genre integer NOT NULL,
    "user" character varying NOT NULL,
    comment character varying NOT NULL,
    created timestamp with time zone NOT NULL,
    modified timestamp with time zone DEFAULT now()
);


ALTER TABLE public.tag OWNER TO traduxio;

--
-- TOC entry 1569 (class 1259 OID 34486)
-- Dependencies: 1857 7
-- Name: user; Type: TABLE; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE TABLE "user" (
    name character varying NOT NULL,
    last_access timestamp with time zone DEFAULT now() NOT NULL,
    options text
);


ALTER TABLE public."user" OWNER TO traduxio;

--
-- TOC entry 1570 (class 1259 OID 34493)
-- Dependencies: 1858 1859 7 1565
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
-- TOC entry 1843 (class 2604 OID 34500)
-- Dependencies: 1557 1556
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE genre ALTER COLUMN id SET DEFAULT nextval('genre_id_seq'::regclass);


--
-- TOC entry 1862 (class 2604 OID 42998)
-- Dependencies: 1575 1574 1575
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE history ALTER COLUMN id SET DEFAULT nextval('history_id_seq'::regclass);


--
-- TOC entry 1848 (class 2604 OID 34501)
-- Dependencies: 1560 1559
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE log ALTER COLUMN id SET DEFAULT nextval('log_id_seq'::regclass);


--
-- TOC entry 1850 (class 2604 OID 34502)
-- Dependencies: 1562 1561
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE privileges ALTER COLUMN id SET DEFAULT nextval('privileges_id_seq'::regclass);


--
-- TOC entry 1853 (class 2604 OID 34503)
-- Dependencies: 1564 1563
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE role ALTER COLUMN id SET DEFAULT nextval('role_id_seq'::regclass);


--
-- TOC entry 1854 (class 2604 OID 34504)
-- Dependencies: 1566 1565
-- Name: id; Type: DEFAULT; Schema: public; Owner: traduxio
--

ALTER TABLE taggable ALTER COLUMN id SET DEFAULT nextval('taggable_id_seq'::regclass);


--
-- TOC entry 1865 (class 2606 OID 34508)
-- Dependencies: 1556 1556
-- Name: genre_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY genre
    ADD CONSTRAINT genre_pkey PRIMARY KEY (id);


--
-- TOC entry 1896 (class 2606 OID 43004)
-- Dependencies: 1575 1575
-- Name: history_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY history
    ADD CONSTRAINT history_pkey PRIMARY KEY (id);


--
-- TOC entry 1867 (class 2606 OID 34510)
-- Dependencies: 1558 1558 1558
-- Name: interpretation_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY interpretation
    ADD CONSTRAINT interpretation_pkey PRIMARY KEY (work_id, from_segment);


--
-- TOC entry 1894 (class 2606 OID 34759)
-- Dependencies: 1572 1572
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- TOC entry 1871 (class 2606 OID 34512)
-- Dependencies: 1559 1559
-- Name: log_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY log
    ADD CONSTRAINT log_pkey PRIMARY KEY (id);


--
-- TOC entry 1873 (class 2606 OID 34514)
-- Dependencies: 1561 1561
-- Name: privileges_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY privileges
    ADD CONSTRAINT privileges_pkey PRIMARY KEY (id);


--
-- TOC entry 1875 (class 2606 OID 34516)
-- Dependencies: 1563 1563
-- Name: role_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY role
    ADD CONSTRAINT role_pkey PRIMARY KEY (id);


--
-- TOC entry 1882 (class 2606 OID 34518)
-- Dependencies: 1567 1567 1567
-- Name: sentence_number_key; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY sentence
    ADD CONSTRAINT sentence_number_key UNIQUE (number, work_id);


--
-- TOC entry 1884 (class 2606 OID 34520)
-- Dependencies: 1567 1567
-- Name: sentence_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY sentence
    ADD CONSTRAINT sentence_pkey PRIMARY KEY (id);


--
-- TOC entry 1887 (class 2606 OID 34695)
-- Dependencies: 1568 1568 1568 1568 1568
-- Name: tag_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_pkey PRIMARY KEY (taggable, genre, "user", comment);


--
-- TOC entry 1877 (class 2606 OID 34524)
-- Dependencies: 1565 1565
-- Name: taggable_id_key; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY taggable
    ADD CONSTRAINT taggable_id_key UNIQUE (id);


--
-- TOC entry 1879 (class 2606 OID 34526)
-- Dependencies: 1565 1565
-- Name: taggable_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY taggable
    ADD CONSTRAINT taggable_pkey PRIMARY KEY (id);


--
-- TOC entry 1889 (class 2606 OID 34528)
-- Dependencies: 1569 1569
-- Name: user_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (name);


--
-- TOC entry 1892 (class 2606 OID 34530)
-- Dependencies: 1570 1570
-- Name: work_pkey; Type: CONSTRAINT; Schema: public; Owner: traduxio; Tablespace: 
--

ALTER TABLE ONLY work
    ADD CONSTRAINT work_pkey PRIMARY KEY (id);


--
-- TOC entry 1890 (class 1259 OID 34531)
-- Dependencies: 1570
-- Name: fki_; Type: INDEX; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE INDEX fki_ ON work USING btree (creator);


--
-- TOC entry 1868 (class 1259 OID 34711)
-- Dependencies: 1558 1558 1558
-- Name: interpretation_work_from_to_idx; Type: INDEX; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE INDEX interpretation_work_from_to_idx ON interpretation USING btree (work_id, from_segment, to_segment);


--
-- TOC entry 1869 (class 1259 OID 34712)
-- Dependencies: 1558
-- Name: interpretation_work_id_idx; Type: INDEX; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE INDEX interpretation_work_id_idx ON interpretation USING btree (work_id);


--
-- TOC entry 1880 (class 1259 OID 34718)
-- Dependencies: 1567 1207
-- Name: pgweb_idx; Type: INDEX; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE INDEX pgweb_idx ON sentence USING gin (to_tsvector('english'::regconfig, (content)::text));


--
-- TOC entry 1885 (class 1259 OID 34719)
-- Dependencies: 1567 1567
-- Name: sentence_work_number_idx; Type: INDEX; Schema: public; Owner: traduxio; Tablespace: 
--

CREATE INDEX sentence_work_number_idx ON sentence USING btree (work_id, number);


--
-- TOC entry 1897 (class 2606 OID 34532)
-- Dependencies: 1558 1881 1567 1567 1558
-- Name: from_sentence; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY interpretation
    ADD CONSTRAINT from_sentence FOREIGN KEY (original_work_id, from_segment) REFERENCES sentence(work_id, number);


--
-- TOC entry 1906 (class 2606 OID 43005)
-- Dependencies: 1570 1575 1891
-- Name: history_work_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY history
    ADD CONSTRAINT history_work_id_fkey FOREIGN KEY (work_id) REFERENCES work(id);


--
-- TOC entry 1898 (class 2606 OID 34537)
-- Dependencies: 1891 1570 1558
-- Name: interpretation_work_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY interpretation
    ADD CONSTRAINT interpretation_work_id_fkey FOREIGN KEY (work_id) REFERENCES work(id);


--
-- TOC entry 1900 (class 2606 OID 34542)
-- Dependencies: 1570 1561 1891
-- Name: privileges_work_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY privileges
    ADD CONSTRAINT privileges_work_id_fkey FOREIGN KEY (work_id) REFERENCES work(id);


--
-- TOC entry 1901 (class 2606 OID 34547)
-- Dependencies: 1570 1891 1567
-- Name: sentence_workid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY sentence
    ADD CONSTRAINT sentence_workid_fkey FOREIGN KEY (work_id) REFERENCES work(id);


--
-- TOC entry 1902 (class 2606 OID 34552)
-- Dependencies: 1556 1568 1864
-- Name: tag_genre_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_genre_fkey FOREIGN KEY (genre) REFERENCES genre(id);


--
-- TOC entry 1903 (class 2606 OID 34557)
-- Dependencies: 1570 1568 1891
-- Name: tag_taggable_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_taggable_fkey FOREIGN KEY (taggable) REFERENCES work(id);


--
-- TOC entry 1904 (class 2606 OID 34562)
-- Dependencies: 1568 1888 1569
-- Name: tag_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY tag
    ADD CONSTRAINT tag_user_fkey FOREIGN KEY ("user") REFERENCES "user"(name);


--
-- TOC entry 1899 (class 2606 OID 34567)
-- Dependencies: 1881 1558 1567 1558 1567
-- Name: to_sentence; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY interpretation
    ADD CONSTRAINT to_sentence FOREIGN KEY (original_work_id, to_segment) REFERENCES sentence(work_id, number);


--
-- TOC entry 1905 (class 2606 OID 34572)
-- Dependencies: 1569 1570 1888
-- Name: work_creator_fkey; Type: FK CONSTRAINT; Schema: public; Owner: traduxio
--

ALTER TABLE ONLY work
    ADD CONSTRAINT work_creator_fkey FOREIGN KEY (creator) REFERENCES "user"(name);


--
-- TOC entry 1910 (class 0 OID 0)
-- Dependencies: 7
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2010-05-14 20:07:57 CEST

--
-- PostgreSQL database dump complete
--

