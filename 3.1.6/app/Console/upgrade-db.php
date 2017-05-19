<?php

if (1 >= count($argv)) {
    echo 'Syntaxe: php ' . basename(__FILE__) . ' <env>' . "\n";
    exit(-1);
}

$env = $argv[1];

$mode = 'exec';

if (3 === count($argv)) {
    $mode = in_array($argv[2], array('php', 'exec')) ? $argv[2] : $mode;
}

include dirname(__FILE__) . '/../Config/database.php';

$dbConfig = new DATABASE_CONFIG;
$updatedRevision = NULL;

try {
    if (FALSE === isset($dbConfig->$env)) {
        throw new RuntimeException("L'environnement '$env' n'existe pas");
    }
    $dbConfig = $dbConfig->$env;
    $cnx = new PDO('pgsql:host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['database'], $dbConfig['login'], $dbConfig['password']);

    $results = $cnx->query("SELECT property_value FROM referentiel.configs WHERE name = 'db.version' LIMIT 1", PDO::FETCH_ASSOC);

    $currentRevision = 0;
    if (FALSE !== $results) {
        if (0 === $results->rowCount()) {
            $result = $cnx->exec("INSERT INTO referentiel.configs (name, property_value) VALUES ('db.version', '0')");
            if (FALSE === $result && '00000' !== $cnx->errorCode()) {
                throw new RuntimeException('Impossible de mettre à jour la version de la base (à 0) : ' . join(',', $cnx->errorInfo()));
            }
            $results = $cnx->query("SELECT property_value FROM referentiel.configs WHERE name = 'db.version' LIMIT 1");

            if (FALSE === $results) {
                throw new RuntimeException('Impossible de récupérer la version de la base : ' . join(',', $cnx->errorInfo()));
            }
        }
        $result = $results->fetch(PDO::FETCH_ASSOC);

        $currentRevision = $result['property_value'];
    }

    $updatedRevision = $currentRevision;

    $scriptDir = dirname(__FILE__) . '/../Config/sql';

    if (TRUE === is_dir($scriptDir)) {
        $dirs = scandir($scriptDir);
        natsort($dirs);
        foreach($dirs as $file) {
            if ('.' === $file || '..' === $file || '.svn' === $file) {
                continue;
            }
            if (TRUE === is_numeric($file) && is_dir($scriptDir . '/' . $file)) {
                $revision = (int)$file;
                if ($revision <= $currentRevision) {
                    $updatedRevision = $revision;
                    continue;
                }
                $updatedRevision = $revision;
                $revisionDir = $scriptDir . '/' . $file;
                $files = scandir($revisionDir);
                natsort($files);
                $pdo = NULL;
                foreach($files as $upgradeFile) {
                    if ('.' === $upgradeFile || '..' === $upgradeFile || '.svn' === $upgradeFile) {
                        continue;
                    }
                    if (TRUE === is_dir($revisionDir . '/' . $upgradeFile)) {
                        continue;
                    }
                    $pPos = strrpos($upgradeFile, '.');

                    if (FALSE === $pPos) {
                        continue;
                    }

                    $extension = substr($upgradeFile, $pPos+1);
                    switch(strtolower($extension)) {
                        case 'sql':
                            switch($mode) {
                                case 'exec':
                                    putenv('PGPASSWORD='.(''!== $dbConfig['password'] ? $dbConfig['password'] : ''));
                                    $cmd = 'psql -h ' . $dbConfig['host'] . ' -U ' . $dbConfig['login'] .  '  -d ' . $dbConfig['database'] . ' < ' . $revisionDir . '/' . $upgradeFile;
                                    echo '  -> ' . $cmd."\n";
                                    passthru($cmd);
                                    break;
                                case 'php':
                                    echo '  -> PDO: ' . $revisionDir . '/' . $upgradeFile."\n";
                                    $cnx->beginTransaction();
                                    foreach(explode(';', file_get_contents($revisionDir . '/' . $upgradeFile)) as $statement) {
                                        $statement = trim($statement);
                                        if (0 === strlen($statement)) {
                                            continue;
                                        }
                                        $r = $cnx->exec($statement);
                                        if (FALSE === $r) {
                                            $err = $cnx->errorInfo();
                                            $cnx->rollBack();
                                            throw new RuntimeException("Error when executing SQL file '".$revisionDir . '/' . $upgradeFile."' for statement '".$statement."': ".join(':', $err). "( all SQL operations have been rollbacked)");
                                        }
                                    }
                                    $cnx->exec("UPDATE referentiel.configs SET property_value = '$updatedRevision' WHERE name = 'db.version'");
                                    $cnx->commit();
                                    break;
                            }
                            break;
                        case 'php':
                            $phpFile = $revisionDir . '/' . $upgradeFile;
                            echo '  -> ' . "include $phpFile\n";
                            include $phpFile;
                            break;
                        case 'csv':
                            $csvFile = $revisionDir . '/' . $upgradeFile;
                            list($num, $type, $name) = explode('_',array_shift(explode('.', $upgradeFile)), 3);
                            switch(strtolower($type)) {
                                case 'import':
                                    // @todo traiter les fichiers csv pour l'import
                                    break;
                            }
                    }
                }

            }
        }
    }
    $result = $cnx->exec("UPDATE referentiel.configs SET property_value = '$updatedRevision' WHERE name = 'db.version'");
    if (FALSE === $result) {
        throw new RuntimeException('Impossible de mettre à jour la version de la base (à '.$updatedRevision.') : ' . join(': ', $cnx->errorInfo()));
    }
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . "\n";
    exit(1);
}

if (NULL !== $updatedRevision) {
    if ((int)$currentRevision !== (int)$updatedRevision) {
        echo "La base de données a été mise à jour à la version '$updatedRevision'" . "\n";
    } else {
        echo "La base de données est déjà à la dernière révision ($updatedRevision)\n";
    }
}
exit(0);