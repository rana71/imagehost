CREATE TABLE artifacts.item_new ( like artifacts.item )


alter table artifacts.item_new OWNER TO imgjetrc


select (min(id)/1000000+1), (max(id)/1000000+1)+1 from artifacts.item; # +1 for 1 table in advance


CREATE TABLE artifacts.item_p_1 (CONSTRAINT item_p_1_id_pk PRIMARY KEY (id), CONSTRAINT item_p_1_id_chk CHECK ( id >= 1 AND id < 1000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_2 (CONSTRAINT item_p_2_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 1000000 AND id < 2000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_3 (CONSTRAINT item_p_3_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 2000000 AND id < 3000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_4 (CONSTRAINT item_p_4_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 3000000 AND id < 4000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_5 (CONSTRAINT item_p_5_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 4000000 AND id < 5000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_6 (CONSTRAINT item_p_6_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 5000000 AND id < 6000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_7 (CONSTRAINT item_p_7_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 6000000 AND id < 7000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_8 (CONSTRAINT item_p_8_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 7000000 AND id < 8000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_9 (CONSTRAINT item_p_9_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 8000000 AND id < 9000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_10 (CONSTRAINT item_p_10_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 9000000 AND id < 10000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_11 (CONSTRAINT item_p_11_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 10000000 AND id < 11000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_12 (CONSTRAINT item_p_12_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 11000000 AND id < 12000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_13 (CONSTRAINT item_p_13_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 12000000 AND id < 13000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_14 (CONSTRAINT item_p_14_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 13000000 AND id < 14000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_15 (CONSTRAINT item_p_15_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 14000000 AND id < 15000000 ) ) INHERITS (artifacts.item_new);
CREATE TABLE artifacts.item_p_16 (CONSTRAINT item_p_16_id_pk PRIMARY KEY (id), CONSTRAINT item_p_2_id_chk CHECK ( id >= 15000000 AND id < 16000000 ) ) INHERITS (artifacts.item_new);

# wybrac sql create dla indexow na .item i zmienic nazwy na _master

# pamiętać o wyłączeniu uploadu pod koniec i odczekaniu 15 min w razie jakby ktos wlasnie cos dodawał !!!!!

INSERT INTO artifacts.item_p_1 SELECT * FROM artifacts.item WHERE id >= 1 AND id < 1000000;
INSERT INTO artifacts.item_p_2 SELECT * FROM artifacts.item WHERE id >= 1000000 AND id < 2000000;
INSERT INTO artifacts.item_p_3 SELECT * FROM artifacts.item WHERE id >= 2000000 AND id < 3000000;
INSERT INTO artifacts.item_p_4 SELECT * FROM artifacts.item WHERE id >= 3000000 AND id < 4000000;
INSERT INTO artifacts.item_p_5 SELECT * FROM artifacts.item WHERE id >= 4000000 AND id < 5000000;
INSERT INTO artifacts.item_p_6 SELECT * FROM artifacts.item WHERE id >= 5000000 AND id < 6000000;
INSERT INTO artifacts.item_p_7 SELECT * FROM artifacts.item WHERE id >= 6000000 AND id < 7000000;
INSERT INTO artifacts.item_p_8 SELECT * FROM artifacts.item WHERE id >= 7000000 AND id < 8000000;
INSERT INTO artifacts.item_p_9 SELECT * FROM artifacts.item WHERE id >= 8000000 AND id < 9000000;
INSERT INTO artifacts.item_p_10 SELECT * FROM artifacts.item WHERE id >= 9000000 AND id < 10000000;
INSERT INTO artifacts.item_p_11 SELECT * FROM artifacts.item WHERE id >= 10000000 AND id < 11000000;
INSERT INTO artifacts.item_p_12 SELECT * FROM artifacts.item WHERE id >= 11000000 AND id < 12000000;
INSERT INTO artifacts.item_p_13 SELECT * FROM artifacts.item WHERE id >= 12000000 AND id < 13000000;
INSERT INTO artifacts.item_p_14 SELECT * FROM artifacts.item WHERE id >= 13000000 AND id < 14000000;
INSERT INTO artifacts.item_p_15 SELECT * FROM artifacts.item WHERE id >= 14000000 AND id < 15000000;
INSERT INTO artifacts.item_p_16 SELECT * FROM artifacts.item WHERE id >= 15000000 AND id < 16000000;

alter table artifacts.item_p_1 OWNER TO imgjetrc;
alter table artifacts.item_p_2 OWNER TO imgjetrc;
alter table artifacts.item_p_3 OWNER TO imgjetrc;
alter table artifacts.item_p_4 OWNER TO imgjetrc;
alter table artifacts.item_p_5 OWNER TO imgjetrc;
alter table artifacts.item_p_6 OWNER TO imgjetrc;
alter table artifacts.item_p_7 OWNER TO imgjetrc;
alter table artifacts.item_p_8 OWNER TO imgjetrc;
alter table artifacts.item_p_9 OWNER TO imgjetrc;
alter table artifacts.item_p_10 OWNER TO imgjetrc;
alter table artifacts.item_p_11 OWNER TO imgjetrc;
alter table artifacts.item_p_12 OWNER TO imgjetrc;
alter table artifacts.item_p_13 OWNER TO imgjetrc;
alter table artifacts.item_p_14 OWNER TO imgjetrc;
alter table artifacts.item_p_15 OWNER TO imgjetrc;
alter table artifacts.item_p_16 OWNER TO imgjetrc;



ALTER TABLE artifacts.item RENAME TO item_old


ALTER TABLE artifacts.item_new RENAME TO item

# utworzyć indeksy
# (...)

# zrestartowac sekwencje

CREATE OR REPLACE FUNCTION artifacts.insert_item_to_master()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
    DECLARE 
       partition_name VARCHAR(25);
    BEGIN
        partition_name := 'artifacts.' || TG_TABLE_NAME || '_p_' || (NEW.id/1000000+1);
        RAISE NOTICE 'check partition %', partition_name;
        EXECUTE format ('CREATE TABLE IF NOT EXISTS %I () INHERITS (artifacts.item)', partition_name);
        EXECUTE 'INSERT INTO ' || partition_name || ' SELECT(artifacts.' || TG_TABLE_NAME || ' ' || quote_literal(NEW) || ').*;';
        RETURN NULL;
    END;
$function$


CREATE TRIGGER insert_item_to_master_trigger BEFORE INSERT ON artifacts.item FOR EACH ROW EXECUTE PROCEDURE artifacts.insert_item_to_master()


SET constraint_exclusion = on

