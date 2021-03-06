# Extract Raster

Le script "extract_raster.php" permet d'extraire un ensemble de fichiers d'un dossier source vers un dossier destination à partir d'une liste au format TXT.
Il est utilisé pour extraire des fichiers raster (ex. orthophoto) à partir d'une emprise donnée. Cette opération nécessite un travail préliminaire dans QGIS.
Ce script fonctionne grâce à PHP qui doit être disponible sur le poste de travail (utiliser Xampp par exemple ou simplement un fichier php.exe joint).

Utilisation:

```
php extract_raster.php index=D:\\Climax\\index.txt src=O:\\open_data\\ORTHO_RVB_0M20_JP2_E100_L93_D68_2015\\data\\ ext=.jp2,.tab
```

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
