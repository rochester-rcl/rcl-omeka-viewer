<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xpath-default-namespace="http://www.tei-c.org/ns/1.0">
    <xsl:character-map name="div_split">
        <xsl:output-character character="&lt;" string="&lt;"/>
        <xsl:output-character character="&gt;" string="&gt;"/>
    </xsl:character-map>
    <xsl:output indent="yes" method="text" use-character-maps="div_split"/>
    

    <!--<xsl:template match="/">
        <xsl:for-each select="//ab">
            <xsl:variable name="page">
            <xsl:value-of select="position() - 1"></xsl:value-of></xsl:variable>
            <xsl:element name="div">
                <xsl:attribute name="class">pb</xsl:attribute>
                <xsl:attribute name="data-page-number"><xsl:value-of select="$page"/></xsl:attribute>
                <h2 class="tei-title"><xsl:value-of select="//titleStmt/title"/></h2>
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>-->

    <xsl:variable name="auth"
        select="doc('http://humanities.lib.rochester.edu/esw/auth/auth.xml')//text"/>
    <xsl:template match="teiHeader"/>

    <xsl:template match="text">
        <div>
            <xsl:attribute name="class" select="'pb'"/>
            <xsl:attribute name="data-page-number" select="'0'"/>
            <xsl:apply-templates/>
        </div>
    </xsl:template>

    <xsl:template match="pb[@facs]">
        <xsl:if test="@n ne '1'">
            <xsl:text>MOOSE</xsl:text>
            <xsl:text>&lt;/div>&lt;div class="pb" </xsl:text>
            <xsl:text>data-page-number="</xsl:text>
            <xsl:value-of select="number(@n) - 1"/>
            <xsl:text>"</xsl:text>
            <xsl:text>&gt;</xsl:text>
        </xsl:if>
    </xsl:template>

    <!--<xsl:template match="placeName">
        <xsl:variable name="place_id" select="replace(@ref, 'pla:', '')"/>
        <xsl:apply-templates/>
        <xsl:text> [</xsl:text>
        <xsl:value-of select="$place_id"/>
        <xsl:value-of select="$auth//place[@xml:id eq $place_id]/placeName"/>
        <xsl:text>]</xsl:text>
    </xsl:template>-->

    <!-- PERSONS -->
    <xsl:template match="persName">
        <xsl:variable name="person_id" select="replace(@ref, 'psn:', '')"/>
        <xsl:variable name="index_name"
            select="$auth//person[@xml:id eq $person_id]/persName[@type eq 'index']"/>
        <xsl:element name="a">
            <xsl:attribute name="tabindex" select="'0'"/>
            <!--<xsl:attribute name="class" select="'btn'"/>
            <xsl:attribute name="role" select="'button'"/>-->
            <xsl:attribute name="data-html" select="'true'"/>
            <xsl:attribute name="data-toggle" select="'popover'"/>
            <xsl:attribute name="data-trigger" select="'focus'"/>
            <xsl:attribute name="title">
                <xsl:value-of select="$index_name"/>
                <xsl:text> (</xsl:text>
                <xsl:value-of select="$person_id"/>
                <xsl:text>)</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="data-content" select="'test content'"/>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <!-- PLACES -->
    <xsl:template match="placeName">
        <xsl:variable name="place_id" select="replace(@ref, 'pla:', '')"/>
        <xsl:variable name="place_name"
            select="$auth//place[@xml:id eq $place_id]/placeName"/>
        <xsl:element name="a">
            <xsl:attribute name="tabindex" select="'0'"/>
            <!--<xsl:attribute name="class" select="'btn'"/>
            <xsl:attribute name="role" select="'button'"/>-->
            <xsl:attribute name="data-html" select="'true'"/>
            <xsl:attribute name="data-toggle" select="'popover'"/>
            <xsl:attribute name="data-trigger" select="'focus'"/>
            <xsl:attribute name="title">
                <xsl:value-of select="$place_name"/>
                <xsl:text> (</xsl:text>
                <xsl:value-of select="$place_id"/>
                <xsl:text>)</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="data-content" select="'test content'"/>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="closer">
        <hr/>
        <xsl:apply-templates/>
    </xsl:template>
    
</xsl:stylesheet>