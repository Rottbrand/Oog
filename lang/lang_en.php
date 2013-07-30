<?php

/*
 * Oog Photo-Gallery v3.2 Beta7
 * http://www.oog-gallery.de/
 * Copyright (C) 2011 Torben Rottbrand
 *
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

//////////////////////////////// core/functionslibrary.php ///////////////////////////

$lang['core']['file_description_info']['1'] = "Descriptions for Gif and Png comming in Oog v3.3!";
$lang['core']['file_description_info']['2'] = "Descriptions for video comming in Oog v3.3!";
$lang['core']['file_description_info']['3'] = "Descriptions for audio comming in Oog v3.3!";

$lang['core']['user_login']['1'] = "User-Login";
$lang['core']['user_login']['2'] = "May your session has timed out! Login again:";
$lang['core']['user_login']['3'] = "Login to your user account:";
$lang['core']['user_login']['4'] = "The user name or password was not correct:";
$lang['core']['user_login']['5'] = "Hello";
$lang['core']['user_login']['6'] = "The following features are enabled for you:";
$lang['core']['user_login']['7'] = "Hidden Albums Access";
$lang['core']['user_login']['8'] = "Edit Content";
$lang['core']['user_login']['9'] = "Manage Users";
$lang['core']['user_login']['10'] = "My Profile";
$lang['core']['user_login']['11'] = "Edit Design";
$lang['core']['user_login']['12'] = "Global Settings";

$lang['core']['album_showpage']['1'] = "There is no content in this album.";
$lang['core']['album_showpage']['2'] = "Open Album";

///////////////////////////////// core/showalbum.php ///////////////////////////////

$lang['core']['album_search']['1'] = "Found:";
$lang['core']['album_search']['2'] = "File(s)";
$lang['core']['album_search']['3'] = "Search";

$lang['core']['album']['1'] = "Views:";

//////////////////////////////// templates/Simply/template.html + core/showalbum.php /////////////////

$lang['template']['button_home'] = "Start-Page";

///////////////////////////////// core/request.php and core/lightbox.php ///////////////////////////////

$lang['core']['lightbox']['1'] = "Name:";
$lang['core']['lightbox']['2'] = "Date, Time:";
$lang['core']['lightbox']['3'] = "Size:";
$lang['core']['lightbox']['4'] = "Camera:";
$lang['core']['lightbox']['5'] = "Location:";
$lang['core']['lightbox']['6'] = "Description:";
$lang['core']['lightbox']['7'] = "Support for comments comming soon!";
$lang['core']['lightbox']['8'] = "In this version only editing descriptions of Jpg files is possible!";
$lang['core']['lightbox']['9'] = "Click here to cancel loading";

//////////////////////////////// templates/Simply/template_config.php /////////////////

$lang['template']['album_showpage']['1'] = "Open Album";
$lang['template']['album_showpage']['2'] = "Views:";
$lang['template']['album_showpage']['3'] = "Date:";
$lang['template']['album_showpage']['4'] = "Welcome to our Gallery!";  // Hauptüberschrift
$lang['template']['album_showpage']['5'] = "Most Viewed Albums - Top";
$lang['template']['album_showpage']['6'] = "Newest and Last Modified Albums - Top";

//////////////////////////////// core/admin/index.php ////////////////////////////////

$lang['admin']['title'] = "Oog Administration-Area";

$lang['admin']['navi']['btn_backtogallery'] = "Back to Gallery";
$lang['admin']['navi']['btn_albummanagement'] = "Albums Management";
$lang['admin']['navi']['btn_galleryconfiguration'] = "Gallery's Appearance";
$lang['admin']['navi']['btn_usermanagement'] = "User Management";

$lang['admin']['connection_error'] = "Error with connection! Try to reload page.";
$lang['admin']['ajax_error'] = "Error creating ajax request object! You may have to update your Browser.";
$lang['admin']['noscript_error'] = "Can not start script! Please make sure, that JavaScript is enabled in your browser.";

$lang['admin']['album_mediafile']['1'] = "Select an album to see its content here!";
$lang['admin']['album_mediafile']['2'] = "This album is empty!";

$lang['admin']['file']['char_error'] = "Not allowed characters in name! Allowed characters are:";
$lang['admin']['file']['noname_error'] = "No name entered!";
$lang['admin']['file']['doublename_error'] = "File with this name already exists!";
$lang['admin']['file']['rename_error'] = "Renaming file(s) faild!";
$lang['admin']['file']['delete_error'] = "Deleting file(s) faild!";
$lang['admin']['file']['saveownorder_error'] = "Could not save own defined order!";
$lang['admin']['file']['move_error'] = "Moving files faild!";
$lang['admin']['file']['btn_download'] = "Download selected file(s) to your device";
$lang['admin']['file']['btn_open'] = "Open selected file in Lightbox";
$lang['admin']['file']['btn_rename'] = "Rename selected file(s)";
$lang['admin']['file']['msg_rename']['1'] = "Do you want to enter name once and number through? Press Cancel for renaming individually.";
$lang['admin']['file']['msg_rename']['2'] = "Enter new name:";
$lang['admin']['file']['btn_delete'] = "Delete selected file(s)";
$lang['admin']['file']['confirm_delete'] = "Do you realy want to delete \'' + selected_files + '\' ?";
$lang['admin']['file']['btn_editDesc'] = "Edit description of selected file";
$lang['admin']['file']['btn_editCom'] = "Manage comments of selected file";
$lang['admin']['file']['btn_rotateL'] = "Rotate selected file(s) by 90 degrees to the left";
$lang['admin']['file']['btn_rotateR'] = "Rotate selected file(s) by 90 degrees to the right";
$lang['admin']['file']['btn_select_all'] = "Select all files";
$lang['admin']['file']['btn_unselect_all'] = "Unselect all files";
$lang['admin']['file']['btn_upload'] = "Upload files";
$lang['admin']['file']['btn_cancel_upload'] = "Cancel";
$lang['admin']['file']['upload_progress'] = "Progress:";
$lang['admin']['file']['upload_tip'] = "Allowed file formats in Oog are JPG, PNG, GIF, MP3, MP4 and FLV (video-codecs: VP6 and H.264). For converting your videos to flv or mp4 format Oog advises the free program:";
$lang['admin']['file']['upload_resize'] = "Resize photos before upload";
$lang['admin']['file']['upload_resize_height'] = "Max new height:";
$lang['admin']['file']['upload_resize_width'] = "Max new width:";
$lang['admin']['file']['upload_resize_quality'] = "New quality:";

$lang['admin']['folder']['hidden'] = "This folder is hidden from publicity!";
$lang['admin']['folder']['cant_move'] = "Can not move folder into an album that contains files!";
$lang['admin']['folder']['path'] = "Path: /";
$lang['admin']['folder']['lastchange'] = "Last Modification:";
$lang['admin']['folder']['type_cat'] = "Type: Category";
$lang['admin']['folder']['type_alb'] = "Type: Album";

$lang['admin']['folder']['btn']['add']['title'] = "Add Folder";
$lang['admin']['folder']['btn']['add']['info']['1'] = "Add a folder to the current selected folder. (Its a category)";
$lang['admin']['folder']['btn']['add']['info']['2'] = "Add a folder to the current selected folder. (Its an album) If you add a folder it will become category!";
$lang['admin']['folder']['btn']['add']['info']['3'] = "Can add folders only to categories and empty albums because an album will become category after adding a folder and categories cant contain files - only folders.";
$lang['admin']['folder']['btn']['hide']['title'] = "Hide";
$lang['admin']['folder']['btn']['hide']['info']['1'] = "Hide the current selected folder. So only user that allowed to can see it.";
$lang['admin']['folder']['btn']['unhide']['title'] = "Unhide";
$lang['admin']['folder']['btn']['unhide']['info']['1'] = "Unhide the current selected folder to let it see every one.";
$lang['admin']['folder']['btn']['unhide']['info']['2'] = "The current selected folder is hidden because a parent folder is hidden. (You have to unhide hidden parent folders to unhide this folder)";
$lang['admin']['folder']['btn']['rename']['title'] = "Rename";
$lang['admin']['folder']['btn']['rename']['prompt'] = "Enter new name:";
$lang['admin']['folder']['btn']['rename']['info']['1'] = "Rename the current selected folder.";
$lang['admin']['folder']['btn']['rename']['info']['2'] = "Can not rename the current selected folder! Its your root dir!";
$lang['admin']['folder']['btn']['delete']['title'] = "Delete";
$lang['admin']['folder']['btn']['delete']['confirm'] = "Do you realy want to delete \'' + selected_name + '\' with all its content?";
$lang['admin']['folder']['btn']['delete']['info']['1'] = "Delete the current selected folder with all its content!";
$lang['admin']['folder']['btn']['delete']['info']['2'] = "Cant delete current selected folder! Its your root dir!";
$lang['admin']['folder']['btn']['reset_order']['title'] = "Reset Order";
$lang['admin']['folder']['btn']['reset_order']['confirm'] = "Do you realy want to delete own defined order of the content of \'' + selected_name + '\'?";
$lang['admin']['folder']['btn']['reset_order']['info']['1'] = "Delete your own defined order in current selected category! New order will be defined by default sorting setting!";
$lang['admin']['folder']['btn']['reset_order']['info']['2'] = "The current selected category has no own defined order! Its contents order is defined by default sorting setting!";
$lang['admin']['folder']['btn']['reset_order']['info']['3'] = "Delete your own defined order in current selected album! New order will be defined by default sorting setting!";
$lang['admin']['folder']['btn']['reset_order']['info']['4'] = "The current selected album has no own defined order! Its contents order is defined by default sorting setting!";
$lang['admin']['folder']['btn']['reset_order_also_subfolders']['title'] = "Also orders of all subfolders";
$lang['admin']['folder']['btn']['reset_order_also_subfolders']['confirm'] = "Do you realy want to delete own defined order of the content of \'' + selected_name + '\' and all orders of its subfolders?";
$lang['admin']['folder']['btn']['reset_order_also_subfolders']['info']['1'] = "If you also want to delete all orders in subfolders click below:";
$lang['admin']['folder']['btn']['refresh']['title'] = "Refresh";
$lang['admin']['folder']['btn']['collapse']['title'] = "Collapse";
$lang['admin']['folder']['btn']['expand']['title'] = "Expand";

$lang['admin']['folder']['char_error'] = "Not allowed characters in name! Allowed characters are:";
$lang['admin']['folder']['noname_error'] =  "No name entered!";
$lang['admin']['folder']['doublename_error'] =  "Album/Category with this name already exists!";
$lang['admin']['folder']['notempty_error'] = "Album has to be empty!";
$lang['admin']['folder']['add_error'] = "Creating folder faild!";
$lang['admin']['folder']['rename_error'] = "Renaming folder faild!";
$lang['admin']['folder']['delete_error'] = "Deleting folder faild!";
$lang['admin']['folder']['deleteroot_error'] = "Can not delete the root-dir!";
$lang['admin']['folder']['renameroot_error'] = "Can not rename the root-dir!";
$lang['admin']['folder']['saveownorder_error'] = "Could not save own defined order!";
$lang['admin']['folder']['deleteownorder_error'] = "Deleting order file(s) faild!";
$lang['admin']['folder']['hide_error'] = "Hiding album/category faild!";
$lang['admin']['folder']['unhide_error'] = "Unhiding album/category faild!";

$lang['admin']['user']['btn']['add']['title'] = "Add User";
$lang['admin']['user']['btn']['add']['error']['1'] = "You are not allowed to add users!";
$lang['admin']['user']['btn']['add']['error']['2'] = "Could not add user!";
$lang['admin']['user']['btn']['rename']['title'] = "Rename";
$lang['admin']['user']['btn']['rename']['prompt'] = "Enter new name:";
$lang['admin']['user']['btn']['delete']['title'] = "Delete";
$lang['admin']['user']['btn']['delete']['confirm'] = "Do you realy want to delete the user \'' + selected_name + '\'?";
$lang['admin']['user']['btn']['change_password']['title'] = "Change Password";
$lang['admin']['user']['btn']['change_password']['prompt'] = "Enter new password:";
$lang['admin']['user']['btn']['refresh']['title'] = "Refresh";

$lang['admin']['user']['namefield'] = "Username:";
$lang['admin']['user']['lastactivity'] = "Last Activity:";

?>