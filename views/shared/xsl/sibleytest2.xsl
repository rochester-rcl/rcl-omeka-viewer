<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xpath-default-namespace="http://www.tei-c.org/ns/1.0"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:output indent="yes" method="html"/>
    
    <xsl:template match="/">
   
        <div class="hiddenHeader">
            <xsl:apply-templates select="//teiHeader"/>
        </div>
        
    </xsl:template>
    
</xsl:stylesheet>