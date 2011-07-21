<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// 

require_once('pre.php');    
require_once('www/project/admin/project_admin_utils.php');
require_once('www/project/export/project_export_utils.php');


session_require(array('group'=>$group_id,'admin_flags'=>'A'));

$request = HTTPRequest::instance();

// Check if group_id is valid
$vGroupId = new Valid_GroupId();
$vGroupId->required();
if($request->valid($vGroupId)) {
    $group_id = $request->get('group_id');
} else {
    exit_no_group();
}

$offset = $request->getValidated('offset', 'uint', 0);
if ( !$offset || $offset < 0 ) {
    $offset = 0;
}
$limit  = 50;

if (isset($_REQUEST['SUBMIT'])) {
    export_grouphistory($group_id);
    exit;
}

project_admin_header(array('title'=>$Language->getText('project_admin_history','proj_history'),'group'=>$group_id));

echo $Language->getText('project_admin_history','proj_change_log_msg');

//for pagination
echo show_grouphistory($group_id, $offset, $limit);

echo '<BR><FORM METHOD="POST" NAME="project_history_export_form">
    <TABLE align="left"><TR><TD>
    <INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'.$GLOBALS['Language']->getText('project_stats_source_code_access','logs_export').'">
    </TD></TR></TABLE></FORM><BR><P>';

project_admin_footer(array());
?>
