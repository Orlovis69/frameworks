<?php 
// work
    function find_all_subjects($options=[]) {
        global $db;
        
        $visible = $options['visible'] ?? false;

        $sql = "SELECT * FROM subjects ";
        if($visible) {
            $sql .= "WHERE visible = true ";
        }
        $sql .= "ORDER BY position ASC";
        // echo $sql;
        $result = mysqli_query($db, $sql);
        confirm_result_set(($result));
        return $result;
    }

    function find_all_pages() {
        global $db;
        $sql = "SELECT * FROM pages ";
        $sql .= "ORDER BY subject_id ASC, position ASC";
        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        return $result;
    }

    function show_all_columns($table) {
        global $db;
        $sql = "SHOW COLUMNS FROM ";
        $sql .= $table;
        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        return $result;
    }

    function find_subject_by_id($id, $options = []) {
        global $db;

        $visible = $options['visible'];
        // create sql query line
        $sql = "SELECT * FROM subjects ";
        $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
        if($visible) {
            $sql .= "AND visible = true";
        }
        // echo $sql;
        // query sql
        $result = mysqli_query($db, $sql);
        // confirm getting the result
        confirm_result_set($result);
        // populate result
        $subject = mysqli_fetch_assoc($result);
        // free memory
        mysqli_free_result($result);
        return $subject;
    }

    function find_page_by_id($id, $options = []) {
        global $db;

        $visible = $options['visible'] ?? false;

        $sql = "SELECT * FROM pages ";
        $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
        if($visible) {
            $sql .= "AND visible=true";
        }

        $result_set = mysqli_query($db, $sql);
        confirm_result_set($result_set);
        $result = mysqli_fetch_assoc($result_set);
        mysqli_free_result($result_set);
        return $result;
    }

    function insert_subject($subject) {
        global $db;

        $errors = validate_subject($subject);
        if (!empty($errors)) {
            return $errors;
        }

        shift_subject_position(0, $subject['position']);

        $sql = "INSERT INTO subjects ";
        $sql .= "(menu_name, position, visible) ";
        $sql .= "VALUES (";
        $sql .= "'" . db_escape($db, $subject["menu_name"]) . "',";
        $sql .= "'" . db_escape($db, $subject["position"]) . "',";
        $sql .= "'" . db_escape($db, $subject["visible"]) . "'";
        $sql .= ");";
        $result = mysqli_query($db, $sql);
        // For INSERT statements, $result is true/false
        if ($result) {
        return true;
        } else {
            // INSERT failed
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function update_subject($subject) {
        global $db;

        $errors = validate_subject($subject);
        if(!empty($errors)) {
            return $errors;
        }

        $old_subject = find_subject_by_id($subject['id']);
        $old_position = $old_subject['position'];
        shift_subject_position($old_position, $subject['position'], $subject['id']);

        $sql = "UPDATE subjects SET ";
        $sql .= "menu_name='" . db_escape($db, $subject['menu_name']) . "',";
        $sql .= "position='" . db_escape($db, $subject['position']) . "',";
        $sql .= "visible='" . db_escape($db, $subject['visible']) . "' ";
        $sql .= "WHERE id='" . db_escape($db, $subject['id']) . "' ";
        $sql .= "LIMIT 1";

        $result = mysqli_query($db, $sql);
        // For UPDATE statements $result is true/false
        if ($result) {
            return true;
        } else {
            // Update failed
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function delete_subject($id) {
        global $db;

        $subject = find_subject_by_id($id);
        shift_subject_position($subject['position'], 0);
        
        $sql = "DELETE FROM subjects ";
        $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
        $sql .= "LIMIT 1";

        $result = mysqli_query($db, $sql);

        if ($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function shift_subject_position($start_pos, $end_pos, $current_id = 0) {
        global $db;

        if($start_pos == $end_pos) { return;}

        $sql = "UPDATE subjects ";
        if($start_pos == 0) {
            // new item, +1 to items greater than $end_pos
            $sql .= "SET position = position + 1 ";
            $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
        } elseif($end_pos == 0) {
            // delete item, -1 from items greater than $start_pos
            $sql .= "SET position = position - 1 ";
            $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
        } elseif($start_pos < $end_pos) {
            // move later, -1 to items between (including $end_pos)
            $sql .= "SET position = position - 1 ";
            $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
            $sql .= "AND position <= '" . db_escape($db, $end_pos) . "' ";
        } elseif($start_pos > $end_pos) {
            // move earlier, +1 to items between (including $end_pos)
            $sql .= "SET position = position + 1 ";
            $sql .= "WHERE position < '" . db_escape($db, $start_pos) . "' ";
            $sql .= "AND position >= '" . db_escape($db, $end_pos) . "' ";
        }
        // Exlude current_id in the SQL WHERE clause
        $sql .= "AND id != '" . db_escape($db, $current_id) . "' ";

        $result = mysqli_query($db, $sql);
        
        // For UPDATE statements, $result is true/false
        if($result) {
            return true;
        } else {
            // UPDATE FAILED
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function shift_page_position($start_pos, $end_pos, $subject_id, $current_id = 0) {
        global $db;

        if($start_pos == $end_pos) {return;}

        $sql = "UPDATE pages ";

        if($start_pos == 0) {
            // new item, +1 to all pages greater than $end_pos
            $sql .= "SET position = position + 1 ";
            $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
        } elseif($end_pos == 0) {
            // delete item, -1 to all pages less than $start_pos
            $sql .= "SET position = position - 1 ";
            $sql .= "WHERE position >= '" . db_escape($db, $start_pos) . "' ";
        } elseif($start_pos > $end_pos) {
            // move higher, +1 to all pages in between
            $sql .= "SET position = position + 1 ";
            $sql .= "WHERE position < '" . db_escape($db, $start_pos) . "' ";
            $sql .= "AND position >= '" . db_escape($db, $end_pos) . "' ";
        } elseif($start_pos < $end_pos) {
            // move lower, -1 to all pages in between
            $sql .= "SET position = position - 1 ";
            $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
            $sql .= "AND position <= '" . db_escape($db, $end_pos) . "' ";
        }

        $sql .= "AND id != '" . db_escape($db, $current_id) . "' ";
        $sql .= "AND subject_id = '" . db_escape($db, $subject_id) . "'";

        $result = mysqli_query($db, $sql);
        if($result) {
            return $result;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function insert_page($page) {
        global $db;

        // Validate input values
        $errors = validate_page($page);
        if(!empty($errors)) {
            return $errors;
        }

        shift_page_position(0, $page['position'], $page['subject_id']);

        $sql = "INSERT INTO pages ";
        $sql .= "(menu_name, position, visible, subject_id, content) ";
        $sql .= "VALUES ";
        $sql .= "('" . db_escape($db, $page['menu_name']) . "', "; 
        $sql .= "'" . db_escape($db, $page['position']) . "', "; 
        $sql .= "'" . db_escape($db, $page['visible']) . "', "; 
        $sql .= "'" . db_escape($db, $page['subject_id']) . "', "; 
        $sql .= "'" . db_escape($db, $page['content']) . "');"; 
        
        $result = mysqli_query($db, $sql);
        if($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function update_page($page) {
        global $db;

        $errors = validate_page($page);
        if(!empty($errors)) {
            return $errors;
        }

        $old_page = find_page_by_id($page['id']);
        $start_pos = $old_page['position'];

        shift_page_position($start_pos, $page['position'], $page['subject_id'], $page['id']);

        $sql = "UPDATE pages SET ";
        $sql .= "menu_name='" . db_escape($db, $page['menu_name']) . "', ";
        $sql .= "position= '" . db_escape($db, $page['position']) . "', ";
        $sql .= "visible= '" . db_escape($db, $page['visible']) . "', ";
        $sql .= "subject_id= '" . db_escape($db, $page['subject_id']) . "', ";
        $sql .= "content= '" . db_escape($db, $page['content']) . "' ";
        $sql .= "WHERE id='" . db_escape($db, $page['id']) . "' ";
        $sql .= "LIMIT 1";

        $result = mysqli_query($db, $sql);

        if($result) {
            return true;
        } else {
            mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function delete_page($id) {
        global $db;

        $page = find_page_by_id($id);

        shift_page_position($page['position'], 0 , $page['subject_id']);

        $sql = "DELETE FROM pages ";
        $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
        $sql .= "LIMIT 1";

        $result = mysqli_query($db, $sql);

        if($result) {
            return true;
        } else {
            echo mysqli_errno($db);
            db_disconnect($db);
            exit;
        }
    }

    function find_pages_by_subject_id($subject_id, $options = []) {
        global $db;

        $visible = $options['visible'] ?? false;

        $sql = "SELECT * FROM pages ";
        $sql .= "WHERE subject_id='" . db_escape($db, $subject_id) . "' ";

        if($visible) {
            $sql .= "AND visible = true ";
        }
        $sql .= "ORDER BY position ASC";

        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        return $result;
    }

    function count_pages_by_subject_id($subject_id, $options = []) {
        global $db;

        $visible = $options['visible'] ?? false;

        $sql = "SELECT COUNT(*) FROM pages ";
        $sql .= "WHERE subject_id='" . db_escape($db, $subject_id) . "' ";

        if($visible) {
            $sql .= "AND visible = true ";
        }
        $sql .= "ORDER BY position ASC";

        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        $row = mysqli_fetch_row($result);
        mysqli_free_result($result);
        $count = $row[0];
        return $count;
    }

    function validate_subject($subject) {

        $errors = [];

        // Menu name
        if(is_blank($subject['menu_name'])) {
            $errors[] = "Name cannot be blank.";
        } elseif (!has_length($subject['menu_name'], ['min' => 2, 'max' => 255])) {
            $errors[] = "Name must be between 2 and 255 characters.";
        }

        // Position
        // Make sure we are working with an integer
        $position_int = (int) $subject['position'];
        if($position_int <= 0) {
            $errors[] = "Position must be greater than zero."; 
        }
        if($position_int > 999) {
            $errors[] = "Position must be less than 999.";
        }

        // visible
        // Make sure we are working with a string
        $visible_str = (string) $subject['visible'];
        if(!has_inclusion_of($visible_str, ["0", "1"])) {
            $errors[] = "Visible must be true or false.";
        }
    
        return $errors;
    }

    function validate_page($page) {
        $errors = [];

        // Subject id
        if(is_blank($page['subject_id'])) {
            $errors[] = "Subject cannot be blank";
        }

        // Menu name
        if(is_blank($page['menu_name'])) {
            $errors[] = "Page name can't be blank";
        } elseif(!has_length($page['menu_name'], ['min' => 2, 'max' => 255])) {
            $errors[] = "Page name must be between 2 anfd 255 characters";
        }
        $current_id = $page['id'] ?? '0';
        if(!has_unique_page_menu_name($page['menu_name'], $current_id)) {
            $errors[] = "Menu name must be unique";
        }

        // Position
        // make sure that position is integer
        $position_int = (int) $page['position'];
        if($position_int <= 0) {
            $errors[] = "Postion must be greater than 0";
        } elseif($position_int > 999) {
            $errors[] = "Position must be less than 999";
        }

        // Visible
        $visible_str = (string) $page['visible'];
        if(has_exclusion_of($visible_str, ["0", "1"])) {
            $errors[] = "Visible must be true or false";
        }

        // Page contetn
        if(is_blank($page['content'])) {
            $errors[] = "Content cannot be blank";
        }

        return $errors;
    }

    function find_all_admins() {    
        global $db;

        $sql = "SELECT * FROM admins ";
        $sql .= "ORDER BY last_name ASC, first_name ASC";

        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        return $result;
    }

    function find_admin_by_id($id) {
        global $db;

        $sql = "SELECT * FROM admins ";
        $sql .= "WHERE id='" . db_escape($db, $id) . "' "; 
        $sql .= "LIMIT 1";

        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        $admin = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $admin;
    }

    function find_admin_by_username($username) {
        global $db;

        $sql = "SELECT * FROM admins ";
        $sql .= "WHERE username='" . db_escape($db, $username) . "' "; 
        $sql .= "LIMIT 1";

        $result = mysqli_query($db, $sql);
        confirm_result_set($result);
        $admin = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $admin;
    }

    function insert_admin($admin) {
        global $db;

        $errors = validate_admin($admin);
        if(!empty($errors)) {
            return $errors;
        }

        $hashed_password = password_hash($admin['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO admins ";
        $sql .= "(first_name, last_name, email, username, hashed_password) ";
        $sql .= "VALUES (";
        $sql .= "'" . db_escape($db, $admin['first_name']) . "', ";
        $sql .= "'" . db_escape($db, $admin['last_name']) . "', ";
        $sql .= "'" . db_escape($db, $admin['email']) . "', ";
        $sql .= "'" . db_escape($db, $admin['username']) . "', ";
        $sql .= "'" . db_escape($db, $hashed_password) . "'";
        $sql .= ");";       
        $result = mysqli_query($db, $sql);

        // for INSERT statements, $result is true/false
        if($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
        mysqli_free_result($result);
    }

    function update_admin($admin) {
        global $db;

        $password_sent = !is_blank($admin['password']);

        $errors = validate_admin($admin, ['password_required' => $password_sent]);
        if(!empty($errors)) {
            return $errors;
        }

        $hashed_password = password_hash($admin['password'], PASSWORD_DEFAULT);

        $sql = "UPDATE admins SET ";
        $sql .= "first_name='" . db_escape($db, $admin['first_name']) . "', ";
        $sql .= "last_name='" . db_escape($db, $admin['last_name']) . "', ";
        $sql .= "email='" . db_escape($db, $admin['email']) . "', ";
        // Check if password field is empty // no changing password
        if($password_sent) {
            $sql .= "hashed_password='" . db_escape($db, $hashed_password) . "', ";
        }
        $sql .= "username='" . db_escape($db, $admin['username']) . "' ";
        $sql .= "WHERE id='" . db_escape($db, $admin['id']) . "' ";
        $sql .= "LIMIT 1;";
        $result = mysqli_query($db, $sql);

        // For UPDATE statements. $result is true/false
        if($result) {
            // echo $result;
            return true;
        } else {
        // Update failed
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function delete_admin($id) {
        global $db;

        $sql = "DELETE FROM admins ";
        $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
        $sql .= "LIMIT 1;";
        $result = mysqli_query($db, $sql);

        // For DELETE statements, result is true/false
        if($result) {
            return true;
        } else {
        // Update failed
            echo mysqli_errno($db);
            db_disconnect($db);
            exit;
        }
    }

    function validate_admin($admin, $options = []) {

        $password_required = $options['password_required'] ?? true;
        echo $password_required;

        // $errors = [];
        // first name 2-255
        if(is_blank($admin['first_name'])) {
            $errors[] = "First name cannot be blank";
        } elseif(!has_length($admin['first_name'], ['min' => '2', 'max' => '255'])) {
            $errors[] = "First name must be between 2 and 255 characters";
        };
        // last name 2-255
        if (is_blank($admin['last_name'])) {
            $errors[] = "Last name cannot be blank";
        } elseif (!has_length($admin['last_name'], ['min' => '2', 'max' => '255'])) {
            $errors[] = "Last name must be between 2 and 255 characters";
        };
        // email
        if (is_blank($admin['email'])) {
            $errors[] = "Email cannot be blank";
        } elseif(!has_valid_email_format($admin['email'])) {
            $errors[] = "Enter valid email";
        };
        // username UNIQUE, 8-255
        if(is_blank($admin['username'])) {
            $errors[] = "Username cannot be blank";
        } elseif(!has_length($admin['username'], ['min' => '8', 'max' => '255'])) {
            $errors[] = "Username name must be between 8 and 255 characters";
        } elseif (!has_unique_username($admin['username'], $admin['id'] ?? 0)) {
            $errors[] = "Username not allowed. Try another!";
        }
        // password 12+ characters
        if($password_required) {
            if(is_blank($admin['password'])) {
                $errors[] = "Password cannot be blank"; 
            } elseif(!has_length($admin['password'], ['min' => '12', 'max' => '255'])) {
                $errors[] = "Password must contain 12 or more characters";
            } elseif(!preg_match("/\d/", $admin['password'])) {
                $errors[] = "Password must contain at least one number";
            } elseif(!preg_match("/[A-Z]/", $admin['password'])) {
                $errors[] = "Password must contain at least one uppercase letter";
            } elseif(!preg_match("/[a-z]/", $admin['password'])) {
                $errors[] = "Password must contain at least one lowercase letter";
            } elseif(!preg_match("/\W/", $admin['password'])) {
                $errors[] = "Password must contain at least one special character";
            }
            // password 1 uppercase, 1 lowercase, 1 number, 1 symbol
        }

        // confirm password not blank, matches password
        if($admin['confirm_password'] !== $admin['password']) {
            $errors[] = "Password and confirm password must match";
        }
        return $errors;
    }

?>