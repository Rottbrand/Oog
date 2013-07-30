<?php

/***  CONFIGS of Oog-Photo-Gallery  ***/


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

// Important: Read chapter 1.1.1 "Important Safety Note" in the ReadMe before you start modifing here!
 
$template = 'Simply'; // Expects the name of the template you want to choose.
                      // (To get more information about the templates in Oog read chapter 2.2 in the ReadMe)


$sorting_content = 'alphabetic'; // Default sorting setting for albums content. Expects: alphabetic or alphabetic_backwards
                                 // (To create your own order of an albums content read chapter 2.1 in the ReadMe) 

$sorting_menu = 'alphabetic';   // Default sorting setting for folders (all menu items: categories & albums). Expects: alphabetic or alphabetic_backwards
                                // (To create your own order of a categories content read chapter 2.1 in the ReadMe)


$htaccess_protection = true; // Expects: true or false. Protects all hidden albums additionly by a htaccess-file.
                             // (More information about hidden and htaccess protected albums/catogories read chapter 2.7 in the ReadMe)


$default_language = 'en';  // Defines language file (in folder lang/) if user has no preferred language or he has no preferred lang. 
                           // 'de' -> Deutsch
                           // 'en' -> English
                          
$contentRootDir = 'albums/';


$thumbsLoadingParallel = 24;


$rightsVisitor = array('allow_searching');


// More configs you will find in the folder of your chosen template! //

?>