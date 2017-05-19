--------------- Ajout du 14/09/2009 à 09h13 ------------------
ALTER TABLE orientsstructs ALTER COLUMN statutrelance SET DEFAULT 'E';
UPDATE orientsstructs SET statutrelance = 'E' WHERE statutrelance IS NULL;