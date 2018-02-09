<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xpath-default-namespace="http://www.tei-c.org/ns/1.0">
    <xsl:output indent="yes" method="text"/>

    <xsl:variable name="auth"
        select="doc('https://humanities.lib.rochester.edu/esw/auth/auth.xml')//text"/>
    <xsl:template match="teiHeader"/>

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



    <!-- PERSONS -->
    <xsl:template match="persName">
        <xsl:variable name="person_id" select="replace(@ref, 'psn:', '')"/>
        <xsl:variable name="person_first_matching" select="$auth//person[@xml:id eq $person_id]"/>
        <xsl:variable name="person" select="$person_first_matching[1]"/>
        <xsl:element name="a">
            <xsl:attribute name="tabindex" select="'0'"/>
            <!--<xsl:attribute name="class" select="'btn btn-lg btn-danger'"/>-->
            <xsl:attribute name="class" select="'btn persname-popover'"/>
            <xsl:attribute name="role" select="'button'"/>
            <xsl:attribute name="data-toggle" select="'popover'"/>
            <xsl:attribute name="data-trigger" select="'focus'"/>
            <xsl:attribute name="data-xml-id" select="$person/@xml:id"/>
            <xsl:attribute name="data-index-name" select="$person/persName[@type eq 'index']"/>
            <xsl:attribute name="data-display-name" select="$person/persName[@type eq 'display']"/>
            <xsl:attribute name="data-married-name" select="$person/persName[@type eq 'married']"/>
            <xsl:attribute name="data-birth" select="$person/birth"/>
            <xsl:attribute name="data-death" select="$person/death"/>
            <xsl:attribute name="data-relation-note" select="normalize-space($person/note[@type eq 'relation'])"/>
            <xsl:attribute name="data-more-link">
                <xsl:text>https://humanities.lib.rochester.edu/esw/search?query=</xsl:text>
                <xsl:value-of select="$person/@xml:id"/>
            </xsl:attribute>
            <xsl:attribute name="data-tree-link">
                <xsl:text>https://humanities.lib.rochester.edu/esw/family/search?query=</xsl:text>
                <xsl:value-of select="$person/@xml:id"/>
            </xsl:attribute>
            <xsl:attribute name="style" select="'padding:0px;'"/>
            <xsl:attribute name="title">
                <xsl:value-of select="$person/persName[@type eq 'index']"/>
            </xsl:attribute>
            <!--<xsl:attribute name="data-content" select="'test content'"/>-->
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>

    <!-- PLACES -->
    <xsl:template match="placeName">
        <xsl:variable name="place_id" select="replace(@ref, 'pla:', '')"/>
        <xsl:variable name="place" select="$auth//place[@xml:id eq $place_id]"/>
        <xsl:variable name="place_name" select="$place/placeName[not(@type)]"/>
        <xsl:element name="a">
            <xsl:attribute name="tabindex" select="'0'"/>
            <!--<xsl:attribute name="class" select="'btn btn-lg btn-danger'"/>-->
            <xsl:attribute name="class" select="'btn placename-popover'"/>
            <xsl:attribute name="role" select="'button'"/>
            <xsl:attribute name="data-xml-id" select="$place/@xml:id"/>
            <xsl:attribute name="data-placename" select="$place/placeName[not(@type)]"/>
            <xsl:attribute name="data-alt-placename" select="$place/placeName[@type eq 'alternate']"/>
            <xsl:attribute name="data-geo" select="$place/location/geo"/>
            <xsl:attribute name="data-note" select="normalize-space($place/note[not(@type)])"/>
            <xsl:attribute name="data-more-link">
                <xsl:text>https://humanities.lib.rochester.edu/esw/search?query=</xsl:text>
                <xsl:value-of select="$place/@xml:id"/>
            </xsl:attribute>
            <xsl:attribute name="data-toggle" select="'popover'"/>
            <xsl:attribute name="data-trigger" select="'focus'"/>
            <xsl:attribute name="style" select="'padding:0px;'"/>
            <xsl:attribute name="title">
                <xsl:value-of select="$place_name"/>
            </xsl:attribute>
            <!--<xsl:attribute name="data-content" select="'test content'"/>-->
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>



    <!-- GLOSSARY -->
    <xsl:template match="rs[contains(@ref, 'glo:')]">
        <xsl:variable name="gloss_id" select="replace(@ref, 'glo:', '')"/>
        <xsl:variable name="gloss"
            select="$auth//div[@type = 'glossary']/entry[@xml:id eq $gloss_id]"/>
        <xsl:element name="a">
            <xsl:attribute name="tabindex" select="'0'"/>
            <!--<xsl:attribute name="class" select="'btn btn-lg btn-danger'"/>-->
            <xsl:attribute name="class" select="'btn glossary-popover'"/>
            <xsl:attribute name="role" select="'button'"/>
            <xsl:attribute name="data-xml-id" select="$gloss/@xml:id"/>
            <xsl:attribute name="data-form" select="normalize-space($gloss/form)"/>
            <xsl:attribute name="data-def" select="normalize-space($gloss/def)"/>
            <xsl:attribute name="data-toggle" select="'popover'"/>
            <xsl:attribute name="data-trigger" select="'focus'"/>
            <xsl:attribute name="style" select="'padding:0px;'"/>
            <xsl:attribute name="title">
                <xsl:value-of select="$gloss/form"/>
            </xsl:attribute>
            <!--<xsl:attribute name="data-content" select="'test content'"/>-->
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>




    <!-- Various TEI Structural Elements -->
    <xsl:template match="opener">
        <div>
            <xsl:attribute name="class" select="'tei-opener'"/>
            <xsl:apply-templates/>
        </div>
        <hr/>
    </xsl:template>

    <xsl:template match="dateline">
        <span>
            <xsl:attribute name="class" select="'tei-dateline'"/>
            <xsl:apply-templates/>
        </span>
    </xsl:template>

    <xsl:template match="salute">
        <span>
            <xsl:attribute name="class" select="'tei-opener'"/>
            <xsl:apply-templates/>
        </span>
    </xsl:template>

    <xsl:template match="note">
        <span>
            <xsl:attribute name="class" select="'tei-note'"/>
            <xsl:apply-templates/>
        </span>
    </xsl:template>

    <xsl:template match="closer">
        <hr/>
        <div>
            <xsl:attribute name="class" select="'tei-closer'"/>
            <xsl:apply-templates/>
        </div>
    </xsl:template>

    <xsl:template match="lb">
        <xsl:element name="br">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>


</xsl:stylesheet>
