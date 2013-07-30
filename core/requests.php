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

if(!isset($_GET['action'])) $_GET['action'] = "";
if(!isset($_GET['language'])) $_GET['language'] = "";

include "../gallery_config.php";

$user_language = $default_language;
$langs = array("de", "en");
for($i = 0; count($langs) > $i; $i++) {
  if($_GET['language'] == $langs[$i]) {
    $user_language = $langs[$i]; 
    break;
  }
}
include "../lang/lang_" . $user_language . ".php";

include "../templates/" . $template . "/template_config.php";

include "functionslibrary.php";

$user_name = session_manager("", "", "", "../users/user.db", "sessions/");
$user_rights = get_user_rights($user_name, "../users/user.db", $rightsVisitor);

///////////////////////////////////////////////////////////////STREAM_CONTENT///////////////////////////////////////////////////////////////
  
  if($_GET['action'] == "stream") {
  
    if(!isset($_GET['album'])) $_GET['album'] = "";
    if(!isset($_GET['file'])) $_GET['file'] = "";

    $hidden_albums_access = (in_array('hidden_albums_access', $user_rights) OR in_array('admin', $user_rights)) ? true : false;
    $visible_albumpaths = get_albumpaths("../" . $contentRootDir, $hidden_albums_access, $sorting_menu, $htaccess_protection);
        
    $albumpath = "../" . $contentRootDir . urldecode($_GET['album']) . "/";
    $file = urldecode($_GET['file']);

    if(in_array($albumpath, $visible_albumpaths)) {
      $all_files = update_imageorderfile($albumpath, $sorting_content);
      if(in_array($file, $all_files) AND $_GET['file'] != "") {

        if(strtolower(strrchr($file, ".")) == ".flv") {

          header("Content-type: video/x-flv");
          header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $file) . "\"");
          header("Content-Length: " . filesize($albumpath . $file));
          // readfile($albumpath . $file);

          $f = fopen($albumpath . $file, "r");
          while($chunk = fread($f, 8192)) echo $chunk;
          fclose($f);

        } elseif(strtolower(strrchr($file, ".")) == ".mp4") {
        
          header("Content-type: video/mp4");
          header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $file) . "\"");
          header("Content-Length: " . filesize($albumpath . $file));
          readfile($albumpath . $file);
        
        } elseif(strtolower(strrchr($file, ".")) == ".mp3") {
        
          header("Content-type: audio/mpa");
          header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $file) . "\"");
          header("Content-Length: " . filesize($albumpath . $file));
          readfile($albumpath . $file);
        
        } elseif(strtolower(strrchr($file, ".")) == ".jpg" OR strtolower(strrchr($file, ".")) == ".jpeg") {
        
          header("Content-type: image/jpeg");
          header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $file) . "\"");
          header("Content-Length: " . filesize($albumpath . $file));
          readfile($albumpath . $file);
           
          // load PHP Exif Library //
          require_once("./admin/pel/PelJpeg.php");

          $jpeg = new PelJpeg($albumpath . $file);

          /* The PelJpeg object contains a number of sections, one of which
           * might be our Exif data. The getExif() method is a convenient way
           * of getting the right section with a minimum of fuzz. */
          $exif = $jpeg->getExif();
        
          if ($exif == null) {
            /* Ups, there is no APP1 section in the JPEG file.  This is where
             * the Exif data should be. */
        
            /* In this case we simply create a new APP1 section (a PelExif
             * object) and adds it to the PelJpeg object. */
            $exif = new PelExif();
            $jpeg->setExif($exif);
        
            /* We then create an empty TIFF structure in the APP1 section. */
            $tiff = new PelTiff();
            $exif->setTiff($tiff);
            
          } 
          
          /* Surprice, surprice: Exif data is really just TIFF data!  So we
           * extract the PelTiff object for later use. */
          $tiff = $exif->getTiff();
          
          /* TIFF data has a tree structure much like a file system.  There is a
           * root IFD (Image File Directory) which contains a number of entries
           * and maybe a link to the next IFD.  The IFDs are chained together
           * like this, but some of them can also contain what is known as
           * sub-IFDs.  For our purpose we only need the first IFD, for this is
           * where the image description should be stored. */
          $ifd0 = $tiff->getIfd();
          
          if ($ifd0 == null) {          
            /* No IFD in the TIFF data?  This probably means that the image
             * didn't have any Exif information to start with, and so an empty
             * PelTiff object was inserted by the code above.  But this is no
             * problem, we just create and inserts an empty PelIfd object. */
            $ifd0 = new PelIfd(PelIfd::IFD0);
            $tiff->setIfd($ifd0);            
          }
          
          /* Each entry in an IFD is identified with a tag.  This will load the
           * ImageDescription entry if it is present.  If the IFD does not
           * contain such an entry, null will be returned. */
          $desc = $ifd0->getEntry(PelTag::IMAGE_DESCRIPTION);
          
          /* We need to check if the image already had a description stored. */
          if ($desc == null) {
            /* The was no description in the image. */
          
            /* In this case we simply create a new PelEntryAscii object to hold
             * the description.  The constructor for PelEntryAscii needs to know
             * the tag and contents of the new entry. */
            $desc = new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $description);
          
            /* This will insert the newly created entry with the description
             * into the IFD. */
            $ifd0->addEntry($desc);
            
          } else {
            /* An old description was found in the image. */
          
            /* The description is simply updated with the new description. */
            $desc->setValue($description);
            
          }
          
          $jpeg->saveFile($albumpath . $file);          

        } elseif(strtolower(strrchr($file, ".")) == ".gif") {
        
          header("Content-type: image/gif");
          header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $file) . "\"");
          header("Content-Length: " . filesize($albumpath . $file));
          readfile($albumpath . $file);

        } elseif(strtolower(strrchr($file, ".")) == ".png") {
        
          header("Content-type: image/png");
          header("Content-Disposition: attachment; filename=\"" . basename($albumpath . $file) . "\"");
          header("Content-Length: " . filesize($albumpath . $file));
          readfile($albumpath . $file);

        }

      }

    }
  
  }

///////////////////////////////////////////////////////////////GET THUMBS///////////////////////////////////////////////////////////////
  
  if($_GET['action'] == "getThumbs") {

    if(!isset($_POST['requestedThumbs'])) $_POST['requestedThumbs'] = "";

    $hidden_albums_access = (in_array('hidden_albums_access', $user_rights) OR in_array('admin', $user_rights)) ? true : false;
    $visible_albumpaths = get_albumpaths("../" . $contentRootDir, $hidden_albums_access, $sorting_menu, $htaccess_protection);
        
    $requestedThumbs = $_POST['requestedThumbs'];
    $thumbnails = array();
    
    for($i = 0; $i < count($requestedThumbs); $i++) {

      if($i >= $thumbsLoadingParallel) break;

      $albumpath = "../" . $contentRootDir . $requestedThumbs[$i]['path'] . "/";

      if(in_array($albumpath, $visible_albumpaths)) {
          
        $all_files = update_imageorderfile($albumpath, $sorting_content);
        $file = $requestedThumbs[$i]['filename'];
        
        if(in_array($file, $all_files)) {
  
          $ext = strtolower(strrchr($file, "."));
          if($ext == ".jpg" OR $ext == ".jpeg" OR $ext == ".png" OR $ext == ".gif") {
          
            $thumbs_db = sqlite_open("cache/thumbs.db");
            $thumbs = @sqlite_query($thumbs_db, "SELECT * FROM thumbsTab");
            
            if(!$thumbs) {
              sqlite_query($thumbs_db, "CREATE TABLE thumbsTab(thumbId integer PRIMARY KEY, thumbPathName text UNIQUE NOT NULL)");
              $thumbs = sqlite_query($thumbs_db, "SELECT * FROM thumbsTab");
            }
          
            $thumbId = 0;
            while($row = sqlite_fetch_array($thumbs)) {
              $thumbId++;
              if($row['thumbPathName'] == $albumpath . $file) {
                $is_thumb_entry = true;
                break;
              }
            }
            
            if(!isset($is_thumb_entry)) {
          
              $thumbId++;
              sqlite_query($thumbs_db, "INSERT INTO thumbsTab VALUES(" . $thumbId . ", '" . $albumpath . $file . "')");
          
            }
    
            if(!is_file("cache/" . $thumbId . ".thumb")) {
            
              $pic = false;
            
              if($ext == ".png") {
                $pic = @imagecreatefrompng($albumpath . $file);
              } elseif($ext == ".gif") {
                $pic = @imagecreatefromgif($albumpath . $file);
              } elseif($ext == ".jpg" OR $ext == ".jpeg") {
                $pic = @imagecreatefromjpeg($albumpath . $file);
              }
              
              if(!$pic) $pic = @imagecreatefrompng("../templates/" . $template . "/imgdata/no_thumb.png");
          
              $original_height = imagesy($pic);
              $original_width = imagesx($pic);
          
              if($original_width > $original_height) {
                $thumbnail_width = $max_thumbwidthheight;
                $thumbnail_height = round($original_height / ($original_width / $thumbnail_width));
              } else {
                $thumbnail_height = $max_thumbwidthheight;
                $thumbnail_width = round($original_width / ($original_height / $thumbnail_height));
              }
          
              $thumbnail = ImageCreateTrueColor($thumbnail_width, $thumbnail_height);
              ImageCopyResampled($thumbnail, $pic, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $original_width, $original_height);
              Imagedestroy($pic);
              ImageJpeg($thumbnail, "cache/" . $thumbId . ".thumb", $thumbquality);
              Imagedestroy($thumbnail);
            
            }
  
            array_push($thumbnails, base64_encode(fread(fopen("cache/" . $thumbId . ".thumb", "r"), filesize("cache/" . $thumbId . ".thumb"))));
  
          } else {
        
          
        
          }
          
        }
          
      }

    }
    
  echo json_encode($thumbnails);
  exit;
  
  }

///////////////////////////////////////////////////////////////GET_EXIF_DATA///////////////////////////////////////////////////////////////

  if($_GET['action'] == "exif") {

    if(!isset($_GET['album'])) $_GET['album'] = "";
    if(!isset($_GET['file'])) $_GET['file'] = "";

    $hidden_albums_access = (in_array('hidden_albums_access', $user_rights) OR in_array('admin', $user_rights)) ? true : false;
    $visible_albumpaths = get_albumpaths("../albums/", $hidden_albums_access, $sorting_menu, $htaccess_protection);
        
    $albumpath = urldecode($_GET['album']);
    $file = urldecode($_GET['file']);

    if(in_array($albumpath, $visible_albumpaths)) {
      $all_files = update_imageorderfile($albumpath, $sorting_content);
      if(in_array($file, $all_files) AND $_GET['file'] != "") {
        if(strtolower(strrchr($file, ".")) == ".jpg" OR strtolower(strrchr($file, ".")) == ".jpeg") {
        
          $exif_data = @exif_read_data($albumpath . $file, "", true, false);
          if($exif_data === false) {
            echo "";      
          } else {
            echo "<div style=\"clear: both;\"></div><div style=\"float: left; width: 38%; height: 150px;\">";
            echo "<p>" . $lang['core']['lightbox']['1'] . " " . $file . "</p>";
            echo "<p>" . $lang['core']['lightbox']['2'] . " " . $exif_data["EXIF"]["DateTimeOriginal"] . "</p>";
            echo "<p>" . $lang['core']['lightbox']['3'] . " " . round(filesize($albumpath . $file) / 1024) . " KiB</p>";
            echo "<p>" . $lang['core']['lightbox']['4'] . " " . $exif_data["IFD0"]["Model"] . "</p>";
          $size = @getimagesize($albumpath . $file);
          echo "<p>Height: " . $size[1] . "px Width: " . $size[0] . "px</p>";

            echo "</div>";            
            
            echo "<div style=\"float: right; width: 40%; height: 150px; text-align: right;\">
<iframe width=\"100%\" height=\"134\" style=\"opacity: 0.6; float: right;\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"http://maps.google.de/maps?hl=de&amp;q=google+maps+link+koordinaten&amp;ie=UTF8&amp;t=h&amp;vpsrc=6&amp;fll=50.303156,8.203011&amp;fspn=0.091114,0.264187&amp;st=105250506097979753968&amp;rq=1&amp;ev=zi&amp;split=1&amp;sll=50.40822,8.150585&amp;sspn=0.741973,1.093509&amp;hq=link+koordinaten&amp;hnear=&amp;ll=50.339764,7.723389&amp;spn=0.061354,0.291824&amp;z=11&amp;output=embed\"></iframe><br />
<a href=\"http://maps.google.de/maps?hl=de&amp;q=google+maps+link+koordinaten&amp;ie=UTF8&amp;t=h&amp;vpsrc=6&amp;fll=50.303156,8.203011&amp;fspn=0.091114,0.264187&amp;st=105250506097979753968&amp;rq=1&amp;ev=zi&amp;split=1&amp;sll=50.40822,8.150585&amp;sspn=0.741973,1.093509&amp;hq=link+koordinaten&amp;hnear=&amp;ll=50.339764,7.723389&amp;spn=0.061354,0.291824&amp;z=11&amp;source=embed\" style=\"color:#0000FF;text-align:left\">Größere Kartenansicht</a>
</div>
";

          }
          
        }
        
      }
      
    }
    
  }

///////////////////////////////////////////////////////////////GET_COMMENTS///////////////////////////////////////////////////////////////

  if($_GET['action'] == "comments") {

    echo $lang['core']['lightbox']['7'];
    
  }

///////////////////////////////////////////////////////////////GET FILE DESCRIPTION///////////////////////////////////////////////////////////////

  if($_GET['action'] == "get_filedescription") {

    if(!isset($_GET['album'])) $_GET['album'] = "";
    if(!isset($_GET['file'])) $_GET['file'] = "";

    $hidden_albums_access = (in_array('hidden_albums_access', $user_rights) OR in_array('admin', $user_rights)) ? true : false;
    $visible_albumpaths = get_albumpaths("../albums/", $hidden_albums_access, $sorting_menu, $htaccess_protection);
        
    $albumpath = urldecode($_GET['album']);
    $file = urldecode($_GET['file']);

    if(in_array($albumpath, $visible_albumpaths)) {
      $all_files = update_imageorderfile($albumpath, $sorting_content);
      if(in_array($file, $all_files) AND $_GET['file'] != "") {

        if(strtolower(strrchr($file, ".")) == ".jpg" OR strtolower(strrchr($file, ".")) == ".jpeg") {
          $exif_data = @exif_read_data($albumpath . $file, "", true, false);
          if($exif_data) echo $exif_data["IFD0"]["ImageDescription"];
        }
        
      }      
    }
    
  }

///////////////////////////////////////////////////////////////SAVE FILE DESCRIPTION///////////////////////////////////////////////////////////////

  /* 
   * SAVE FILE DESCRIPTION
   *
   * Saves a description to a file.
   * If file is Jpeg format, description will be stored in Exif header.
   *
   * Parameter:
   * String  $_GET['album']  (path to album. format: '../abums/[category]/[album]/')
   * String  $_GET['file']  (filename with extension)
   * String  $_GET['description']  (the description text)    
   *                      
   * Returns:   
   */     	  
  if($_GET['action'] == "save_filedescription") {

    if(!isset($_GET['album'])) $_GET['album'] = "";
    if(!isset($_GET['file'])) $_GET['file'] = "";

    $hidden_albums_access = (in_array('hidden_albums_access', $user_rights) OR in_array('admin', $user_rights)) ? true : false;
    $visible_albumpaths = get_albumpaths("../albums/", $hidden_albums_access, $sorting_menu, $htaccess_protection);
        
    $albumpath = urldecode($_GET['album']);
    $file = urldecode($_GET['file']);

    if(in_array("albums_management_access", $user_rights) OR in_array("admin", $user_rights) AND in_array($albumpath, $visible_albumpaths)) {
    
      $allfiles = update_imageorderfile($albumpath, $sorting_content);
      
      if(in_array($file, $allfiles)) {
      
        $extension = strtolower(strrchr($file, "."));
        $description = htmlentities($_GET['description']);    /////////////////////////////////////////////////////////////////////////////// check for security

        // if format is Jpeg //
        if($extension == ".jpg" OR $extension == ".jpeg") {

          // load PHP Exif Library //
          require_once("./admin/pel/PelJpeg.php");

          $jpeg = new PelJpeg($albumpath . $file);

          /* The PelJpeg object contains a number of sections, one of which
           * might be our Exif data. The getExif() method is a convenient way
           * of getting the right section with a minimum of fuzz. */
          $exif = $jpeg->getExif();
        
          if ($exif == null) {
            /* Ups, there is no APP1 section in the JPEG file.  This is where
             * the Exif data should be. */
        
            /* In this case we simply create a new APP1 section (a PelExif
             * object) and adds it to the PelJpeg object. */
            $exif = new PelExif();
            $jpeg->setExif($exif);
        
            /* We then create an empty TIFF structure in the APP1 section. */
            $tiff = new PelTiff();
            $exif->setTiff($tiff);
            
          } 
          
          /* Surprice, surprice: Exif data is really just TIFF data!  So we
           * extract the PelTiff object for later use. */
          $tiff = $exif->getTiff();
          
          /* TIFF data has a tree structure much like a file system.  There is a
           * root IFD (Image File Directory) which contains a number of entries
           * and maybe a link to the next IFD.  The IFDs are chained together
           * like this, but some of them can also contain what is known as
           * sub-IFDs.  For our purpose we only need the first IFD, for this is
           * where the image description should be stored. */
          $ifd0 = $tiff->getIfd();
          
          if ($ifd0 == null) {          
            /* No IFD in the TIFF data?  This probably means that the image
             * didn't have any Exif information to start with, and so an empty
             * PelTiff object was inserted by the code above.  But this is no
             * problem, we just create and inserts an empty PelIfd object. */
            $ifd0 = new PelIfd(PelIfd::IFD0);
            $tiff->setIfd($ifd0);            
          }
          
          /* Each entry in an IFD is identified with a tag.  This will load the
           * ImageDescription entry if it is present.  If the IFD does not
           * contain such an entry, null will be returned. */
          $desc = $ifd0->getEntry(PelTag::IMAGE_DESCRIPTION);
          
          /* We need to check if the image already had a description stored. */
          if ($desc == null) {
            /* The was no description in the image. */
          
            /* In this case we simply create a new PelEntryAscii object to hold
             * the description.  The constructor for PelEntryAscii needs to know
             * the tag and contents of the new entry. */
            $desc = new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $description);
          
            /* This will insert the newly created entry with the description
             * into the IFD. */
            $ifd0->addEntry($desc);
            
          } else {
            /* An old description was found in the image. */
          
            /* The description is simply updated with the new description. */
            $desc->setValue($description);
            
          }
          
          $jpeg->saveFile($albumpath . $file);
          
          //////////////////////////////////////////////////////////////////////////////////// todo: check success!
          echo "succeed";
          
        } else {
        
          echo $lang['core']['lightbox']['8'];
        
        }
        
      }
      
    }
    exit;
    
  }

?>