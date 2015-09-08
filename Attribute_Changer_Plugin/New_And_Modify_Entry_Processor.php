<?php


	function Process_All_New_And_Modify() {
        if(count($Commited_New_Entires) > 0) {
            Push_New_Entries();
        }
        if(count($Commited_Modify_Entries) > 0) {
            Push_Modify_Entries();
        }
        $return_html = '<html><body>Complete</body></html>';

        $attribute_changer->close_session();

        print($return_html);
    }

    $Failed_New_Entries;

    function Push_New_Entries() {
        foreach ($Session->Commited_New_Entires as $email_key => $new_attributes_and_values) {
            $exists = Sql_Fetch_Row_Query(sprintf('select id from %s where email = "%s"', $GLOBALS['tables']['user'],$email_key));
            if($exists) {
                $Failed_New_Entries[$email_key] = $new_attributes_and_values;
            }
            else{
                $new_user_id = addNewUser($email_key);
                foreach ($new_attributes_and_values as $this_attribute_name => $this_attribute_value) {
                    if($Session->attribute_list[$this_attribute_name]['type'] === 'checkboxgroup') {
                        $new_attribute_value_ids = array();

                        foreach ($this_attribute_value as $this_key => $attribute_new_value) {
                            array_push($new_attribute_value_ids, $Session->attribute_value_ids[$attribute_new_value]);
                        }

                        $proper_this_attribute_value = implode(',', $new_attribute_value_ids);
                    }
                    else{
                        if($Session->attribute_list[$this_attribute_name]['type'] === 'checkbox'|'radio') {
                            $proper_this_attribute_value = $Session->attribute_value_ids[$this_attribute_value];
                        }
                        else{
                            $proper_this_attribute_value = $this_attribute_value;
                        }
                    }
                    //need a way for 'STICKY' attributes
                    saveUserAttribute($new_user_id, $Session->attribute_list[$this_attribute_name]['id'], $proper_this_attribute_value);
                }   
            }
        }
    }

    $Failed_Modify_Entries;
    function Push_Modify_Entries() {
        foreach ($Commited_Modify_Entries as $email_key => $modify_attributes_and_values) {
            $exists = Sql_Fetch_Row_Query(sprintf('select id from %s where email = "%s"', $GLOBALS['tables']['user'],$email_key));
            if(!$exists) {
                $Failed_Modify_Entries[$email_key] = $modify_attributes_and_values;
            }
            else{
                $modify_user_id = $exists[0];
                foreach ($modify_attributes_and_values as $this_attribute_name => $this_attribute_value) {
                    if($Session->attribute_list[$this_attribute_name]['type'] == 'checkboxgroup') {
                        $modify_attribute_value_ids = array();

                        foreach ($this_attribute_value as $this_key => $attribute_new_value) {
                            array_push($modify_attribute_value_ids, $Session->attribute_value_ids[$attribute_new_value]);
                        }

                        $proper_this_attribute_value = implode(',', $modify_attribute_value_ids);
                    }
                    else{
                        if($Session->attribute_list[$this_attribute_name]['type'] === 'checkbox'|'radio') {
                            $proper_this_attribute_value = $Session->attribute_value_ids[$this_attribute_value];
                        }
                        else{
                            $proper_this_attribute_value = $this_attribute_value;
                        }
                    }
                    //need a way for 'STICKY' attributes
                    saveUserAttribute($modify_user_id, $Session->attribute_list[$this_attribute_name]['id'], $proper_this_attribute_value);
                }
            }
        }
    }

?>