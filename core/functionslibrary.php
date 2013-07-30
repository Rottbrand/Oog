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

/////////////////////////////////////////////////////////////GET_ALBUMPATHS/////////////////////////////////////////////////////////////

function get_albumpaths($dir, $hidden_albums_access, $sorting_menu, $htaccess_protection) {

  $albumpaths = array();  
  $order = update_albumorderfile($dir, $hidden_albums_access, $sorting_menu, $htaccess_protection);
  
  for($i = 0; $i < count($order); $i++) {

    $is_category = 0;
    $handle = opendir($dir . $order[$i]);
    while($file = readdir($handle)) {
      if($file != "." && $file != "..") {
        if(is_dir($dir . $order[$i] . "/" . $file)) {
          $is_category = 1;
          break;
        }
      }
    }
    closedir($handle);
    
    if($is_category == 1) {
      $albumpaths = array_merge($albumpaths, get_albumpaths($dir . $order[$i] . "/", $hidden_albums_access, $sorting_menu, $htaccess_protection));
    } else {
      array_push($albumpaths, $dir . $order[$i] . "/");
    }
    
  }
  
  return $albumpaths;

}

/////////////////////////////////////////////////////////////ALBUM_VIEWS/////////////////////////////////////////////////////////////

function folderviews($folderpath, $increment) {

  @chmod($folderpath, 0777);
  $viewscounterfile = $folderpath . "views.php";

  if(!is_file($viewscounterfile)) {
    @$file = fopen($viewscounterfile,"w");
    @chmod($viewscounterfile,0777);
    @fwrite($file,"<?php\n\n\$views = 0;\n\n?>");
    @fclose($file);
    if(!is_file($viewscounterfile)) return "ERROR!";
  }
  
  $views = 0;
  @include $viewscounterfile;
  $comp = $views;

  if($increment == 1) {
    $views++;
    $new_views_content = "<?php\n\n\$views = $views;\n\n?>";
    @$file = fopen($viewscounterfile,"w");
    @fwrite($file, $new_views_content);
    @fclose($file);
    @include $viewscounterfile;
    if($views==$comp) return "ERROR!";
  }

  return $views;

}

/////////////////////////////////////////////////////////////UPDATE_IMAGEORDERFILE/////////////////////////////////////////////////////////////
 
function update_imageorderfile($albumpath, $sorting_photos) {

  $imageorderfile = $albumpath . "order.php";
  @chmod($albumpath, 0777);

  $a_order = array();
  $handle = opendir($albumpath);
  while($file_in_album = readdir ($handle)) {
    if($file_in_album != "." && $file_in_album != "..") {
    
      $ext = strtolower(strrchr($file_in_album, "."));
      if($ext == ".jpg" OR $ext == ".jpeg" OR $ext == ".gif" OR $ext == ".png" OR $ext == ".webm" OR $ext == ".html" OR $ext == ".htm") array_push($a_order, $file_in_album);
      
    }
  }
  closedir ($handle);
  
  if($sorting_photos == "alphabetic" AND !is_file($imageorderfile)) {

    $a_order_lowercase = array_map("strtolower", $a_order);
    array_multisort($a_order_lowercase, SORT_STRING, SORT_ASC, $a_order);
    return $a_order;

  } elseif($sorting_photos == "alphabetic_backwards" AND !is_file($imageorderfile)) {

    $a_order_lowercase = array_map("strtolower", $a_order);
    array_multisort($a_order_lowercase, SORT_STRING, SORT_ASC, $a_order);
    return array_reverse($a_order);

  } elseif(is_file($imageorderfile)) { 

    $status = add_photos_to_orderfile("add_top", $imageorderfile, $albumpath);
    if($status == "ok") {
      include $imageorderfile;
      return $order;
    }

  }

  $a_order_lowercase = array_map("strtolower", $a_order);
  array_multisort($a_order_lowercase, SORT_STRING, SORT_ASC, $a_order);
  return $a_order;
 
}

/////////////////////////////////////////////////////////////ADD_PHOTO_ORDER/////////////////////////////////////////////////////////////

function add_photos_to_orderfile($pos, $imageorderfile, $albumpath) {

  include $imageorderfile;
  $comp = $order;
  $handle = opendir ($albumpath);
  while ($file_in_album = readdir ($handle)) {
    if ($file_in_album != "." && $file_in_album != "..") {    
      @chmod($albumpath . $file_in_album,0775);
      if (!in_array($file_in_album,$order)) {
        $info = pathinfo($file_in_album);
        $ext = strtolower($info["extension"]);
        if ($ext == "jpg" OR $ext == "jpeg" OR $ext == "gif" OR $ext == "png" OR $ext == "webm" OR $ext == "html" OR $ext == "htm") {
          $order = array_reverse($order);
          array_push($order,$file_in_album);
          $order = array_reverse($order);
        }
      }
    }
  }
  closedir ($handle);
  
  $entries = count($order);
  $i = 0;
  while($i < $entries) {
    if(!is_file($albumpath . $order[$i])) {
      array_splice($order,$i,1);
      $i--;
    }
    $i++;
    $entries = count($order);  
  }

  if ($order != $comp) {
    $str_order = "array(";
    for($i=0;$i<count($order);$i++) $str_order .= "\"$order[$i]\",";
    $str_order .= ")";
    $new_order = "<?php\n\n\$order = $str_order;\n\n?>";
    @$file = fopen($imageorderfile,"w");
    @rewind($file);
    @fwrite($file, $new_order);
    @fclose($file);
    
    $new_comp = $order;
    include $imageorderfile;
    if($order != $new_comp) {
      return "error";
    } else {
      return "ok";
    }
  }
  
  return "ok";
    
}

/////////////////////////////////////////////////////////////CHECK_INDEXFILE/////////////////////////////////////////////////////////////

function make_indexfile($folderpath) {

  $indexfile = $folderpath . "index.php";  
  @$file = fopen($indexfile, "w");
  @chmod($indexfile, 0777);
  @fwrite($file, "<?php\n\n\$hidden_album = false;  //Set \$hidden_album = true if you want to hide this album/category.\n\n?>");
  @fclose($file);

}

/////////////////////////////////////////////////////////////UPDATE_HTACCESS/////////////////////////////////////////////////////////////

function update_htaccess($folderpath, $htaccess_protection) {

  $htaccess_file = $folderpath . ".htaccess";    
  $hidden_album = false;
  @include $folderpath . "index.php";
  if($hidden_album == true) { 
    if($htaccess_protection == true AND !is_file($htaccess_file)) {
      @$file = fopen($htaccess_file, "w");
      @chmod($htaccess_file, 0777);
      @fwrite($file, "deny from all\n");
      @fclose($file);
    }
  } else {
    @unlink($htaccess_file);  
  }

}

/////////////////////////////////////////////////////////////UPDATE_ALBUMORDERFILE/////////////////////////////////////////////////////////////

function update_albumorderfile($albumpath, $hidden_albums_access, $sorting_albums, $htaccess_protection) {
//todo: change names: album -> folder !!

  // make an array of order which will be returned //
  $a_order = array();

  // if there is no indexfile -> try making one //
  if(!is_file($albumpath . "index.php")) make_indexfile($albumpath);

  // update htaccess file (will look up hiding stetting in indexfile) //
  update_htaccess($albumpath, $htaccess_protection);
  
  // if given folderpath is not accessible for user -> empty return //
  $hidden_album = false;
  @include $albumpath . "index.php";
  if($hidden_album == true AND $hidden_albums_access == false) return $a_order;

  // go through all folders in given folderpath //
  $albumorderfile = $albumpath . "order.php";
  @chmod($albumpath, 0777);
  $handle = opendir($albumpath);
  while($file_in_album = readdir ($handle)) {
    if($file_in_album != "." && $file_in_album != "..") {
      if(is_dir($albumpath . $file_in_album)) {
      
        // if there is no indexfile -> try making one //
        if(!is_file($albumpath . $file_in_album . "/index.php")) make_indexfile($albumpath . $file_in_album . "/");
        
        // update htaccess file (will look up hiding stetting in indexfile) //
        update_htaccess($albumpath . $file_in_album . "/", $htaccess_protection);

        // check if folder is hidden //
        @include $albumpath . $file_in_album . "/index.php";
        if($hidden_album == true) {
        
          // check users right to access //
          if($hidden_albums_access == true) array_push($a_order, $file_in_album);
          
        } else {
        
          // folder isnt hidden //
          array_push($a_order, $file_in_album);
          
        }
                 
      }
    }
  }
  closedir($handle);

  // return individual sorted order //  
  if($sorting_albums == "alphabetic" AND !is_file($albumorderfile)) {
  
    $a_order_lowercase = array_map("strtolower", $a_order);
    array_multisort($a_order_lowercase, SORT_STRING, SORT_ASC, $a_order);
    return $a_order;
    
  } elseif($sorting_albums == "alphabetic_backwards" AND !is_file($albumorderfile)) {
  
    $a_order_lowercase = array_map("strtolower", $a_order);
    array_multisort($a_order_lowercase, SORT_STRING, SORT_ASC, $a_order);
    return array_reverse($a_order);
    
  } elseif(is_file($albumorderfile)) {

    $status = add_albums_to_orderfile("add_top", $albumorderfile, $albumpath);
    if($status == "ok") {
      include $albumorderfile;
      return $order;
    }
    
  }
  
  $a_order_lowercase = array_map("strtolower", $a_order);
  array_multisort($a_order_lowercase, SORT_STRING, SORT_ASC, $a_order);
  return $a_order;
  
}

/////////////////////////////////////////////////////////////ADD_ALBUM_ORDER/////////////////////////////////////////////////////////////

function add_albums_to_orderfile($pos, $albumorderfile, $albumpath) {

  include $albumorderfile;
  $comp = $order;
  $handle = opendir ($albumpath);
  while ($file_in_album = readdir ($handle)) {
    if($file_in_album != "." && $file_in_album != "..") {    
      if(!in_array($file_in_album,$order)) {
        if(is_dir($albumpath . $file_in_album)) {
          if($pos=="add_top") { $order = array_reverse($order); }
          array_push($order, $file_in_album);
          if($pos=="add_top") { $order = array_reverse($order); }
        }
      }
    }
  }
  closedir ($handle);
  
  $entries = count($order);
  $i = 0;
  while($i < $entries) {
    if(!is_dir($albumpath . $order[$i])) {
      array_splice($order, $i, 1);
      $i--;
    }
    $i++;
    $entries = count($order);
  }

  if ($order != $comp) {
    $str_order = "array(";
    for($i = 0; $i < count($order); $i++) $str_order .= "\"$order[$i]\",";
    $str_order .= ")";
    $new_order = "<?php\n\n\$order = $str_order;\n\n?>";
    @$file = fopen($albumorderfile,"w");
    @rewind($file);
    @fwrite($file, $new_order);
    @fclose($file);
    
    $new_comp = $order;
    include $albumorderfile;
    if($order != $new_comp) {
      return "error";
    } else {
      return "ok";
    }
  }
  
  return "ok";
    
}

/////////////////////////////////////////////////////////////CREATE_ALBUMTITLE/////////////////////////////////////////////////////////////

function create_albumtitle($albumpath, $separator) {

  $albumpath = (substr($albumpath, 0, 6) == "../../") ? substr(substr($albumpath, 13), 0, -1) : substr(substr($albumpath, 10), 0, -1);

  $split = explode("/", $albumpath);
  $title = "";

  for($i = 0; count($split) > $i; $i++) {
    if($i == 0) {
      $title .= $split[$i];
    } else {
      $title .= $separator . $split[$i];
    }
  }

  return $title;

}

/////////////////////////////////////////////////////////////READ_ALBUMINFO/////////////////////////////////////////////////////////////

function read_albuminfo($albumpath) {

  $infofile = $albumpath . "info.txt";
  $albuminfo = "";
  
  if(file_exists($infofile)) {
    @$albuminfo = file_get_contents($infofile);
  }

  return $albuminfo;

}

/////////////////////////////////////////////////////////////GENERATE_MENU/////////////////////////////////////////////////////////////

function generate_menu($structure, $last_keys, $userRights, $contentRootDir) {

  $list = "";
  global $node;
  if($last_keys == "") {
    $node = 1;
  } else {
    $last_keys = $last_keys . "/";
  }

  if(in_array('admin', $userRights) OR in_array('can_edit_content', $userRights)) {

    // if user can edit content //
    foreach($structure as $key => $value) {

      $hidden_album = false;
      @include $albumsPath . $last_keys . $key . "/index.php";
      $hiddenSymbol = ($hidden_album == true) ? "<img src=\"admin/images/lock.png\" style=\"margin-left: 3px;\" />" : "";

      if($value == "") {
      
        if(count(update_imageorderfile($contentRootDir . $last_keys . $key . "/", "alphabetic")) == 0) {
          $list .= "<li id=\"node" . $node++ . "\" class=\"sf-link\"><a href=\"javascript: select_folder('Menu/" . $last_keys . $key . "', true)\"><span data-isalbum=\"true\" data-path=\"Menu/" . $last_keys . $key . "\" style=\"cursor:pointer;\">" . $key . $hiddenSymbol . "</span></a></li>\n";
        } else {
          $list .= "<li id=\"node" . $node++ . "\" noChildren=\"true\" class=\"sf-link\"><a href=\"javascript: select_folder('Menu/" . $last_keys . $key . "', true)\"><span data-isalbum=\"true\" data-path=\"Menu/" . $last_keys . $key . "\" style=\"cursor:pointer;\">" . $key . $hiddenSymbol . "</span></a></li>\n";
        }      
      
      } else {
        $list .= "<li id=\"node" . $node++ . "\" class=\"sf-no-link\"><a href=\"javascript: select_folder('Menu/" . $last_keys . $key . "', true)\"><span style=\"cursor:pointer;\">" . $key . $hiddenSymbol . "</span></a>\n<ul style=\"list-style: none;\">\n" . generate_menu($value,$last_keys . $key, $userRights, $contentRootDir) . "\n</ul>\n</li>\n";
      }
      
    }
    
  } else {

    // if user can not edit content //
    foreach($structure as $key => $value) {
    
      if($value == "") {
        $list .= "<li id=\"node" . $node++ . "\" noChildren=\"true\" class=\"sf-link\"><a href=\"showalbum.php?album=" . $last_keys . $key . "\"><span style=\"cursor:pointer;\">" . $key . "</span></a></li>\n";
      } else {
        $list .= "<li id=\"node" . $node++ . "\" class=\"sf-no-link\"><a href=\"#\"><span style=\"cursor:pointer;\">" . $key . "</span></a>\n<ul>\n" . generate_menu($value,$last_keys . $key, $userRights, $contentRootDir) . "\n</ul>\n</li>\n";
      }
      
    }
  
  }
  
  return $list;

}

/////////////////////////////////////////////////////////////GENERATE_STRUCTURE/////////////////////////////////////////////////////////////

function generate_structure($albumpaths) {

  if(substr($albumpaths[0],0,9) == "../albums") {
    for($i = 0; $i < count($albumpaths); $i++) $albumpaths[$i] = substr($albumpaths[$i],10);
  }

  if(substr($albumpaths[0],0,12) == "../../albums") {
    for($i = 0; $i < count($albumpaths); $i++) $albumpaths[$i] = substr($albumpaths[$i],13);
  }

  $last = array();
  $paths = array();

  for($i = 0; $i < count($albumpaths); $i++) {
    $arr = explode("/",substr($albumpaths[$i],0,-1));
    array_push($last,array_shift($arr));
    $rest = "";
    for($j = 0; $j < count($arr); $j++) {
      $rest .= $arr[$j] . "/";
    }
    if(!isset($paths[$last[$i]])) $paths[$last[$i]] = array();
    array_push($paths[$last[$i]],$rest);
  }

  foreach($paths as $key => $value) {
    $bah = 0;
    for($j = 0; $j < count($paths[$key]); $j++) { 
      if(count(explode("/",$paths[$key][$j])) > 1) { 
        $bah = 1;
        break;
      }
    }
    if($bah == 1) {
      $paths[$key] = array();
      $paths[$key] = generate_structure($value);
    } else {
      $paths[$key] = "";
    } 
  }
  
  return $paths;

}

///////////////////////////////////////////////////////////// CREATE_THUMBNAIL (WRAPPER) /////////////////////////////////////////////////////////////

function create_thumbnail($albumpath, $file, $max_thumbwidthheight, $thumbquality, $thumbnail_wrapper, $user_rights, $langCore, $template, $separator) {
  
  $urlAlbumpath = (substr($albumpath, 0, 9) == "../../alb") ? str_replace("'", "\\'", substr(substr($albumpath, 13), 0, -1)) : str_replace("'", "\\'", substr(substr($albumpath, 10), 0, -1));

  if($file === "") {

    $file = "";
    $ext = "";
    $description = "";
    
    $thumbnail = "<div style=\"height:" . ( $max_thumbwidthheight ) . "px; width:" . $max_thumbwidthheight . "px;\" class=\"no_thumb\"></div>";
  
  } else {

    $ext = strtolower(strrchr($file, "."));
    if($ext == ".jpg" OR $ext == ".png" OR $ext == ".gif" OR $ext == ".jpeg") {

      // try to get image description if Jpg //
      if($ext == ".jpg" OR $ext == ".jpeg") {
        $exif_data = (function_exists("read_exif_data")) ? @read_exif_data($albumpath . $file, 0, true) : 0;
        $description = (isset($exif_data["IFD0"]["ImageDescription"])) ? $exif_data["IFD0"]["ImageDescription"] : "";
      } else {
        $description = $langCore['file_description_info']['1'];
      }
    
      $thumbnail = "<table class=\"thumbnail\" data-path=\"" . $urlAlbumpath . "\" data-filename=\"" . $file . "\" style=\"width: " . ($max_thumbwidthheight + 6) . "px; height: " . ($max_thumbwidthheight + 6) . "px; border: 0px;\"><th style=\"border: 0px;\"><img style=\"margin-bottom: -3px; border: 0px;\" src=\"../templates/" . $template . "/imgdata/loading.gif\" /></th></table>";

    }
    
    if($ext == ".webm") {
                                 
      $description = $langCore['file_description_info']['2'];
    
      $thumbnail = "";
    
    }
    
    if($ext == ".mp3") {
    
      $description = $langCore['file_description_info']['3'];
    
      $thumbnail = "";
    
    }
    
  }
  
  $lightboxEdiDes = (in_array("can_edit_content", $user_rights) OR in_array("admin", $user_rights)) ? "true" : "false";
  $lightboxOnclick = (in_array("can_edit_content", $user_rights) OR in_array("admin", $user_rights)) ? "" : "onclick=\"startlightbox('" . $urlAlbumpath . "', '" . str_replace("'", "\\'", $file) . "', false, " . $lightboxEdiDes . ", false)\"";

  if(true) $thumbnail_wrapper = "<li style=\"float: left;\" class=\"oogLightboxSequence\" data-isfile=\"true\" data-ishtml=\"false\" data-path=\"" . $urlAlbumpath . "\" data-filename=\"" . $file . "\" " . $lightboxOnclick . ">" . $thumbnail_wrapper . "</li>";

  $thumbnail_wrapper = str_replace("{THUMBNAIL}", $thumbnail, str_replace("\r\n", "", $thumbnail_wrapper));
  $thumbnail_wrapper = str_replace("{NAME}", substr($file, 0, strrpos($file, '.')), $thumbnail_wrapper);
  $thumbnail_wrapper = str_replace("{EXTENSION}", str_replace(".", "", strrchr($file, ".")), $thumbnail_wrapper);
  $thumbnail_wrapper = str_replace("{DESCRIPTION}", $description, $thumbnail_wrapper);
  $thumbnail_wrapper = str_replace("{ALBUMTITLE}", create_albumtitle($albumpath, $separator), $thumbnail_wrapper);
  $thumbnail_wrapper = str_replace("{ALBUMLINK}", "showalbum.php?album=" . $urlAlbumpath, $thumbnail_wrapper);
  $thumbnail_wrapper = str_replace("{ALBUMVIEWS}", folderviews($albumpath, 0), $thumbnail_wrapper);
  $thumbnail_wrapper = str_replace("{ALBUMDATE}", date("F d Y H:i", filemtime($albumpath)), $thumbnail_wrapper);

  return str_replace("\n", "", $thumbnail_wrapper);

}

/////////////////////////////////////////////////////////////SESSION_MANAGER/////////////////////////////////////////////////////////////

function session_manager($try, $user_name, $user_password, $path_user_db, $sessions_path) {

  if($try == "logout") {
    session_save_path($sessions_path);
    session_unset("OogPhotoGallery"); 
    session_destroy();    

    return -1;
  }

  $user_db = sqlite_open($path_user_db);

  if($try == "login") {
    
    $users = sqlite_query($user_db, "SELECT * FROM userTab");
    while($row = sqlite_fetch_array($users)) {
      if($row['userName'] == $user_name) {


        if(md5(md5($user_password)) == md5(md5($row['userPw']))) {

          session_save_path($sessions_path);
          $_SESSION["OogPhotoGallery"] = $row['userName'] . "#-#" . md5(md5($row['userPw']));

          sqlite_close($user_db);
          return $row['userName'];
          
        } else {
          sqlite_close($user_db);
          return -2;
        }      
      
      }
    }
    sqlite_close($user_db);
    return -2;
  } 

  session_save_path($sessions_path);
  $session_data = explode("#-#", (isset($_SESSION["OogPhotoGallery"])) ? $_SESSION["OogPhotoGallery"] : "");

  $users = sqlite_query($user_db, "SELECT * FROM userTab");
  while($row = sqlite_fetch_array($users)) {
    if($row['userName'] == $session_data[0]) {
      if($session_data[1] == md5(md5($row['userPw']))) {
        sqlite_close($user_db);
        return $row['userName'];
      }
    }
  }
  sqlite_close($user_db);
  return -1;

}

/////////////////////////////////////////////////////////////GENERATE_USER_LOGIN/////////////////////////////////////////////////////////////

function generate_user_login($user_name, $timeout, $album, $user_rights, $langCore, $user_language) {

  if($user_name == -1 OR $user_name == -2) {

    $user_login = "<script type=\"text/javascript\">\n";

    if($user_name == -1) {
      if($timeout == "true") {
        $user_login .= "login_display = 1;\n";
      } else {
        $user_login .= "login_display = 0;\n";
      }
    } elseif($user_name == -2) {
      $user_login .= "login_display = 1;\n";
    }
    
    $user_login .= "function open_close_login() {
  if(login_display == 0) {
    document.getElementById('login_button').style.backgroundPosition = '-24px 0px';
    document.getElementById('login_form').style.display = 'block';
    login_display = 1;
  } else {
    document.getElementById('login_button').style.backgroundPosition = '0px 0px';
    document.getElementById('login_form').style.display = 'none';
    login_display = 0;
  }
}
</script>
<input type=\"button\" onclick=\"open_close_login()\" id=\"login_button\" title=\"" . $langCore['user_login']['1'] . "\" />
<div style=\"clear:both;\"></div>\n";

    if($album != "") $album = "?album=" . $album;

    if($user_name == -1) {
      if($timeout == "true") {
        $user_login .= "<form method=\"POST\" action=\"showalbum.php" . $album . "\" id=\"login_form\" style=\"display:block;\">
<p style=\"color:red;\">" . $langCore['user_login']['2'] . "</p>\n";
      } else {
        $user_login .= "<form method=\"POST\" action=\"showalbum.php" . $album . "\" id=\"login_form\">
<p>" . $langCore['user_login']['3'] . "</p>\n";
      }
    } elseif($user_name == -2) {
      $user_login .= "<form method=\"POST\" action=\"showalbum.php" . $album . "\" id=\"login_form\" style=\"display:block;\">
<p style=\"color:red;\">" . $langCore['user_login']['4'] . "</p>\n";
    }

    $user_login .= "<input type=\"hidden\" name=\"try\" value=\"login\" />
<input type=\"input\" name=\"user_name\" value=\"Name\" class=\"input\" onclick=\"this.value=''\" />
<input type=\"password\" name=\"user_password\" value=\"Password\" class=\"input\" onclick=\"this.value=''\" />
<input type=\"submit\" name=\"login\" value=\"Login\" class=\"button\" />\n";

    $user_login .= "</form>
<div style=\"clear:both;\"></div>\n"; 

  } else {

    if($album != "") $album = "?album=" . $album;
            
    $user_login = "<form method=\"POST\" action=\"showalbum.php" . $album . "\" id=\"login_form\" style=\"display:block;margin-top:7px;\">
<p style=\"color:green;font-size:1.3em;\">" . $langCore['user_login']['5'] . " " . $user_name . "!</p>
<p>" . $langCore['user_login']['6'] . "</p>
<ul>\n";

    if(in_array("can_edit_design", $user_rights) OR in_array("admin", $user_rights)) $user_login .= "<li><a href=\"\">" . $langCore['user_login']['11'] . " &raquo;</a></li>\n";
    $user_login .= "<li><a href=\"#\" onclick=\"startlightbox('admin/','user_management_requests.php?ajax_request=get_users',true,false,false)\">" . $langCore['user_login']['10'] . " &raquo;</a></li>\n";
    if(in_array("admin", $user_rights)) $user_login .= "<li><a href=\"#\" onclick=\"startlightbox('','',false,false,true)\">" . $langCore['user_login']['9'] . " &raquo;</a></li>\n";
    if(in_array("admin", $user_rights)) $user_login .= "<li><a href=\"\">" . $langCore['user_login']['12'] . " &raquo;</a></li>\n";
    if(in_array("can_edit_content", $user_rights) OR in_array("admin", $user_rights)) $user_login .= "<li>" . $langCore['user_login']['8'] . "</li>";
    if(in_array("hidden_albums_access", $user_rights) OR in_array("admin", $user_rights)) $user_login .= "<li>" . $langCore['user_login']['7'] . "</li>";

    $user_login .= "</ul>
<input type=\"hidden\" name=\"try\" value=\"logout\" />
<input type=\"submit\" name=\"logout\" value=\"Logout\" class=\"button\" />
</form>\n";

  }

  return $user_login;

}  







function generate_main_content($albumpath, $max_thumbwidthheight, $thumbquality, $thumbnail_wrapper, $sorting_content, $user_rights, $alternativeFiles = false, $langCore, $template, $separator) {

  $content = "";  
  
  if($alternativeFiles === false) {
  
    $order = update_imageorderfile($albumpath, $sorting_content);
  
    for($i = 0; $i < count($order); $i++) {  
    
      $content .= create_thumbnail($albumpath, $order[$i], $max_thumbwidthheight, $thumbquality, $thumbnail_wrapper, $user_rights, $langCore, $template, $separator);

    }
  
  } else {
  
    for($i = 0; $i < count($alternativeFiles); $i++) {  
    
      $content .= create_thumbnail($alternativeFiles[$i][0], $alternativeFiles[$i][1], $max_thumbwidthheight, $thumbquality, $thumbnail_wrapper, $user_rights, $langCore, $template, $separator);
  
    }

  }

  return $content;

}






function generate_startpage($show_views, $top_most_viewed_albums, $top_newest_albums, $code_on_startpage, $all_albumpaths, $sorting_content, $max_thumbwidthheight_on_startpage, $top_most_viewed_thumbnail_wrapper, $top_newest_thumbnail_wrapper, $user_rights, $max_thumbwidthheight, $thumbquality, $langCore, $template, $separator) {

  if($show_views != "yes") $top_most_viewed_albums = 0;
  if($top_most_viewed_albums > 0) {
    $most_viewed = generate_most_viewed_albums($top_most_viewed_albums, $max_thumbwidthheight_on_startpage, $all_albumpaths, $sorting_content, $top_most_viewed_thumbnail_wrapper, $user_rights, $max_thumbwidthheight, $thumbquality, $langCore, $template, $separator);
  } else {
    $most_viewed = "";
  }
  $code_on_startpage = str_replace("{MOST-VIEWED-ALBUMS}", $most_viewed, $code_on_startpage);

  if($top_newest_albums > 0) {
    $newest = generate_newest_albums($top_newest_albums, $max_thumbwidthheight_on_startpage, $all_albumpaths, $sorting_content, $top_newest_thumbnail_wrapper, $user_rights, $max_thumbwidthheight, $thumbquality, $langCore, $template, $separator);
  } else {
    $newest = "";
  }
  $code_on_startpage = str_replace("{NEWEST-ALBUMS}", $newest, $code_on_startpage);

  return $code_on_startpage;

}





/**************************************************************************
 * GENERATE MOST VIEWED ALBUMS                                            *
 *                                                                        *
 *                                                                        *
 *                                                                        *
 * Parameter:                                                             *
 *                                                                        *
 *                                                                        *
 * Returns:                                      *
 *                                               *
 ***************************************************************************/     	  
function generate_most_viewed_albums($top_most_viewed_albums, $max_thumbwidthheight_on_startpage, $all_albumpaths, $sorting_content, $top_most_viewed_thumbnail_wrapper, $user_rights, $max_thumbwidthheight, $thumbquality, $langCore, $template, $separator) {

  $list = "";
  $views = array();

  for($i = 0; count($all_albumpaths) > $i; $i++) array_push($views, folderviews($all_albumpaths[$i], 0));
  array_multisort($views, SORT_DESC, $all_albumpaths);
  $limit = $top_most_viewed_albums;
  if($top_most_viewed_albums > count($all_albumpaths)) $limit = count($all_albumpaths);

  for($i = 0; $limit > $i; $i++) {

    $img_order = update_imageorderfile($all_albumpaths[$i], $sorting_content);

    $list .= create_thumbnail($all_albumpaths[$i], (isset($img_order[0])) ? $img_order[0] : "", $max_thumbwidthheight_on_startpage, $thumbquality, $top_most_viewed_thumbnail_wrapper, $user_rights, $langCore, $template, $separator);

  } 

  return $list . "<div style=\"clear:both;\"></div>\n";

}






/* 
 * GENERATE NEWEST ALBUMS
 *
 * 
 *
 * Parameter:
 *  
 *                      
 * Returns:
 *        
 */     	  
function generate_newest_albums($top_newest_albums, $max_thumbwidthheight_on_startpage, $all_albumpaths, $sorting_content, $top_newest_thumbnail_wrapper, $user_rights, $max_thumbwidthheight, $thumbquality, $langCore, $template, $separator) {

  $list = "";
  $ages = array();

  for($i = 0; count($all_albumpaths) > $i; $i++) array_push($ages, filemtime($all_albumpaths[$i]));
  array_multisort($ages, SORT_DESC, $all_albumpaths);
  $limit = $top_newest_albums;
  if($top_newest_albums > count($all_albumpaths)) $limit = count($all_albumpaths);

  for($i = 0; $limit > $i; $i++) {

    $img_order = update_imageorderfile($all_albumpaths[$i], $sorting_content);
    
    $list .= create_thumbnail($all_albumpaths[$i], (isset($img_order[0])) ? $img_order[0] : "", $max_thumbwidthheight_on_startpage, $thumbquality, $top_newest_thumbnail_wrapper, $user_rights, $langCore, $template, $separator);

  } 

  return $list . "<div style=\"clear:both;\"></div>";

}






/* 
 * SEARCH FILES
 *
 * Will search $searchString in all given $albumpaths and names of their files.
 * If $searchString matches in a albumpath -> returns all files in album/category.
 * If $searchString matches in a name (+extension) of file -> returns file. 
 * It is not case-sensitive and will not return files twice.  
 *
 * Parameter:
 * String  $searchString  (string user is searching for. format: more searches can be added to one by connecting the strings with plus symbols)
 * Array[] $albumpaths  (visible albumpaths where function will search. format of each string: ../albums/[album]/)    
 *                      
 * Returns:
 * Array[Array['albumpath']['file']]  (Array with found files and their albumpath)      
 */     	  
function search_files($searchString, $albumpaths, $max_thumbwidthheight, $thumbquality) {

  // init array for returning found files //
  $found = array();

  // split searchString. users can combine searches by connecting search strings with plus symbol //
  $searchStrings = explode("+", $searchString);
  for($h = 0; $h < count($searchStrings); $h++) {

    // go through all albums //
    for($i = 0; $i < count($albumpaths); $i++) {
    
      // if search string occurs in the albumpath //
      $pathMatches = false;
      $parted = explode(" ", $searchStrings[$h]);
      for($m = 0; $m < count($parted); $m++) if(stripos(substr($albumpaths[$i], 9), $parted[$m]) !== false) $pathMatches = true;  // the !== is important!
      if($pathMatches) {
      
        // add all files of album to found //
        $files = update_imageorderfile($albumpaths[$i], "alphabetic");
        for($n = 0; $n < count($files); $n++)
          if(!in_array(array($albumpaths[$i], $files[$n]), $found)) array_push($found, array($albumpaths[$i], $files[$n]));
    
      } else {

        // go through all files in album //
        $files = update_imageorderfile($albumpaths[$i], "alphabetic");
        for($j = 0; $j < count($files); $j++)
          // if search string occurs in files name (including extension) //
          if(stripos($files[$j], $searchStrings[$h]) !== false)
            // add array with file's albumpath and name to found array which will be returned //
            if(!in_array(array($albumpaths[$i], $files[$j]), $found)) array_push($found, array($albumpaths[$i], $files[$j]));
      }
      
    }
  
  }
  
  return $found;

}






/* 
 * GENERATE LANGUAGE SWITCHER
 *
 * 
 *
 * Parameter:
 * String  $selectedLanguage  (can be 'de' or 'en' ...)
 * Array[String]  $supportedLangs  (can be 'de' or 'en' ...)
 * String  $template
 *                        
 * Returns:
 * String  (html for switcher)      
 */     	  
function generate_language_switcher($selectedLanguage, $supportedLangs, $template, $url, $admin = false) {

  $language_switcher = "";
  $additionalPath = ($admin) ? "../" : "";

  for($i = 0; $i < count($supportedLangs); $i++) {
    if(is_file($additionalPath . "../lang/lang_" . $supportedLangs[$i] . ".php")) {
      $selected = ($selectedLanguage == $supportedLangs[$i]) ? "class=\"selectedLang\"" : "";
      $language_switcher .= "<a data-lang=\"" . $supportedLangs[$i] . "\" href=\"" . $url . "language=" . $supportedLangs[$i] . "\"><img " . $selected . " src=\"" . $additionalPath . "../templates/" . $template . "/imgdata/lang_" . $supportedLangs[$i] . ".png\" /></a>";
    }
  }

  if($language_switcher == "") $language_switcher .= "<p>Error: No language found!</p>";

  return $language_switcher; 

}






/* 
 * GET USER RIGHTS
 *
 * Get rights of $user_name in an array.
 *
 * Parameter:
 * String  $user_name  (name of user)
 * String  $path_user_db  (path to user.db where userdata is stored)
 *                        
 * Returns:
 * Array[String]  ()      
 */     	  
function get_user_rights($user_name, $path_user_db, $rightsVisitor) {

  if($user_name === -1 OR $user_name === -2) return $rightsVisitor; 

  $user_db = sqlite_open($path_user_db);
  $users = sqlite_query($user_db, "SELECT * FROM userTab");
  while($row = sqlite_fetch_array($users)) {
    if($row['userName'] == $user_name) {
      $user_rights = explode(",", $row['userRights']);
      for($i = 0; count($user_rights) > $i; $i++) $user_rights[$i] = rtrim(ltrim($user_rights[$i]));
      return $user_rights;
    }
  }
    
}






/* 
 * CHECK ADMIN
 *
 * Checks existence of the user db and existence of an admin-user.
 * If user db doesnt exist, it creates one.
 *
 * Parameter:
 * String  $path_user_db  (path to user.db where userdata is stored)
 *                        
 * Returns:
 * Bool      
 */     	  
function check_admin($path_user_db) {

  $user_db = sqlite_open($path_user_db);

  $users = @sqlite_query($user_db, "SELECT * FROM userTab");
  
  if(!$users) {
  
    sqlite_query($user_db, "CREATE TABLE userTab(id integer PRIMARY KEY, userName text UNIQUE NOT NULL, userPw text NOT NULL, userRights text, userMail text)");

    $users = sqlite_query($user_db, "SELECT * FROM userTab");

  }
  
  sqlite_close($user_db);
  
  return;
    
}

?>