La feuille de style menu2selenium.xsl permet de transformer un menu (menu
principal ou menu d'un dossier RSA) au format HTML (extrait de l'interface
web-rsa) en un fichier de tests fonctionnels Selenium.

Les paramètres possibles sont:
	- url_base
	- url_login
	- url_logout
	- mode
	- title
	- username
	- password

2 modes sont disponibles:
	- access: accès simple aux URL, vérification d'un h1 (valeur par défaut)
	- search: accès aux URL, envoi du formulaire, vérification d'un h2 "Résultats de la recherche"

Usage:
1°) Menu principal
xsltproc \
	--stringparam username "webrsa" \
	--stringparam password "webrsa" \
	--stringparam title "Menu principal - utilisateur webrsa" \
	-o "menu-principal.selenium.html" \
	"test2selenium.xsl" \
	"menu-principal.html"

2°) Recherches, à partir du menu principal
xsltproc \
	--stringparam username "webrsa" \
	--stringparam password "webrsa" \
	--stringparam mode "search" \
	--stringparam title "Recherches - utilisateur webrsa" \
	-o "menu-recherches.selenium.html" \
	"test2selenium.xsl" \
	"menu-principal.html"

3°) Menu d'un dossier RSA (accès simple aux URL, vérification d'un h1)
xsltproc \
	--stringparam username "webrsa" \
	--stringparam password "webrsa" \
	--stringparam title "Menu dossier - utilisateur webrsa" \
	-o "menu-dossier.selenium.html" \
	"test2selenium.xsl" \
	"menu-dossier.html"
