
/***  CONFIGS of Oog-Lightbox  ***/


/*
 * Oog Photo-Gallery v3.2
 * http://www.oog-gallery.de/ 
 * Copyright (C) 2010 Torben Rottbrand
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


resize_photos = 'only_to_bigs';     // Expects: all or only_to_bigs
                                    // Set all to force the Oog-Lightbox to resize all photos to display's size.
                                    // Set only_to_bigs to force the Oog-Lightbox to resize only photos that are bigger than display's size to display's size.

timeout_slideshow = 5000;           // Defines the time of switching photos in the slideshow-mode (in milliseconds).

fadeInOut_loading = 250;            // Durations of fade in/out animations in milliseconds
fadeInOut_overlay = 400;
fadeInOut_file = 350;


/******************************************************
	Language-Specific Titles of Oog-Lightbox
*******************************************************/

// You can add languages: Read chapter 2.6 in the ReadMe for more information.

defaultLanguage = 'en'; // (If the required language is not available, the default language will be used.)

languages = new Array(); // (Don't delete this!)

// en - English

languages['en'] = new Array(); // (Don't delete this!)
languages['en']['cancel_loading_title'] = 'You can also cancel loading by pressing ESC';
languages['en']['cancel_loading'] = 'Click here to cancel loading';
languages['en']['close_button_title'] = 'Close | Esc';
languages['en']['previous_button_title'] = 'Previous Photo | Left Arrow Key';
languages['en']['next_button_title'] = 'Next Photo | Right Arrow Key';

// de - Deutsch

languages['de'] = new Array(); // (Don't delete this!)
languages['de']['cancel_loading_title'] = 'Sie können das Laden auch abbrechen, indem Sie die ESC-Taste drücken';
languages['de']['cancel_loading'] = 'Klicken Sie hier, um das Laden abzubrechen';
languages['de']['close_button_title'] = 'Schließen | Esc';
languages['de']['previous_button_title'] = 'Vorheriges Foto | Linke Pfeiltaste';
languages['de']['next_button_title'] = 'Nächstes Foto | Rechte Pfeiltaste';