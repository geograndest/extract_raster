#!/usr/bin/php
<?php

/*********************************************************
 * DOCUMENTATION
 *********************************************************  

 Le script "extract_raster.php" permet d'extraire un ensemble de fichiers d'un dossier source vers un dossier destination à partir d'une liste au format TXT.
 Il est utilisé pour extraire des fichiers raster (ex. orthophoto) à partir d'une emprise donnée. Cette opération nécessite un travail préliminaire dans QGIS.
 Ce script fonctionne grâce à PHP qui doit être disponible sur le poste de travail (utiliser Xampp par exemple ou simplement un fichier php.exe joint).

 Utilisation:
    > php extract_raster.php index=D:\\Climax\\index.txt src=O:\\open_data\\ORTHO_RVB_0M20_JP2_E100_L93_D68_2015\\data\\ ext=.jp2,.tab

 Détails:

 Première étape: produire un fichier "index.txt" avec la liste des fichiers à extraire (sans extension)
 Dans QGIS:
 - Ajouter flux WFS ou charger couche d'emprise (ex.: flux Région Grand Est des EPCI ou fichier SHP fourni par le partenaire)
 - Fltrer la couche de façon attributaire ou géographique si nécessaire sur l'emprise à conserver (ex.: pour les EPCI, "nom_complet"= '...')
 - Ajouter une zone tampon si nécessaire, avec création d'un nouvelle couche temporaire (vecteurs > outils de géotraitement > tampom)
 - Ajouter la couche d'index à extraire (ex.: cf. dalles orthos dans ODGEO)
 - Sélectionner les dalles par intersection (vecteurs > outils de géotraitement > intersection)
 - Ouvrir la table attributaire, copier l'ensemble de la liste et la coller dans Libreoffice Calc
    => Si plusieurs départements => plusieurs orthos => reproduire la démarche
 - Nettoyer la liste obtenue pour ne conserver que la liste des fichiers (sans extenston) et l'enregistrer au format TXT (ex.: "index.txt")

 Deuxième étape: extraire les fichiers
 - Placer le fichier "index.txt" dans le dossier d'extraction (ex.: "D:\Climax")
 - Se placer dans le dossier du fichier "extract_raster.php"
 - Lancer la commande du type: `$ php extract_raster.php index=D:\\Climax\\index.txt src=O:\\open_data\\ORTHO_RVB_0M20_JP2_E100_L93_D68_2015\\data\\ ext=.jp2,.tab`

 */




/*********************************************************
 * FUNCTIONS
 *********************************************************

/**
 * Function get_arg()
 */
function get_arg($args, $arg, $default = '')
{
    if (isset($args[$arg])) {
        return $args[$arg];
    }
    return $default;
}

/*********************************************************
 * MAIN SCRIPT
 *********************************************************

/**
 * GET ARGS
 * 
 * For command `$ php -f somefile.php a=1 b[]=2 b[]=3`
 * This will set $args['a'] to '1' and $args['b'] to array('2', '3') 
 */

parse_str(implode('&', array_slice($argv, 1)), $args);
$index = get_arg($args, 'index', 'index.txt');
$src = get_arg($args, 'src', './');
$dst = get_arg($args, 'dst', './');
$ext = get_arg($args, 'ext');

$is_src = is_dir($src);
$is_dst = is_dir($dst);
$is_index = is_file($index);
$is_ext = ($ext != '');

if (!$is_dst) {
    mkdir($dst, 0777);
    echo "The directory $dst was successfully created." . PHP_EOL;
} else {
    echo "The directory $dst exists." . PHP_EOL;
}

if ($is_src and $is_index and $is_ext) {

    // Read files list
    $index_content = file_get_contents($index);
    $filenames = explode("\r\n", $index_content);

    // Get extensions
    $extensions = explode(',', $ext);

    // List of files
    $files = [];
    foreach ($filenames as $filename) {
        if ($filename and !in_array($filename, ['.', '..'])) {
            foreach ($extensions as $extension) {
                $files[] = $filename . $extension;
                file_put_contents('test.txt', $src . $filename . $extension);
            }

        }
    }

    $n = 1;
    $nb_files = count($files);
    foreach ($files as $file) {
        echo "Fichier $n/$nb_files: " . PHP_EOL;
        if (is_file($src . $file)) {
            if (copy($src . $file, $dst . $file)) {
                echo "SUCCESS copy '$file' to '$dst'.\n" . PHP_EOL;
            } else {
                echo "FAILED copy '$file' to '$dst'...\n" . PHP_EOL;
            }
        } else {
            echo "ERROR: file '$file' doesn't exist in '$src'";
        }
        $n++;
    }

} else {
    if ($is_src) {
        die("ERROR: index file " . $index . " doesn't exist.");
    }
    if ($is_index) {
        die("ERROR: source directory " . $src . " doesn't exist.");
    }
    if ($is_ext) {
        die("ERROR: destination directory " . $dst . " doesn't exist.");
    }
}
