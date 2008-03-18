<?php
/* 
 * Copyright (c) STMicroelectronics, 2006. All Rights Reserved.
 *
 * Originally written by Nicolas Terray, 2006
 *
 * This file is a part of CodeX.
 *
 * CodeX is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * CodeX is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CodeX; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * 
 */
require_once('Docman_View_ItemDetailsSectionActions.class.php');

class Docman_View_ItemDetailsSectionDelete extends Docman_View_ItemDetailsSectionActions {
    
    var $token;
    function Docman_View_ItemDetailsSectionDelete(&$item, $url, &$controller, $token) {
        parent::Docman_View_ItemDetailsSectionActions($item, $url, false, true, $controller);
        $this->token = $token;
    }
    function getContent() {
        $folder_or_document = is_a($this->item, 'Docman_Folder') ? 'folder' : (is_a($this->item, 'Docman_File') ? 'file' : 'document');
        
        $content = '';
        $content .= '<dl><dt>'. $GLOBALS['Language']->getText('plugin_docman', 'details_actions_delete') .'</dt><dd>';
        $content .= '<form action="'. $this->url .'" method="POST">';
        $content .= '<div class="docman_confirm_delete">';
        $content .= $GLOBALS['Language']->getText('plugin_docman', 'details_delete_warning_'.$folder_or_document,  $this->hp->purify($this->item->getTitle(), CODEX_PURIFIER_CONVERT_HTML) );
        $content .= '<div class="docman_confirm_delete_buttons">';
        if ($this->token) {
            $content .= '<input type="hidden" name="token" value="'. $this->token .'" />';
        }
        $content .= '     <input type="hidden" name="section" value="actions" />';
        $content .= '     <input type="hidden" name="action" value="delete" />';
        $content .= '     <input type="hidden" name="id" value="'. $this->item->getId() .'" />';
        $content .= '     <input type="submit" tabindex="2" name="confirm" value="'. $GLOBALS['Language']->getText('plugin_docman', 'details_delete_confirm') .'" />';
        $content .= '     <input type="submit" tabindex="1" name="cancel" value="'. $GLOBALS['Language']->getText('plugin_docman', 'details_delete_cancel') .'" />';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</form>';
        $content .= '</dd></dl>';
        return $content;
    }
}
?>
