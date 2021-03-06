<?php
/**
 * Copyright (c) STMicroelectronics, 2007. All Rights Reserved.
 * Copyright (c) Enalean, 2016. All Rights Reserved.
 *
 * Originally written by Manuel VACELET, 2007.
 *
 * This file is a part of Codendi.
 *
 * Codendi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Codendi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codendi; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

use Tuleap\Admin\AdminPageRenderer;
use Tuleap\Userlog\UserLogBuilder;
use Tuleap\Userlog\UserLogPresenter;


class UserLogManager {
    /**
     * @var AdminPageRenderer
     */
    private $admin_page_renderer;
    /**
     * @var UserManager
     */
    private $user_manager;

    public function __construct(AdminPageRenderer $admin_page_renderer, UserManager $user_manager)
    {
        $this->admin_page_renderer = $admin_page_renderer;
        $this->user_manager        = $user_manager;
    }

    function getDao()
    {
        $da  = CodendiDataAccess::instance();
        $dao = new UserLogDao($da);

        return $dao;
    }

    function logAccess($time, $gid, $uid, $sessionHash, $userAgent, $requestMethod, $requestUri, $remoteAddr, $httpReferer) {
        $dao = $this->getDao();
        $dao->addRequest($time, $gid, $uid, $sessionHash, $userAgent, $requestMethod, $requestUri, $remoteAddr, $httpReferer);
    }

    function displayNewOrIdem($key, $row, &$pval, $display = null) {
        if($pval[$key] != $row[$key]) {
            if($display === null) {
                $dis = $row[$key];
            } else {
                $dis = $display;
            }
            // Display treatment
            if($dis == '') {
                $dis = '&nbsp;';
            } else {
                $hp = Codendi_HTMLPurifier::instance();
                $dis = $hp->purify($dis);
            }
        } else {
            $dis = '-';
        }

        $pval[$key] = $row[$key];
        return $dis;
    }

    function initPval(&$pval) {
        $pval = array('time' => -1,
                      'hour' => -1,
                      'group_id' => -1,
                      'user_id' => -1,
                      'session_hash' => -1,
                      'http_user_agent' => -1,
                      'http_request_method' => -1,
                      'http_request_uri' => -1,
                      'http_remote_addr' => -1,
                      'http_referer' => -1);
    }

    function displayLogs($offset, $selected_day = null)
    {
        $year  = null;
        $month = null;
        $day   = null;

        if ($selected_day !== null) {
            if (preg_match('/^([0-9]+)-([0-9]{1,2})-([0-9]{1,2})$/', $selected_day, $match)) {
                $year  = $match[1];
                $month = $match[2];
                $day   = $match[3];
            }
        }
        if ($year === null) {
            $year  = date('Y');
            $month = date('n');
            $day   = date('j');
        }

        $start = mktime(0, 0, 0, $month, $day, $year);
        $end   = mktime(23, 59, 59, $month, $day, $year);
        $count = 100;

        $log_builder              = new UserLogBuilder($this->getDao(), $this->user_manager);
        list($logs, $total_count) = $log_builder->build($start, $end, $offset, $count);

        $presenter = new UserLogPresenter($logs, $selected_day, $count, $offset, $total_count);

        $this->admin_page_renderer->renderAPresenter(
            'userlog',
            USERLOGS_TEMPLATE_DIR,
            'userlogs',
            $presenter
        );

        $GLOBALS['Response']->includeCalendarScripts();
    }
}
