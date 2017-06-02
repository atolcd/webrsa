DELETE FROM structuresreferentes
    WHERE typeorient_id IN ( SELECT id FROM typesorients WHERE typesorients.parentid IS NOT NULL );

DELETE FROM typesorients WHERE typesorients.parentid IS NOT NULL;