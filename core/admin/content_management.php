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

?>

/* 
 * INIT ALBUMS MANAGEMENT
 *
 * 
 *
 * Parameter: -                     
 * Returns: -     
 */     	  
function init_albums_management() {
uploadInProc = false;
  // define first selected folderpath //
  selected_folder = (arguments.length > 0) ? arguments[0] : 'Menu'; 

  //  //
  selectedFilesRenember = new Array();

  // init array for paths of files in upload queue //
  queuedFilesPaths = [];
  // and counter //
  counter_uploaded = 0;
  
  renameInProcNxtNum = undefined;

  selected_id = false;
  
  // init folder actions //
  $('#folder_actions').html('<a id="btn_add_folder" class="tooltip"><?php echo $lang['admin']['folder']['btn']['add']['title']; ?><span></span></a><img id="loading_add_folder" src="images/mini_loading.gif" title="Please wait a moment..." />|<a id="btn_hide_folder" class="tooltip"><?php echo $lang['admin']['folder']['btn']['hide']['title']; ?><span></span></a><img id="loading_hide_folder" src="images/mini_loading.gif" title="Please wait a moment..." />|<a id="btn_rename_folder" class="tooltip"><?php echo $lang['admin']['folder']['btn']['rename']['title']; ?><span></span></a><img id="loading_rename_folder" src="images/mini_loading.gif" title="Please wait a moment..." />|<a id="btn_delete_folder" class="tooltip"><?php echo $lang['admin']['folder']['btn']['delete']['title']; ?><span></span></a><img id="loading_delete_folder" src="images/mini_loading.gif" title="Please wait a moment..." />|<a id="btn_reset_order" class="tooltip"><?php echo $lang['admin']['folder']['btn']['reset_order']['title']; ?><span></span></a><img id="loading_reset_order" src="images/mini_loading.gif" title="Please wait a moment..." />');

  // build folder tree //    
  update_folder_tree(false, true);
  
  // hide loading gifs //
  $('#loading_add_folder').css('display','none');
  $('#loading_hide_folder').css('display','none');
  $('#loading_rename_folder').css('display','none');
  $('#loading_delete_folder').css('display','none');
  $('#loading_reset_order').css('display','none');
  $('#loading_rename_file').css('display','none');
  $('#loading_delete_file').css('display','none');
  $('#loading_show_file').css('display','none');
  $('#loading_description_file').css('display','none');
  $('#loading_comments_file').css('display','none');

  file_actionsOffset = $('#file_actions').offset();
  $(window).scroll(function () { 
    if($(document).scrollTop() > file_actionsOffset.top + 20 + $('#file_actions').height()) {
      $('#file_actions').css('top', '0');
      $('#file_actions').css('position', 'fixed');
      $('#file_actions').css('border-bottom', '6px solid #EDF3FE');
    } else {
      $('#file_actions').css('position', 'static');
      $('#file_actions').css('border-bottom', '0');
    } 
  });
  
  $(document).bind('click', function(event) { 

    filesOffset = $('#album_mediafiles').offset();

    if(event.pageX < filesOffset.left || event.pageY > (filesOffset.top + $('#album_mediafiles').outerHeight()) || event.pageY < (filesOffset.top - $('#file_actions').outerHeight()) || event.pageX > (filesOffset.left + $('#album_mediafiles').outerWidth())) {

      if($('#oog_lightbox_overlay').css('visibility') != 'visible' && !renameInProcNxtNum) unselect_files();

    }
  
  } );
  
}

function update_folder_tree(unselectFolders, unselectFiles) {

  var get_folder_tree_request = false;
  try {
    get_folder_tree_request = new XMLHttpRequest();
  } catch(e) {
    document.getElementById('oogErrorBox').innerHTML = '<p><?php $lang['admin']['ajax_error']; ?></p>';
    document.getElementById('oogErrorBox').style.display = 'block';
    return false;
  }

  get_folder_tree_request.onreadystatechange = function() {
  
    if(get_folder_tree_request.readyState == 4) {
      if(get_folder_tree_request.status == 200) {

        if(get_folder_tree_request.responseText.substr(2,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        document.getElementById('dhtmlgoodies_tree2').innerHTML = get_folder_tree_request.responseText;
        treeObj = new JSDragDropTree();
        treeObj.setTreeId('dhtmlgoodies_tree2');
        treeObj.setMaximumDepth(999);
        treeObj.setMessageMaximumDepthReached('Maximum depth reached');
        treeObj.initTree();
        
        jQuery(function(){
          jQuery('ul.sf-menu').superfish();
        });

        menuOriginalBg = $('#node0').css('background');
        
        select_folder(selected_folder, unselectFiles);
    
      } else {
      
        document.getElementById('oogErrorBox').innerHTML = '<p><?php $lang['admin']['connection_error']; ?></p>';
        document.getElementById('oogErrorBox').style.display = 'block';
        document.getElementById('album_mediafiles').innerHTML = '<p><?php $lang['admin']['connection_error']; ?></p>';

      }
    }
  }
  
  get_folder_tree_request.open('GET', 'admin/content_management_requests.php?ajax_request=get_folder_tree', true);
  get_folder_tree_request.send(null);

}

function save_fileorder() {

  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request for changing file order //
  var change_file_order_request = false;
  change_file_order_request = new XMLHttpRequest();
  change_file_order_request.onreadystatechange = function() {
    if(change_file_order_request.readyState == 4) {
      if(change_file_order_request.status == 200) {

        // check for session timeout //
        if(change_file_order_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(change_file_order_request.responseText != 'succeed') {

          // display received error //
          $('#oogErrorBox').html(change_file_order_request.responseText);
          $('#oogErrorBox').css('display', 'block');
          
        }
        
        update_folder_tree(false, false);

      }      
    }
  }

  // send change file order request //
  change_file_order_request.open('GET', 'admin/content_management_requests.php?ajax_request=change_file_order&album=' + selected_folder + '&new_order=' + getFileOrder().join('|*|'), true);
  change_file_order_request.send(null);

}

function moveSelectedFiles(albumpathTo) {

  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request for moving files to album //
  var move_files_to_album_request = false;
  move_files_to_album_request = new XMLHttpRequest();
  move_files_to_album_request.onreadystatechange = function() {
    if(move_files_to_album_request.readyState == 4) {
      if(move_files_to_album_request.status == 200) {

        // check for session timeout //
        if(move_files_to_album_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(move_files_to_album_request.responseText != 'succeed') {

          // display received error //
          $('#oogErrorBox').html(move_files_to_album_request.responseText);
          $('#oogErrorBox').css('display', 'block');
          
          update_folder_tree(false, false);
          
        } else {
        
          update_folder_tree(false, true);
        
        }
        
        //  //
        $('#arrow_moveTo').css('display', 'none');

      }      
    }
  }

  // send change file order request //
  move_files_to_album_request.open('GET', 'admin/content_management_requests.php?ajax_request=move_files&album_from=' + selected_folder + '&album_to=' + albumpathTo + '&files=' + getSelectedFiles().join('|*|'), true);
  move_files_to_album_request.send(null);

}

function inArray(array, value) {

  for(var i = 0; i < array.length; i++) if(array[i] == value) return true;
  return false;
  
}
 
function update_fileActions(renember) {

  if(renember === undefined) {
   
    var selected_files = getSelectedFiles();

  } else {
  
    var selected_files = renember;
    
  	var objects = document.getElementById('album_mediafiles').getElementsByTagName('li');
  	for(var no = 0; no < objects.length; no++)	
  		if(objects[no].getAttribute('data-isfile') == 'true' && inArray(selected_files, objects[no].getAttribute('data-filename'))) $(objects[no]).addClass('demo-selected');
  
  }

  if(renameInProcNxtNum !== undefined) rename_files(renameInProcNxtNum);

  // update buttons //

  if(selected_files.length == 1) {

    $('#btn_show_file').css('cursor','pointer');
    $('#btn_show_file').css('opacity','0.75');
    $('#btn_show_file').unbind('click');
    $('#btn_show_file').click(function() { startlightbox(getSelectedFilesPaths().join(), getSelectedFiles().join(), false, true, false); });
    
    $('#btn_rename_file').css('cursor','pointer');
    $('#btn_rename_file').css('opacity','0.75');
    $('#btn_rename_file').unbind('click');
    $('#btn_rename_file').click(function() { rename_files(); });
    
    $('#btn_delete_file').css('cursor','pointer');
    $('#btn_delete_file').css('opacity','0.75');
    $('#btn_delete_file').unbind('click');
    $('#btn_delete_file').click(function() { delete_files(); });
    
    $('#btn_save_file').css('cursor','pointer');
    $('#btn_save_file').css('opacity','0.75');
    $('#btn_save_file').unbind('click');
    $('#btn_save_file').click(function() { save_files(); });
    
    $('#btn_description_file').css('cursor','pointer');
    $('#btn_description_file').css('opacity','0.75');
    $('#btn_description_file').unbind('click');
    $('#btn_description_file').click(function() { startlightbox(getSelectedFilesPaths().join(), getSelectedFiles().join(), false, true, true); });
    
    $('#btn_comments_file').css('cursor','pointer');
    $('#btn_comments_file').css('opacity','0.75');
    $('#btn_comments_file').unbind('click');
    $('#btn_comments_file').click(function() { alert('This function is comming soon!'); });

    $('#btn_rotate_left').css('cursor','pointer');
    $('#btn_rotate_left').css('opacity','0.75');
    $('#btn_rotate_left').unbind('click');
    $('#btn_rotate_left').click(function() { alert('This function is comming soon!'); });

    $('#btn_rotate_right').css('cursor','pointer');
    $('#btn_rotate_right').css('opacity','0.75');
    $('#btn_rotate_right').unbind('click');
    $('#btn_rotate_right').click(function() { alert('This function is comming soon!'); });
    
    $('#btn_select_all').css('cursor','pointer');
    $('#btn_select_all').css('opacity','0.75');
    $('#btn_select_all').unbind('click');
    $('#btn_select_all').click(function() { select_files(); });

    $('#btn_unselect_all').css('cursor','pointer');
    $('#btn_unselect_all').css('opacity','0.75');
    $('#btn_unselect_all').unbind('click');
    $('#btn_unselect_all').click(function() { unselect_files(); });

  } else if(selected_files.length == 0) {
  
    $('#btn_show_file').css('cursor','default');
    $('#btn_show_file').css('opacity','0.3');
    $('#btn_show_file').unbind('click');
    
    $('#btn_rename_file').css('cursor','default');
    $('#btn_rename_file').css('opacity','0.3');
    $('#btn_rename_file').unbind('click');
    
    $('#btn_delete_file').css('cursor','default');
    $('#btn_delete_file').css('opacity','0.3');
    $('#btn_delete_file').unbind('click');
    
    $('#btn_save_file').css('cursor','default');
    $('#btn_save_file').css('opacity','0.3');
    $('#btn_save_file').unbind('click');
    
    $('#btn_description_file').css('cursor','default');
    $('#btn_description_file').css('opacity','0.3');
    $('#btn_description_file').unbind('click');
    
    $('#btn_comments_file').css('cursor','default');
    $('#btn_comments_file').css('opacity','0.3');
    $('#btn_comments_file').unbind('click');
 
    $('#btn_rotate_left').css('cursor','default');
    $('#btn_rotate_left').css('opacity','0.3');
    $('#btn_rotate_left').unbind('click');

    $('#btn_rotate_right').css('cursor','default');
    $('#btn_rotate_right').css('opacity','0.3');
    $('#btn_rotate_right').unbind('click');
    
    $('#btn_select_all').css('cursor','pointer');
    $('#btn_select_all').css('opacity','0.75');
    $('#btn_select_all').unbind('click');
    $('#btn_select_all').click(function() { select_files(); });

    $('#btn_unselect_all').css('cursor','default');
    $('#btn_unselect_all').css('opacity','0.3');
    $('#btn_unselect_all').unbind('click');

  } else {
  
    $('#btn_show_file').css('cursor','default');
    $('#btn_show_file').css('opacity','0.3');
    $('#btn_show_file').unbind('click');
    
    $('#btn_rename_file').css('cursor','pointer');
    $('#btn_rename_file').css('opacity','0.75');
    $('#btn_rename_file').unbind('click');
    $('#btn_rename_file').click(function() { rename_files(); });
    
    $('#btn_delete_file').css('cursor','pointer');
    $('#btn_delete_file').css('opacity','0.75');
    $('#btn_delete_file').unbind('click');
    $('#btn_delete_file').click(function() { delete_files(); });
    
    $('#btn_save_file').css('cursor','pointer');
    $('#btn_save_file').css('opacity','0.75');
    $('#btn_save_file').unbind('click');
    $('#btn_save_file').click(function() { save_files(); });
    
    $('#btn_description_file').css('cursor','default');
    $('#btn_description_file').css('opacity','0.3');
    $('#btn_description_file').unbind('click');
    
    $('#btn_comments_file').css('cursor','default');
    $('#btn_comments_file').css('opacity','0.3');
    $('#btn_comments_file').unbind('click');

    $('#btn_rotate_left').css('cursor','pointer');
    $('#btn_rotate_left').css('opacity','0.75');
    $('#btn_rotate_left').unbind('click');
    $('#btn_rotate_left').click(function() { alert('This function is comming soon!'); });

    $('#btn_rotate_right').css('cursor','pointer');
    $('#btn_rotate_right').css('opacity','0.75');
    $('#btn_rotate_right').unbind('click');
    $('#btn_rotate_right').click(function() { alert('This function is comming soon!'); });
    
    $('#btn_select_all').css('cursor','pointer');
    $('#btn_select_all').css('opacity','0.75');
    $('#btn_select_all').unbind('click');
    $('#btn_select_all').click(function() { select_files(); });

    $('#btn_unselect_all').css('cursor','pointer');
    $('#btn_unselect_all').css('opacity','0.75');
    $('#btn_unselect_all').unbind('click');
    $('#btn_unselect_all').click(function() { unselect_files(); });

  }

}

function getSelectedFiles() {

	var order = new Array();
	var objects = document.getElementById('album_mediafiles').getElementsByTagName('li');
	
	for(var no = 0; no < objects.length; no++)	
		if(objects[no].getAttribute('data-isfile') == 'true' && $(objects[no]).hasClass('demo-selected')) order.push(objects[no].getAttribute('data-filename'));
	
  selectedFilesRenember = order;

	return order;

}

function getSelectedFilesPaths() {

	var order = new Array();
	var objects = document.getElementById('album_mediafiles').getElementsByTagName('li');
	
	for(var no = 0; no < objects.length; no++)	
		if(objects[no].getAttribute('data-isfile') == 'true' && $(objects[no]).hasClass('demo-selected')) order.push(objects[no].getAttribute('data-path'));

	return order;

}

function getFileOrder() {

	var order = new Array();
	var objects = document.getElementById('album_mediafiles').getElementsByTagName('li');

	// go through all objs in the album container //
	for(var no = 0; no < objects.length; no++)
	  // and collect all files //
    if(objects[no].getAttribute('data-isfile') == 'true') order.push(objects[no].getAttribute('data-filename'));
    			
	return order;
	
}

function getFile(name) {

	var file = false;
	var objects = document.getElementById('album_mediafiles').getElementsByTagName('li');

	// go through all objs in the album container //
	for(var no = 0; no < objects.length; no++)
	  // and look for file with matching name //
    if(objects[no].getAttribute('data-isfile') == 'true' && objects[no].getAttribute('data-filename') == name) file = objects[no];
    			
	return file;
	
}

function select_folder(folder, unselectFiles) {

  // and define ajax_request to get folder content //
  var get_folder_content_request = false;
  get_folder_content_request = new XMLHttpRequest();
  get_folder_content_request.onreadystatechange = function() {
    if(get_folder_content_request.readyState == 4) {
      if(get_folder_content_request.status == 200) {

        // receive folder infos //
        infos = JSON.parse(get_folder_content_request.responseText);
        id = infos.id;
        name = infos.name;
        is_category = infos.is_category;
        modificationDate = infos.modificationDate;
        is_own_order = infos.is_own_order;
        is_empty = infos.is_empty;
        views = infos.views;
        is_hidden = infos.is_hidden;
        content = infos.content;

        // update bg of menu nodes //
        if(selected_id) $('#' + selected_id).css('background', menuOriginalBg);
        $('#' + id).css('background', '#A6B1B3');       

        // update name field //
        $('#folder_path').html('<?php echo $lang['admin']['folder']['path']; ?>' + folder.substr(5));
        
        // update modification date //
        $('#outModificationDate').html('<?php echo $lang['admin']['folder']['lastchange']; ?> ' + modificationDate);

        // update logout button //
        $('#login_form').attr('action', 'showalbum.php?album=' + folder.substr(5));
        
        // update language_switcher //
        $('#language_switcher > a').each(function () { 
          $(this).attr('href', 'showalbum.php?album=' + folder.substr(5) + '&language=' + this.dataset['lang']); 
        });

        // update delete folder button //
        if(folder == 'Menu') {
          $('#btn_delete_folder').css('color','#9CAFD1');
          $('#btn_delete_folder').css('cursor','help');
          $('#btn_delete_folder > span').html('<?php echo $lang['admin']['folder']['btn']['delete']['info']['2']; ?>');
          $('#btn_delete_folder > span').css('color','red');
          $('#btn_delete_folder').unbind('click');
        } else {
          $('#btn_delete_folder').css('color','#385EA2');
          $('#btn_delete_folder').css('cursor','pointer');
          $('#btn_delete_folder > span').html('<?php echo $lang['admin']['folder']['btn']['delete']['info']['1']; ?>');
          $('#btn_delete_folder > span').css('color','green');        
          $('#btn_delete_folder').unbind('click');
          $('#btn_delete_folder').click(function() { delete_folder(); });
        }
        
        // update hide folder button //
        if(is_hidden == 'hidden') {
          $('#btn_hide_folder').html('<?php echo $lang['admin']['folder']['btn']['unhide']['title']; ?><span><?php echo $lang['admin']['folder']['btn']['unhide']['info']['1']; ?></span>');
          $('#btn_hide_folder').unbind('click');
          $('#btn_hide_folder').click(function() { unhide_folder(); });
          $('#btn_hide_folder').css('cursor','pointer');
          $('#btn_hide_folder').css('color','#385EA2');
          $('#btn_hide_folder > span').css('color','green');
        }
        if(is_hidden == 'notHidden') {
          $('#btn_hide_folder').html('<?php echo $lang['admin']['folder']['btn']['hide']['title']; ?><span><?php echo $lang['admin']['folder']['btn']['hide']['info']['1']; ?></span>');
          $('#btn_hide_folder').unbind('click');
          $('#btn_hide_folder').click(function() { hide_folder(); });
          $('#btn_hide_folder').css('cursor','pointer');
          $('#btn_hide_folder').css('color','#385EA2');
          $('#btn_hide_folder > span').css('color','green');
        }
        if(is_hidden == 'hiddenByParent') {
          $('#btn_hide_folder').html('<?php echo $lang['admin']['folder']['btn']['unhide']['title']; ?><span><?php echo $lang['admin']['folder']['btn']['unhide']['info']['2']; ?></span>');
          $('#btn_hide_folder').unbind('click');
          $('#btn_hide_folder').css('cursor','help');
          $('#btn_hide_folder').css('color','#9CAFD1');
          $('#btn_hide_folder > span').css('color','red');
        }
        
        // update rename folder button //
        if(folder != 'Menu') {
          $('#btn_rename_folder').css('cursor','pointer');
          $('#btn_rename_folder').css('color','#385EA2');
          $('#btn_rename_folder').unbind('click');
          $('#btn_rename_folder').click(function() { rename_folder(); });
          $('#btn_rename_folder > span').html('<?php echo $lang['admin']['folder']['btn']['rename']['info']['1']; ?>');
          $('#btn_rename_folder > span').css('color','green');
        } else {
          $('#btn_rename_folder').css('cursor','help');
          $('#btn_rename_folder').css('color','#9CAFD1');
          $('#btn_rename_folder').unbind('click');
          $('#btn_rename_folder > span').html('<?php echo $lang['admin']['folder']['btn']['rename']['info']['2']; ?>');
          $('#btn_rename_folder > span').css('color','red');
        }

        // update folders views //
        $('#outViews').html('<?php echo $lang['core']['album']['1'] ?> ' + views);

        if(is_category == 'true') {
        
          //////// CATEGORY SELECTED ////////
          
          // unselectImage();
                    
          // update add folder button //
          $('#btn_add_folder').css('color','#385EA2');
          $('#btn_add_folder').css('cursor','pointer');
          $('#btn_add_folder > span').html('<?php echo $lang['admin']['folder']['btn']['add']['info']['1']; ?>');
          $('#btn_add_folder > span').css('color','green');
          $('#btn_add_folder').unbind('click');
          $('#btn_add_folder').click(function() { add_folder(); });

          // update reset order button //
          if(is_own_order == 'true') {
            $('#btn_reset_order').css('cursor','pointer');
            $('#btn_reset_order > span').html('<p><?php echo $lang['admin']['folder']['btn']['reset_order']['info']['1']; ?></p><?php echo $lang['admin']['folder']['btn']['reset_order_also_subfolders']['info']['1']; ?><br/><a id="btn_also_subfolders"><?php echo $lang['admin']['folder']['btn']['reset_order_also_subfolders']['title']; ?></a>');
            $('#btn_reset_order > span > p').css('color','green');
            $('#btn_reset_order').unbind('click');
            $('#btn_reset_order').click(function() { reset_order(); });
          } else {
            $('#btn_reset_order').css('cursor','help');
            $('#btn_reset_order > span').html('<p><?php echo $lang['admin']['folder']['btn']['reset_order']['info']['2']; ?></p><?php echo $lang['admin']['folder']['btn']['reset_order_also_subfolders']['info']['1']; ?><br/><a id="btn_also_subfolders"><?php echo $lang['admin']['folder']['btn']['reset_order_also_subfolders']['title']; ?></a>');
            $('#btn_reset_order > span > p').css('color','red');
            $('#btn_reset_order').unbind('click');
          }
          $('#btn_reset_order').css('color','#385EA2');
          $('#btn_reset_order > span > p').css('margin','0px');
          $('#btn_also_subfolders').unbind('click');
          $('#btn_also_subfolders').click(function() { reset_order(true); });

          // update folders type //
          $('#outType').html('<?php echo $lang['admin']['folder']['type_cat']; ?>');
          type = 'category';
          /*
          // update description field //
          document.getElementById('description_field').disabled = true;
          $('#description_field').html('Categories do not have a description!');
          */
          
          // update album mediafiles container //
          if(folder != 'Menu') {
            $('#album_mediafiles').html('<p><?php echo $lang['admin']['album_mediafile']['1']; ?></p>');
          } else {
            $('#album_mediafiles').html(content + '<div style="clear:both;"></div>');
          }

        } else {
  
          //////// ALBUM SELECTED ////////
          
          // update add folder button //
          if(is_empty == 'true') {
            $('#btn_add_folder').css('color','#385EA2');
            $('#btn_add_folder').css('cursor','pointer');
            $('#btn_add_folder > span').html('<?php echo $lang['admin']['folder']['btn']['add']['info']['2']; ?>');
            $('#btn_add_folder > span').css('color','green');
            $('#btn_add_folder').unbind('click');
            $('#btn_add_folder').click(function() { add_folder(); });             
          } else {
            $('#btn_add_folder').css('color','#9CAFD1');
            $('#btn_add_folder').css('cursor','help');
            $('#btn_add_folder > span').html('<?php echo $lang['admin']['folder']['btn']['add']['info']['3']; ?>');
            $('#btn_add_folder > span').css('color','red');
            $('#btn_add_folder').unbind('click');      
          }
          
          // update reset order button //
          if(is_own_order == 'true') {
            $('#btn_reset_order').css('color','#385EA2');
            $('#btn_reset_order').css('cursor','pointer');
            $('#btn_reset_order > span').html('<p><?php echo $lang['admin']['folder']['btn']['reset_order']['info']['3']; ?></p>');
            $('#btn_reset_order > span > p').css('color','green');
            $('#btn_reset_order').unbind('click');
            $('#btn_reset_order').click(function() { reset_order(); });
          } else {
            $('#btn_reset_order').css('color','#9CAFD1');
            $('#btn_reset_order').css('cursor','help');
            $('#btn_reset_order > span').html('<p><?php echo $lang['admin']['folder']['btn']['reset_order']['info']['4']; ?></p>');
            $('#btn_reset_order > span > p').css('color','red');
            $('#btn_reset_order').unbind('click');
          }
          $('#btn_reset_order > span > p').css('margin','0px');

          // update folders type //
          $('#outType').html('<?php echo $lang['admin']['folder']['type_alb']; ?>');
          type = 'album';

          // init uploader //

		  if(!uploadInProc) {
(function () {

	var filesUpload = document.getElementById("inpUpload");
		var dropArea = document;
		//var fileList = document.getElementById("file-list");
		
	function uploadFile (file) {
	uploadInProc = true;
		var li = document.createElement("li"),
			div = document.createElement("div"),
			img,
			progressBarContainer = document.createElement("div"),
			progressBar = document.createElement("div"),
			reader,
			xhr,
			fileInfo;
			
		li.appendChild(div);
		
		progressBarContainer.className = "progress-bar-container";
		progressBar.className = "progress-bar";
		progressBarContainer.appendChild(progressBar);
		li.appendChild(progressBarContainer);
		
		/*
			If the file is an image and the web browser supports FileReader,
			present a preview in the file list
		*/
		if (typeof FileReader !== "undefined" && (/image/i).test(file.type)) {
			img = document.createElement("img");
			li.appendChild(img);
			reader = new FileReader();
			reader.onload = (function (theImg) {
				return function (evt) {
					theImg.src = evt.target.result;
				};
			}(img));
			reader.readAsDataURL(file);
		}
		
		// Uploading - for Firefox, Google Chrome and Safari
		xhr = new XMLHttpRequest();
		
		// Update progress bar
		xhr.upload.addEventListener("progress", function (evt) {
			if (evt.lengthComputable) {
if(evt.loaded == evt.total)
				$('#statusUpload').text(Math.round((evt.loaded / evt.total) * 100) + "%");
			}
			else {
				$('#statusUpload').text('Uploading...');
			}
		}, false);
		
		// File uploaded
		xhr.addEventListener("load", function () {
		uploadInProc = false;
		alert('');
select_folder(selected_folder, false);
$('#statusUpload').text('100%');
		}, false);
		
		xhr.open("post", "admin/content_management_requests.php?ajax_request=upload&folder=" + folder, true);
	
		// Set appropriate headers
		xhr.setRequestHeader("Content-Type", "multipart/form-data");
		xhr.setRequestHeader("X-File-Name", file.name);
		xhr.setRequestHeader("X-File-Size", file.size);
		xhr.setRequestHeader("X-File-Type", file.type);

		// Send the file (doh)
		xhr.send(file);
		
		// Present file info and append it to the list of files
		fileInfo = "<div><strong>Name:</strong> " + file.name + "</div>";
		fileInfo += "<div><strong>Size:</strong> " + parseInt(file.size / 1024, 10) + " kb</div>";
		fileInfo += "<div><strong>Type:</strong> " + file.type + "</div>";
		div.innerHTML = fileInfo;
		
		//fileList.appendChild(li);
	}
	
	function traverseFiles (files) {
		if (typeof files !== "undefined") {
			for (var i=0, l=files.length; i<l; i++) {
				uploadFile(files[i]);
			}
		}
		else {
		  alert("No support for the File API in this web browser");
		}	
	}
	
	filesUpload.addEventListener("change", function () {
		traverseFiles(this.files);
	}, false);
	
	dropArea.addEventListener("dragleave", function (evt) {
		var target = evt.target;
		
		if (target && target === dropArea) {
			this.className = "";
		}
		evt.preventDefault();
		evt.stopPropagation();
	}, false);
	
	dropArea.addEventListener("dragenter", function (evt) {
		this.className = "over";
		evt.preventDefault();
		evt.stopPropagation();
	}, false);
	
	dropArea.addEventListener("dragover", function (evt) {
		evt.preventDefault();
		evt.stopPropagation();
	}, false);
	
	dropArea.addEventListener("drop", function (evt) {
		traverseFiles(evt.dataTransfer.files);
		this.className = "";
		evt.preventDefault();
		evt.stopPropagation();
	}, false);										
})();

}
          // insert received content //
          if(is_empty == 'false') {
            $('#album_mediafiles').html(content + '<div style="clear:both;"></div>');
            $("#album_mediafiles").multisortable({selectedClass:'demo-selected'});
            if(unselectFiles) {
              update_fileActions();
            } else {
              update_fileActions(selectedFilesRenember);
            }
          } else {
            $('#album_mediafiles').html('<p><?php echo $lang['admin']['album_mediafile']['2']; ?></p>');
          }

        }
  
        // save infos about current selected //
        selected_folder = folder;
        selected_id = id;
        selected_name = name;
        selected_type = type;
                  
      }
      
    }
  }
  
  // send get folder content request //
  get_folder_content_request.open('GET', 'admin/content_management_requests.php?ajax_request=get_folder_content&folder=' + folder, true);
  get_folder_content_request.send(null);  
  
}

function add_folder() {

  // display loading gif //
  $('#loading_add_folder').css('display','inline');
  
  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request to add folder //
  var add_folder_request = false;
  add_folder_request = new XMLHttpRequest();
  add_folder_request.onreadystatechange = function() {
    if(add_folder_request.readyState == 4) {
      if(add_folder_request.status == 200) {
      
        // check for session timeout //
        if(add_folder_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(add_folder_request.responseText == 'succeed') {

          // adding folder was successful -> update foler tree //        
          selected_folder = selected_folder + '/' + name;
          update_folder_tree(false, true);
          
        } else {

          // display received error //
          $('#oogErrorBox').html(add_folder_request.responseText);
          $('#oogErrorBox').css('display','block');
          
        }
        
        // hide loading gif //
        $('#loading_add_folder').css('display','none');
        
      }      
    }
  }
  
  var name = prompt('Enter name of new folder:', '');
  
  if(name) {
  
    // send add folder request //
    add_folder_request.open('GET', 'admin/content_management_requests.php?ajax_request=add_folder&folder=' + selected_folder + '&name=' + name, true);
    add_folder_request.send(null);

  } else {

    // user cancelled //
    // hide loading gif //
    $('#loading_add_folder').css('display','none');

  }

}

function rename_folder() {

  // display loading gif //
  $('#loading_rename_folder').css('display','inline');
  
  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request to rename folder //
  var rename_folder_request = false;
  rename_folder_request = new XMLHttpRequest();
  rename_folder_request.onreadystatechange = function() {
    if(rename_folder_request.readyState == 4) {
      if(rename_folder_request.status == 200) {
      
        // check for session timeout //
        if(rename_folder_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(rename_folder_request.responseText == 'succeed') {

          // renaming folder was successful -> update foler tree //
          selected_parentfolder = selected_folder.slice(0, selected_folder.lastIndexOf("/"));        
          selected_folder = selected_parentfolder + '/' + newname;
          update_folder_tree(false, false);
          
        } else {

          // display received error //
          $('#oogErrorBox').html(rename_folder_request.responseText);
          $('#oogErrorBox').css('display','block');
          
        }
        
        // hide loading gif //
        $('#loading_rename_folder').css('display','none');
        
      }      
    }
  }
  
  var newname = prompt('<?php $lang['admin']['folder']['btn']['rename']['prompt']; ?>', selected_name);
  
  if(newname) {
  
    // send rename folder request //
    rename_folder_request.open('GET', 'admin/content_management_requests.php?ajax_request=rename_folder&folder=' + selected_folder + '&name=' + newname, true);
    rename_folder_request.send(null);

  } else {

    // user cancelled //
    // hide loading gif //
    $('#loading_rename_folder').css('display','none');

  }

}

function hide_folder() {

  // display loading gif //
  $('#loading_hide_folder').css('display','inline');
  
  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request for hiding folder //
  var hide_folder_request = false;
  hide_folder_request = new XMLHttpRequest();
  hide_folder_request.onreadystatechange = function() {
    if(hide_folder_request.readyState == 4) {
      if(hide_folder_request.status == 200) {

        // check for session timeout //
        if(hide_folder_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(hide_folder_request.responseText == 'succeed') {

          // hiding was successful -> update foler tree //
          update_folder_tree(false, false);
          
        } else {

          // display received error //
          $('#oogErrorBox').html(hide_folder_request.responseText);
          $('#oogErrorBox').css('display','block');
          
        }

        // hide loading gif //
        $('#loading_hide_folder').css('display','none');
        
      }      
    }
  }

  // send hide folder request //
  hide_folder_request.open('GET', 'admin/content_management_requests.php?ajax_request=hide_folder&folder=' + selected_folder, true);
  hide_folder_request.send(null);

}

function unhide_folder() {

  // display loading gif //
  $('#loading_unhide_folder').css('display','inline');
  
  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request for unhiding folder //
  var unhide_folder_request = false;
  unhide_folder_request = new XMLHttpRequest();
  unhide_folder_request.onreadystatechange = function() {
    if(unhide_folder_request.readyState == 4) {
      if(unhide_folder_request.status == 200) {

        // check for session timeout //
        if(unhide_folder_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(unhide_folder_request.responseText == 'succeed') {

          // unhiding was successful -> update foler tree //
          update_folder_tree(false, false);
          
        } else {

          // display received error //
          $('#oogErrorBox').html(unhide_folder_request.responseText);
          $('#oogErrorBox').css('display','block');
          
        }

        // hide loading gif //
        $('#loading_hide_folder').css('display','none');
        
      }      
    }
  }

  // send unhide folder request //
  unhide_folder_request.open('GET', 'admin/content_management_requests.php?ajax_request=unhide_folder&folder=' + selected_folder, true);
  unhide_folder_request.send(null);

}

function delete_folder() {

  // display loading gif //
  $('#loading_delete_folder').css('display','inline');
  
  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request to delete folder //
  var delete_folder_request = false;
  delete_folder_request = new XMLHttpRequest();
  delete_folder_request.onreadystatechange = function() {
    if(delete_folder_request.readyState == 4) {
      if(delete_folder_request.status == 200) {

        // check for session timeout //
        if(delete_folder_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(delete_folder_request.responseText == 'succeed') {

          // deleting was successful -> update foler tree //
          selected_folder = 'Menu';
          update_folder_tree(false, true);
          
        } else {

          // display received error //
          $('#oogErrorBox').html(delete_folder_request.responseText);
          $('#oogErrorBox').css('display','block');
          
        }

        // hide loading gif //
        $('#loading_delete_folder').css('display','none');
        
      }      
    }
  }
  
  var ok = confirm('<?php echo $lang['admin']['folder']['btn']['delete']['confirm']; ?>', '');
  
  if(ok) {

    // send delete folder request //
    delete_folder_request.open('GET', 'admin/content_management_requests.php?ajax_request=delete_folder&folder=' + selected_folder, true);
    delete_folder_request.send(null);

  } else {

    // user cancelled //
    // hide loading gif //
    $('#loading_delete_folder').css('display','none');

  }

}

function reset_order(also_subfolders) {

  // display loading gif //
  $('#loading_reset_order').css('display','inline');
  
  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request to reset order //
  var reset_order_request = false;
  reset_order_request = new XMLHttpRequest();
  reset_order_request.onreadystatechange = function() {
    if(reset_order_request.readyState == 4) {
      if(reset_order_request.status == 200) {

        // check for session timeout //
        if(reset_order_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(reset_order_request.responseText == 'succeed') {

          // reseting order was successful -> update foler tree //
          update_folder_tree(false, false);
          
        } else {

          // display received error //
          $('#oogErrorBox').html(reset_order_request.responseText);
          $('#oogErrorBox').css('display','block');
          
        }

        // hide loading gif //
        $('#loading_reset_order').css('display','none');
        
      }      
    }
  }

  // check for reseting orders of subfolders //
  if(also_subfolders == true) {
    
    also_subfolders = 'true';
    var ok = confirm('<?php echo $lang['admin']['folder']['btn']['reset_order_also_subfolders']['confirm']; ?>', '');

  } else {

    also_subfolders = 'false';
    var ok = confirm('<?php echo $lang['admin']['folder']['btn']['reset_order']['confirm']; ?>', '');
  
  }
  
  if(ok) {

    // send reset order request //
    reset_order_request.open('GET', 'admin/content_management_requests.php?ajax_request=reset_order&folder=' + selected_folder + '&also_subfolders=' + also_subfolders, true);
    reset_order_request.send(null);

  } else {

    // user cancelled //
    // hide loading gif //
    $('#loading_reset_order').css('display','none');

  }

}
/* 
 * RENAME FILES              // todo: show errors!
 *
 * 
 *
 * Parameter: -                     
 * Returns: -     
 */     	  
function rename_files(selFileNum) {

  // display loading gif //
  $('#loading_rename_files').css('display','inline');
  
  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request to rename files //
  var rename_files_request = false;
  rename_files_request = new XMLHttpRequest();
  rename_files_request.onreadystatechange = function() {
    if(rename_files_request.readyState == 4) {
      if(rename_files_request.status == 200) {
      
        // check for session timeout //
        if(rename_files_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        var resultNames = rename_files_request.responseText.split('|*|');
        
        if(files_oldName.length == 1 || resultNames.length > 1) {
          selectedFilesRenember = resultNames;
        } else {
           selectedFilesRenember[renameInProcNxtNum-1] = resultNames;
        }
        
        if(renameInProcNxtNum == files_oldName.length) renameInProcNxtNum = undefined;
        
        update_folder_tree(false, false);
        
        // hide loading gif //
        $('#loading_rename_files').css('display','none');
        
      }      
    }
  }

  var files_oldName = getSelectedFiles();

  if(files_oldName.length > 1 && selFileNum === undefined) {
  
    var numThrou = confirm('<?php echo $lang['admin']['file']['msg_rename']['1']; ?>');
    
    if(numThrou) {
    
      // ask for new name //
      var newName = prompt('<?php echo $lang['admin']['file']['msg_rename']['2']; ?>', '');
      
      if(newName) {
      
        // todo: check valid input on clientside if not ask again?                                                                                         !!!!!!!!!!!!!!!!!!!!!!!!!!!!!! TODO !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        
        // send rename files request //
        rename_files_request.open('GET', 'admin/content_management_requests.php?ajax_request=rename_files&album=' + selected_folder + '&files=' + files_oldName.join('|*|') + '&new_name=' + newName, true);
        rename_files_request.send(null);
      
      }
      
      return false;
    
    }
    
  }

  if(selFileNum === undefined) selFileNum = 0;

  var oldName = files_oldName[selFileNum].split('.').reverse().slice(1).reverse().join('.');
  var ext = files_oldName[selFileNum].substring(files_oldName[selFileNum].lastIndexOf('.') + 1, files_oldName[selFileNum].length);

  var label = getFile(files_oldName[selFileNum]).getElementsByClassName('fileBox_label');

  $(label).html('<input id="input_newFilename" value="' + oldName + '" size="10" />');

  $('#input_newFilename').blur(function() {
  
    // todo: validate input                                                                                                             !!!!!!!!!!!!!!!!!!!!!!!!!!!!!! TODO !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    if($('#input_newFilename').val() != '') {

      renameInProcNxtNum = selFileNum + 1;

      getSelectedFiles();

      // send rename files request //
      rename_files_request.open('GET', 'admin/content_management_requests.php?ajax_request=rename_files&album=' + selected_folder + '&files=' + oldName + '.' + ext + '&new_name=' + $('#input_newFilename').val(), true);
      rename_files_request.send(null);
      
    } else {
    
      $('#input_newFilename').css('background', '#FAA');
      $('#input_newFilename').select();
    
    }
  
  });
  $('#input_newFilename').bind('keypress', function(e) {
  
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code == 13) {

      // todo: validate input                                                                                                             !!!!!!!!!!!!!!!!!!!!!!!!!!!!!! TODO !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
      if($('#input_newFilename').val() != '') {

        renameInProcNxtNum = selFileNum + 1;
      
        getSelectedFiles();

        // send rename files request //
        rename_files_request.open('GET', 'admin/content_management_requests.php?ajax_request=rename_files&album=' + selected_folder + '&files=' + oldName + '.' + ext + '&new_name=' + $('#input_newFilename').val(), true);
        rename_files_request.send(null);
        
      } else {
      
        $('#input_newFilename').css('background', '#FAA');
        $('#input_newFilename').select();
      
      }

    }  

  });
  
  $('#input_newFilename').select();
  
}

/* 
 * DELETE FILES
 *
 * Sends a delete_files request.
 * Needs global vars selected_folder! 
 * Will update folder tree and display error message if error occurred.  
 *
 * Parameter: -                     
 * Returns: -     
 */     	  
function delete_files() {

  // display loading gif //
  $('#loading_delete_file').css('display','inline');
  
  // clear oogErrorBox //
  $('#oogErrorBox').html('');
  $('#oogErrorBox').css('display','none');

  // define ajax_request to delete file //
  var delete_file_request = false;
  delete_file_request = new XMLHttpRequest();
  delete_file_request.onreadystatechange = function() {
    if(delete_file_request.readyState == 4) {
      if(delete_file_request.status == 200) {
      
        // check for session timeout //
        if(delete_file_request.responseText.substr(0,5) == "<scri") window.location.href = '../sowalbum.php?timeout=true';

        if(delete_file_request.responseText == 'succeed') {

          // deleting file was successful -> update foler tree //        
          update_folder_tree(false, true);
          
        } else {

          // display received error //
          $('#oogErrorBox').html(delete_file_request.responseText);
          $('#oogErrorBox').css('display','block');
          
        }
        
        // hide loading gif //
        $('#loading_delete_file').css('display','none');
        
      }      
    }
  }
  
  var selected_files = getSelectedFiles().join('\' & \'');
  var ok = confirm('<?php echo $lang['admin']['file']['confirm_delete']; ?>', '');
  
  if(ok) {

    // send delete file request //
    delete_file_request.open('GET', 'admin/content_management_requests.php?ajax_request=delete_files&album=' + selected_folder + '&files=' + getSelectedFiles().join('|*|'), true);
    delete_file_request.send(null);

  } else {

    // user cancelled //
    // hide loading gif //
    $('#loading_delete_file').css('display','none');

  }

}

/* 
 * SAVE FILES
 *
 * 
 *
 * Parameter: -                     
 * Returns: -     
 */     	  
function save_files() {

  window.location.href = 'admin/content_management_requests.php?ajax_request=save_files&album=' + selected_folder + '&files=' + getSelectedFiles().join('|*|');

}

/* 
 * SELECT FILES
 *
 * Selects all files.
 *
 * Parameter: -                     
 * Returns: -     
 */     	  
function select_files() {

  var objects = document.getElementById('album_mediafiles').getElementsByTagName('li');
  for(var no = 0; no < objects.length; no++)	
  	if(objects[no].getAttribute('data-isfile') == 'true') $(objects[no]).addClass('demo-selected');

  selectedFilesRenember = new Array();
  
  update_fileActions();

}

/* 
 * UNSELECT FILES
 *
 * Unselects all files.
 *
 * Parameter: -                     
 * Returns: -     
 */     	  
function unselect_files() {

  var objects = document.getElementById('album_mediafiles').getElementsByTagName('li');
  for(var no = 0; no < objects.length; no++)	
  	if(objects[no].getAttribute('data-isfile') == 'true') $(objects[no]).removeClass('demo-selected');

  selectedFilesRenember = new Array();
  
  update_fileActions();

}