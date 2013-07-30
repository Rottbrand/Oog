<?php

/*
 * Oog Photo-Gallery v3.2 Beta8
 * http://www.oog-gallery.de/ 
 * Copyright (C) 2012 Torben Rottbrand
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

session_start();

if(!isset($_POST['try'])) $_POST['try'] = "";
if(!isset($_POST['user_name'])) $_POST['user_name'] = "";
if(!isset($_POST['user_password'])) $_POST['user_password'] = "";
if(!isset($_GET['album'])) $_GET['album'] = "";
if(!isset($_GET['timeout'])) $_GET['timeout'] = "";
if(!isset($_GET['search'])) $_GET['search'] = "";
if(!isset($_GET['language'])) $_GET['language'] = "";

@chmod("../core/", 0775);
@chmod("../templates/", 0775);
@chmod("../lang/", 0775);
@chmod("../gallery_config.php", 0775);

include "../gallery_config.php";

// find out language //
session_save_path("sessions/");
$user_language = $default_language;
$langs = array("de", "en");
for($i = 0; count($langs) > $i; $i++) {
  if($_GET['language'] == $langs[$i]) {
    $user_language = $langs[$i];
    setcookie('OogGalleryLanguage', $user_language, time() + 60 * 60 * 24 * 30); 
    break;
  }
}
if(!in_array($_GET['language'], $langs)) {
  $cookie = (isset($_COOKIE['OogGalleryLanguage'])) ? $_COOKIE['OogGalleryLanguage'] : "";
  $langBrowser = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : "";
  for($i = 0; count($langs) > $i; $i++) {
    if($cookie == $langs[$i]) {
      $user_language = $langs[$i];
      break;
    }
  }
  if(!in_array($cookie, $langs)) {
    for($i = 0; count($langs) > $i; $i++) {
      if(substr($langBrowser, 0, 2) == $langs[$i]) {
        $user_language = $langs[$i];
        break;
      }
    }
  }
}
include "../lang/lang_" . $user_language . ".php";

include "../templates/" . $template . "/template_config.php";

include "functionslibrary.php";


// if no admin is defined
check_admin("../users/user.db");



// store generated user login form in $user_login and rights of user in $user_rights //
$user_name = session_manager($_POST['try'], $_POST['user_name'], $_POST['user_password'], "../users/user.db", "sessions/");
$user_rights = get_user_rights($user_name, "../users/user.db", $rightsVisitor);
$user_login = generate_user_login($user_name, $_GET['timeout'], $_GET['album'], $user_rights, $lang['core'], $user_language);

// store visible albumpaths in an array //
$hidden_albums_access = (in_array('hidden_albums_access', $user_rights) OR in_array('admin', $user_rights)) ? true : false;
$visible_albumpaths = get_albumpaths("../" . $contentRootDir, $hidden_albums_access, $sorting_menu, $htaccess_protection);

// store generated menu in $menu //
$visible_structure = generate_structure($visible_albumpaths);
$menu = (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "<div id=\"folder_actions\"></div>
<img id=\"arrow_moveTo\" src=\"admin/images/dragDrop_ind2.gif\" style=\"position: absolute; top: 0; left: 0; z-index: 9999; display: none;\" />
<ul id=\"dhtmlgoodies_tree2\"  class=\"sf-menu sf-vertical\"></ul>
" : "<ul class=\"sf-menu sf-vertical\">
<li><a href=\"showalbum.php\">" . $lang['template']['button_home'] . "</a></li>
" . generate_menu($visible_structure, "", $user_rights, "../" . $contentRootDir) . "
</ul>";

  $main_content = (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "<noscript>
<div style=\"background-color: #FFBBBB; border: solid 2px #FF9999; margin-bottom: 12px; padding: 10px 15px;\">
" . $lang['admin']['noscript_error'] . "
</div>
</noscript>
<div id=\"oogErrorBox\" class=\"content\" style=\"background-color: #FFBBBB; border: solid 2px #FF9999; margin-bottom: 12px; padding: 10px 15px; display: none;\"></div>" : ""; 

$important_includes = "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"../templates/" . $template . "/imgdata/favicon.ico\" />
<script type=\"text/javascript\" src=\"jquery-1.7.1.min.js\"></script>
<script type=\"text/javascript\" src=\"jquery.json-2.3.js\"></script>
<link type=\"text/css\" rel=\"stylesheet\" href=\"../templates/" . $template . "/menu.css\" />
<link type=\"text/css\" rel=\"stylesheet\" href=\"../templates/" . $template . "/menu-vertical.css\" />
<link type=\"text/css\" rel=\"stylesheet\" href=\"../templates/" . $template . "/template_style.css\" />
<link type=\"text/css\" rel=\"stylesheet\" href=\"../templates/" . $template . "/lightbox_style.css\" />
<script type=\"text/javascript\" src=\"superfish.js\"></script>
<script type=\"text/javascript\" src=\"flowplayer-3.1.4.min.js\"></script>
<script type=\"text/javascript\" src=\"../templates/" . $template . "/lightbox_config.js\"></script>
<script type=\"text/javascript\" src=\"lightbox.php?language=" . $user_language . "\"></script>
<script type=\"text/javascript\">
initOogLightbox('requests.php?language=" . $user_language . "&action=', 'flowplayer-3.1.5.swf');
</script>
<script type=\"text/javascript\">

function loadFewThumbs(part) {

  var allThumbs = document.getElementsByClassName('thumbnail');
  var requestedThumbs = new Array();
  var maxIndexRequestedThumbs = (part * " . $thumbsLoadingParallel . " + " . $thumbsLoadingParallel . " < allThumbs.length) ? part * " . $thumbsLoadingParallel . " + " . $thumbsLoadingParallel . " : allThumbs.length ;

  for(var i = part * " . $thumbsLoadingParallel . "; i < maxIndexRequestedThumbs; i++) requestedThumbs.push({path: allThumbs[i].dataset.path, filename: allThumbs[i].dataset.filename});

  $.post('requests.php?action=getThumbs', {'requestedThumbs': requestedThumbs},
    function(response) {

      receivedThumbs = JSON.parse(response);
      
      j = 0;
      for(var i = part * " . $thumbsLoadingParallel . "; i < part * " . $thumbsLoadingParallel . " + receivedThumbs.length; i++) {
      
        allThumbs[i].childNodes[0].childNodes[0].childNodes[0].childNodes[0].src = 'data:image/jpeg;base64,' + receivedThumbs[j];  

        j++;
      
      }
      
    }
  );

}

function loadVisibleThumbs() {

  var allThumbs = document.getElementsByClassName('thumbnail');
  var allParts = Math.floor(allThumbs.length / " . $thumbsLoadingParallel . ") + 1;
 
  for(var part = 0; part < allParts; part++) {

    if(!allThumbs[part * " . $thumbsLoadingParallel . "].dataset.loaded) {

      allThumbs[part * " . $thumbsLoadingParallel . "].id = 'firstRequestedThumb';
      posLastRequestedThumb = ((part + 1) * " . $thumbsLoadingParallel . " - 1 <= allThumbs.length - 1) ? (part + 1) * " . $thumbsLoadingParallel . " - 1 : allThumbs.length - 1;
      allThumbs[posLastRequestedThumb].id = 'lastRequestedThumb';

      firstThumbOffset = $('#firstRequestedThumb').offset();
      lastThumbOffset = $('#lastRequestedThumb').offset();
    
      if(firstThumbOffset.top <= $(document).scrollTop() + window.innerHeight && $(document).scrollTop() <= lastThumbOffset.top + $('#lastRequestedThumb').height()) {

        loadFewThumbs(part);
        allThumbs[part * " . $thumbsLoadingParallel . "].dataset.loaded = 'true';
        
      }
      
      $('#firstRequestedThumb').attr('id', null);
      allThumbs[part * " . $thumbsLoadingParallel . "].removeAttribute('id', 0);
      $('#lastRequestedThumb').attr('id', null);
      allThumbs[posLastRequestedThumb].removeAttribute('id', 0);
    
    }
  
  }

}

windowScrollTop = 0;
window.setInterval(function () {
      
  if(windowScrollTop == $(document).scrollTop() && document.getElementsByClassName('thumbnail').length > 0) loadVisibleThumbs();
  windowScrollTop = $(document).scrollTop();
  
}, 250);

</script>";

// if user sended search request and is allowed to search //
if((in_array('allow_searching', $user_rights) OR in_array('admin', $user_rights)) AND $_GET['search'] != "") {

  $language_switcher = generate_language_switcher($user_language, $langs, $template, "showalbum.php?search=" . $_GET['search'] . "&");


  $sort_options = "";
  $start_slideshow = "";


  $foundFiles = search_files($_GET['search'], $visible_albumpaths, $max_thumbwidthheight, $thumbquality);

  // define $main_content //
  $main_content .= (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "<div id=\"file_actions\">
  <img id=\"btn_rename_file\" style=\"opacity: 0.3;\" src=\"admin/images/rename.png\" title=\"" . $lang['admin']['file']['btn_rename'] . "\" />
  <img id=\"btn_delete_file\" style=\"opacity: 0.3;\" src=\"admin/images/delete.png\" title=\"" . $lang['admin']['file']['btn_delete'] . "\" />
  <img id=\"btn_save_file\" style=\"opacity: 0.3;\" src=\"admin/images/save.png\" title=\"" . $lang['admin']['file']['btn_download'] . "\" />
  <img id=\"btn_show_file\" style=\"margin-left: 13px; opacity: 0.3;\" src=\"admin/images/open.png\" title=\"" . $lang['admin']['file']['btn_open'] . "\" />
  <img id=\"btn_description_file\" style=\"opacity: 0.3;\" src=\"admin/images/editDescription.png\" title=\"" . $lang['admin']['file']['btn_editDesc'] . "\" />
  <img id=\"btn_comments_file\" style=\"opacity: 0.3;\" src=\"admin/images/editComments.png\" title=\"" . $lang['admin']['file']['btn_editCom'] . "\" />
  <img id=\"btn_rotate_left\" style=\"opacity: 0.3;\" src=\"admin/images/rotateL.png\" title=\"" . $lang['admin']['file']['btn_rotateL'] . "\" />
  <img id=\"btn_rotate_right\" style=\"opacity: 0.3;\" src=\"admin/images/rotateR.png\" title=\"" . $lang['admin']['file']['btn_rotateL'] . "\" />
  <span style=\"float: right;\">
    <img id=\"btn_select_all\" style=\"opacity: 0.3;\" src=\"admin/images/select_all.png\" title=\"" . $lang['admin']['file']['btn_select_all'] . "\" />
    <img id=\"btn_unselect_all\" style=\"opacity: 0.3;\" src=\"admin/images/unselect_all.png\" title=\"" . $lang['admin']['file']['btn_unselect_all'] . "\" />
  </span>
</div>

<ul id=\"album_mediafiles\">
  <p>" . generate_main_content("", $max_thumbwidthheight, $thumbquality, $found_thumbnail_wrapper, $sorting_content, $user_rights, $foundFiles, $lang['core'], $template, $separator) . "</p>
</ul>\n" : "<ul id=\"album_mediafiles\">" . generate_main_content("", $max_thumbwidthheight, $thumbquality, $found_thumbnail_wrapper, $sorting_content, $user_rights, $foundFiles, $lang['core'], $template, $separator) . "</ul><div style=\"clear: both;\"></div>";

  $title = (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "<span style=\"margin-left: 6px;\" id=\"folder_path\">" . $lang['core']['album_search']['1'] . " " . count($foundFiles) . " " . $lang['core']['album_search']['2'] . "</span>\n" : $lang['core']['album_search']['1'] . " " . count($foundFiles) . " " . $lang['core']['album_search']['2'];
  
  $albuminfo = "";
  
  $views = "";
  
  if(in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) $important_includes .= "<script type=\"text/javascript\">
window.onload = function() {
//  init_albums_management();
}</script>\n";

} else {

  // build $albumpath that user wants to see //
  $albumpath = "../" . $contentRootDir . urldecode($_GET['album']) . "/"; 
  
  // if user is allowed to see albumpath //
  if(in_array($albumpath, $visible_albumpaths) AND $_GET['album'] != "") {
  
    $language_switcher = generate_language_switcher($user_language, $langs, $template, "showalbum.php?album=" . $_GET['album'] . "&");
  
    if(in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) {
      $views = "<span id=\"outType\"></span><br />
<span id=\"outModificationDate\"></span><br />
<span id=\"outViews\"></span>";
    } else {
      $views = ($show_views == "yes") ? $lang['core']['album']['1'] . " " . folderviews(dirname(__FILE__) . "/" . $albumpath, 1) : "";
    }
  
    $title = (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "<span style=\"margin-left: 6px;\" id=\"folder_path\"></span>\n" : create_albumtitle($albumpath, $separator);
  
    $albuminfo = read_albuminfo($albumpath);


    $sort_options = "";
    $start_slideshow = "";


    $main_content .= (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "<div id=\"file_actions\">
  <input id=\"inpUpload\" type=\"file\" multiple>
  <div id=\"statusUpload\"></div>

  <img id=\"btn_rename_file\" style=\"opacity: 0.3;\" src=\"admin/images/rename.png\" title=\"" . $lang['admin']['file']['btn_rename'] . "\" />
  <img id=\"btn_delete_file\" style=\"opacity: 0.3;\" src=\"admin/images/delete.png\" title=\"" . $lang['admin']['file']['btn_delete'] . "\" />
  <img id=\"btn_save_file\" style=\"opacity: 0.3;\" src=\"admin/images/save.png\" title=\"" . $lang['admin']['file']['btn_download'] . "\" />
  <img id=\"btn_show_file\" style=\"margin-left: 13px; opacity: 0.3;\" src=\"admin/images/open.png\" title=\"" . $lang['admin']['file']['btn_open'] . "\" />
  <img id=\"btn_description_file\" style=\"opacity: 0.3;\" src=\"admin/images/editDescription.png\" title=\"" . $lang['admin']['file']['btn_editDesc'] . "\" />
  <img id=\"btn_comments_file\" style=\"opacity: 0.3;\" src=\"admin/images/editComments.png\" title=\"" . $lang['admin']['file']['btn_editCom'] . "\" />
  <img id=\"btn_rotate_left\" style=\"opacity: 0.3;\" src=\"admin/images/rotateL.png\" title=\"" . $lang['admin']['file']['btn_rotateL'] . "\" />
  <img id=\"btn_rotate_right\" style=\"opacity: 0.3;\" src=\"admin/images/rotateR.png\" title=\"" . $lang['admin']['file']['btn_rotateL'] . "\" />
  <span style=\"float: right;\">
    <img id=\"btn_select_all\" style=\"opacity: 0.3;\" src=\"admin/images/select_all.png\" title=\"" . $lang['admin']['file']['btn_select_all'] . "\" />
    <img id=\"btn_unselect_all\" style=\"opacity: 0.3;\" src=\"admin/images/unselect_all.png\" title=\"" . $lang['admin']['file']['btn_unselect_all'] . "\" />
  </span>
</div>

<ul id=\"album_mediafiles\">
  <p>" . $lang['admin']['album_mediafile']['1'] . "</p>
</ul>\n" : "<ul id=\"album_mediafiles\">" . generate_main_content($albumpath, $max_thumbwidthheight, $thumbquality, $thumbnail_wrapper, $sorting_content, $user_rights, false, $lang['core'], $template, $separator) . "</ul><div style=\"clear: both;\"></div>";

    if(in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) $important_includes .= "<script type=\"text/javascript\">
window.onload = function() {
  init_albums_management('Menu/" . urldecode($_GET['album']) . "');
}</script>\n";

  } else {
  // not allowed to see or startpage was requested //
    
    $language_switcher = generate_language_switcher($user_language, $langs, $template, "showalbum.php?");
  
    $title = (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "<span style=\"margin-left: 6px;\" id=\"folder_path\"></span>\n" : $lang['template']['button_home'];
  
    $albuminfo = "";
  
    if(in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) {
      $views = "<span id=\"outType\"></span><br />
<span id=\"outModificationDate\"></span><br />
<span id=\"outViews\"></span>";
    } else {
      $views = ($show_views == "yes") ? $lang['core']['album']['1'] . " " . folderviews(dirname(__FILE__) . "/" . $albumpath, 1) : "";
    }

    $sort_options = "";
    $start_slideshow = "";
  
    $main_content .= (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "<div id=\"file_actions\">
  <img id=\"btn_rename_file\" style=\"opacity: 0.3;\" src=\"admin/images/rename.png\" title=\"" . $lang['admin']['file']['btn_rename'] . "\" />
  <img id=\"btn_delete_file\" style=\"opacity: 0.3;\" src=\"admin/images/delete.png\" title=\"" . $lang['admin']['file']['btn_delete'] . "\" />
  <img id=\"btn_save_file\" style=\"opacity: 0.3;\" src=\"admin/images/save.png\" title=\"" . $lang['admin']['file']['btn_download'] . "\" />
  <img id=\"btn_show_file\" style=\"margin-left: 13px; opacity: 0.3;\" src=\"admin/images/open.png\" title=\"" . $lang['admin']['file']['btn_open'] . "\" />
  <img id=\"btn_description_file\" style=\"opacity: 0.3;\" src=\"admin/images/editDescription.png\" title=\"" . $lang['admin']['file']['btn_editDesc'] . "\" />
  <img id=\"btn_comments_file\" style=\"opacity: 0.3;\" src=\"admin/images/editComments.png\" title=\"" . $lang['admin']['file']['btn_editCom'] . "\" />
  <img id=\"btn_rotate_left\" style=\"opacity: 0.3;\" src=\"admin/images/rotateL.png\" title=\"" . $lang['admin']['file']['btn_rotateL'] . "\" />
  <img id=\"btn_rotate_right\" style=\"opacity: 0.3;\" src=\"admin/images/rotateR.png\" title=\"" . $lang['admin']['file']['btn_rotateL'] . "\" />
  <span style=\"float: right;\">
    <img id=\"btn_select_all\" style=\"opacity: 0.3;\" src=\"admin/images/select_all.png\" title=\"" . $lang['admin']['file']['btn_select_all'] . "\" />
    <img id=\"btn_unselect_all\" style=\"opacity: 0.3;\" src=\"admin/images/unselect_all.png\" title=\"" . $lang['admin']['file']['btn_unselect_all'] . "\" />
  </span>
</div>

<ul id=\"album_mediafiles\">
  <p>" . $lang['admin']['album_mediafile']['1'] . "</p>
</ul>\n" : generate_startpage($show_views, $top_most_viewed_albums, $top_newest_albums, $code_on_startpage, $visible_albumpaths, $sorting_content, $max_thumbwidthheight_on_startpage, $top_most_viewed_thumbnail_wrapper, $top_newest_thumbnail_wrapper, $user_rights, $max_thumbwidthheight, $thumbquality, $lang['core'], $template, $separator) . "<div style=\"clear: both;\"></div>";

    if(in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) $important_includes .= "<script type=\"text/javascript\">
window.onload = function() {
  init_albums_management('Menu');
}</script>\n";

  }

}

$language_switcher = "<div id=\"language_switcher\">" . $language_switcher . "</div>";

// if user is allowed to search //
if(in_array('allow_searching', $user_rights) OR in_array('admin', $user_rights)) {

  // show search_form //
  $search_form = "<form method=\"GET\" action=\"showalbum.php?album=" . $_GET['album'] . "\" id=\"search_form\">
<input type=\"input\" name=\"search\" class=\"input\" />
<input type=\"submit\" value=\"" . $lang['core']['album_search']['3'] . "\" class=\"button\" />
</form>\n";
  
} else {
  $search_form = "";
}

$important_includes .= (in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) ? "
<script type=\"text/javascript\" src=\"admin/jqueryui/jquery-ui.js\"></script>
<script type=\"text/javascript\" src=\"admin/multisor.js\"></script>
<link type=\"text/css\" href=\"admin/jqueryui/jquery-ui.css\" rel=\"stylesheet\" />
<link type=\"text/css\" href=\"admin/content_management.css\" rel=\"stylesheet\" />
<script type=\"text/javascript\" src=\"admin/content_management.php?language=" . $user_language . "\"></script>
<script type=\"text/javascript\" src=\"admin/dhtmlgoodies/ajax.js\"></script>
<script type=\"text/javascript\" src=\"admin/dhtmlgoodies/context-menu.js\"></script>
<script type=\"text/javascript\" src=\"admin/dhtmlgoodies/drag-drop-folder-tree.js\">
/************************************************************************************************************
	(C) www.dhtmlgoodies.com, July 2006
	
	Update log:
	
	
	This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	
	
	Terms of use:
	You are free to use this script as long as the copyright message is kept intact.
	
	For more detailed license information, see http://www.dhtmlgoodies.com/index.html?page=termsOfUse 
	
	Thank you!
	
	www.dhtmlgoodies.com
	Alf Magne Kalleland
************************************************************************************************************/	
</script>

<script type=\"text/javascript\">
//--------------------------------
// Save functions
//--------------------------------
var ajaxObjects = new Array();
// Use something like this if you want to save data by Ajax.
function saveMyTree() {
	saveString = treeObj.getNodeOrders();
	var ajaxIndex = ajaxObjects.length;
	ajaxObjects[ajaxIndex] = new sack();
	var url = 'saveNodes.php?saveString=' + saveString;
	ajaxObjects[ajaxIndex].requestFile = url;	// Specifying which file to get
	ajaxObjects[ajaxIndex].onCompletion = function() { saveComplete(ajaxIndex); } ;	// Specify function that will be executed after file has been found
	ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function				
}
function saveComplete(index) {
	alert(ajaxObjects[index].response);			
}	
// Call this function if you want to save it by a form.
function saveMyTree_byForm(){
	document.myForm.elements['saveString'].value = treeObj.getNodeOrders();
	document.myForm.submit();		
}
</script>" : "
<script type=\"text/javascript\">
jQuery(function(){
	jQuery('ul.sf-menu').superfish();
});
</script>";

$browser_tip = "<!--[if lt IE 9]>
<style type=\"text/css\">
#iemsg { border:3px solid #900; background-color:#fcc; color:#000; }
#iemsg h4 { margin:8px; padding:0; }
#iemsg p { margin:8px; padding:0; }
#iemsg a { font-weight:bold; color:#B00; }
#iemsg a:hover { font-weight:bold; color:#E00; }
</style>
<div id=\"iemsg\">
<h4>Sie benutzen eine veraltete Version des Internet Explorers!</h4>
<p>
Um alle Funktionen dieser Seite nutzen zu k&ouml;nnen, installieren Sie bitte einen aktuellen Browser. Denken Sie bitte auch an Sicherheitsl&uuml;cken in veralteten Browsern, die Sie zu einem leichtem Opfer im Internet machen! Unter folgendem Link k&ouml;nnen Sie sich &uuml;ber aktuelle Browser informieren und einen Ihrer Wahl herunterladen: 
<a href=\"http://www.oog-gallery.de/de/tools.php#browser\">Informieren Sie sich!</a>
</p>
</div>
<![endif]-->";

require "../templates/" . $template . "/template.html";

?>