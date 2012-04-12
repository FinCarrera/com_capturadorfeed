<?php
/**
* FeedGator - Aggregate RSS newsfeed content into a Joomla! database
* @version 2.3.4
* @package FeedGator
* @author Original author Stephen Simmons
* @now continued and modified by Matt Faulds, Remco Boom & Stephane Koenig and others
* @email mattfaulds@gmail.com
* @Joomla 1.5 Version by J. Kapusciarz (mrjozo)
* @copyright (C) 2005 by Stephen Simmons - All rights reserved
* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
*
**/

defined('_JEXEC') or die('Restricted access'); ?>

<div class="fgnarrow">
	<div class="fglogo"></div>
	<h1>FeedGator RSS news feed aggregator component for Joomla!</h1>

	<?php FeedgatorHelper::renderVersionUpdatePanel($this->version_data); ?>
	<p><strong>FeedGator</strong> imports RSSfeeds into your Joomla! database as regular content items, so you can get more control of the syndicated content on your site. Display RSS content in blog format, or any other method supported by Joomla! Turn your site into a sophisticated news reader.<br />
	This component (or derivatives) is what drives the news section of many Joomla! websites. FeedGator has lots of features to give you the power to manipulate the imported content in useful ways.</p>
	<p>Now maintained by Matthew Faulds (<span class="blue"><a href="http://www.trafalgardesign.com">Trafalgar Design</a></span>)</p>
	<br />
	
	<h3 class="blue">Features Include</h3>
	<div id="featureleft">
		<ul>
			<li>Native Joomla! 1.5 and 1.6 support</li>
			<li>RSS feed content can be stored in any type of content within Joomla via the new internal plugins: com_content, <strong class="red"><a href="http://getk2.org/" target="_blank">K2</a></strong> and <strong class="red"><a href="http://www.kunena.com/" target="_blank">Kunena</a></strong></li>
			<li><strong>Robust RSS fetching</strong> FeedGator uses <span class="blue"><a href="http://simplepie.org" target="_blank">SimplePie</a></span> to process feeds. It supports many different feed types including: RSS 0.90 to 2.0, Atom 0.3 and 1.0 and namespaced elements like Dublin Core, iTunes RSS and XHTML 1.0. See <span class="blue"><a href="http://simplepie.org/wiki/faq/what_versions_of_rss_or_atom_do_you_support" target="_blank">SimplePie FAQ</a></span></li>
			<li><strong>Full text importing</strong>, even if not included in the source feed using the <span class="blue"><a href="http://lab.arc90.com/experiments/readability/">Readability</a></span> port by <span class="blue"><a href="http://fivefilters.org/content-only/">FiveFilters.org</a></span></li>
			<li>Robust duplicate handling</li>
			<li>Import logging</li>
			<li>Auto-publishing imported content</li>
			<li>Access control for imported content</li>
			<li>Ability to specify the number of days content remains published</li>
			<li style="list-style-type:none;">&nbsp;</li>
			<li>Intelligent upgrade - simply install on top of any version of FeedGator higher than 2.0!</li>
		</ul>
	</div>
	<div id="featureright">
		<ul>
			<li><strong>Feed filtering</strong> based on whitelist/blacklist set for each feed</li>
			<li><strong class="blue"><a href="http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/">htmLawed</a></strong> (X)HTML filter, processor, purifier, sanitizer and beautifier for imported text</li>
			<li><strong>HTML tag</strong> filtering</li>
			<li>Optional built-in automatic keyword tagging</li>
			<li>Optional <strong>automatic metadata generation</strong> using <span class="blue"><a href="http://www.trafalgardesign.com">Trafalgar Design's</a></span> <span class="blue"><a href="http://extensions.joomla.org/extensions/news-display/related-items/5701">Add Keywords</a></span> plugin</li>
			<li>Optional automatic Term Extraction (Tagging) using <span class="blue"><a href="http://developer.yahoo.com/search/content/V1/termExtraction.html">Yahoo's term extraction API</a></span></li>
			<li>Optional trackback links to the original content</li>
			<li>Optional trackback link accessibility compliance</li>
			<li>Optional automatic feed caching</li>
			<li>Optional <strong>automated imports</strong> using cron</li>
			<li>Optional <strong>automated imports</strong> using pseudo-cron Joomla system plugin</li>
			<li>Easy to read HTML reports online or via email</li>
		</ul>
	</div>
	<div style="clear:both;">
	<br />
	<br />
	
	
	<h3 class="blue">Release Notes</h3>
	<?php if(!$page = FeedgatorUtility::getUrl('http://www.trafalgardesign.com/index.php?view=article&id=45&tmpl=component',$this->fgParams->get('scrape_type'))) {
		echo 'The release notes require an internet connection and cURL to be active on your PHP installation.';
	} else {
		$regex = '#<div id="page">([\s\S]*?)<\/div>#';
		preg_match($regex,$page,$matches);
		echo $matches[1];
	} ?>
</div>