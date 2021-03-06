<?php

require_once _ROOT . '/admin/functions/db_functions.php';
require_once _ROOT . '/admin/functions/utility_functions.php';


// Get all service with the correspondent group
function get_services(){
    $services = selectJoin(TAB_GR_SERV, TAB_SERVICES, "service = id", "");
    return $services;
}


// Get all services about one group
function get_groupService($group){
    $group_service = selectJoin(TAB_GR_SERV, TAB_SERVICES, "service = id", "groupId = $group");
    return $group_service;
}


// Check the privilegies
function check_service($page, $group, $level){
    $services = get_groupService($group);
    foreach ($services as $service) {
        if($service['name'] == $page)
            return true;
    }
    if($level > 0)
        redirect("../error_page.php?typeError=403&message=Please respect the services assigned at your own group.", true);
    else
        redirect("error_page.php?typeError=403&message=Please respect the services assigned at your own group.", true);
}


// Go to the starter page of the current logged user
function go_to_start($group_id){
    $start = selectRecord(TAB_GROUPS, "id = $group_id")['start'];
    $service = selectRecord(TAB_SERVICES, "label = '$start'")['name'];
    redirect("../" . $service, true); break;
}


// Get infos about admins
function get_admins_infos(){
    $admins = selectQuery(TAB_PERSONALINFO, "", "user DESC");
    foreach($admins as $admin) {
        $id = $admin['user'];
        $result[] = selectRecord(TAB_USERS, "id = $id");
    }
    for($i = 0; $i < count($admins); $i++){
        $admins[$i]['username'] = $result[$i]['username'];
        $admins[$i]['email'] = $result[$i]['email'];
    }
    return $admins;
}

?>
