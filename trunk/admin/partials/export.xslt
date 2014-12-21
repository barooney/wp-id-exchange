<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml"/>

	<xsl:template match="indesign-export">
		<indesign-export>
			
			<xsl:for-each select="post">
				<xsl:copy>
					<xsl:attribute name="id">
						<xsl:value-of select="@id" />
					</xsl:attribute>
					<post_title><xsl:value-of select="post_title" /></post_title><xsl:text disable-output-escaping="yes">
<!-- create a new line --></xsl:text>
					<post_date><xsl:value-of select="post_date" /></post_date><xsl:text disable-output-escaping="yes">
<!-- create a new line --></xsl:text>
					<post_author><xsl:value-of select="post_author" /></post_author><xsl:text disable-output-escaping="yes">
<!-- create a new line --></xsl:text>
					<post_content>
						<xsl:for-each select="post_content//p">
							<xsl:copy-of select="current()" />
								<!-- <xsl:if test="position() != last()"> -->
									<xsl:text disable-output-escaping="yes">
<!-- create a new line --></xsl:text>
								<!-- </xsl:if> -->
						</xsl:for-each>
					</post_content>
				</xsl:copy>
			</xsl:for-each>
	     </indesign-export>
	</xsl:template>
</xsl:stylesheet>