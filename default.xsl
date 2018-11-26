<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:html="http://www.w3.org/1999/xhtml" xpath-default-namespace="http://www.tei-c.org/ns/1.0">
    <xsl:output indent="yes" method="html"/>

    <xsl:template match="teiHeader"/>

    <!-- PAGES -->
    <xsl:template match="div[@type eq 'page']">
        <div>
            <xsl:variable name="page" select="number(@n) - 1"/>
            <xsl:attribute name="class">pb</xsl:attribute>
            <xsl:attribute name="data-page-number">
                <xsl:value-of select="$page"/>
            </xsl:attribute>
            <h2 class="tei-title">
                <xsl:value-of select="//titleStmt/title"/>
            </h2>
            <xsl:apply-templates/>
        </div>
    </xsl:template>
   
    <!-- PARAGRAPHS -->
    <xsl:template match="p">
        <p><xsl:apply-templates/>
        </p>
    </xsl:template>

    <!-- ITEM HYPERLINKS -->
    <xsl:template match="ref[@type eq 'url']">
        <xsl:element name="a">
            <xsl:attribute name="href">
                <xsl:value-of select="encode-for-uri(@target)"/>
            </xsl:attribute>
            <xsl:attribute name="target">
                <xsl:text>_blank</xsl:text>
            </xsl:attribute>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <!-- DELETED TEXT -->
    <xsl:template match="del">
        <span class="deleted-text">
            <xsl:apply-templates/>
        </span>
    </xsl:template>

    <!-- ADDED TEXT -->
    <xsl:template match="add">
        <xsl:choose>
            <xsl:when test="@place eq 'above'">
                <span class="add-above">
                    <xsl:apply-templates/>
                </span>
            </xsl:when>
            <xsl:otherwise>
                <span class="add">
                    <xsl:apply-templates/>
                </span>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- UNDERLINED TEXT -->
    <xsl:template match="hi[@rend = 'underline']">
        <span class="underline">
            <xsl:apply-templates/>
        </span>
    </xsl:template>
    
    <!-- NOTE -->
    <xsl:template match="note">
        <xsl:element name="a">
            <xsl:attribute name="tabindex" select="'0'"/>
            <xsl:attribute name="class" select="'btn persname-popover'"/>
            <xsl:attribute name="role" select="'button'"/>
            <xsl:attribute name="data-toggle" select="'popover'"/>
            <xsl:attribute name="data-trigger" select="'focus'"/>
            <xsl:attribute name="data-relation-note" select="normalize-space(.)"/>
            <xsl:attribute name="style" select="'padding:0px;'"/>
            <xsl:attribute name="title">
                <xsl:text>Note</xsl:text>
            </xsl:attribute>
            <xsl:text>
                [*]
            </xsl:text>
        </xsl:element>
    </xsl:template>
    
    <!-- LINE BREAKS -->
    <xsl:template match="lb">
        <xsl:element name="br">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <!-- DATES -->
    <xsl:template match="date">
        <xsl:element name="a">
            <xsl:attribute name="tabindex" select="'0'"/>
            <xsl:attribute name="class" select="'btn persname-popover'"/>
            <xsl:attribute name="role" select="'button'"/>
            <xsl:attribute name="data-toggle" select="'popover'"/>
            <xsl:attribute name="data-trigger" select="'focus'"/>
            <xsl:attribute name="data-display-name" select="@when"/>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

</xsl:stylesheet>
