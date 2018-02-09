<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:tei="http://www.tei-c.org/ns/1.0">

    <xsl:output indent="yes" method="html"/>
    
    <xsl:template match="/">
        
        <script type="text/javascript" src="../sites/all/modules/xsl_formatter/xsl/scripts/sewardTools.js"/>
        <div class="hiddenHeader">
            <xsl:apply-templates select="//tei:teiHeader"/>
        </div>
        
        <xsl:apply-templates select="//tei:text/tei:body/tei:ab"/>
        
    </xsl:template>
    
    <xsl:template match="tei:text/tei:body/tei:ab">
        <div class="overlay"></div>
        
        <div class="teiBody">
            <h2 id="letter-title"><xsl:value-of select="/tei:TEI/tei:teiHeader[1]/tei:fileDesc[1]/tei:titleStmt[1]/tei:title[1]"></xsl:value-of></h2>
    	<div class="sewardToolbox">
            <label id="headerToggle">Information</label>
            <label id="noteToggle">Hide Notes</label>
            <label id="letterExpand">Expand Letter</label>
    	    <label id="text-expand">Expand Text</label>
    	    <label id='page-text'> |  Page</label>
    	    <form class='page-select' method='post'>
    		  <input id='page-number' type='text' name='page' value='1'/>
    	    </form>
    	    <label id="total-page">Of <span id="page-count"></span></label>
    	    <label id="letter-return">| <a class="letter-link" href="/letter-view">Back to Letters</a></label>
        </div>
            <div class="letter-info">
                <h4 id="letter-response">
                    action: <xsl:value-of select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:correspDesc[1]/tei:correspAction[1]/@type"></xsl:value-of>
                </h4>
                <h4>
                    sender:
                    <xsl:variable name="personPTR" select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:correspDesc[1]/tei:correspAction[1]/tei:persName/@ref"/>
                    <xsl:variable name="person"><xsl:value-of select="substring-after($personPTR,'psn:')"/></xsl:variable>
                    <xsl:choose>
                        <xsl:when test="$person != 'unknown'">
                            <a class="note">
                                <xsl:value-of select="document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:forename[@type = 'first']"></xsl:value-of><xsl:text>&#160;</xsl:text>
                                <xsl:value-of select="document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:surname[@type = 'last' or 'maiden']"></xsl:value-of>
                            </a>
                            <div class="hiddenDiv">
                                <div class="person"><span class="x-button">x</span><h3>
                                        <xsl:element name="a">
                                            <xsl:attribute name="href">
                                                <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($person,'_')"/>
                                            </xsl:attribute>
                                            <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                                            <xsl:attribute name="class">
                                                <xsl:text>more</xsl:text>
                                            </xsl:attribute>
                                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:forename[@type = 'first']"></xsl:value-of>
                                            <xsl:text>&#160;</xsl:text>
                                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:surname[@type = 'last' or 'maiden']"></xsl:value-of>
                                        </xsl:element>
                                    </h3>
                                </div>
                    
                                <strong>Birth:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:birth"></xsl:value-of>
                                <xsl:text>&#xa;</xsl:text><xsl:text>&#160;</xsl:text>
                                <strong>Death:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:death"></xsl:value-of>
                            </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class="hiddenDiv"><span class="x-button">x</span>
                                <strong class="unknown">Unknown</strong>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </h4>
                <h4>
                    location: 
                    <xsl:variable name="placePTR" select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:correspDesc[1]/tei:correspAction[1]/tei:placeName/@ref"/>
                    <xsl:variable name="place"><xsl:value-of select="substring-after($placePTR,'pla:')"/></xsl:variable>
                    <xsl:choose>
                        <xsl:when test="$place != 'unknown'">
                            <a class="note">
                                <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/places.xml')//tei:text/tei:body/tei:div/tei:listPlace/tei:place[@xml:id = $place]/tei:head"/>
                            </a>
                            <div class="hiddenDiv">
                                <div class="person"><span class="x-button">x</span>
                                    <h3>
                                        <xsl:element name="a">
                                            <xsl:attribute name="href">
                                                <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($place,'_')"/>
                                            </xsl:attribute>
                                            <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                                            <xsl:attribute name="class">
                                                <xsl:text>more</xsl:text>
                                            </xsl:attribute>
                                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/places.xml')//tei:text/tei:body/tei:div/tei:listPlace/tei:place[@xml:id = $place]/tei:head"></xsl:value-of>
                                        </xsl:element>
                                    </h3>
                                </div>
                            </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class="hiddenDiv">
                                <strong class="unknown">Unknown</strong>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </h4>
                <h4>
                    receiver:
                    <xsl:variable name="personPTR" select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:correspDesc[1]/tei:correspAction[2]/tei:persName/@ref"/>
                    <xsl:variable name="person"><xsl:value-of select="substring-after($personPTR,'psn:')"/></xsl:variable>
                    <xsl:choose>
                        <xsl:when test="$person != 'unknown'">
                            <a class="note">
                                <xsl:value-of select="document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:forename[@type = 'first']"></xsl:value-of><xsl:text>&#160;</xsl:text>
                                <xsl:value-of select="document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:surname[@type = 'last' or 'maiden']"></xsl:value-of>
                            </a>
                            <div class="hiddenDiv">
                                   <div class="person"><span class="x-button">x</span><h3>
                                        <xsl:element name="a">
                                            <xsl:attribute name="href">
                                                <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($person,'_')"/>
                                            </xsl:attribute>
                                            <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                                            <xsl:attribute name="class">
                                                <xsl:text>more</xsl:text>
                                            </xsl:attribute>
                                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:forename[@type = 'first']"></xsl:value-of>
                                            <xsl:text>&#160;</xsl:text>
                                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:surname[@type = 'last' or 'maiden']"></xsl:value-of>
                                        </xsl:element>
                                    </h3>
                                  </div>
                                <strong>Birth:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:birth"></xsl:value-of>
                                <xsl:text>&#xa;</xsl:text><xsl:text>&#160;</xsl:text>
                                <strong>Death:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:death"></xsl:value-of>
                            </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <a class="note">Unknown</a>
                            <div class="hiddenDiv">
                                <strong class="unknown">Unknown</strong>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </h4>
                <h4>
                    location: 
                    <xsl:variable name="placePTR" select="/tei:TEI/tei:teiHeader[1]/tei:profileDesc[1]/tei:correspDesc[1]/tei:correspAction[2]/tei:placeName/@ref"/>
                    <xsl:variable name="place"><xsl:value-of select="substring-after($placePTR,'pla:')"/></xsl:variable>
                    <xsl:choose>
                        <xsl:when test="$place != 'unknown'">
                            <a class="note">
                                <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/places.xml')//tei:text/tei:body/tei:div/tei:listPlace/tei:place[@xml:id = $place]/tei:head"/>
                            </a>
                            <div class="hiddenDiv">
                                <div class="person"><span class="x-button">x</span>
                                    <h3>
                                        <xsl:element name="a">
                                            <xsl:attribute name="href">
                                                <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($place,'_')"/>
                                            </xsl:attribute>
                                            <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                                            <xsl:attribute name="class">
                                                <xsl:text>more</xsl:text>
                                            </xsl:attribute>
                                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/places.xml')//tei:text/tei:body/tei:div/tei:listPlace/tei:place[@xml:id = $place]/tei:head"></xsl:value-of>
                                        </xsl:element>
                                    </h3>
                                   </div>  
                                </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <a class="note">Unknown</a>
                            <div class="hiddenDiv">
                                <strong class="unknown">Unknown</strong>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </h4>
                <xsl:for-each select="/tei:TEI/tei:teiHeader/tei:revisionDesc/tei:listChange/tei:change">
                    <h4 class="revisions">
                        <xsl:value-of select="@type"/>:<xsl:text>&#160;</xsl:text><xsl:value-of select="substring(@who,5)"/><xsl:text>&#160;</xsl:text><xsl:value-of select="@when"/> 
                    </h4>
                </xsl:for-each>
            </div>
        <div class="page-controls">
            <i class="page-back">&lt;</i>
            <i class="page-forward">&gt;</i>
        </div>
	   <div class="sewardViewer">
          <div id="viewer" class="viewer"></div>
       </div>    
       <div class="tei-text">    
            <xsl:apply-templates/>
       </div>    
       </div>
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:lb | tei:seg/tei:lb">
        <xsl:apply-templates/>
        <br />
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:note | tei:seg/tei:note">
        <span class="note">
            <xsl:apply-templates/>
        </span>
        <div class="hiddenDiv"> 
        </div>
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:hi/@strikethrough | tei:seg/tei:hi/@strikethrough">
        <span class="strikethrough">
            <xsl:apply-templates/>
        </span>
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:hi[@rend = 'superscript'] | tei:seg/tei:hi[@rend = 'superscript']">
        <sup>
            <xsl:apply-templates/>
        </sup>
    </xsl:template>
    <xsl:template match="//tei:hi[@rend = 'underline']">
        <span class="underline">
            <xsl:apply-templates/>
        </span>
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:hi[@rend = 'underline quotes'] | tei:seg/tei:hi[@rend = 'underline quotes']">
        <span class="underline">
            <xsl:apply-templates/>
        </span>
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:del">
        <span class="strikethrough">
            <xsl:apply-templates/>
        </span>
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:add | tei:seg/tei:add">
        <span class="add">
            ^<xsl:apply-templates/>^
        </span>
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:gap | tei:seg/tei:gap">
        <xsl:if test="@reason = 'hole'">
            <span class="gap">
                [hole]
                <xsl:apply-templates/>
            </span>
        </xsl:if>
        <xsl:if test="@reason = 'illegible'">
            <span class="gap">
                [illegible]
                <xsl:apply-templates/>
            </span>
        </xsl:if>
        <xsl:if test="@reason = 'stamp'">
            <span class="gap">
                [stamp]
                <xsl:apply-templates/>
            </span>
        </xsl:if>
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:stamp | tei:seg/tei:stamp">
        <a class="note">
            <xsl:apply-templates/>  
        </a>
        <div class="hiddenDiv">
            <div class="person"><span class="x-button">x</span><h3>Stamp</h3></div>
            <strong>Type:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select="@type"/>
        </div>
    </xsl:template>
    <xsl:template name="getSupplied" match="tei:text/tei:body/tei:ab/tei:supplied | tei:seg/tei:supplied">
      
            <a class="note">[
                <xsl:apply-templates/>
                ]
            </a>
            <div class="hiddenDiv">
                <div class="person"><span class="x-button">x</span><h3>Supplied</h3></div>
                <strong>Reason:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select="@reason"/>
            </div>
    </xsl:template>
    <xsl:template name="getMisspell" match="tei:text/tei:body/tei:ab/tei:choice | tei:seg/tei:choice">
  
            <a class="note">[
                <xsl:value-of select="tei:sic"/>
                ]
            </a>
   
        <div class="hiddenDiv">
            <div class="person"><span class="x-button">x</span><h3>Alternate Text</h3></div>
            <strong>Alternate Text:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select="tei:corr"/>
        </div>
    </xsl:template>
    <xsl:template name="getMarginalia" match="tei:text/tei:body/tei:ab/tei:seg[@type = 'marginalia']">
        <br />
        [<xsl:value-of select="@subtype"/> Margin]
        <span class="marginalia">
            <xsl:apply-templates/>
        </span>    
    </xsl:template>
    <xsl:template match="tei:text/tei:body/tei:ab/tei:pb">
        <xsl:choose>
            <xsl:when test="@n != 1">
                <xsl:text disable-output-escaping="yes">&lt;/div&gt;</xsl:text>
            </xsl:when>
        </xsl:choose> 
        <xsl:element name="a">
            <xsl:attribute name="href">#</xsl:attribute>
            <xsl:attribute name="class">letter-page</xsl:attribute>
            <xsl:attribute name="id">page<xsl:value-of select="@n"/></xsl:attribute>
            Page <xsl:value-of select="@n"/>
            <xsl:apply-templates/>
        </xsl:element>
        <xsl:text disable-output-escaping="yes">&lt;div class="page-break"&gt;</xsl:text>
    </xsl:template>
    
    <xsl:template match="//tei:titleStmt/tei:title">
        <h2 class="title"><strong>
            <xsl:apply-templates/>
        </strong></h2>
    </xsl:template>
    <xsl:template match="//tei:titleStmt/tei:respStmt/tei:persName">
        <h4 class="transcriber"><strong>Transcriber:</strong><xsl:value-of select="@ref"/>
            <xsl:apply-templates/>
        </h4>
    </xsl:template>
    <xsl:template match="//tei:publicationStmt/tei:distributor">
        <h4 class="distributor"><strong>Distributor:</strong>
            <xsl:apply-templates/>
        </h4>
    </xsl:template>
    <xsl:template match="//tei:sourceDesc/tei:msDesc/tei:msIdentifier/tei:institution">
        <h4 class="institution"><strong>Institution:</strong>
            <xsl:apply-templates/>
        </h4>
    </xsl:template>
    <xsl:template match="//tei:sourceDesc/tei:msDesc/tei:msIdentifier/tei:repository">
        <h4 class="repository"><strong>Repository:</strong> 
            <xsl:apply-templates/>
        </h4>
    </xsl:template>
    <xsl:template match="//tei:sourceDesc/tei:msDesc/tei:history/tei:origin/tei:date">
        <h4 class="date"><strong>Date:</strong> <xsl:value-of select="@when"/>
            <xsl:apply-templates/>
        </h4>
    </xsl:template>
    <xsl:template match="//tei:encodingDesc">
        <div class="encodingDesc">
            <xsl:apply-templates/>
        </div>
        
    </xsl:template>
    <xsl:template name="editorNote" match="tei:text/tei:body/tei:ab/tei:note[@type = 'editorial'] | tei:seg/tei:note[@type = 'editorial']">
        <a class="edit-note"><img src="../sites/all/icons/Seward-Icons-E.svg" alt="seward-editorial-note"/>
            <xsl:apply-templates/>
        </a>
        <div class="hiddenDiv">
            <div class="person"><span class="x-button">x</span><h3>Editorial Note</h3></div>
            <xsl:value-of select="tei:note[@type = 'editorial']"/>
        </div>
    </xsl:template>
    
    <xsl:template name="getPeople" match="//tei:persName[not(parent::tei:respStmt) and not(parent::tei:correspAction)]">
        <a class="note">
            <xsl:apply-templates/>
        </a>
        <xsl:variable name="personPTR" select="@ref"/>
        <xsl:variable name="person"><xsl:value-of select="substring-after($personPTR,'psn:')"/></xsl:variable>
        <xsl:variable name="personCert"><xsl:value-of select="@cert"/></xsl:variable>
        <xsl:if test="$personCert = 'unknown'">
            <div class="hiddenDiv">
                <strong class="unknown">Unknown</strong>
            </div>
        </xsl:if>
        <xsl:choose>
            <xsl:when test="$person != 'unknown'">
                <div class="hiddenDiv">
                    <div class="person"><span class="x-button">x</span><h3>
                        <xsl:element name="a">
                            <xsl:attribute name="href">
                                <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($person,'_')"/>
                            </xsl:attribute>
                            <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                            <xsl:attribute name="class">
                                <xsl:text>more</xsl:text>
                            </xsl:attribute>
                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:forename[@type = 'first']"></xsl:value-of>
                            <xsl:text>&#160;</xsl:text>
                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:surname[@type = 'last' or 'maiden']"></xsl:value-of>
                          </xsl:element>
                    </h3></div>
                    <xsl:variable name="birth"><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:birth"></xsl:value-of></xsl:variable>
                    <xsl:variable name="death"><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:death"></xsl:value-of></xsl:variable>
                    
                    <xsl:if test="$birth != ''">
                        <strong>Birth:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select="$birth"/>
                    </xsl:if>
                    
                    <xsl:if test="$death != ''">
                        <xsl:text>&#160;</xsl:text><strong>Death:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select="$death"/>
                    </xsl:if>

                    <xsl:if test="$personCert = 'low'">
                        <strong>Certainty: Possible</strong>
                    </xsl:if>
                    <xsl:if test="$personCert = 'medium'">
                        <strong>Certainty: Probable</strong>
                    </xsl:if>
                </div>
             </xsl:when>
         <xsl:otherwise>
             <div class="hiddenDiv">
                 <strong class="unknown">Unknown</strong>
             </div>
         </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template name="getGroup" match="//tei:name[@type = 'group']">
        <xsl:param name="text" select="@ref"/>
        <xsl:param name="separator" select="' '"/> 
        <xsl:variable name="first"><xsl:value-of select="substring-before($text,' ')"/></xsl:variable>
        
        <xsl:if test="string-length($text)">
            <xsl:variable name="groupPerson"><xsl:value-of select="substring-before(concat($text,' '),' ')"/></xsl:variable>
            <xsl:choose>
            <xsl:when test="$groupPerson = $first">
                <a class="group-note">
                    <xsl:apply-templates/>
                </a>
                <div class="group-div">
                    <div class="group-container"><span class="x-button">x</span>
                    <xsl:call-template name="getGroup">
                        <xsl:with-param name="text" select="substring-after($text, ' ')"/>
                    </xsl:call-template>
                    <xsl:variable name="personPTR" select="$groupPerson"/>
                    <xsl:variable name="person"><xsl:value-of select="substring-after($personPTR,'psn:')"/></xsl:variable>
                    <xsl:variable name="personCert"><xsl:value-of select="@cert"/></xsl:variable>
                    <xsl:if test="$personCert = 'unknown'">
                            <strong class="unknown">Unknown</strong>
                        
                    </xsl:if>
                    <xsl:choose>
                        <xsl:when test="$person != 'unknown'">
                            <div class="person"><h3>
                                    <xsl:element name="a">
                                        <xsl:attribute name="href">
                                            <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($person,'_')"/>
                                        </xsl:attribute>
                                        <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                                        <xsl:attribute name="class">
                                            <xsl:text>more</xsl:text>
                                        </xsl:attribute>
                                        <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:forename[@type = 'first']"></xsl:value-of>
                                        <xsl:text>&#160;</xsl:text>
                                        <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:surname[@type = 'last' or 'maiden']"></xsl:value-of>
                                    </xsl:element>
                            </h3></div>
                                <strong>Birth:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:birth"></xsl:value-of>
                            <xsl:text>&#xa;</xsl:text><xsl:text>&#160;</xsl:text>
                            <strong>Death:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:death"></xsl:value-of><xsl:text>&#160;</xsl:text>
                                <xsl:if test="$personCert = 'low'">
                                    <strong>Certainty: Possible</strong>
                                </xsl:if>
                                <xsl:if test="$personCert = 'medium'">
                                    <strong>Certainty: Probable</strong>
                                </xsl:if>
              
                            
                        </xsl:when>
                        <xsl:otherwise>
                            
                                <br /><strong class="unknown">Unknown</strong>
            
                        </xsl:otherwise>
                    </xsl:choose>
                </div>
                </div>
            </xsl:when>
            <xsl:otherwise>
                <xsl:call-template name="getGroup">
                    <xsl:with-param name="text" select="substring-after($text, ' ')"/>
                </xsl:call-template>
                <xsl:variable name="personPTR" select="$groupPerson"/>
                <xsl:variable name="person"><xsl:value-of select="substring-after($personPTR,'psn:')"/></xsl:variable>
                <xsl:variable name="personCert"><xsl:value-of select="@cert"/></xsl:variable>
                <xsl:if test="$personCert = 'unknown'">
                    
                    <br /><strong class="unknown">Unknown</strong>
                   
                </xsl:if>
                <xsl:choose>
                    <xsl:when test="$person != 'unknown'">
                        <div class="person"><h3>
                            <xsl:element name="a">
                                <xsl:attribute name="href">
                                    <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($person,'_')"/>
                                </xsl:attribute>
                                <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                                <xsl:attribute name="class">
                                    <xsl:text>more</xsl:text>
                                </xsl:attribute>
                                <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:forename[@type = 'first']"></xsl:value-of>
                                <xsl:text>&#160;</xsl:text>
                                <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:surname[@type = 'last' or 'maiden']"></xsl:value-of>
                            </xsl:element>
                        </h3></div>
                        <strong>Birth:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:birth"></xsl:value-of>
                        <xsl:text>&#xa;</xsl:text><xsl:text>&#160;</xsl:text>
                        <strong>Death:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:death"></xsl:value-of><xsl:text>&#160;</xsl:text>
                        <xsl:if test="$personCert = 'low'">
                            <strong>Certainty: Possible</strong>
                        </xsl:if>
                        <xsl:if test="$personCert = 'medium'">
                        <strong>Certainty: Probable</strong>
                        </xsl:if>
                    </xsl:when>
                    <xsl:otherwise>
                        
                            <br /><strong class="unknown">Unknown</strong>
                     
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:otherwise>
          </xsl:choose> 
       </xsl:if>
    </xsl:template>
    
    <xsl:template name="getScribe" match="tei:text/tei:body/tei:ab/tei:handShift | tei:seg/tei:handShift">
        <a class="hand-note"><img src="../sites/all/icons/Seward-Icons-Pen.svg" alt="seward-pen"/>
            <xsl:apply-templates/>
        </a>
        <xsl:variable name="personPTR" select="@scribe"/>
        <xsl:variable name="person"><xsl:value-of select="substring-after($personPTR,'psn:')"/></xsl:variable>
        <xsl:variable name="personCert" select="@cert"/>
        <xsl:if test="$personCert = 'unknown'">
            <div class="hiddenDiv">
                <strong class="unknown">Unknown</strong>
            </div>
        </xsl:if>
        <xsl:choose>
            <xsl:when test="$person != 'unknown'">
                <div class="hiddenDiv">
                    <div class="person"><span class="x-button">x</span><h3>
                        <xsl:element name="a">
                            <xsl:attribute name="href">
                                <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($person,'_')"/>
                            </xsl:attribute>
                            <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                            <xsl:attribute name="class">
                                <xsl:text>more</xsl:text>
                            </xsl:attribute>
                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:forename[@type = 'first']"></xsl:value-of>
                            <xsl:text>&#160;</xsl:text>
                            <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:persName/tei:surname[@type = 'last' or 'maiden']"></xsl:value-of>
                        </xsl:element>
                    </h3></div>
                    <xsl:variable name="birth"><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:birth"></xsl:value-of></xsl:variable>
                    <xsl:variable name="death"><xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/persons.xml')//tei:text/tei:body/tei:div/tei:listPerson/tei:person[@xml:id = $person]/tei:death"></xsl:value-of></xsl:variable>
                    
                    <xsl:if test="$birth != ''">
                        <strong>Birth:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select="$birth"/>
                    </xsl:if>
                    
                    <xsl:if test="$death != ''">
                        <xsl:text>&#160;</xsl:text><strong>Death:</strong><xsl:text>&#160;</xsl:text><xsl:value-of select="$death"/>
                    </xsl:if>
                    
                    <xsl:if test="$personCert = 'low'">
                        <strong>Certainty: Possible</strong>
                    </xsl:if>
                    <xsl:if test="$personCert = 'medium'">
                        <strong>Certainty: Probable</strong>
                    </xsl:if>
                </div>
            </xsl:when>
            <xsl:otherwise>
                <div class="hiddenDiv">
                    <strong class="unknown">Unknown</strong>
                </div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template name="getPlace" match="//tei:placeName[not(parent::tei:correspAction)]">
        <a class="note">
            <xsl:apply-templates/>
        </a>
        <xsl:variable name="placePTR" select="@ref"/>
        <xsl:variable name="place"><xsl:value-of select="substring-after($placePTR,'pla:')"/></xsl:variable>
        <xsl:choose>
           <xsl:when test="$place != 'unknown'">
               <div class="hiddenDiv">
                   <div class="person"><span class="x-button">x</span>
                       <h3>
                           <xsl:element name="a">
                               <xsl:attribute name="href">
                                   <xsl:text>person-public-fields/</xsl:text><xsl:value-of select="substring-after($place,'_')"/>
                                   
                               </xsl:attribute>
                               <xsl:attribute name="target"><xsl:text>_blank</xsl:text></xsl:attribute>
                               <xsl:attribute name="class">
                                   <xsl:text>more</xsl:text>
                               </xsl:attribute>
                               <xsl:value-of select = "document('/usr/local/apache2/htdocs/tei/places.xml')//tei:text/tei:body/tei:div/tei:listPlace/tei:place[@xml:id = $place]/tei:head"></xsl:value-of>
                           </xsl:element>
                        </h3>
                   </div>    
               </div>
           </xsl:when>
            <xsl:otherwise>
                <div class="hiddenDiv">
                    <strong class="unknown">Unknown</strong>
                </div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
</xsl:stylesheet>
