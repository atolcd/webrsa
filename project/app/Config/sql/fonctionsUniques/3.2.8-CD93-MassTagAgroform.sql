Create or replace function createTAG() RETURNS VOID as
$$
DECLARE 
	-- ID de la valeur du tag à affacté trouvable dans la table valeurstags.id 
	IDValTag INTEGER := 2;
	-- Liste des ID 
	IDATraite INTEGER[] := '{
693880,1344773,978025,351925,443824,1361699,1361700,508715,401472,383591,
254413,182939,830446,371229,264539,201592,370179,565242,565243,267380,233226,945071,
211755,688250,640494,385175,368202,245190,245191,650020,650019,546582,345344,738905,292173,
1149713,569500,569501,261179,334350,485164,584441,362732,1219041,362860,1093711,395892,
1241676,347389,243973,243974,665637,365101,221070,313254,697946,697947,305281,305282,295377,
295378,263096,192369,326743,398686,1372298,1326131,285331,245881,509945,523403,191239,581431,
581432,227376,649350,649349,545233,211562,532598,369452,272997,269624,269625,522235,278827,
271774,271775,294073,185634,527975,1322380,522205,718896,354675,362939,352981,311454,555933,
647678,410684,411921,346826,346825,1365164,257647,200992,1218664,901348,402304,837228,374668,
272923,259454,209784,551119,313870,363820,482737,224611,679856,264298,561214,411735,695593,524214,
624709,267780,302949,302948,341436,341435,640776,1167985,225118,522228,551436,720827,617421,1213569,
303995,698381,617422,1291211,847075,288583,1181905,176994,566337,566339,399576,347481,242490,221936,
245250,1330350,177706,269928,669649,417785,274790,264471,630391,244047,296942,366303,288205,288206,
556577,728410,361383,209443,566886,970477,605277,400822,282397,1112572,247381,524273,353802,447143,
293396,1322034,607535,206158,569487,1230738,387156,375643,191237,316090,715218,741461,629406,314005,
718518,533538,326615,407036,373636,195021,244169,273480,273483,214333,233370,927903,1119971,775534,
355268,318007,196403,528564,368855,1209757,903110,377158,245300,245301,421532,1110190,461063,461064,
1045304,491241,369526,551975,337476,629494,594283,226456,357958,684535,550904,199802,318061,501237,
487821,200009,196375,620727,370013,1167849,806722,356482,365516,1161497,649379,183342,183341,658349,
340915,335924,592131,483730,791688,410017,768554,280424,1124755,532253,375034,626980,644130,1322385,
1028930,693565,360091,221254,1148570,654481,196624,226567,686948,356823,217395,679384,604122,909752,
520577,457490,427143,624477,1307228,323076,200976,662377,193437,292048,612450,291073,995158,351416,
346497,346496,189935,317844,270822,624884,504084,584150,283793,271677,752050,754886,966458,294823,
294775,229892,241275,176400,411772,522232,328077,252342,252343,281697,662943,186847,605155,633963,
217720,217721,581535,1243290,678045,179044,628792,385754,209129,374383,1337859,396493,1081697,714797,
528558,1077821,560253,209551,174549,942144,330598,185849,288540,220585,362462,528650,317754,577529,209316,
182101,548306,747720,304747,515078,327630,736542,187850,264872,475823,714470,248170,348623,500347,565565,
326210,595063,320563,226280,190009,398984,175514,524212,652751,342841,706429,942139,711193,291786,329453,
214572,567779,1267163,577649,523292,178420,178419,1330631,622655,622654,384338,1237646,1237645,374837,
1081216,698146,987562,289342,279736,1173616,228473,203536,566297,463205,618684,1310673,344285,344286,
178980,361055,195013,221807,300351,540925,540927,285012,670089,617460,456383,1093909,1093910,1018627,
186812,186813,720935,1381126,568226,759568,248028,555746,487439,604373,604374,340448,346744,798413,
602487,602484,255873,365189,200649,226628,405630,257991,900361,1276137,287250,359490,231306,231305,
535755,894968,1019044,218894,207255,380942,391262,391263,243915,315934,201827,377156,229661,406360,
667724,667725,701189,790007,248154,1310055,244865,1261538,973518,699053,199385,199384,203023,611524,
382318,385122,603090,397416,386662,366540,214176,1295337,980898,1248027,243398,183231,402631,309196,
380906,849787,568153,1167425,306848,662678,305860,379323,928328,319208,521978,192710,345034,224780,
220278,377558,1050800,270665,374384,250009,692931,607562,193171,411559,307873,245015,291601,331889,
286252,627187,505497,396572,566167,757798,499736,1131829,759005,714597,372841,206802,479934,283894,
208457,208456,504564,595205,268071,758008,404651,314736,462975,582040,268022,690637,207192,227973,
377020,212680,182912,395367,759737,245374,330919,714812,616079,727048,476252,240959,941021,1339015,
1344245,256183,1292539,336182,344612,265333,265332,375288,353633,704030,768291,241137,398876,710561,
665258,374767,282182,1136939,492926,296649,266781,573206,369527,299819,218508,271719,873808,236142,
326704,325792,239161,1167331,1065821,344569,566529,361452,342038,197639,367332,1349770,317259,290469,
691054,302936,282435,399481,282434,303603,490279,768052,860177,860178,208717,282250,510284,509030,
1014413,660644,645094,195700,362773,688011,1093342,1237336,1255826,375865,343522,1214149,187803,
244521,244522,242965,549082,242966,266109,400475,372821,189186,736939,736940,241491,797976,434278,
318489,430706,310640,195986,195987,670701,1286538,222496,338547,338546,483310,266666,308302,359268,
614621,1376656,334861,391498,202795,608973,360236,181508,567553,351600,759796,389954,259680,255672,
255671,670702,190495,232454,235404,353274,454872,454873,702965,249076,310278,630977,538560,538561,
1315118,269760,175513,633101,287376,1120249,337930,271618,1323613,342397,262680,240659,690552,703141,
193184,285426,616800,377096,239249,218360,329273,599144,328092,328093,284157,284158,285423,390246,
390247,1210429,342249,350753,339006,487515,181912,235262,366559,298681,927134,175370,301219,743154,
367865,199438,221428,522201,326909,178983,363148,207977,352574,756680,503226,756679,1127191,749562,
661721,561270,1296421,248377,314023,859415,302863,398236,546822,980550,241782,244524,244950,652287,
348670,678881,368655,257287,858048,627796,287816,352123,329263,378682,895666,563899,367866,372864,
262044,344658,226215,839911,274241,1282330,393780,1294616,194026,523701,269921,647702,262321,330762,
557776,679979,555775,270039,997534,288260,632098,193040,390423,193438,304453,240260,728123,815828,
524931,385321,873308,182137,182136,670197,641435,744275,1361331,284442,618923,618924,242066,268075,
348352,354653,282422,313454,335698,642502,1310571,343262,225429,600750,526478,359078,374879,327858,
354565,523969,177098,385926,224422,247569,292819,486183,314191,283395,283394,366128,192933,329115,
727538,189893,192741,608151,237289,291198,218945,202196,348827,1338416,272369,657435,536059,612247,
249298,300379,175468,666088,223149,369685,1139775,362829,987571,1265185,252301,895060,218628,277219,
212838,190434,754386,385655,559236,522770,913384,412109,675191,628055,666183,839461,184304,409602,
291576,741453,1288630,362727,457762,206362,539515,401190,333644,212906,593261,251521,315374,571679,
964629,352122,321492,407493,519124,609116,345379,386429,282738,206137,396576,213693,585004,822413,
388798,408143,384111,314585,283192,704526,679316,679315,1201652,483527,563888,563889,1316277,247077,
637178,637179,176244,1333872,984025,241716,1111906,304354,636162,227691,331160,397907,550470,303744,
636134,189851,262795,262486,175352,175351,202281,263456,658072,314826,1342880,408965,408964,669038,
190407,531605,250297,298500,661734,231880,711710,364120,364121,201400,788547,266949,756159,782428,
184651,338894,346214,1361684,204059,404946,322583,748485,678873,535202,317221,367093,399977,203383,
405968,546722,609138,254060,227454,322042,273087,240225,193874,207139,215676,203264,292113,712585,
311154,351985,361300,1334180,291233,352471,360397,331456,375698,499478,538622,210449,1082563,193207,
193206,227469,264445,264446,356583,1228943,1313733,526975,310068,178395,594361,373052,548357,612719,
791128,976437,317077,317078,978751,712949,216802,338925,698034,266898,523892,379618,1348465,1201223,
1331025,174775,207753,657872,249099,677279,212152,1010979,500118,622222,291100,291101,624439,508116,
689664,1159060,401043,192694,628416,455765,378972,612599,254245,651543,518541,571721,625537,276319,
251658,410037,404389,455691,264381,311048,290963,291791,363219,616309,353233,532354,604303,182018,
683602,644370,352083,666592,308970,210518,302504,208711,233875,839428,214977,1149877,635297,580280,
221578,264501,458804,217178,194320,362937,266468,274643,221506,303405,526424,195038,1361325,181355,
216277,216274,325945,412990,521879,850697,307531,256712,877199,183154,377766,864932,289952,538698,
327596,648049,775807,500783,383004,808713,527127,505550,355444,354105,643125,213368,566291,668427,
559405,223858,301749,223859,250155,330723,193153,609252,592645,663515,290924,311749,199978,350454,
210996,876999,399471,298719,195960,195999,233782,655475,539582,433968,629144,466309,282801,1092034,
711564,205107,743460,201276,302479,1337823,409167,379274,371310,327398,850168,240480,959883,249210,
249000,243187,265406,600533,1379506,386770,376550,179355,629203,629202,1212378,617537,278022,349807,
277855,501352,182430,451863,386404,297206,725577,242589,564299,265236,312558,391288,614070,435200,
344333,408886,674636,327550,363559,363558,183715,191196,183712,450330,1309998,520829,661969,178856,
353083,641793,284538,284541,617304,1093094,1005829,1012130,178346,293915,524144,347387,367334,208189,
389847,822033,1109316,385889,385890,191836,382560,541354,466153,961683,365550,370383,362087,640817,
1349041,315603,266172,615043,615044,282427,609325,543011,523667,302879,273391,183979,1239090,394802,
296442,309743,1315332,196592,340208,401981,401982,266142,558144,212832,292041,220254,208088,244944,
522037,637890,197030,902775,394394,278912,364687,364688,463027,383915,557392,275754,306312,652176,
345368,474082,474083,749528,270814,654727,270813,387922,592064,766314,399517,616917,237590,670471,
593227,329002,659516,192722,1082633,366706,398717,256569,256568,250333,287610,1260063,321635,532400,
254851,384179,208893,705866,523079,368420,316729,1342461,1340601,1254475,376827,328488,1075915,
1227819,1139516,192458,278521,304053,1041681,222617,448925,305488,522666,791598,859186,287706,356827,
252302,179932,622858,568963,206152,822013,719482,301987,247624,352161,292646,292647,197927,481519,
490725,361784,332653,266324,254873,201627,294002,275734,372600,341982,263251,251973,673861,434241,
196263,376445,265183,563231,409111,366092,401357,367778,677418,510134,262791,491178,695749,394712,
294988,328748,360074,328747,287899,237738,558598,509312,508056,231645,469370,613455,280311,295374,
390647,850846,278757,200380,217854,193275,222258,775354,216993,736554,218829,717294,179210,463415,
1050259,298550,264559,1006442,285971,252266,258955,264236,289499,453425,606657,554370,943175,671066,
678933,504541,307657,632349,427044,354080,354081,309696,569285,569286,251938,522444,345161,345162,
202127,323257,328654,209283,255575,588223,1181898,1181899,396945,407455,235580,306674,274380,363387,
215517,184136,379245,248146,384904,678934,178541,624688,242668,488281,1383225,1361668,227387,583014,
366715,178314,192834,920399,191829,306111,529936,233968,214346,694475,195051,234650,1360235,426213,
426214,331148,679748,273155,343758,337847,1225521,767639,1374279,396646,353892,564620,353891,693749,
411995,690570,256762,401782,311747,290840,327366,361686,509719,603509,297586,830309,505621,323195,
524702,209115,570246,520374,609620,1342943,600272,304921,913205,182940,529273,480488,230055,510395,
183152,505407,295158,364827,309221,507808,307073,357853,791574,296489,296488,402176,1345545,1345544,
535784,290961,265099,271262,407940,301841,266147,406400,895858,479317,178174,178173,480609,480610,
338941,319614,199270,624306,696991,175491,175492,598610,377100,339546,188708,210607,228629,532835,
304686,410105,279191,279192,384226,384225,337849,697155,684224,480182,859688,298948,1262839,582206,
326842,376189,873874,329071,662633,374842,508103,584445,345378,262217,617513,608415,321258,698769,
333759,380511,234898,213408,1248523,1248522,798531,429345,194991,673664,637576,333579,705393,577050,
657405,354974,441441,1289478,606057,372431,196168,252890,661417,257337,257338,365126,614019,300066,
425012,381044,255735,219079,407381,352572,409246,528399,269436,267040,235407,611487,188769,582339,
538862,535812,1105608,1105609,274907,209462,1374248,425144,270147,212568,244101,302654,367878,
174473,247062,242418,481988,316817,244598,242676,768583,234049,554974,276453,1032776,508030,
1082811,556106,193319,367090,499283,660747,241525,391583,624096,312503,539132,411477,255839,
366430,209642,263873,341502,836983,361090,808417,216656,398673,388076,912647,644119,1093878,
257032,1107231,201179,209128,177903,652481,1032758,423909,697782,347437,325920,235537,443864,
399262,361697,508629,198262,486044,522378,315933,246600,290898,279153,298700,628785,650680,
650681,187982,202038,323338,345733,335719,256135,206966,386636,693548,397441,312617,694225,
523066,694224,1156590,526897,523138,410367,329468,264642,649616,366851,253543,228585,645010,
463797,221416,287822,348828,522739,455690,309922,309921,312012,713702,584299,1112849,254927,
703846,732897,902877,649200,815846,525416,196402,182625,297204,1246669,266385,791704,319207,
298909,477753,191138,322159,322158,355562,626180,340328,608498,383064,320059,791343,553104,
324684,1011632,222835,318380,280523,201056,345073,278940,234530,523944,520490,325763,315160,
329070,348961,972848,921487,312690,798013,1321790,659857,197111,197110,724135,243816,617282,
510094,510092,397806,204748,373844,294769,204887,768074,204759,409419,245344,945786,241526,
297685,698627,591778,336532,675410,175085,675411,343818,523207,407653,407654,403022,325408,
282916,453653,400280,233105,178415,224986,602813,525249,1318974,830541,623893,326477,991329,
860087,1041927,523349,687571,1378873,508051,374835,671705,671706,198443,198444,451575,706743,
269815,256195,207089,188411,364458,617503,624750,1073875,225568,923349,360037,286969,286967,
607897,758006,744009,663001,346290,603088,215142,1242577,397805,704004,174836,366042,275076,
850865,232199,816132,265231,394900,518548,178378,1283648,522665,229059,330405,408636,311982,
291232,329942,482346,294224,361874,382974,404893,644878,329769,1073893,254487,254488,662002,
1366034,463433,883983,980890,980891,1213265,913585,678770,343232,923356,716633,300010,682994,
240736,240737,666418,1200689,705062,229595,642176,570228,783525,711334,277051,214059,483500,
335761,1099216,340988,327679,242855,211916,350855,363169,298790,830682,390905,697219,235298,
327309,1353026,992026,253789,300349,329759,300286,510261,199928,207745,1203281,194616,657586,
1139874,334294,305007,333424,700534,874353,326755,305719,538047,386664,222815,577680,192131,
703622,703621,922617,696524,696523,574865,371269,1376669,217915,189157,626905,1233473,196043,
593538,217441,376188,532551,1188328,651453,656120,207662,388649,529348,188728,183508,370807,
243815,618215,1352503,1091400,684527,324869,425720,211561,259753,613458,577078,224273,1322127,
661713,319258,246070,405887,240036,280612,355202,267986,322681,262374,1167949,249873,187181,
186225,344620,408908,384192,409541,912989,608159,286852,465598,195619,375001,554241,284236,
403367,208418,1075022,527521,576136,555279,351057,732912,604366,343430,956507,689057,1002426,
632109,552332,195529,496509,294118,635246,1336506,1333407,388724,697387,191139,205062,384600,
591816,317493,641716,1347628,177522,260060,233072,791008,1278101,831049,535465,368149,621032,
407506,255502
	}';
	-- ID en cours de traitement
	PersonneId RECORD;
	-- Nouveau tag
	TagId RECORD;
BEGIN 
FOR PersonneId IN (
	-- Le petit test pour voir si ca marche
	SELECT DISTINCT id FROM personnes WHERE id IN (693880,1344773,978025,351925,443824,1361699,1361700)
	-- le lancement depuis le tableau de INT
	/*SELECT DISTINCT id FROM nonoriente where id IN IDATraite ORDER BY nonoriente.id ASC*/
) LOOP
	--Creation de l'état du Tag
	INSERT INTO tags (valeurtag_id, etat, commentaire, limite, created, modified)
	VALUES (IDValTag,'encours','',NULL,NOW(),NOW())
	RETURNING id INTO TagId;
	--Allocation du tag au user
	INSERT INTO entites_tags (tag_id, fk_value, modele)
	VALUES (TagId.id,PersonneId.id,'Personne');
	 END LOOP;
END ;
$$ LANGUAGE 'plpgsql' ;
-- lancement de la fonction
SELECT * FROM createTAG();
-- Suppression de la fonction
DROP FUNCTION createTAG();