-- change types
ALTER TABLE galette_fields_config ALTER COLUMN visible DROP DEFAULT;
ALTER TABLE galette_fields_config ALTER visible TYPE integer USING CASE WHEN visible='1' THEN 1 ELSE 0 END;
ALTER TABLE galette_fields_config ALTER COLUMN visible SET DEFAULT 0;