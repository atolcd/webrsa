----------- 29/03/2010 - 10h50 ----------------------

ALTER TABLE contratsinsertion ALTER COLUMN typocontrat_id DROP NOT NULL;
ALTER TABLE contratsinsertion ADD COLUMN numcontrat VARCHAR(15);


UPDATE contratsinsertion
    SET numcontrat = 'Premier contrat'
    WHERE rg_ci = '1';

UPDATE contratsinsertion
    SET numcontrat = 'Renouvellement'
    WHERE rg_ci <> '1';