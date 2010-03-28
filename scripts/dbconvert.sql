BEGIN;

INSERT INTO import.user (name, last_access)
SELECT name, last_login FROM public.users WHERE last_login IS NOT NULL
UNION
SELECT name, now() FROM public.users WHERE last_login IS NULL
;

INSERT INTO import.user (name,last_access) values('tdxioAnonymousUser',now());

UPDATE public.texts SET creator='tdxioAnonymousUser' WHERE public.texts.creator IS NULL;

INSERT INTO import."work" (id, title, author, "language", created, creator, visibility, modified)
SELECT id, title, author, "language", created, creator, visibility, modified FROM public.texts;

UPDATE import."work" SET author = public.authors.name FROM public.authors
	WHERE import."work".author = public.authors.id::character varying ;
	
--INSERT INTO import."work" (id, title, author, "language", created, creator, visibility, modified)
--	SELECT public.texts.id, title, public.authors."name", "language", public.texts.created, creator, visibility, public.texts.modified 	
--	FROM public.texts JOIN public.authors ON public.texts.author=public.authors.id


DELETE FROM public.unit_segments 
where public.unit_segments.text_id NOT IN 
(SELECT public.texts.id FROM public.texts);

INSERT INTO import.sentence ("number", "content", work_id)
SELECT segnum, segment, text_id FROM public.unit_segments ;
--WHERE public.unit_segments.text_id IN (SELECT translation_of FROM public.texts);


DELETE FROM public.translation_blocks 
where public.translation_blocks.translation_id NOT IN 
(SELECT public.texts.id FROM public.texts);


INSERT INTO import.interpretation (work_id,translation, from_segment, to_segment, original_work_id)
	SELECT translation_id, translation, from_segment, to_segment, translation_of
	FROM public.translation_blocks JOIN public.texts ON public.texts.id = public.translation_blocks.translation_id
	WHERE translation_of IN (SELECT public.texts.id FROM public.texts) AND translation_id IN (SELECT public.texts.id FROM public.texts);


DELETE FROM public.privileges 
where public.privileges.text_id NOT IN 
(SELECT public.texts.id FROM public.texts);

INSERT INTO import.privileges (id, privilege, user_id, work_id, created, visibility)
SELECT id, privilege, user_id, text_id, created, visibility FROM public.privileges;

--importa i tag relativi al genere release
INSERT INTO import.genre (name, created) values('release', now());

INSERT INTO import.tag (taggable, "user", created, "comment", genre)
	SELECT texts.id, creator, texts.created, "release", genre.id FROM public.texts, import.genre 
	WHERE public.texts.release IS NOT NULL AND genre.name='release'; 

--------------------------------------------------------------
INSERT INTO import.genre (name, created) values('book', now());


INSERT INTO import.tag (taggable, "user", created, "comment", genre)
	SELECT texts.id, creator, texts.created, "books".title, genre.id 
	FROM public.texts JOIN public.books ON book=books.id, import.genre 
	WHERE public.texts.book IS NOT NULL AND genre.name='book'; 


--------------------------------------------------------------
INSERT INTO import.genre (name, created) values('genre', now());


INSERT INTO import.tag (taggable, "user", created, "comment", genre)
	SELECT texts.id, creator, texts.created, "genres".name, genre.id 
	FROM public.texts JOIN public.genres ON genre=genres.id, import.genre 
	WHERE public.texts.genre IS NOT NULL AND genre.name='genre'; 



--------------------------------------------------------------
INSERT INTO import.genre (name, created) values('period', now());


INSERT INTO import.tag (taggable, "user", created, "comment", genre)
	SELECT texts.id, creator, texts.created, "periods".name, genre.id 
	FROM public.texts JOIN public.periods ON period=periods.id, import.genre 
	WHERE public.texts.period IS NOT NULL AND genre.name='period'; 

DELETE FROM import.tag WHERE "comment" = '';

--

ALTER SCHEMA public RENAME TO olddb;
ALTER SCHEMA import RENAME TO public;

COMMIT;