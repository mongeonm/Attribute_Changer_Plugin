<?php


class Single_Session {

    public $attribute_list = null;

    // // $attribute_value_ids = null;
    // // $file_location = null;

    // // // $attribute_column_match;

    public $New_Entry_List;
    public $Modify_Entry_List;

    public $Current_Users_Values;


    // // // $Current_Modify_Entries_Display_Amount;
    // // // $Modify_Enties_Total_Amount;
    // // // $Modify_Entires_Number_Of_Blocks;
    // // // $Current_Modify_Entry_Block_Number;

    public $Committed_Modify_Entries;



    // // // //either 10, 100, 1000, 10000, all
    // // // //default 100
    // // // $Current_New_Entries_Display_Amount;
    // // // $New_Entries_Total_Amount;
    // // // $New_Entires_Number_Of_Blocks;
    // // // $Current_New_Entry_Block_Number;

    public $Committed_New_Entries;


    // //attribute value arrays are array => ([id] => name)
    //     //id is auto increment, no duplicates

    // //null return here is pretty much error


    // //now need to index by attribute [id]=> ([id]=>.., [name]=>.., ..)
    function Get_Attribute_List() {
        $query = sprintf('select * from %s', $GLOBALS['tables']['attribute']);
        $attribute_data_return = Sql_Query($query); 

        if($attribute_data_return) {

            $new_attribute_list = array();
   

            while(($attribute_data = Sql_Fetch_Assoc($attribute_data_return))) {

            	
            	if(!isset($attribute_data['id']) || !isset($attribute_data['name']) || !isset($attribute_data['type'])) {
                   //not known format, cannot use

                }

                

                else{

                    if(isset($new_attribute_list[$attribute_data['id']])) {
                        //cannot have duplicates, but really wont
                        continue;
                    }
                    //use the attribute list to get type and value information
                    $new_attribute_list[$attribute_data['id']] = $attribute_data;

                    //must check tables for values
                    if($attribute_data['type'] == "radio" || $attribute_data['type'] == "checkboxgroup"|| $attribute_data['type'] == "select" || $attribute_data['type'] =="checkbox") {
                    	
                        if(!isset($attribute_data['tablename'])) {
                            //this wouldnt make sense

                            unset($new_attribute_list[$attribute_data['id']]);
                        }

                        else {

                            $new_attribute_list[$attribute_data['id']]['allowed_value_ids'] = $this->Get_Attribute_Value_Id_List($attribute_data['id']);

                            if($new_attribute_list[$attribute_data['id']]['allowed_value_ids'] === null) {
                                //was an error, something missing, no values is just empty array, must still match, so unset
                                unset($new_attribute_list[$attribute_data['id']]);
                            }
                            
                        }
                    }
                    else{
                        //is other input type, do not set values array
                    }
                }
            }
            //print_r($new_attribute_list);
            return $new_attribute_list;
        }
        else{
            //no rows :S

            //PRINT AN ERROR I GUESS LOL

            return null;
        }
    }

    function Get_Attribute_Value_Id_List($attribute_id) {
		
        $query = sprintf('select type, tablename from %s where id = %d', $GLOBALS['tables']['attribute'], $attribute_id);
        $type_table_return = Sql_Query($query);
        if(!$type_table_return) {

        } 
        else {
            $row = Sql_Fetch_Row($type_table_return);

            if(!$row[0] || !$row[1]) { 

            }
            else{

                $type = $row[0];
                $table = $row[1];

                if($type == "radio" || $type == "checkboxgroup" || $type == "select" || $type == "checkbox") {

                    $attribute_value_id_array = array();
                    $tablename =$GLOBALS['table_prefix']."listattr_".$table;

                    $value_query = sprintf('select id, name from %s', $tablename);

                    $value_query_return = Sql_Query($value_query);
                    if(!$value_query_return) {
	
                    }
                    else {

                        while(($value_row = Sql_Fetch_Row($value_query_return))) {
                            $attribute_value_id_array[$value_row[0]] = $value_row[1];
                        }
                    }

                    return $attribute_value_id_array;
                }
            }
        }
        return null;
    }



    function __construct() {

        if(($this->attribute_list = $this->Get_Attribute_List()) === null ) {
            
        }
        
        $this->New_Entry_List = array();
        $this->Modify_Entry_List = array();

        $this->Current_Users_Values = array();

        $this->Committed_Modify_Entries = array();

        $this->Committed_New_Entries = array();
    }

    // function Set_File_Location ($file_name) {

    //     $this->file_location = $file_name;
    // }

    // function Get_File_Location() {
    //     return $this->file_location;
    // }

}



?>