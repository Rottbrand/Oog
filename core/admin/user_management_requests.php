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

if(!isset($_GET['ajax_request'])) $_GET['ajax_request'] = "";
if(!isset($_GET['language'])) $_GET['language'] = "";

include "../../gallery_config.php";

$user_language = $default_language;
$langs = array("de", "en");
for($i = 0; count($langs) > $i; $i++) {
  if($_GET['language'] == $langs[$i]) {
    $user_language = $langs[$i]; 
    break;
  }
}
include "../../lang/lang_" . $user_language . ".php";

include "../../templates/" . $template . "/template_config.php";

include "../functionslibrary.php";

// get user_rights //
$user_name = session_manager("", "", "", "../../users/user.db", "../sessions/");
$user_rights = get_user_rights($user_name, "../../users/user.db");

if(in_array('admin', $user_rights)) {

  if($_GET['ajax_request'] == "get_users") {
  
    $list = "";
    $node = 0;
    
    $user_db = sqlite_open("../../users/user.db");
    $users = sqlite_query($user_db, "SELECT * FROM userTab");
    
    while($row = sqlite_fetch_array($users)) {
    
      $list .= "<li id=\"node" . $node . "\" noDrag=\"true\"><a><span style=\"cursor: pointer; padding: 2px;\" id=\"bg" . $node . "\" onclick=\"select_user('" . $row['userName'] . "', 'node" . $node++ . "')\">" . $row['userName'] . "</span></a></li>\n";
    
    }
    
    echo $list."<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>test";
    exit;
    
  }
  
  if($_GET['ajax_request'] == "add_user") {
  
    if(!isset($_GET['new_user_name'])) $_GET['new_user_name'] = "";
    if(!isset($_GET['new_user_pw'])) $_GET['new_user_pw'] = "";
    if(!isset($_GET['new_user_rights'])) $_GET['new_user_rights'] = "";
      
    if(in_array('admin', $user_rights)) {
  
      $user_db = sqlite_open("../../users/user.db");
        
      $users = sqlite_query($user_db, "SELECT * FROM userTab");
      $nameInUse = false;
      $nUsers = 0;
      while($row = sqlite_fetch_array($users)) { $nUsers++; if($row['userName'] == $_GET['new_user_name']) $nameInUse = true; }
      $users = sqlite_query($user_db, "SELECT * FROM userTab");
      if($nameInUse) {
  
        // JSON
  
      } elseif($_GET['new_user_name'] != preg_replace("/^[^a-zA-Z0-9_-]+$/", "", $_GET['new_user_name'])) {
  
        // JSON
      
      } elseif($_GET['new_user_pw'] != preg_replace("/^[^a-zA-Z0-9_+!?-]+$/", "", $_GET['new_user_pw'])) {
      
        // JSON
      
      } elseif($_GET['new_user_rights'] != preg_replace("/^[^a-zA-Z_,]+$/", "", $_GET['new_user_rights'])) {
      
        // JSON
      
      } elseif(sqlite_query($user_db, "INSERT INTO userTab VALUES(" . ( $nUsers + 1 ) . ", '" . $_GET['new_user_name'] . "', '" . $_GET['new_user_pw'] . "', '" . $_GET['new_user_rights'] . "', 'E-Mail')")) {
      
        // JSON  SUCCESS
      
      } else {
         echo $nUsers;
        // JSON
      
      }
      
    }
  
    sqlite_close($user_db);
    exit;
  
  }
  
}