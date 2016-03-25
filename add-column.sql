BEGIN; 
set schema 'artifacts';
LOCK TABLE item IN SHARE MODE;
set temp_buffers = 5120;
SET LOCAL work_mem = 3072;
create temp table item_tmp (like item);
insert into item_tmp select * from item;
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_index1";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_index2";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_index3";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_is_on_homepage_add_timestamp";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_is_on_homepage_idx";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_item_author_account_id_idx";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_itemIdx";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_itemIdx1";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_search_data_gin_idx";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_tags_gin_idx";
DROP INDEX IF EXISTS "artifacts"."artifacts_item_master_thumbs_list";
truncate item;
ALTER TABLE item ADD COLUMN "is_anonymous" Boolean NOT NULL default false;
ALTER TABLE item ADD COLUMN "elements_count" INTEGER NOT NULL default 0;
INSERT INTO item_p_1 SELECT * FROM item_tmp WHERE id >= 1 AND id < 1000000;
INSERT INTO item_p_2 SELECT * FROM item_tmp WHERE id >= 1000000 AND id < 2000000;
INSERT INTO item_p_3 SELECT * FROM item_tmp WHERE id >= 2000000 AND id < 3000000;
INSERT INTO item_p_4 SELECT * FROM item_tmp WHERE id >= 3000000 AND id < 4000000;
INSERT INTO item_p_5 SELECT * FROM item_tmp WHERE id >= 4000000 AND id < 5000000;
INSERT INTO item_p_6 SELECT * FROM item_tmp WHERE id >= 5000000 AND id < 6000000;
INSERT INTO item_p_7 SELECT * FROM item_tmp WHERE id >= 6000000 AND id < 7000000;
INSERT INTO item_p_8 SELECT * FROM item_tmp WHERE id >= 7000000 AND id < 8000000;
INSERT INTO item_p_9 SELECT * FROM item_tmp WHERE id >= 8000000 AND id < 9000000;
INSERT INTO item_p_10 SELECT * FROM item_tmp WHERE id >= 9000000 AND id < 10000000;
INSERT INTO item_p_11 SELECT * FROM item_tmp WHERE id >= 10000000 AND id < 11000000;
INSERT INTO item_p_12 SELECT * FROM item_tmp WHERE id >= 11000000 AND id < 12000000;
INSERT INTO item_p_13 SELECT * FROM item_tmp WHERE id >= 12000000 AND id < 13000000;
INSERT INTO item_p_14 SELECT * FROM item_tmp WHERE id >= 13000000 AND id < 14000000;
INSERT INTO item_p_15 SELECT * FROM item_tmp WHERE id >= 14000000 AND id < 15000000;
INSERT INTO item_p_16 SELECT * FROM item_tmp WHERE id >= 15000000 AND id < 16000000;
CREATE INDEX "artifacts_item_master_index1" ON "artifacts"."item" USING btree( "add_timestamp" Asc NULLS Last );
CREATE INDEX "artifacts_item_master_index2" ON "artifacts"."item" USING btree( "id" Asc NULLS Last );
CREATE INDEX "artifacts_item_master_index3" ON "artifacts"."item" USING btree( "add_timestamp" Desc NULLS Last );
CREATE INDEX "artifacts_item_master_is_on_homepage_add_timestamp" ON "artifacts"."item" USING btree( "is_on_homepage" Desc NULLS First, "add_timestamp" Desc NULLS Last );
CREATE INDEX "artifacts_item_master_is_on_homepage_idx" ON "artifacts"."item" USING btree( "is_on_homepage" Asc NULLS Last );
CREATE INDEX "artifacts_item_master_item_author_account_id_idx" ON "artifacts"."item" USING btree( "author_account_id" Asc NULLS Last );
CREATE INDEX "artifacts_item_master_itemIdx" ON "artifacts"."item" USING btree( "is_public" Asc NULLS Last, "is_removed" Asc NULLS Last, "is_age_restricted" Asc NULLS Last );
CREATE INDEX "artifacts_item_master_itemIdx1" ON "artifacts"."item" USING btree( "is_imported" Asc NULLS Last );
CREATE INDEX "artifacts_item_master_search_data_gin_idx" ON "artifacts"."item" USING gin( "search_data" );
CREATE INDEX "artifacts_item_master_tags_gin_idx" ON "artifacts"."item" USING gin( "tags" );
CREATE INDEX "artifacts_item_master_thumbs_list" ON "artifacts"."item" USING btree( "is_public" Asc NULLS Last, "is_removed" Asc NULLS Last, "is_imported" Asc NULLS Last, "id" Desc NULLS First );
REINDEX TABLE item_p_1; 
REINDEX TABLE item_p_2; 
REINDEX TABLE item_p_3; 
REINDEX TABLE item_p_4; 
REINDEX TABLE item_p_5; 
REINDEX TABLE item_p_6; 
REINDEX TABLE item_p_7; 
REINDEX TABLE item_p_8; 
REINDEX TABLE item_p_9; 
REINDEX TABLE item_p_10; 
REINDEX TABLE item_p_11; 
REINDEX TABLE item_p_12; 
REINDEX TABLE item_p_13; 
REINDEX TABLE item_p_14; 
REINDEX TABLE item_p_15; 
REINDEX TABLE item_p_16; 
REINDEX TABLE item_p_17; 
REINDEX TABLE item_p_18; 
REINDEX TABLE item_p_19; 
REINDEX TABLE item; 

! service down

COMMIT;

! service up

drop table item_tmp;