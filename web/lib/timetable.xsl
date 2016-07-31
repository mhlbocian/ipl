<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html"/>
    <xsl:template match="/">
        <html>
            <head>	
                <meta charset="UTF-8"/>
                <title>Internetowy Plan Lekcji - klasa 
                    <xsl:value-of select="@class"/>
                </title>
                <style>
                    @import url('lib/css/style.css');
                    body{
                    margin: 10px;
                    }
                </style>
            </head>
            <body>
                <table class="przed" align="center" style="font-size: 9pt; width: auto;">
                    <thead style="background: #ccccff;">
                        <tr class="a_odd">
                            <td colspan="7" style="text-align: center">
                                <p>
                                    <span class="pltxt">
                                        <xsl:value-of select="timetable/@class"/>
                                    </span>
                                </p>
                            </td>
                        </tr>
                    </thead>
                    <tr>
                        <xsl:for-each select="timetable/day">
                            <td>
                                <xsl:value-of select="./@name"/>
                            </td>
                        </tr>
                        <tr>
                            <xsl:for-each select="./lesson">
                                
                                <td>aa</td>
                            </xsl:for-each>
                        </tr>
                    </xsl:for-each>
                    
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
