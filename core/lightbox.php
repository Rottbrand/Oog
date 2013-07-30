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

if(!isset($_GET['language'])) { $_GET['language'] = ""; }

include "../gallery_config.php";

$user_language = $default_language;
$langs = array("de", "en");
for($i = 0; count($langs) > $i; $i++) {
  if($_GET['language'] == $langs[$i]) {
    $user_language = $langs[$i]; 
    break;
  }
}
include("../lang/lang_" . $user_language . ".php");

?>

/* 
 * INIT OOG LIGHTBOX
 * 
 * Adds the html for lightbox to document and inits some variables/events.
 *
 * Parameter:
 * String  phpBackEnd  (path to requests.php)
 * String  flowplayer  (path to flowplayer swf)
 *                      
 * Returns: -  
 */     	
function initOogLightbox(backEndPath, flowplayer) {

  var html = '<div style="height: 0; width: 0; visibility: hidden; position: fixed; top: 0px; left: 0px;" id="oog_lightbox_overlay"></div>' + 
'<div id="oog_lightbox_loading" onclick="exitlightbox()" title="You can also cancel loading by pressing ESC">' + 
'<br />' + 
'<p><?php echo $lang['core']['lightbox']['9']; ?></p>' + 
'</div>' + 
'<div id="oog_lightbox_main">' + 
'<table id="oog_lightbox_photo_box" border="0" cellPadding="0" cellSpacing="0">' + 
'<tr>' +
'<td id="oog_lightbox_shad_topleft"></td>' +
'<td id="oog_lightbox_shad_top"></td>' +
'<td id="oog_lightbox_shad_topright"></td>' +
'</tr>' +
'<tr>' +
'<td id="oog_lightbox_shad_left"></td>' +
'<th id="oog_lightbox_photo_border">' +
'<div id="oog_lightbox_photo_wrapper">' +
// Oog-Lightbox will paste file here!
'</div>' +
'<div id="oog_lightbox_filedescription"></div>' +
'<div style="display: none;" id="oog_lightbox_extended_box">' +
'<div id="oog_lightbox_extended_box_request">' +
'</div>' +
'</div>' +
'</th>' +
'<td id="oog_lightbox_shad_right"></td>' +
'</tr>' +
'<tr>' +
'<td id="oog_lightbox_shad_bottomleft"></td>' +
'<td id="oog_lightbox_shad_bottom"></td>' +
'<td id="oog_lightbox_shad_bottomright"></td>' +
'</tr>' +
'<input type="button" onclick="exitlightbox()" id="oog_lightbox_close_but" title="Close | Esc" />' + 
'<div id="oog_lightbox_previous_radius" onclick="previous()">' +
'<input type="button" id="oog_lightbox_previous_but" title="Previous Photo | Left Arrow Key" />' + 
'</div>' + 
'<div id="oog_lightbox_next_radius" onclick="next()">' +
'<input type="button" id="oog_lightbox_next_but" title="Next Photo | Right Arrow Key" />' + 
'</div>' +
'</table>' +
'<div id="oog_lightbox_functions">' +
'<div id="oog_lightbox_functions_buttons" style="text-align:center;">' +
'<input type="button" onclick="save_file()" id="oog_lightbox_save_but" title="Download this File" />' +
'<input type="button" onclick="refresh_lightbox_extended_box(\'exif\',true)" id="oog_lightbox_exif_but" title="Show/Close Exif-Data" />' +
'<input type="button" onclick="refresh_lightbox_extended_box(\'comments\',true)" id="oog_lightbox_comments_but" title="Show/Close Comments" />' +
'<input type="button" onclick="slideshow()" id="oog_lightbox_slideshow_but" title="Start/Stop Sildeshow" />' +
'</div>' +
'</div>' +
'</div>';

  document.write(html);

  var windowheight = (window.innerHeight) ? window.innerHeight : window.document.documentElement.clientHeight;
  var windowwidth = (window.innerWidth) ? window.innerWidth : window.document.documentElement.clientWidth;

  lightbox_main_height_percent = document.getElementById('oog_lightbox_main').offsetHeight / windowheight * 100;
  lightbox_main_width_percent = document.getElementById('oog_lightbox_main').offsetWidth / windowwidth * 100;
  lightbox_main_top_percent = (100 - lightbox_main_height_percent) / 2;
  lightbox_main_left_percent = (100 - lightbox_main_width_percent) / 2;
  oogLightbox_backEndPath = backEndPath;
  oogLightbox_docTitle = document.title;
  lightbox_extended = false;
  isDescription = false;

}

/* 
 * RESIZE LIGHTBOX
 *
 * 
 *
 * Parameter: -                      
 * Returns: -  
 */     	
function resizelightbox() {

  refresh_lightbox_extended_box('', false);

  var windowheight = (window.innerHeight) ? window.innerHeight : window.document.documentElement.clientHeight;
  var windowwidth = (window.innerWidth) ? window.innerWidth : window.document.documentElement.clientWidth;

  var extra_margin_top = 0;
  var extra_height = 0;

  if(lightbox_extended != '') {
    extra_margin_top = extra_margin_top - 100;
    extra_height = extra_height - 150;
  }
  
  if(isDescription) {
    extra_margin_top = extra_margin_top - 12;
    extra_height = extra_height - 36;
  }
  
  var heightspace = Math.round(windowheight  / 100 * lightbox_main_height_percent - 64 + extra_height);
  var widthspace = Math.round(windowwidth / 100 * lightbox_main_width_percent - 54);

  var new_photo_height = 0;
  var new_photo_width = 0;

  if(resize_photos == 'all') {

    new_photo_height = heightspace;    
    new_photo_width = heightspace * (oogLightbox_firstWidth / oogLightbox_firstHeight);

    if(new_photo_width > widthspace) {
      new_photo_width = widthspace;
      new_photo_height = widthspace * (oogLightbox_firstHeight / oogLightbox_firstWidth);
    }

  } else {

    if(oogLightbox_firstHeight > heightspace || oogLightbox_firstWidth > widthspace) {
      new_photo_height = heightspace;    
      new_photo_width = heightspace * (oogLightbox_firstWidth / oogLightbox_firstHeight);

      if(new_photo_width > widthspace) {
        new_photo_width = widthspace;
        new_photo_height = widthspace * (oogLightbox_firstHeight / oogLightbox_firstWidth);
      }
    } else {
      new_photo_height = oogLightbox_firstHeight;    
      new_photo_width = oogLightbox_firstWidth;
    }
    
  }
 
  if(oogLightbox_firstWidth != 0 && oogLightbox_firstHeight != 0) {

      new_photo_width = ( new_photo_height - 7 ) * ( oogLightbox_firstWidth / oogLightbox_firstHeight );
    
      $('#oog_lightbox_photo').height(new_photo_height);
      $('#oog_lightbox_photo').width(new_photo_width);
    
  }
  
  $('#oog_lightbox_filedescription').css('width', new_photo_width + 'px');

  document.getElementById('oog_lightbox_photo_box').style.marginLeft = ( ( $('#oog_lightbox_main').width() - ( new_photo_width + 54 ) ) / 2 ) + 'px';
  document.getElementById('oog_lightbox_photo_box').style.marginTop = ( ( $('#oog_lightbox_main').height() - ( new_photo_height + 64 ) ) / 2 + extra_margin_top ) + 'px';

  document.getElementById('oog_lightbox_shad_top').style.width = new_photo_width + 28 + 'px';
  document.getElementById('oog_lightbox_shad_bottom').style.width = new_photo_width + 28 + 'px';
  document.getElementById('oog_lightbox_shad_left').style.height = new_photo_height + 28 + 'px';
  document.getElementById('oog_lightbox_shad_right').style.height = new_photo_height + 28 + 'px';

  document.getElementById('oog_lightbox_next_radius').style.height = ( new_photo_height / 100 * 85 ) + 'px';
  document.getElementById('oog_lightbox_next_radius').style.width = ( new_photo_width / 100 * 45 ) + 'px';
  document.getElementById('oog_lightbox_next_radius').style.bottom = ( ( $('#oog_lightbox_main').height() - ( new_photo_height + 64 ) ) / 2 + 34 - extra_margin_top ) + 'px';
  document.getElementById('oog_lightbox_next_radius').style.right = ( ( $('#oog_lightbox_main').width() - ( new_photo_width + 54 ) ) / 2 - 16 ) + 'px';

  document.getElementById('oog_lightbox_previous_radius').style.height = ( new_photo_height / 100 * 85 ) + 'px';
  document.getElementById('oog_lightbox_previous_radius').style.width = ( new_photo_width / 100 * 45 ) + 'px';
  document.getElementById('oog_lightbox_previous_radius').style.bottom = ( ( $('#oog_lightbox_main').height() - ( new_photo_height + 64  ) ) / 2 + 34 - extra_margin_top ) + 'px';
  document.getElementById('oog_lightbox_previous_radius').style.left = ( ( $('#oog_lightbox_main').width() - ( new_photo_width + 54 ) ) / 2 - 16 ) + 'px';

  document.getElementById('oog_lightbox_close_but').style.top = ( ( $('#oog_lightbox_main').height() - ( new_photo_height + 64 ) ) / 2 - 6 + extra_margin_top ) + 'px';
  document.getElementById('oog_lightbox_close_but').style.right = ( ( $('#oog_lightbox_main').width() - ( new_photo_width + 54 ) ) / 2 - 10 ) + 'px';

}

/* 
 * LOAD MEDIA
 *
 * 
 *
 * Parameter: -                    
 * Returns: -  
 */     	
function load_media() {

  // make loading visible //
  $('#oog_lightbox_loading').fadeOut(0);
  document.getElementById('oog_lightbox_loading').style.visibility = 'visible';
  $('#oog_lightbox_loading').fadeIn(fadeInOut_loading);

  // clear filewrapper & description //
  document.getElementById('oog_lightbox_photo_wrapper').innerHTML = '';
  document.getElementById('oog_lightbox_filedescription').innerHTML = '';

  if(oogLightbox_fileExtension == 'html') {
  
    var request = false;
    request = new XMLHttpRequest();
    request.onreadystatechange = function() {
      if(request.readyState == 4) {
        if(request.status == 200) {

          var div_tag = document.createElement('div');
          var div_tag_id = document.createAttribute('id');
          div_tag_id.nodeValue = 'oog_lightbox_photo';
          div_tag.setAttributeNode(div_tag_id);
          var div_tag_style = document.createAttribute('style');
          div_tag_style.nodeValue = 'overflow: auto; background: #FFF; margin-left: 14px;';
          div_tag.setAttributeNode(div_tag_style);
          document.getElementById('oog_lightbox_photo_wrapper').appendChild(div_tag);
                  
          // define ratio //
          oogLightbox_firstHeight = 9000;
          oogLightbox_firstWidth = 16000;
    
          // resize before file will be visible //
          resizelightbox();

          $('#oog_lightbox_photo').html(request.responseText);

          // hide loading //
          $('#oog_lightbox_loading').fadeOut(fadeInOut_loading, function() {
            document.getElementById('oog_lightbox_loading').style.visibility = 'hidden';
          });

          // make lightbox visible //
          $('#oog_lightbox_main').fadeOut(0);
          document.getElementById('oog_lightbox_main').style.visibility = 'visible';
          $('#oog_lightbox_main').fadeIn(fadeInOut_file);
          
        }      
      }
    }

    request.open('GET', oogLightbox_path + oogLightbox_file, true);
    request.send(null);
  
  } else if(oogLightbox_fileExtension == 'jpg' || oogLightbox_fileExtension == 'jpeg' || oogLightbox_fileExtension == 'gif' || oogLightbox_fileExtension == 'png') {

    // insert img element for photo into the filewrapper //
    var img_tag = document.createElement('img');
    var img_tag_id = document.createAttribute('id');
    img_tag_id.nodeValue = 'oog_lightbox_photo';
    var img_tag_src = document.createAttribute('src');
    img_tag_src.nodeValue = '';
    var img_tag_border = document.createAttribute('border');
    img_tag_border.nodeValue = '0px';
    img_tag.setAttributeNode(img_tag_id);
    img_tag.setAttributeNode(img_tag_src);
    img_tag.setAttributeNode(img_tag_border);
    document.getElementById('oog_lightbox_photo_wrapper').appendChild(img_tag);

    lightbox_img = new Image();
    lightbox_img.onload = function() {
    // loading photo done //

      // take original dimensions (resizelightbox() needs them) //
      oogLightbox_firstHeight = lightbox_img.height;
      oogLightbox_firstWidth = lightbox_img.width;

      // resize before file will be visible //
      resizelightbox();
            
      // hide loading //
      $('#oog_lightbox_loading').fadeOut(fadeInOut_loading, function() {
        document.getElementById('oog_lightbox_loading').style.visibility = 'hidden';
      });

      // make lightbox visible //
      $('#oog_lightbox_main').fadeOut(0);
      document.getElementById('oog_lightbox_main').style.visibility = 'visible';
      $('#oog_lightbox_main').fadeIn(fadeInOut_file, function() {

        // display description //
        display_filedescription();
        
      });

    }

    // start loading photo //
    lightbox_img.src = oogLightbox_backEndPath + 'stream&album=' + escape(oogLightbox_path) + '&file=' + escape(oogLightbox_file);
    document.getElementById('oog_lightbox_photo').src = oogLightbox_backEndPath + 'stream&album=' + escape(oogLightbox_path) + '&file=' + escape(oogLightbox_file);
    
  } else if(oogLightbox_fileExtension == 'flv' || oogLightbox_fileExtension == 'mp4') {



  } else if(oogLightbox_fileExtension == 'mp3') {



  } else {
  
    alert('Error: Unknown Format');
    exitlightbox();
  
  }

}

/* 
 * START LIGHTBOX
 *
 * Have to be called if you want to show a file with Lightbox. 
 *
 * Parameter: 
 * String  albumpath  (path to album. format: '../albums/[somePath]/')
 * String  photoname  (filename to show in lightbox including extension)
 * Boolean  focusDesc  (focus the description.)    
 *                    
 * Returns: -  
 */     	
function startlightbox(path, file, isHtml, descEdit, focusDesc) {

  // make parameters global //
  oogLightbox_path = path;
  oogLightbox_file = file;
  oogLightbox_isHtml = isHtml;
  if(descEdit) {
    oogLightbox_descEdit = true;
  } else {
    oogLightbox_descEdit = false;
  }
  if(focusDesc && descEdit) {
    oogLightbox_focusDesc = true;
  } else {
    oogLightbox_focusDesc = false;
  }

  // init onresize event //
  window.onresize = function() {
    if(document.getElementById('oog_lightbox_main').style.visibility == 'visible') resizelightbox();
  }
  
  // init onkeydown event //
  window.document.onkeydown = function(event)  { 
    if(!event) event = window.event; 
    var keycode = event.which || event.keyCode; 
  
    // left arrowkey //
    if(keycode==37 && document.getElementById('oog_lightbox_main').style.visibility=='visible' && !descriptionFocused) previous();
    // right arrowkey //
    if(keycode==39 && document.getElementById('oog_lightbox_main').style.visibility=='visible' && !descriptionFocused) next();
    // esc //
    if(keycode==27) exitlightbox();

  }

  // get extension of file //
  if(isHtml) {
    oogLightbox_fileExtension = 'html';
  } else {  
    oogLightbox_fileExtension = oogLightbox_file.substring(oogLightbox_file.lastIndexOf('.') + 1, oogLightbox_file.length).toLowerCase();
  }
  
  // add name of file to document title //
  document.title = oogLightbox_docTitle + ' - ' + oogLightbox_file;

  // if a file is displayed at this moment //
  if(document.getElementById('oog_lightbox_main').style.visibility == 'visible') {

    // hide lightbox, means the file & buttons and then start loading //
    $('#oog_lightbox_main').fadeOut(fadeInOut_file, function() {
      document.getElementById('oog_lightbox_main').style.visibility = 'hidden';

      // start loading file //
      load_media();

    });

  } else {
  // no file is displayed //
  
    // make overlay visible //
    document.getElementById('oog_lightbox_overlay').style.width = '100%';
    document.getElementById('oog_lightbox_overlay').style.height = '100%';
    $('#oog_lightbox_overlay').fadeOut(0);
    document.getElementById('oog_lightbox_overlay').style.visibility = 'visible';
    $('#oog_lightbox_overlay').fadeIn(fadeInOut_overlay);
    
    // start loading file //
    load_media();
    
  }

}

/* 
 * EXIT LIGHTBOX
 *
 * 
 *
 * Parameter: -                    
 * Returns: -  
 */     	
function exitlightbox() {

  // cancle current loading //
  if(oogLightbox_fileExtension == 'jpg' || oogLightbox_fileExtension == 'jpeg' || oogLightbox_fileExtension == 'gif' || oogLightbox_fileExtension == 'png') {
    lightbox_img.onload = null;
    lightbox_img.src = null;
    if(self.stop) { 
      window.stop(); 
    } else if(document.execCommand) { 
      document.execCommand('Stop'); 
    }
  } else if(oogLightbox_fileExtension == 'flv' || oogLightbox_fileExtension == 'mp3' || oogLightbox_fileExtension == 'mp4') {



  }

  $('#oog_lightbox_extended_box').css('display', 'none');

  // hide loading //
  $('#oog_lightbox_loading').fadeOut(fadeInOut_loading, function() {
    document.getElementById('oog_lightbox_loading').style.visibility = 'hidden';
  });

  // hide lightbox //
  $('#oog_lightbox_main').fadeOut(fadeInOut_file, function() {
    document.getElementById('oog_lightbox_main').style.visibility = 'hidden';
    document.getElementById('oog_lightbox_photo_wrapper').innerHTML = '';
    document.getElementById('oog_lightbox_filedescription').innerHTML = '';
  });

  // restore document title //
  document.title = oogLightbox_docTitle;

  // hide overlay //
  $('#oog_lightbox_overlay').fadeOut(fadeInOut_overlay, function() {
    document.getElementById('oog_lightbox_overlay').style.width = '0px';
    document.getElementById('oog_lightbox_overlay').style.height = '0px';
    document.getElementById('oog_lightbox_overlay').style.visibility = 'hidden';
  });

}

/* 
 * PREVIOUS
 *
 * Will call startlightbox() with previous file of .oogLightboxSequence as parameter.
 *
 * Parameter: -                    
 * Returns: -  
 */     	
function previous() {

  var num = $('.oogLightboxSequence').length;
  for(var i = 0; i < num; i++) {
    if(document.getElementsByClassName('oogLightboxSequence')[i].dataset['filename'] == oogLightbox_file && document.getElementsByClassName('oogLightboxSequence')[i].dataset['path'] == oogLightbox_path) {
      if(i == 0) {
        var previousFile = document.getElementsByClassName('oogLightboxSequence')[num-1].dataset['filename'];
        var previousPath = document.getElementsByClassName('oogLightboxSequence')[num-1].dataset['path'];
        var previousIsHtml = (document.getElementsByClassName('oogLightboxSequence')[num-1].dataset['ishtml'] == 'true') ? true : false;
      } else {      
        var previousFile = document.getElementsByClassName('oogLightboxSequence')[i-1].dataset['filename'];
        var previousPath = document.getElementsByClassName('oogLightboxSequence')[i-1].dataset['path'];
        var previousIsHtml = (document.getElementsByClassName('oogLightboxSequence')[i-1].dataset['ishtml'] == 'true') ? true : false;
      }
      break;
    }
  }

  startlightbox(previousPath, previousFile, previousIsHtml, oogLightbox_descEdit, oogLightbox_focusDesc);

}

/* 
 * NEXT
 *
 * Will call startlightbox() with next file of .oogLightboxSequence as parameter. 
 *
 * Parameter: -                    
 * Returns: -  
 */     	
function next() {

  var num = $('.oogLightboxSequence').length;
  for(var i = 0; i < num; i++) {
    if(document.getElementsByClassName('oogLightboxSequence')[i].dataset['filename'] == oogLightbox_file && document.getElementsByClassName('oogLightboxSequence')[i].dataset['path'] == oogLightbox_path) {
      if(i == num-1) {
        var nextFile = document.getElementsByClassName('oogLightboxSequence')[0].dataset['filename'];
        var nextPath = document.getElementsByClassName('oogLightboxSequence')[0].dataset['path'];
        var nextIsHtml = (document.getElementsByClassName('oogLightboxSequence')[0].dataset['ishtml'] == 'true') ? true : false;
      } else {      
        var nextFile = document.getElementsByClassName('oogLightboxSequence')[i+1].dataset['filename'];
        var nextPath = document.getElementsByClassName('oogLightboxSequence')[i+1].dataset['path'];
        var nextIsHtml = (document.getElementsByClassName('oogLightboxSequence')[i+1].dataset['ishtml'] == 'true') ? true : false;
      }
      break;
    }
  }

  startlightbox(nextPath, nextFile, nextIsHtml, oogLightbox_descEdit, oogLightbox_focusDesc);

}

function save_file() {
  
  window.location.href = oogLightbox_backEndPath + 'stream&album=' + escape(oogLightbox_path) + '&file=' + escape(oogLightbox_file);
  
}

/* 
 * DISPLAY FILE DESCRIPTION
 *
 * Sends a get_filedescription request. 
 * Needs global vars oogLightbox_path and oogLightbox_file!
 * Will display description if exists.  
 *
 * Parameter: -                    
 * Returns: -   
 */     	
function display_filedescription() {

  // define ajax_request to get filedescription //
  var filedescription_request = false;
  filedescription_request = new XMLHttpRequest();
  filedescription_request.onreadystatechange = function() {
    if(filedescription_request.readyState == 4) {
      if(filedescription_request.status == 200) {
        
        if(oogLightbox_descEdit) {
          document.getElementById('oog_lightbox_filedescription').contentEditable = 'true';
          $('#oog_lightbox_filedescription').css('cursor', 'text');
        } else {
          document.getElementById('oog_lightbox_filedescription').contentEditable = 'false';
          $('#oog_lightbox_filedescription').css('cursor', 'default');
        }

        $('#oog_lightbox_filedescription').text(filedescription_request.responseText);

        if(filedescription_request.responseText == '' || filedescription_request.responseText == '<br>' || filedescription_request.responseText == '<br />' || filedescription_request.responseText == '<br/>') {
          document.getElementById('oog_lightbox_filedescription').style.height = '0px';
          document.getElementById('oog_lightbox_filedescription').innerHTML = '';
          isDescription = false;
        } else {
          document.getElementById('oog_lightbox_filedescription').style.height = '';
          isDescription = true;
        }

        resizelightbox();
                
        if(oogLightbox_descEdit) {
         
          var div = document.getElementById('oog_lightbox_filedescription');
          
          div.onfocus = function() {
            window.setTimeout(function() {

              if($('#oog_lightbox_filedescription').text() == '' && !descriptionFocused) $('#oog_lightbox_filedescription').text('Write some text in here..');

              descriptionFocused = true;

              isDescription = true;
              resizelightbox();
              
              document.getElementById('oog_lightbox_filedescription').style.height = '';
              
              var sel, range;
              if (window.getSelection && document.createRange) {
                range = document.createRange();
                range.selectNodeContents(div);
                sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
              } else if (document.body.createTextRange) {
                range = document.body.createTextRange();
                range.moveToElementText(div);
                range.select();
              }
                  
            }, 5);
          };
          
          div.onblur = function() {
            window.setTimeout(function() {
              
              descriptionFocused = false;

              save_filedescription($('#oog_lightbox_filedescription').text());
              
              if(document.getElementById('oog_lightbox_filedescription').innerHTML == '<br>' || document.getElementById('oog_lightbox_filedescription').innerHTML == '<br />' || document.getElementById('oog_lightbox_filedescription').innerHTML == '<br/>') {
                document.getElementById('oog_lightbox_filedescription').innerHTML = '';
                document.getElementById('oog_lightbox_filedescription').style.height = '0px';
                isDescription = false;
              } else {
                document.getElementById('oog_lightbox_filedescription').style.height = '';
                isDescription = true;
              }
              
              resizelightbox();

            }, 5);
          
          };

          $('#oog_lightbox_filedescription').live('keyup paste', function() {

            save_filedescription($('#oog_lightbox_filedescription').text());

          });
                                    
          if(oogLightbox_focusDesc) div.focus();
      
        }
        
      }      
    }
  }
  
  // send get filedescription request //
  filedescription_request.open('GET', oogLightbox_backEndPath + 'get_filedescription&album=' + escape(oogLightbox_path) + '&file=' + escape(oogLightbox_file), true);
  filedescription_request.send(null);

}

/* 
 * SAVE FILE DESCRIPTION
 *
 * Sends a save_filedescription request.
 * Needs global vars oogLightbox_path and oogLightbox_file!   
 *
 * Parameter: -                     
 * Returns: -     
 */     	  
function save_filedescription(description) {
  
  // define ajax_request to save filedescription //
  var save_filedescription_request = false;
  save_filedescription_request = new XMLHttpRequest();
  save_filedescription_request.onreadystatechange = function() {
    if(save_filedescription_request.readyState == 4) {
      if(save_filedescription_request.status == 200) {

        // if saving description faild show error msg //
        if(save_filedescription_request.responseText != 'succeed') {
          if(show_error_filedescription) alert(save_filedescription_request.responseText);
          show_error_filedescription = false;
          setTimeout(function () { show_error_filedescription = true; }, 3000);
        }
                
      }      
    }
  }

  // send save filedescription request //
  save_filedescription_request.open('GET', oogLightbox_backEndPath + 'save_filedescription&album=' + escape(oogLightbox_path) + '&file=' + escape(oogLightbox_file) + '&description=' + escape(description), true);
  save_filedescription_request.send(null);

}

function refresh_lightbox_extended_box(command, resize) {
  
  if(!command) { command = ''; }
  
  var http_request = false;
  try {
    http_request = new XMLHttpRequest();
  } catch(e) {
    alert('Error: Can not create XMLHttpRequest');
    return false;
  }

  http_request.onreadystatechange = function() {

    if(http_request.readyState == 4) {
      if(http_request.status == 200) {
        document.getElementById('oog_lightbox_extended_box_request').innerHTML = http_request.responseText;
      } else {
        http_request = false;
        alert('Error: Can not load data. Reload page and try again.');
        lightbox_extended = '';        
        $('#oog_lightbox_extended_box').css('display', 'none');
        resizelightbox();
        return false;
      }
    }

  }

  if(command == 'exif' || command == 'comments') {

    if(lightbox_extended == command) {

      lightbox_extended = '';
  $('#oog_lightbox_extended_box').css('display', 'none');
      if(resize) {
        resizelightbox();
      }
      return false;

    }

    lightbox_extended = command;
  $('#oog_lightbox_extended_box').css('display', 'block');

    http_request.open('GET', oogLightbox_backEndPath + command + '&album=' + escape(oogLightbox_path) + '&file=' + escape(oogLightbox_file), true);
    http_request.send(null);

  } else {

    if(lightbox_extended != '') {
    
  $('#oog_lightbox_extended_box').css('display', 'block');

      http_request.open('GET', oogLightbox_backEndPath + lightbox_extended + '&album=' + escape(oogLightbox_path) + '&file=' + escape(oogLightbox_file), true);
      http_request.send(null);

    }

  }

  if(resize) {
    resizelightbox();
  }

}

function slideshow() {

  if(lightbox_slideshow == 0) {
  
    if(oogLightbox_fileExtension == 'jpg' || oogLightbox_fileExtension == 'jpeg' || oogLightbox_fileExtension == 'gif' || oogLightbox_fileExtension == 'png')
      lightbox_timeout = window.setTimeout('next()',timeout_slideshow);

    document.getElementById('oog_lightbox_slideshow_but').style.backgroundPosition = '-17px -17px';
    
    lightbox_slideshow = 1;

  } else {

    document.getElementById('oog_lightbox_slideshow_but').style.backgroundPosition = '-17px 0px';

    window.clearTimeout(lightbox_timeout);
    
    lightbox_slideshow = 0;

  }

}