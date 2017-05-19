<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://www.w3.org/1999/xhtml">
	<xsl:output method="xml" indent="yes" encoding="UTF-8"
		doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
		doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" />

	<!-- Paramètres et valeurs par défaut -->
	<xsl:param name="url_base" select="'http://localhost/'" />
	<xsl:param name="url_login" select="'/webrsa/WebRSA-trunk/users/login'" />
	<xsl:param name="url_logout" select="'/webrsa/WebRSA-trunk/users/logout'" />
	<xsl:param name="mode" select="'access'" />
	<xsl:param name="title" select="'result.selenium'" />
	<xsl:param name="username" select="'username'" />
	<xsl:param name="password" select="'password'" />

	<!-- Traitement du document principal -->
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
			<head profile="http://selenium-ide.openqa.org/profiles/test-case">
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<!-- FIXME -->
				<!--<link rel="selenium.base" href="$url_base" />-->
				<link rel="selenium.base" href="http://localhost/" />
				<title><xsl:value-of select="$title" /></title>
			</head>
			<body>
				<table cellpadding="1" cellspacing="1" border="1">
					<thead>
						<tr>
							<td rowspan="1" colspan="3"><xsl:value-of select="$title" /></td>
						</tr>
					</thead>
					<tbody>
						<xsl:comment> Connexion </xsl:comment>
						<tr>
							<td>open</td>
							<td><xsl:value-of select="$url_logout" /></td>
							<td></td>
						</tr>
						<tr>
							<td>open</td>
							<td><xsl:value-of select="$url_login" /></td>
							<td></td>
						</tr>
						<tr>
							<td>type</td>
							<td>id=UserUsername</td>
							<td><xsl:value-of select="$username" /></td>
						</tr>
						<tr>
							<td>type</td>
							<td>id=UserPassword</td>
							<td><xsl:value-of select="$password" /></td>
						</tr>
						<tr>
							<td>clickAndWait</td>
							<td>css=input[type=&quot;submit&quot;]</td>
							<td></td>
						</tr>
						<xsl:comment> Tests </xsl:comment>
						<xsl:apply-templates />
						<xsl:comment> Déconnexion </xsl:comment>
						<tr>
							<td>open</td>
							<td><xsl:value-of select="$url_logout" /></td>
							<td></td>
						</tr>
						<tr>
							<td>assertText</td>
							<td>css=h1</td>
							<td>Connexion</td>
						</tr>
					</tbody>
				</table>
			</body>
		</html>
	</xsl:template>

	<!-- Traitement des liens -->
	<xsl:template match="a">
		<!-- TODO: ouvrir le lien dont le href... -->
		<xsl:if test="@href!='#' and ( contains(@class ,'search') or $mode != 'search' )">
			<tr>
				<td>open</td>
				<td><xsl:value-of select="@href"/></td>
				<td></td>
			</tr>
			<xsl:choose>
				<xsl:when test="$mode = 'search'">
					<tr>
						<td>clickAndWait</td>
						<td>css=*[type=&quot;submit&quot;]</td>
						<td></td>
					</tr>
					<tr>
						<td>assertText</td>
						<td>css=h2</td>
						<td>Résultats de la recherche</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<tr>
						<td>assertText</td>
						<td>css=h1</td>
						<td>*</td>
					</tr>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:template>

	<!-- Traitement des autres noeuds (on n'affiche rien) -->
	<xsl:template match="text()|@*"></xsl:template>
</xsl:stylesheet>