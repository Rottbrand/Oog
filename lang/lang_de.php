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

$lang['core']['file_description_info']['1'] = "Eine Beschreibung für Gif- und Png-Dateien kommt in Oog v3.3!";
$lang['core']['file_description_info']['2'] = "Eine Beschreibung für Video-Dateien kommt in Oog v3.3!";
$lang['core']['file_description_info']['3'] = "Eine Beschreibung für Audio-Dateien kommt in Oog v3.3!";

$lang['core']['user_login']['1'] = "Benutzeranmeldung";
$lang['core']['user_login']['2'] = "Wahrscheinlich wurde Ihre Sitzung beendet. Bitte neu anmelden:";
$lang['core']['user_login']['3'] = "Anmeldung in Ihr Account:";
$lang['core']['user_login']['4'] = "Benutzername oder Passwort falsch:";
$lang['core']['user_login']['5'] = "Hallo";
$lang['core']['user_login']['6'] = "Die folgenden Funktionen sind Ihnen erlaubt:";
$lang['core']['user_login']['7'] = "Versteckte Alben Sehen";
$lang['core']['user_login']['8'] = "Inhalte Bearbeiten";
$lang['core']['user_login']['9'] = "Benutzer Verwalten";
$lang['core']['user_login']['10'] = "Mein Profil";
$lang['core']['user_login']['11'] = "Design Bearbeiten";
$lang['core']['user_login']['12'] = "Globale Einstellungen";

$lang['core']['album_showpage']['1'] = "Dieses Album ist noch leer.";
$lang['core']['album_showpage']['2'] = "Album öffnen";

///////////////////////////////// core/showalbum.php ///////////////////////////////

$lang['core']['album_search']['1'] = "Gefunden:";
$lang['core']['album_search']['2'] = "Datei(en)";
$lang['core']['album_search']['3'] = "Suche";

$lang['core']['album']['1'] = "Gesehen:";

//////////////////////////////// templates/Simply/template.html /////////////////

$lang['template']['button_home'] = "Start-Seite";

///////////////////////////////// core/request.php ///////////////////////////////

$lang['core']['lightbox']['1'] = "Name:";
$lang['core']['lightbox']['2'] = "Datum, Uhrzeit:";
$lang['core']['lightbox']['3'] = "Gr&ouml;&szlig;e:";
$lang['core']['lightbox']['4'] = "Kamera:";
$lang['core']['lightbox']['5'] = "Ort:";
$lang['core']['lightbox']['6'] = "Beschreibung:";
$lang['core']['lightbox']['7'] = "Kommentarfunktion ist geplant";
$lang['core']['lightbox']['8'] = "In dieser Version k&ouml;nnen nur JPEG-Kommentare bearbeitet werden.";
$lang['core']['lightbox']['9'] = "Hier klicken, um abzubrechen";

//////////////////////////////// templates/Simply/template_config.php /////////////////

$lang['template']['album_showpage']['1'] = "Album öffnen";
$lang['template']['album_showpage']['2'] = "Gesehen:";
$lang['template']['album_showpage']['3'] = "Datum:";
$lang['template']['album_showpage']['4'] = "Willkommen in unserer Galerie!";  // Hauptüberschrift
$lang['template']['album_showpage']['5'] = "Meist gesehene Alben - Top";
$lang['template']['album_showpage']['6'] = "Neueste und zuletzt geänderte Alben - Top";

//////////////////////////////// core/admin/index.php ////////////////////////////////

$lang['admin']['title'] = "Oog Administrations-Bereich";

$lang['admin']['navi']['btn_backtogallery'] = "Zurück zur Galerie";
$lang['admin']['navi']['btn_albummanagement'] = "Albenverwaltung";
$lang['admin']['navi']['btn_galleryconfiguration'] = "Erscheinungsbild";
$lang['admin']['navi']['btn_usermanagement'] = "Benutzerverwaltung";

$lang['admin']['connection_error'] = "Verbindung unterbrochen! Bitte Seite neu laden.";
$lang['admin']['ajax_error'] = "Ajax-Objekt konnte nicht erstellt werden. Möglicherweise muss Ihr Internet-Browser aktualisiert werden!";
$lang['admin']['noscript_error'] = "Kann Script nicht starten! Bitte vergewissern Sie sich, das JavaScript in Ihrem Browser aktiviert ist.";

$lang['admin']['album_mediafile']['1'] = "Wählen Sie ein Album aus und dessen Inhalt wird hier angezeigt!";
$lang['admin']['album_mediafile']['2'] = "Dieses Album ist leer!";

$lang['admin']['file']['char_error'] = "Nichterlaubte Zeichen! Erlaubte Zeichen sind:";
$lang['admin']['file']['noname_error'] = "Kein Name eingegeben!";
$lang['admin']['file']['doublename_error'] = "Eine Datei mit diesem Namen existert bereits!";
$lang['admin']['file']['rename_error'] = "Datei(en) umbenennen fehlgeschlagen!";
$lang['admin']['file']['delete_error'] = "Datei(en) löschen fehlgeschlagen!";
$lang['admin']['file']['saveownorder_error'] = "Selbsterstellte Reihenfolge konnte nicht gespeichert werden!";
$lang['admin']['file']['move_error'] = "Datei(en) verschieben fehlgeschlagen!";
$lang['admin']['file']['btn_download'] = "Laden Sie die ausgewählte(n) Datei(en) auf Ihr Gerät herunter";
$lang['admin']['file']['btn_open'] = "Öffnet die ausgewählte Datei in der Lightbox";
$lang['admin']['file']['btn_rename'] = "Umbenennen der ausgewählte(n) Datei(en)";
$lang['admin']['file']['msg_rename']['1'] = "Möchten Sie einmal einen Namen eingeben und den Rest Durchnummerieren lassen? Drücken Sie Abbrechen, um einzeln umzubenennen.";
$lang['admin']['file']['msg_rename']['2'] = "Bitte neuen Name eingeben:";
$lang['admin']['file']['btn_delete'] = "Löschen der ausgewählte(n) Datei(en)";
$lang['admin']['file']['confirm_delete'] = "Soll wirklich \'' + selected_files + '\' gelöscht werden?";
$lang['admin']['file']['btn_editDesc'] = "Beschreibung der ausgewählten Datei bearbeiten";
$lang['admin']['file']['btn_editCom'] = "Kommentare der ausgewählten Datei bearbeiten";
$lang['admin']['file']['btn_rotateL'] = "Ausgewählte Datei(en) 90 Grad nach links drehen";
$lang['admin']['file']['btn_rotateR'] = "Ausgewählte Datei(en) 90 Grad nach rechts drehen";
$lang['admin']['file']['btn_select_all'] = "Alle Dateien auswählen";
$lang['admin']['file']['btn_unselect_all'] = "Dateienauswahl aufheben";
$lang['admin']['file']['btn_upload'] = "Dateien Upload";
$lang['admin']['file']['btn_cancel_upload'] = "Abbrechen";
$lang['admin']['file']['upload_progress'] = "Fertig:";
$lang['admin']['file']['upload_tip'] = "Erlaubte Formate in Oog sind JPG, PNG, GIF, MP3, MP4 and FLV (Video-Codecs: VP6 und H.264). Zum Konvertieren Ihrer Videos empfiehlt Oog das kostenlose Programm:";
$lang['admin']['file']['upload_resize'] = "Bilder vor Upload verkleinern";
$lang['admin']['file']['upload_resize_height'] = "Max. neue Höhe:";
$lang['admin']['file']['upload_resize_width'] = "Max. neue Breite:";
$lang['admin']['file']['upload_resize_quality'] = "Neue Qualität:";

$lang['admin']['folder']['hidden'] = "Dies ist ein versteckter Ordner";
$lang['admin']['folder']['cant_move'] = "Ordner können nicht in Alben, die Datein enthalten, verschoben werden!)";
$lang['admin']['folder']['path'] = "Pfad: /";
$lang['admin']['folder']['lastchange'] = "Zuletzt geändert am:";
$lang['admin']['folder']['type_cat'] = "Verzeichnis-Art: Kategorie";
$lang['admin']['folder']['type_alb'] = "Verzeichnis-Art: Album";

$lang['admin']['folder']['btn']['add']['title'] = "Hinzufügen";
$lang['admin']['folder']['btn']['add']['info']['1'] = "Ein Verzeichnis (neues Album) zur markierten Kategorie hinzufügen.";
$lang['admin']['folder']['btn']['add']['info']['2'] = "Ein Verzeichnis (neues Album) zum markierten Verzeichnis (wird somit zur Kategorie) hinzufügen.";
$lang['admin']['folder']['btn']['add']['info']['3'] = "Ein Verzeichnis (neues Album) kann nur zu einer Kategorie oder einem leeren Album hinzugefügt werden.";
$lang['admin']['folder']['btn']['hide']['title'] = "Verstecken";
$lang['admin']['folder']['btn']['hide']['info']['1'] = "Verstecken Sie das ausgewählte Verzeichnis, um es nur noch berechtigte Nutzer sehen zu lassen.";
$lang['admin']['folder']['btn']['unhide']['title'] = "Aufdecken";
$lang['admin']['folder']['btn']['unhide']['info']['1'] = "Decken Sie das ausgewählte Verzeichnis auf, um es jeden sehen zu lassen.";
$lang['admin']['folder']['btn']['unhide']['info']['2'] = "Das ausgewählte Verzeichnis ist versteckt, da bereits ein Eltern-Verzeichnis versteckt ist. (Um dieses Verzeichnis aufzudecken müssen Sie das versteckte Eltern-Verzeichnis aufdecken)";
$lang['admin']['folder']['btn']['rename']['title'] = "Umbenennen";
$lang['admin']['folder']['btn']['rename']['prompt'] = "Bitte neuen Verzeichnisnamen eingeben:";
$lang['admin']['folder']['btn']['rename']['info']['1'] = "Das ausgewählte Verzeichnis umbenennen";
$lang['admin']['folder']['btn']['rename']['info']['2'] = "Das Stammverzeichnis kann nicht umbenannt werden!";
$lang['admin']['folder']['btn']['delete']['title'] = "Löschen";
$lang['admin']['folder']['btn']['delete']['confirm'] = "Soll wirklich \'' + selected_name + '\' und der gesamte Inhalt gelöscht werden?";
$lang['admin']['folder']['btn']['delete']['info']['1'] = "Das ausgewählte Verzeichnis mit allen Inhalten löschen";
$lang['admin']['folder']['btn']['delete']['info']['2'] = "Das Stammverzeichnis kann nicht gelöscht werden!";
$lang['admin']['folder']['btn']['reset_order']['title'] = "Reihenfolge";
$lang['admin']['folder']['btn']['reset_order']['confirm'] = "Soll wirklich selbstdefinierte Reihenfolge in \'' + selected_name + '\' zurückgesetzt werden?";
$lang['admin']['folder']['btn']['reset_order']['info']['1'] = "Selbstdefinierte Reihenfolge dieser Kategorie zurücksetzen.";
$lang['admin']['folder']['btn']['reset_order']['info']['2'] = "Diese Kategorie hat keine selbstdefinierte Reihenfolge.";
$lang['admin']['folder']['btn']['reset_order']['info']['3'] = "Selbstdefinierte Reihenfolge dieses Albums zurücksetzen.";
$lang['admin']['folder']['btn']['reset_order']['info']['4'] = "Dieses Album hat keine selbstdefinierte Reihenfolge.";
$lang['admin']['folder']['btn']['reset_order_also_subfolders']['title'] = "Auch Unterordner zurücksetzen";
$lang['admin']['folder']['btn']['reset_order_also_subfolders']['confirm'] = "Sollen wirklich alle selbstdefinierten Reihenfolgen von \'' + selected_name + '\' und die aller Unterverzeichnisse gelöscht werden?";
$lang['admin']['folder']['btn']['reset_order_also_subfolders']['info']['1'] = "Falls Sie auch selbstdefinierte Reihenfolgen in Unter-verzeichnissen zurückgesetzen möchten:";
$lang['admin']['folder']['btn']['refresh']['title'] = "Aktualisieren";
$lang['admin']['folder']['btn']['collapse']['title'] = "Komprimieren";
$lang['admin']['folder']['btn']['expand']['title'] = "Erweitern";

$lang['admin']['folder']['char_error'] = "Nichterlaubte Zeichen! Erlaubte Zeichen sind:";
$lang['admin']['folder']['noname_error'] =  "Kein Name eingegeben!";
$lang['admin']['folder']['doublename_error'] =  "Album oder Kategorie mit diesem Namen existert bereits!";
$lang['admin']['folder']['notempty_error'] = "Album muss leer sein!";
$lang['admin']['folder']['add_error'] = "Verzeichnis erstellen fehlgeschlagen!";
$lang['admin']['folder']['rename_error'] = "Verzeichnis umbenennen fehlgeschlagen!";
$lang['admin']['folder']['delete_error'] = "Das Verzeichnis konnte nicht gelöscht werden!";
$lang['admin']['folder']['deleteroot_error'] = "Das Root-Verzeichnis kann nicht gelöschet werden!";
$lang['admin']['folder']['renameroot_error'] = "Root-Verzeichnis kann nicht umbenannt werden!";
$lang['admin']['folder']['saveownorder_error'] = "Selbsterstellte Reihenfolge konnte nicht gespeichert werden!";
$lang['admin']['folder']['deleteownorder_error'] = "Selbsterstellte Reihenfolge konnte nicht gelöscht werden!";
$lang['admin']['folder']['hide_error'] = "Kategorie oder Album konnte nicht versteckt werden!";
$lang['admin']['folder']['unhide_error'] = "Kategorie oder Album konnte nicht sichtbar gemacht werden!";

$lang['admin']['user']['btn']['add']['title'] = "Nutzer Hinzufügen";
$lang['admin']['user']['btn']['add']['error']['1'] = "Sie sind nicht berechtigt Benutzer hinzuzufügen!";
$lang['admin']['user']['btn']['add']['error']['2'] = "Es konnte kein Benutzer hinzugefügt werden!";
$lang['admin']['user']['btn']['rename']['title'] = "Umbenennen";
$lang['admin']['user']['btn']['rename']['prompt'] = "Bitte neuen Benutzernamen eingeben:";
$lang['admin']['user']['btn']['delete']['title'] = "Löschen";
$lang['admin']['user']['btn']['delete']['confirm'] = "Soll wirklich der Benutzer \'' + selected_name + '\' gelöscht werden?";
$lang['admin']['user']['btn']['change_password']['title'] = "Passwort Ändern";
$lang['admin']['user']['btn']['change_password']['prompt'] = "Neues Passwort eingeben:";
$lang['admin']['user']['btn']['refresh']['title'] = "Aktualisieren";

$lang['admin']['user']['namefield'] = "Benutzername:";
$lang['admin']['user']['lastactivity'] = "Letzte Aktivität:";

?>