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
if(!isset($_GET['album'])) $_GET['album'] = "";
if(!isset($_GET['folder'])) $_GET['folder'] = "";
if(!isset($_GET['file'])) $_GET['file'] = "";
if(!isset($_GET['files'])) $_GET['files'] = "";
if(!isset($_GET['new_names'])) $_GET['new_names'] = "";
if(!isset($_GET['also_subfolders'])) $_GET['also_subfolders'] = "";
if(!isset($_GET['destination'])) $_GET['destination'] = "";
if(!isset($_GET['destination_order'])) $_GET['destination_order'] = "";
if(!isset($_GET['new_order'])) $_GET['new_order'] = "";
if(!isset($_GET['album_from'])) $_GET['album_from'] = "";
if(!isset($_GET['album_to'])) $_GET['album_to'] = "";
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
$user_rights = get_user_rights($user_name, "../../users/user.db", $rightsVisitor);

if(in_array('admin', $user_rights) OR in_array('can_edit_content', $user_rights)) {

  function albumOrderfileReplace($folderpath, $oldname, $newname) {

    // check for orderfile //
    if(is_file($folderpath . "order.php")) {
    
      // get order //
      $order = update_albumorderfile($folderpath, true, $sorting_menu, $htaccess_protection);
  
      // replace old with new name and build new order as string for writing in file //
      $str_order = "array(";
      for($i = 0; $i < count($order); $i++) {
        if($order[$i] == $oldname) {
          $str_order .= "\"" . $newname . "\",";
        } else {
          $str_order .= "\"" . $order[$i] . "\",";
        }
      }
      $str_order .= ")";
  
      // write new order in file //
      @$file = fopen($folderpath . "order.php", "w");
      @rewind($file);
      @fwrite($file, "<?php\n\n\$order = " . $str_order . ";\n\n?>");
      @fclose($file);
  
      // if order taken from begining is different to new -> replacing succeed //
      $order_from_begining = $order;
      @include $folderpath . "order.php";
      if($order != $order_from_begining) {
        return true;
      } else {
        return false;
      }
      
    } else {
      return false;
    }
  
  }

  function imageOrderfileReplace($albumpath, $oldname, $newname) {

    // check for orderfile //
    if(is_file($albumpath . "order.php")) {
    
      // get order //
      $order = update_imageorderfile($albumpath, true, $sorting_menu, $htaccess_protection);
  
      // replace old with new name and build new order as string for writing in file //
      $str_order = "array(";
      for($i = 0; $i < count($order); $i++) {
        if($order[$i] == $oldname) {
          $str_order .= "\"" . $newname . "\",";
        } else {
          $str_order .= "\"" . $order[$i] . "\",";
        }
      }
      $str_order .= ")";
  
      // write new order in file //
      @$file = fopen($albumpath . "order.php", "w");
      @rewind($file);
      @fwrite($file, "<?php\n\n\$order = " . $str_order . ";\n\n?>");
      @fclose($file);
  
      // if order taken from begining is different to new -> replacing succeed //
      $order_from_begining = $order;
      @include $albumpath . "order.php";
      if($order != $order_from_begining) {
        return true;
      } else {
        return false;
      }
      
    } else {
      return false;
    }
  
  }
  
  function imageOrderfileReplaceAll($albumpath, $oldname, $newname) {

    // check for orderfile //
    if(is_file($albumpath . "order.php")) {
    
      // get order //
      $order = update_imageorderfile($albumpath, true, $sorting_menu, $htaccess_protection);
  
      // replace old with new name and build new order as string for writing in file //
      $str_order = "array(";
      for($i = 0; $i < count($order); $i++) {
        if($order[$i] == $oldname) {
          $str_order .= "\"" . $newname . "\",";
        } else {
          $str_order .= "\"" . $order[$i] . "\",";
        }
      }
      $str_order .= ")";
  
      // write new order in file //
      @$file = fopen($albumpath . "order.php", "w");
      @rewind($file);
      @fwrite($file, "<?php\n\n\$order = " . $str_order . ";\n\n?>");
      @fclose($file);
  
      // if order taken from begining is different to new -> replacing succeed //
      $order_from_begining = $order;
      @include $albumpath . "order.php";
      if($order != $order_from_begining) {
        return true;
      } else {
        return false;
      }
      
    } else {
      return false;
    }
  
  }

  function get_folderpaths($structure, $last_keys) {
  
    global $node;
    global $folderpaths; 
    if($last_keys == "") {
      $node = 0;
      $folderpaths = array();
    } else {
      $last_keys = $last_keys . "/";
    }

    foreach($structure as $key => $value) {

      $new = array($last_keys . $key, "node" . $node++);
      array_push($folderpaths, $new);
      if($value != "") get_folderpaths($value, $last_keys . $key);
      
    }    
    return $folderpaths;

  }
  
  function get_folderid($folder, $folderpaths) {
  
    for($i = 0; $i < count($folderpaths); $i++) {
    
      if($folder == $folderpaths[$i][0]) return $folderpaths[$i][1];
    
    }
    return "illegal_path";
  
  }

  $albumpaths = get_albumpaths("../../" . $contentRootDir, true, $sorting_menu, $htaccess_protection);
  $structure = array("Menu" => generate_structure($albumpaths));  
  $folderpaths = get_folderpaths($structure, "");

  if($_GET['ajax_request'] == "get_folder_tree") {

    echo "<li id=\"node0\" noDrag=\"true\" class=\"sf-no-link\"><a href=\"javascript: select_folder('Menu', true)\"><span style=\"cursor:pointer;\">" . $lang['template']['button_home'] . " (Root)</span></a></li>";
    $visible_albumpaths = get_albumpaths("../../" . $contentRootDir, true, $sorting_menu, $htaccess_protection, true);
    if($visible_albumpaths[0] != "") {
      $visible_structure = generate_structure($visible_albumpaths);
      echo generate_menu($visible_structure, "", $user_rights, "../../" . $contentRootDir);
    }
    exit;
    
  }

  if($_GET['ajax_request'] == "get_folder_content") {

    $id = get_folderid($_GET['folder'], $folderpaths);

    if($id != "illegal_path" AND $id != "node0") {

      $t = explode("/", $_GET['folder']);  
      $name = $t[count($t)-1];

      if(in_array("../../albums" . substr($_GET['folder'], 4) . "/", $albumpaths)) {
        $is_category = "false";
      } else {
        $is_category = "true";
      }

      if($is_category == "false") {
        if(count(update_imageorderfile("../../albums" . substr($_GET['folder'], 4) . "/", $sorting_content)) == 0) {
          $is_empty = "true";
        } else {
          $is_empty = "false";
        }
      } else {
        $is_empty = "true";
      }  
      
      // check if folder is hidden //
      $hidden_album = false;
      @include "../../albums" . substr($_GET['folder'], 4) . "/index.php";
      $is_hidden = ($hidden_album == true) ? "hidden" : "notHidden";

      // check if a parent folder is hidden //
      $parent_folder = "albums" . substr(substr($_GET['folder'], 0, (-1) * (strlen(strrchr($_GET['folder'], "/")))), 4);    
      $num = count(explode("/", $parent_folder));
      for($i = 0; $num > $i; $i++) {
        $hidden_album = false;
        @include "../../" . $parent_folder . "/index.php";
        if($hidden_album AND $_GET['folder'] != "Menu") { 
          $is_hidden = "hiddenByParent"; 
          break;
        }
        $parent_folder = substr($parent_folder, 0, (-1) * (strlen(strrchr($parent_folder, "/"))));
      }
      
      $folder_infos = array( "id"                => $id,
                             "name"              => $name,
                             "is_category"       => $is_category,
                             "modificationDate"  => date("F d Y - H:i", filemtime("../../albums" . substr($_GET['folder'], 4) . "/")),
                             "is_own_order"      => (is_file("../../albums" . substr($_GET['folder'], 4) . "/order.php")) ? "true" : "false",
                             "is_empty"          => $is_empty,
                             "is_hidden"         => $is_hidden, 
                             "views"             => folderviews("../../albums" . substr($_GET['folder'], 4) . "/", 1),
                             "content"           => generate_main_content("../../albums" . substr($_GET['folder'], 4) . "/", $max_thumbwidthheight, $thumbquality, $thumbnail_wrapper, $sorting_content, $user_rights, false, $lang['core'], $template, $separator) );

    } else {

      $visible_albumpaths = get_albumpaths("../../albums/", true, $sorting_menu, $htaccess_protection);

      // check if folder is hidden //
      $hidden_album = false;
      @include "../../albums/index.php";
      $is_hidden = ($hidden_album == true) ? "hidden" : "notHidden";

      $folder_infos = array( "id"                => "bg0",
                             "name"              => "/",
                             "is_category"       => "true",
                             "modificationDate"  => date("F d Y - H:i", filemtime("../../albums/")),
                             "is_own_order"      => (is_file("../../albums/order.php")) ? "true" : "false",
                             "is_empty"          => "true",
                             "is_hidden"         => $is_hidden, 
                             "views"             => folderviews("../../albums/", 1),
                             "content"           => generate_startpage($show_views, $top_most_viewed_albums, $top_newest_albums, $code_on_startpage, $visible_albumpaths, $sorting_content, $max_thumbwidthheight_on_startpage, $top_most_viewed_thumbnail_wrapper, $top_newest_thumbnail_wrapper, $user_rights, $max_thumbwidthheight, $thumbquality, $lang['core'], $template, $separator) );
    
    }
        
    echo json_encode($folder_infos);
    exit;
  
  }

  if($_GET['ajax_request'] == "add_folder") {

    if(get_folderid($_GET['folder'], $folderpaths) !== "illegal_path") {

      $foldername = $_GET['name'];
      $path = "../../albums" . substr($_GET['folder'], 4) . "/";

      if(strspn($foldername, "yaqwsxcderfvbgtzhnmjuiklopYAQWSXCDERFVBGTZHNMJUIKLOP0123456789_ +-!$%&()=ßäöüÄÖÜ,;^") !== strlen($foldername)) {
      
        echo $lang['admin']['folder']['char_error'] . " a-z 0-9 ß ä ü ö _ + - ! $ % & ( ) = , ; ^";
        
      } elseif($foldername == "") {
      
        echo $lang['admin']['folder']['noname_error'];
        
      } elseif(is_dir($path . $foldername)) {
      
        echo $lang['admin']['folder']['doublename_error'];
        
      } elseif(count(update_imageorderfile("../../albums" . substr($_GET['folder'], 4) . "/", $sorting_content)) != 0) {
      
        echo $lang['admin']['folder']['notempty_error'];
        
      } elseif(mkdir($path . $foldername, 0775)) {
                  
        echo "succeed";     
        
      } else {
      
        echo $lang['admin']['folder']['add_error'];
        
      }
      
    }
    exit;

  } 
  
  if($_GET['ajax_request'] == "rename_folder") {

    if(get_folderid($_GET['folder'], $folderpaths) !== "illegal_path") {

      $newfoldername = $_GET['name'];
      $oldfoldername = substr(strrchr($_GET['folder'], "/"), 1);
      $folderpath = "../../albums" . substr($_GET['folder'], 4) . "/";
      $parent_folderpath = "../../albums" . substr(substr($_GET['folder'], 0, (-1) * (strlen(strrchr($_GET['folder'], "/")))), 4) . "/";

      if(strspn($newfoldername, "yaqwsxcderfvbgtzhnmjuiklopYAQWSXCDERFVBGTZHNMJUIKLOP0123456789_ +-!$%&()=ßäöüÄÖÜ,;^") !== strlen($newfoldername)) {
      
        echo $lang['admin']['folder']['char_error'] . " a-z 0-9 ß ä ü ö _ + - ! $ % & ( ) = , ; ^";
        
      } elseif($newfoldername == "") {
      
        echo $lang['admin']['folder']['noname_error'];
        
      } elseif(is_dir($parent_folderpath . $newfoldername)) {
      
        echo $lang['admin']['folder']['doublename_error'];
        
      } elseif($folderpath == "../../albums/") {
      
        echo $lang['admin']['folder']['renameroot_error'];

      } else {

        // if own order is defined in parent dir -> replace old with new foldername in order file //
        if(is_file($parent_folderpath . "order.php")) {
          if(!albumOrderfileReplace($parent_folderpath, $oldfoldername, $newfoldername)) echo $lang['admin']['folder']['saveownorder_error'];     
        }
        if(rename($folderpath, $parent_folderpath . $newfoldername)) {
          echo "succeed";
        } else {
          echo $lang['admin']['folder']['rename_error'];
        }

      }
      
    }
    exit;


  } 
  
  if($_GET['ajax_request'] == "delete_folder") {

    function delete_all($directory) {
      
      // go through each file and directory //
      $handle = opendir($directory);         
      while($contents = readdir($handle)) { 
        if($contents != "." && $contents != "..") {  
          if(is_dir($directory . "/" . $contents)) {
           
            // recursive call if is directory to delete //
            delete_all($directory . "/" . $contents);
             
          } else {
           
            // if file -> simple unlink //
            unlink($directory . "/" . $contents); 
          
          } 
        } 
      } 
      closedir($handle);
      
      // try to delete dirctory. if fail -> return false //   
      if(!rmdir($directory)) return false;

      // deleting succeed! //  
      return true;
        
    }

    if(get_folderid($_GET['folder'], $folderpaths) !== "illegal_path") {

      $folderpath = "../../albums" . substr($_GET['folder'], 4) . "/";

      if($folderpath == "../../albums/") {
      
        echo $lang['admin']['folder']['deleteroot_error'];
        
      } elseif(delete_all($folderpath)) {
                  
        echo "succeed";     
        
      } else {
      
        echo $lang['admin']['folder']['delete_error'];
        
      }
      
    }
    exit;

  }

  if($_GET['ajax_request'] == "reset_order") {

    function reset_order($directory, $also_subfolders) {

      // if directory has an own defined order //
      if(is_file($directory . "order.php")) {
      
        // delete its order file //
        if(!unlink($directory . "order.php")) return false;
      
      }
      
      // if also reset subfolders orders //
      if($also_subfolders) {
      
        // search for folders in directory //
        $handle = opendir($directory);         
        while($contents = readdir($handle)) { 
          if($contents != "." AND $contents != "..") {
            if(is_dir($directory . "/" . $contents)) {
            
              // reset found folders order //
              if(!reset_order($directory . "/" . $contents . "/", $also_subfolders)) return false;
              
            }
          } 
        } 
        closedir($handle);
            
      }
      
      return true;
        
    }
 
    if(get_folderid($_GET['folder'], $folderpaths) != "illegal_path") {

      $folderpath = "../../albums" . substr($_GET['folder'], 4) . "/";
      $also_subfolders = false;
      if($_GET['also_subfolders'] == 'true') $also_subfolders = true;

      if(reset_order($folderpath, $also_subfolders)) {
                  
        echo "succeed";     
        
      } else {
      
        echo $lang['admin']['folder']['deleteownorder_error'];
        
      }
      
    }
    exit;

  }

  if($_GET['ajax_request'] == "upload") {

    $folderpath = "../../albums" . substr($_GET['folder'], 4) . "/";

    if(get_folderid($_GET['folder'], $folderpaths) !== "illegal_path") {

      $upload_folder = $folderpath;
  
      if(count($_FILES) > 0) {
  
        if(move_uploaded_file( $_FILES['upload']['tmp_name'], $upload_folder . $_FILES['upload']['name'])) {
          echo 'done1';
        } else {
          echo "fail1";
        }
        
      } else {
  
       /* if(isset($_GET['base64'])) {
          $content = base64_decode(file_get_contents('php://input'));
        } else {    */
          $content = file_get_contents('php://input');
    
        if(file_put_contents($upload_folder . $_SERVER["HTTP_X_FILE_NAME"], $content)) {
          echo 'done2';
        } else {
          echo "fail2";
        }
        
      }
          
    }
    exit;
    
  }
  
  if($_GET['ajax_request'] == "hide_folder") {
  
    $folderpath = "../../albums" . substr($_GET['folder'], 4) . "/";

    if(get_folderid($_GET['folder'], $folderpaths) !== "illegal_path") {
      
      // change hiding setting in index file //
      @$file = fopen($folderpath . "index.php", "w");
      @rewind($file);
      @fwrite($file, "<?php\n\n\$hidden_album = true;  //Set \$hidden_album = false if you want to make this album/category public.\n\n?>");
      @fclose($file);

      // update htaccess file (will look up hiding setting in indexfile) //
      update_htaccess($folderpath, $htaccess_protection);

      // check success //
      @include $folderpath . "index.php";
      if($hidden_album) {
                  
        echo "succeed";     
        
      } else {
      
        // return error //
        echo $lang['admin']['folder']['hide_error'];
        
      }
      
    }
    exit;
  
  } 
  
  if($_GET['ajax_request'] == "unhide_folder") {
  
    $folderpath = "../../albums" . substr($_GET['folder'], 4) . "/";

    if(get_folderid($_GET['folder'], $folderpaths) !== "illegal_path") {
      
      // change hiding setting in index file //
      @$file = fopen($folderpath . "index.php", "w");
      @rewind($file);
      @fwrite($file, "<?php\n\n\$hidden_album = false;  //Set \$hidden_album = true if you want to hide this album/category.\n\n?>");
      @fclose($file);

      // update htaccess file (will look up hiding setting in indexfile) //
      update_htaccess($folderpath, $htaccess_protection);

      // check success //
      @include $folderpath . "index.php";
      if(!$hidden_album) {
                  
        echo "succeed";     
        
      } else {
      
        // return error //
        echo $lang['admin']['folder']['unhide_error'];
        
      }
      
    }
    exit;
  
  }

  /* 
   * RENAME FILES
   *
   * 
   *
   * Parameter:
   * String  $_GET['album']  (path to album. format: 'Menu/[category]/[album]')
   * String  $_GET['files']  (filename(s) with extensions. separated by |*|)    
   *                      
   * Returns:
   * String  (new renamed filenames .separated by |*|)      
   */     	  
  if($_GET['ajax_request'] == "rename_files") {

    function renameFile($albumpath, $oldFilename, $newFilename) {
    
      $extension =  strrchr($oldFilename, ".");
      $allfiles = update_imageorderfile($albumpath, $sorting_content);

      if($newFilename . $extension == $oldFilename) {
      
        return $newFilename;
      
      } elseif(in_array($oldFilename, $allfiles)) {

        if(strspn($newFilename, "yaqwsxcderfvbgtzhnmjuiklopYAQWSXCDERFVBGTZHNMJUIKLOP0123456789_ +-!$%&()=ßäöüÄÖÜ,;^") != strlen($newFilename)) {          ///////////// todo!
        
          return false;
          
        } elseif($newFilename == "") {
        
          return false;
          
        } else {

          // if filename already exists, add (new[1,2,..]) to name //
          if(in_array($newFilename . $extension, $allfiles)) {
            if(in_array($newFilename . " (new)" . $extension, $allfiles)) {
              if(in_array($newFilename . " (new1)" . $extension, $allfiles)) {
                for($i = 1; in_array($newFilename . " (new" . $i . ")" . $extension, $allfiles); $i++) $tmp = $newFilename . " (new" . $i + 1 . ")";
              } else {
                $tmp = $newFilename . " (new1)";
              }
            } else {
              $tmp = $newFilename . " (new)";
            }
            $newFilename = $tmp;
          }
          
          // if own order is defined in album dir -> replace old with new filename in order file //
          if(is_file($albumpath . "order.php")) imageOrderfileReplace($albumpath, $oldFilename, $newFilename . $extension);     
          
          if(rename($albumpath . $oldFilename, $albumpath . $newFilename . $extension)) {
            return $newFilename;
          } else {
            return false;  //echo "Error: Renaming file faild! For help visit <a href=\"forum.oog-gallery.de\">forum.oog-gallery.de</a>";
          }
    
        }
        
      }
    
    }

    $_GET['album'] = substr($_GET['album'], 5);
    $albumpath = "../../albums/" . $_GET['album'] . "/";  

    if(in_array($albumpath, $albumpaths)) {

      $newfilename = $_GET['new_name'];
      $oldfilenames = explode("|*|", $_GET['files']);
      
      $changedNames = "";
      
      for($i = 0; $i < count($oldfilenames); $i++) {
      
        $num = ($i > 0) ? " " . $i : "";

        $changedName = renameFile($albumpath, $oldfilenames[$i], $newfilename . $num);

        $changedNames .= ($changedName) ? $changedName . strrchr($oldfilenames[$i], ".") : $oldfilenames[$i];

        if($i + 1 < count($oldfilenames)) $changedNames .= "|*|";

      }
      
      echo $changedNames;
      
    }
    exit;

  }

  /* 
   * DELETE FILES
   *
   * Deletes files. 
   *
   * Parameter:
   * String  $_GET['album']  (path to album. format: 'Menu/[category]/[album]')
   * String  $_GET['files']  (filename(s) with extensions. separated by |*|)    
   *                      
   * Returns:
   * String  ('succeed' or 'Error: [...]')      
   */     	  
  if($_GET['ajax_request'] == "delete_files") {

    $_GET['album'] = substr($_GET['album'], 5);
    $albumpath = "../../albums/" . $_GET['album'] . "/";  

    if(in_array($albumpath, $albumpaths)) {
    
      $allfiles = update_imageorderfile($albumpath, $sorting_content);
      $files = explode("|*|", $_GET['files']);
      
      for($i = 0; $i < count($files); $i++)
        if(in_array($files[$i], $allfiles)) unlink($albumpath . $files[$i]);
        
      $cmp = update_imageorderfile($albumpath, $sorting_content);
      for($i = 0; $i < count($files); $i++) {

        if(in_array($files[$i], $cmp)) {
          echo $lang['admin']['file']['delete_error'];
          exit;
        }
      }
      echo "succeed";
      
    }
    exit;

  }

  /* 
   * SAVE FILES
   *
   * Delivers single files as download. More files will be packed in a zip archive.
   *
   * Parameter:
   * String  $_GET['album']  (path to album. format: 'Menu/[category]/[album]')
   * String  $_GET['files']  (filename(s) with extensions. separated by |*|)    
   *                      
   * Returns:
   * String  ('succeed' or 'Error: [...]')      
   */     	  
  if($_GET['ajax_request'] == "save_files") {

    $_GET['album'] = substr($_GET['album'], 5);
    $albumpath = "../../albums/" . $_GET['album'] . "/";  

    if(in_array($albumpath, $albumpaths)) {
    
      $allfiles = update_imageorderfile($albumpath, $sorting_content);
      $files = explode("|*|", $_GET['files']);
      $cnfFiles = array();

      for($i = 0; $i < count($files); $i++)
        if(in_array($files[$i], $allfiles)) array_push($cnfFiles, $files[$i]);

      if(count($cnfFiles) > 1) {

        $zip = new ZipArchive();
        if ($zip->open($albumpath . "tmp.zip", ZIPARCHIVE::CREATE)!== TRUE) exit("cannot open");

        for($i = 0; $i < count($cnfFiles); $i++) $zip->addFile($albumpath . $cnfFiles[$i], $cnfFiles[$i]);

        $zip->close();

        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=\"" . substr(strrchr(substr($albumpath, 0, -1), "/"), 1) . ".zip\"");
        header("Content-Length: " . filesize($albumpath . "tmp.zip"));
        readfile($albumpath . "tmp.zip");
        
        unlink($albumpath . "tmp.zip");
        
      } elseif(strtolower(strrchr($cnfFiles[0], ".")) == ".flv") {

        header("Content-type: video/x-flv");
        header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $cnfFiles[0]) . "\"");
        header("Content-Length: " . filesize($albumpath . $cnfFiles[0]));
        readfile($albumpath . $cnfFiles[0]);

      } elseif(strtolower(strrchr($cnfFiles[0], ".")) == ".mp4") {
      
        header("Content-type: video/mp4");
        header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $cnfFiles[0]) . "\"");
        header("Content-Length: " . filesize($albumpath . $cnfFiles[0]));
        readfile($albumpath . $cnfFiles[0]);
      
      } elseif(strtolower(strrchr($cnfFiles[0], ".")) == ".mp3") {
      
        header("Content-type: audio/mpa");
        header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $cnfFiles[0]) . "\"");
        header("Content-Length: " . filesize($albumpath . $cnfFiles[0]));
        readfile($albumpath . $cnfFiles[0]);
      
      } elseif(strtolower(strrchr($cnfFiles[0], ".")) == ".jpg" OR strtolower(strrchr($file, ".")) == ".jpeg") {
      
        header("Content-type: image/jpeg");
        header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $cnfFiles[0]) . "\"");
        header("Content-Length: " . filesize($albumpath . $cnfFiles[0]));
        readfile($albumpath . $cnfFiles[0]);

      } elseif(strtolower(strrchr($cnfFiles[0], ".")) == ".gif") {
      
        header("Content-type: image/gif");
        header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $cnfFiles[0]) . "\"");
        header("Content-Length: " . filesize($albumpath . $cnfFiles[0]));
        readfile($albumpath . $cnfFiles[0]);

      } elseif(strtolower(strrchr($cnfFiles[0], ".")) == ".png") {
      
        header("Content-type: image/png");
        header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $cnfFiles[0]) . "\"");
        header("Content-Length: " . filesize($albumpath . $cnfFiles[0]));
        readfile($albumpath . $cnfFiles[0]);

      }

    }
    exit;

  }

  /* 
   * MOVE FOLDER
   *
   * Moves a folder. 
   *
   * Parameter:
   * String  $_GET['folder']  (path to folder. format: 'Menu/[category]/[album]')
   * String  $_GET['destination']  (path where to move. can be categorie or empty album! format: 'Menu/[category]/[album]')    
   *                      
   * Returns:
   * String  ('succeed' or 'Error: [...]')      
   */     	  
  if($_GET['ajax_request'] == "move_folder") {

    $_GET['folder'] = substr($_GET['folder'], 5);
    $folderpath = "../../albums/" . $_GET['folder'] . "/";

    $_GET['destination'] = substr($_GET['destination'], 5);
    $destinationpath = "../../albums/" . $_GET['destination'] . "/";    

    if(in_array($folderpath, $folderpaths) AND in_array($destinationpath, $folderpaths)) {
    
      $numFiles = (in_array($destinationpath, $albumpaths)) ? update_imageorderfile() : 0;       //?bekifft oderwas?
      $parent_folderpath = "../../albums" . substr(substr($_GET['folder'], 0, (-1) * (strlen(strrchr($_GET['folder'], "/")))), 4) . "/";
        
      if($numFiles != 0) {
      
        echo $lang['admin']['folder']['cant_move'];
        
      } elseif($destinationpath == $parent_folderpath) {
      // if this -> just change/create own defined order of folders //
        
        $destOrder = explode("*:;\\/#OOG", $_GET['destination_order']); //problem: 
      
      } else {
      
      }
      
    }
    exit;

  }

  /* 
   * CHANGE FILE ORDER
   *
   * Changes/creates an own defined order of files in the given albumpath. 
   *
   * Parameter:
   * String  $_GET['album']  (path to album. format: 'Menu/[category]/[album]')
   * String  $_GET['new_order']  (new order of files. filenames are seperated by following string: |*|)    
   *                      
   * Returns:
   * String  ('succeed' or 'Error: [...]')      
   */     	  
  if($_GET['ajax_request'] == "change_file_order") {

    $_GET['album'] = substr($_GET['album'], 5);
    $albumpath = "../../albums/" . $_GET['album'] . "/";

    $new_order = explode("|*|", $_GET['new_order']);    

    if(in_array($albumpath, $albumpaths)) {
    
      $current_order = update_imageorderfile($albumpath, $sorting_content);
      
      if($current_order == $new_order) {
        echo "succeed";
        exit;
      }
      
      $cmpCnt = 0;

      $new_order_string = "<?php\n\n\$order = array(";
      for($i = 0; $i < count($new_order); $i++) {
        if(in_array($new_order[$i], $current_order)) {
          $new_order_string .= "\"" . $new_order[$i] . "\"";
          if($i < (count($new_order) - 1)) $new_order_string .= ",";
          $cmpCnt++;
        }
      }
      $new_order_string .= ");\n\n?>";

      if($cmpCnt != count($current_order)) {
      
        echo $lang['admin']['file']['saveownorder_error'];
        
      } else {
      
        // write new order in file //
        @$file = fopen($albumpath . "order.php", "w");
        @rewind($file);
        @fwrite($file, $new_order_string);
        @fclose($file);
    
        // if order taken from begining is different to new -> replacing succeed //
        @include $albumpath . "order.php";
        if($order != $current_order) {
          echo "succeed";
        } else {
          echo $lang['admin']['file']['saveownorder_error'];
        }

      }
      
    }
    exit;

  }
  
  /* 
   * MOVE FILES
   *
   * Moves files from album to album.
   *
   * Parameter:
   * String  $_GET['album_from']  (path where to take files from. format: 'Menu/[category]/[album]')
   * String  $_GET['album_to']  (path where to move files to. format: 'Menu/[category]/[album]')    
   * String  $_GET['files']  (files to move. files are seperated by following string: |*|)
   *                          
   * Returns:
   * String  ('succeed' or 'Error: [...]')      
   */     	  
  if($_GET['ajax_request'] == "move_files") {

    $_GET['album_from'] = substr($_GET['album_from'], 5);
    $albumpathFrom = "../../albums/" . $_GET['album_from'] . "/";

    $_GET['album_to'] = substr($_GET['album_to'], 5);
    $albumpathTo = "../../albums/" . $_GET['album_to'] . "/";    

    if(in_array($albumpathFrom, $albumpaths) AND in_array($albumpathTo, $albumpaths)) {
    
      $files = explode("|*|", $_GET['files']);
      $files_albumpathFrom = update_imageorderfile($albumpathFrom, $sorting_content);
      $cmpCnt = 0;

      for($i = 0; $i < count($files); $i++) {
        if(in_array($files[$i], $files_albumpathFrom)) {
        
          $filename = substr($files[$i], 0, -(strlen(strrchr($files[$i], "."))));
          $fileExt = strrchr($files[$i], ".");
          $filepathTo = $albumpathTo . $filename . $fileExt;
          
          // check for not overwriting existing files //
          if(is_file($filepathTo)) $filepathTo = $albumpathTo . $filename . "-new" . $fileExt;
          for($n = 1; is_file($filepathTo); $n++) $filepathTo = $albumpathTo . $filename . "-new" . $n . $fileExt;

          // try to move file //
          if(@rename($albumpathFrom . $files[$i], $filepathTo)) $cmpCnt++;
           
        }
      }
      
      if($cmpCnt == count($files)) {
      
        echo "succeed";
      
      } else {
      
        echo $lang['admin']['file']['move_error'];
      
      }
      
    }
    exit;

  }
  
}

?>