
CREATE AGGREGATE concat(text) (
    SFUNC = textcat,
    STYPE = text,
    INITCOND = ''
);

-- View: interpretation_sentence

-- DROP VIEW interpretation_sentence;

CREATE OR REPLACE VIEW interpretation_sentence AS 
 SELECT interpretation.work_id, interpretation.original_work_id, interpretation.from_segment, interpretation.to_segment, concat(sentence_order.content::text) AS source, interpretation.translation
   FROM interpretation
   JOIN ( SELECT sentence.work_id, sentence.number, sentence.content
           FROM sentence
          ORDER BY sentence.work_id, sentence.number) sentence_order ON sentence_order.work_id = interpretation.original_work_id AND sentence_order.number >= interpretation.from_segment AND sentence_order.number <= interpretation.to_segment
  GROUP BY interpretation.work_id, interpretation.from_segment, interpretation.original_work_id, interpretation.to_segment, interpretation.translation
  ORDER BY interpretation.work_id, interpretation.from_segment;

ALTER TABLE interpretation_sentence OWNER TO postgres;

