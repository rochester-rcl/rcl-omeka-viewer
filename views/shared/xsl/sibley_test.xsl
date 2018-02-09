<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xpath-default-namespace="http://www.tei-c.org/ns/1.0">
    <xsl:output indent="yes" method="html"/>
   
    <xsl:template match="/">
        <xsl:for-each select="//ab">
            <xsl:variable name="page"><xsl:value-of select="position() - 1"></xsl:value-of></xsl:variable>
            <xsl:element name="div">
                <xsl:attribute name="class">pb</xsl:attribute>
                <xsl:attribute name="data-page-number"><xsl:value-of select="$page"/></xsl:attribute>
                <h2 class="tei-title"><xsl:value-of select="//titleStmt/title"/></h2>
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
</xsl:stylesheet>
