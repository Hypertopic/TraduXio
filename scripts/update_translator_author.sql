update work
set translator=author where id in (select work_id from interpretation);

update work set author=original_work.author from
interpretation 
join work as original_work on original_work.id=interpretation.original_work_id
where interpretation.work_id=work.id;
-- 
-- 
select distinct work.id, work.title, work.author, original_work_id, original_work.title, original_work.author 
from work join interpretation on interpretation.work_id=work.id
join work as original_work on original_work.id=interpretation.original_work_id
order by id 

-- select distinct work.id, work.title, work.author, original_work_id, original_work.title, original_work.author 
-- from temp_work as work join interpretation on interpretation.work_id=work.id
-- join work as original_work on original_work.id=interpretation.original_work_id
-- 
-- create table temp_work as (select * from work)







-- update work
-- set author = 
-- (select distinct author from (select author, work_id
-- from (interpretation as i join  work as w 
-- 	on i.original_work_id = w.id)
-- as atable
-- where atable.work_id = work.id
-- )
-- as ntable)
