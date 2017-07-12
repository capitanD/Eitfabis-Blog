<?php

require_once _ROOT . '/admin/functions/db_functions.php';
require_once _ROOT . '/admin/functions/utility_functions.php';


// Get all DB table elements
function get_tagList(){
     return selectQuery("tag", "", "id DESC");
}

// Get a selected tags by id
function get_tagById($id){
    $query = selectRecord("tag", "id = $id");
    return $query;
}

// Get a selected tags by label
function get_tagByLabel($label){
    $query = selectRecord("tag", "label = '$label'");
    return $query;
}

// Get tags by category
function get_tagsByCategory($category){
    $query = selectJoin("tag_category", "tag", "tag = id", "category = '$category'");
    return $query;
}

// Get an empty tag row
function get_emptyTag(){
    $result = array();
    $result['id'] = 0;
    $result['label'] = "example";
    $result['description'] = "brief tag description";
    return $result;
}

// Modify an existing tag
function set_tag($data, $oldId){
    $query = updateRecord("tag", $data, "id = $oldId");
    return $query;
}

//Delete one or more tags
function delete_tag($idList, $number){
    if($number == 1){
        $reference_records = selectQuery("tag_category", "tag = $idList", "tag ASC");
        if(count($reference_records) > 0)
            deleteRecord("tag_category", $idList);

        $has_records = selectQuery("article_tag", "tag = $idList", "tag ASC");
        if(count($has_records) > 0){
            deleteRecord("article_tag", $idList);
        }
        deleteRecord("tag", "id = $idList");
    }else{
        for($i = 0; $i < count($idList); $i++){
            $id = $idList[$i];
            $reference_records = selectQuery("tag_category", "tag = $id", "tag ASC");
            if(count($reference_records) > 0)
                deleteRecord("tag_category", $id);

            $has_records = selectQuery("article_tag", "tag = $id", "tag ASC");
            if(count($has_records) > 0){
                //for($i = 0; $i < count($has_records); $i++)
                    deleteRecord("article_tag", $id);
            }
            deleteRecord("tag", "id = $id");
        }
    }
}

// Insert one row in the Tag table of the DB
function insert_tag($data){
    $tagId = insertRecord("tag", $data);
    return $tagId;
}

// Restore one or more rows in the Tag table of the DB.
function restore_tag($data, $number){
    $new_data = array();

    if($number == 1){
        $new_data['id'] = $data[0]['id'];
        $new_data['label'] = $data[0]['label'];
        $new_data['description'] = $data[0]['description'];
        insertRecord("tag", $new_data);
    }else{
        foreach($data as $data_element){
            $new_data['id'] = $data_element['id'];
            $new_data['label'] = $data_element['label'];
            $new_data['description'] = $data_element['description'];
            insertRecord("tag", $new_data);
        }
    }
}

// Redefine an array sent by javascript
function restructure_tag($list, $more){
    $result = array();
    if($more){
        for($i = 0; $i < count($list); $i++){
            $result[$i]['id'] = $list[$i][0];
            $result[$i]['label'] = $list[$i][1];
            $result[$i]['description'] = $list[$i][2];
        }
    }else{
        $result['id'] = $list[0];
        $result['label'] = $list[1];
        $result['description'] = $list[2];
    }
    return $result;
}

// Get the header of the DB table Tag
function get_tagTableHeader(){
    $table_head = array();
    $table_head[0] = "Id";
    $table_head[1] = "Label";
    $table_head[2] = "Description";
    return $table_head;
}

// Check tag fields to be insered
function check_tagFields($data){
    if(empty($data['id']) || empty($data['label']) || empty($data["description"]))
        return "Empty field not allowed";
    if($data['id'] == 0)
        return "Id 0 not allowed.";
    if(get_tagById($data['id']))
        return "Id already exist!";
    if(get_tagByLabel($data['label']))
        return "Tag label already exist!";

    return $data;
}

//Set and send an html string which represents the row of the table
function push_tagRowObject($data){
    $id = $data['id'];
    $label = $data['label'];
    $description = $data['description'];

    $resultObject = '<tr class="even pointer" id="data_row" name="data_row" role="row">
                        <td class="a-center " name="table_td-checkbox">
                            <div class="icheckbox_flat-green" style="position: relative;" name="data_check" onClick="selected_checkbox(this)">
                                <input id="row_check" type="checkbox" class="table-checkbox" value="'.$id.'" name="table_records">
                                <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background-color: rgb(255, 255, 255); border: 0px; opacity: 0; background-position: initial initial; background-repeat: initial initial;"></ins>
                                <input id="row_index" type="hidden" value="" name="row_index">
                            </div>
                        </td>
                        <td id="id" class=" " name="id" style="width:7%; margin-right:5px;">
                            <input id="id" class="table_td-input" name="table_input-field" value="'.$id.'" readonly="readonly"/>
                        </td>
                        <td id="email" class=" " name="email">
                            <input id="email" class="table_td-input" name="table_input-field" value="'.$label.'" readonly="readonly"/>
                        </td>
                        <td id="date" class=" " name="date">
                            <input id="date" class="table_td-input" name="table_input-field" value="'.$description.'" readonly="readonly"/>
                        </td>
                        <td class="table-operation" name="table_td-operation">
                            <a name="delete_button" href="#" onclick="select_operation(event, '.$id.')">
                                <i id="delete" class="fa fa-trash" title="Delete"></i>
                            </a>
                            <a name="edit_button" href="#" onclick="select_operation(event, '.$id.')" >
                                <i id="edit" class="fa fa-pencil" title="Edit"></i>
                            </a>
                            <a name="load_button" class="op-not-enable" href="#" onclick="select_operation(event, '.$id.')">
                                <i id="load" class="fa fa-play-circle" title="Load"></i>
                            </a>
                        </td>
                    </tr>';
    return $resultObject;
}

?>
