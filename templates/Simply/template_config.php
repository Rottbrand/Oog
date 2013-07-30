<?php

/***  Some DESIGN-CONFIGS of the Template  ***/


/*
 * Oog Photo-Gallery v3.2
 * http://www.oog-gallery.de/ 
 * Copyright (C) 2011 Torben Rottbrand
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */


// Contact our forum, if you have problems with modifying configs: forum.oog-gallery.de !

// Note: All variables beginning with $lang_ are placeholders for language. For changing have a look in the language files in folder lang.

/////////////////////////////////////////////////////////////Configs of Thumbnails/////////////////////////////////////////////////////////////  

$max_thumbs = 36; // Maximal number of thumbnails on one page of an album. Set 0 for unlimited number of thumbnails.

$thumbquality = 100; // Expects: 0-100  Set 0 for lowest quality and 100 for highest. Lower quality is faster.

$max_thumbwidthheight = 124; // Maximal height/width of a thumbnail.
 
// The following four variables define the HTML-Code that wraps up the different thumbnail kinds.
// (associating CSS can be found in template_style.css) The following expressions will be replaced by Oog:
// {THUMBNAIL}             = thumbnail (important!)
// {NAME}                  = name of file without extension
// {EXTENSION}             = extension of file
// {DESCRIPTION}           = description from pictures EXIF-Header/audiofiles id3/videos .....
// Also you can use:
// {ALBUMLINK}             = link to album
// {ALBUMTITLE}            = title of album
// {ALBUMVIEWS}            = views of album
// {ALBUMDATE}             = date of album# 

$thumbnail_wrapper = '<div class="thumbnail_shad">
<div class="thumbnail_border">
{THUMBNAIL}
</div>
</div>
';

$found_thumbnail_wrapper = '<div class="thumbnail_shad">
<div class="thumbnail_border">
{THUMBNAIL}
</div>
</div>
<div class="thumbnail_legend" style="width: ' . ($max_thumbwidthheight + 36) . 'px;">
<p><a href="{ALBUMLINK}" title="' . $lang['template']['album_showpage']['1'] . '">{ALBUMTITLE} &raquo;</a> {NAME}.{EXTENSION}</p>
</div>
';

$top_most_viewed_thumbnail_wrapper = '<div class="most_viewed_albums">
<a href="{ALBUMLINK}">{THUMBNAIL}</a><br />
<a href="{ALBUMLINK}" title="' . $lang['template']['album_showpage']['1'] . '">{ALBUMTITLE}</a>
<p style="margin: 0px;">(' . $lang['template']['album_showpage']['2'] . ' {ALBUMVIEWS})</p>
</div>
';

$top_newest_thumbnail_wrapper = '<div class="newest_albums">
<a href="{ALBUMLINK}">{THUMBNAIL}</a><br />
<a href="{ALBUMLINK}" title="' . $lang['template']['album_showpage']['1'] . '">{ALBUMTITLE}</a>
<p style="margin: 0px;">(' . $lang['template']['album_showpage']['3'] . ' {ALBUMDATE})</p>
</div>
';

/////////////////////////////////////////////////////////////Configs of the Start-Page/////////////////////////////////////////////////////////////

$top_most_viewed_albums = 3; // Maximal number of most viewed albums shown on start-page. (Set 0 for dont showing)

$top_newest_albums = 3;      // Maximal number of newest albums shown on start-page. (Set 0 for dont showing)

$max_thumbwidthheight_on_startpage = 124;

// To the following variable you can add some XHTML-Code wich will be shown on the start-page. For example a welcome address or an photo.
// {MOST-VIEWED-ALBUMS} and {NEWEST-ALBUMS} will be replaced with the top of the most viewed and newest albums.

$code_on_startpage = '<h1>' . $lang['template']['album_showpage']['4'] . '</h1>
<h2>' . $lang['template']['album_showpage']['5'] . ' '. $top_most_viewed_albums . ':</h2>{MOST-VIEWED-ALBUMS}
<h2>' . $lang['template']['album_showpage']['6'] . ' '. $top_newest_albums . ':</h2>{NEWEST-ALBUMS}';

/////////////////////////////////////////////////////////////More Configs/////////////////////////////////////////////////////////////

$show_views = 'yes'; // Expects: yes or no  Set yes for counting the views of your albums, no for dont showing and counting views.

$separator = ' &raquo; ';

?>