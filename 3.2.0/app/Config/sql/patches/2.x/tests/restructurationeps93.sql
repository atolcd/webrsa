-- *****************************************************************************
BEGIN;
-- *****************************************************************************

UPDATE regroupementseps
	SET
		nonrespectsanctionep93 = 'decisioncg',
		reorientationep93 = 'decisioncg',
-- 		radiepoleemploiep93 = 'decisioncg',
		nonorientationproep93 = 'decisioncg',
		regressionorientationep93 = 'decisioncg';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************