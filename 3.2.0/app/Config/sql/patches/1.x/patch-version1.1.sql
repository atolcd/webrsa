----------- 20/01/2010 - 16h03 ----------------------
ALTER TABLE apres ADD COLUMN montantaverser NUMERIC(10,2);
ALTER TABLE apres_etatsliquidatifs ADD COLUMN montantattribue NUMERIC(10,2);
ALTER TABLE apres ADD COLUMN nbpaiementsouhait INTEGER;
ALTER TABLE apres ADD COLUMN montantdejaverse NUMERIC(10,2);
