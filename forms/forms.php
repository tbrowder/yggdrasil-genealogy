<?php

/***************************************************************************
 *   forms.php                                                             *
 *   Yggdrasil: Common Form Functions                                      *
 *                                                                         *
 *   Copyright (C) 2006-2011 by Leif B. Kristensen <leif@solumslekt.org>   *
 *   All rights reserved. For terms of use, see LICENSE.txt                *
 ***************************************************************************/

function select_tag_type($selected=0) {
    // print table row with an option box for tag type
    echo "<tr><td>Type:  </td><td>\n<select name = \"tag_type\">\n";
    $handle = pg_query("SELECT tag_type_id, description
                            FROM tag_types ORDER BY tag_type_id");
    while ($rec = pg_fetch_assoc($handle)) {
        $option = "<option ";
        if ($rec['tag_type_id'] == $selected)
            $option .= "selected=\"selected\" ";
        $option .= "value=\"" . $rec['tag_type_id'] . "\">";
        $option .= $rec['description'] . "</option>\n";
        echo $option;
    }
    echo "</select></td></tr>\n";
}

function select_tag_group($selected=0) {
    // print table row with an option box for tag groups
    global $language;
    $tag_group_name = 'tag_group_name_' . $language;
    echo "<tr><td>Type:  </td><td>\n<select name = \"tag_group\">\n";
    $handle = pg_query("SELECT tag_group_id, $tag_group_name
                            FROM tag_groups ORDER BY tag_group_id");
    while ($rec = pg_fetch_assoc($handle)) {
        $option = "<option ";
        if ($rec['tag_group_id'] == $selected)
            $option .= "selected=\"selected\" ";
        $option .= "value=\"" . $rec['tag_group_id'] . "\">";
        $option .= $rec[$tag_group_name] . "</option>\n";
        echo $option;
    }
    echo "</select></td></tr>\n";
}

function select_tag($selected=0, $person=0, $event=0) {
    // print table row with an option box for tags
    global $language, $_Type;
    echo "<tr><td>$_Type:  </td><td>\n<select name=\"tag_fk\">";
    if ($language == 'nb')
        $handle = pg_query("SELECT tag_id, tag_label, tag_count(tag_id) AS tc
                            FROM tags ORDER BY tc desc");
    elseif ($language == 'en')
    $handle = pg_query("SELECT tag_id, tag_name, tag_count(tag_id) AS tc
                            FROM tags ORDER BY tc desc");
    while ($rec = pg_fetch_row($handle)) {
        $option = "<option ";
        if ($rec[0] == $selected)
            $option .= "selected=\"selected\" ";
        $option .= "value=\"" . $rec[0] . "\">" . $rec[1] . "</option>\n";
        echo $option;
    }
    echo "</select><span id=\"coprincipal\"></span></td></tr>\n";
}

function select_source_type($prompt, $name, $selected=0) {
    // print table row with an option box for source part types
   global $language;
   $label = 'label_' . $language;
   echo "<tr><td>$prompt</td><td>\n<select name=\"$name\">";
    $handle = pg_query("SELECT part_type_id, $label, part_type_count(part_type_id) AS tc
                            FROM source_part_types ORDER BY tc desc");
    while ($rec = pg_fetch_assoc($handle)) {
        $option = "<option ";
        if ($rec['part_type_id'] == $selected)
            $option .= "selected=\"selected\" ";
        $option .= "value=\"" . $rec['part_type_id'] . "\">" . $rec[$label] . "</option>\n";
        echo $option;
    }
    echo "</select></td></tr>\n";
}

function select_role($selected=0) {
    // print table row with an option box for roles
    echo "<tr><td>Rolle:  </td><td>\n<select name=\"role_id\">";
    $handle = pg_query("SELECT role_id, role_no FROM linkage_roles ORDER BY role_id");
    while ($rec = pg_fetch_assoc($handle)) {
        $option = "<option ";
        if ($rec['role_id'] == $selected)
            $option .= "selected=\"selected\" ";
        $option .= "value=\"" . $rec['role_id'] . "\">" . $rec['role_no'] . "</option>\n";
        echo $option;
    }
    echo "</select></td></tr>\n";
}

function select_surety($selected=0) {
    // print table row with an option box for sureties
    echo "<tr><td>Sikkerhet:  </td><td>\n<select name=\"surety\">";
    $handle = pg_query("SELECT surety_id, surety_no FROM sureties ORDER BY surety_id DESC");
    while ($rec = pg_fetch_assoc($handle)) {
        $option = "<option ";
        if ($rec['surety_id'] == $selected)
            $option .= "selected=\"selected\" ";
        $option .= "value=\"" . $rec['surety_id'] . "\">" . $rec['surety_no'] . "</option>\n";
        echo $option;
    }
    echo "</select></td></tr>\n";
}

function select_bool($prompt, $name, $selected='f') {
    // print table row with an option box for false/true
    echo "<tr><td>$prompt: </td><td>\n<select name=\"$name\">";
    echo "<option ";
    if ($selected == 'f')
        echo "selected=\"selected\" ";
    echo "value=\"f\">False</option>";
    echo "<option ";
    if ($selected == 't')
        echo "selected=\"selected\" ";
    echo "value=\"t\">True</option>";
    echo "</select></td></tr>\n";
}

function select_place($selected=0) {
    // print table row with an option box for places.
    global $_Place;
    $recent_selected = false;
    echo "<tr><td>$_Place:  </td><td>\n<select name=\"place_fk\">\n";
    if (!$selected) {
        $selected = get_last_selected_place();
    }
    // insert 10 last selected places here
    $handle = pg_query("SELECT place_fk, get_place_name(place_fk) AS place_name
                            FROM recent_places ORDER BY place_name");
    while ($rec = pg_fetch_assoc($handle)) {
        $option = "<option ";
        // if $selected is in recent-list, point to it here
        if ($rec['place_fk'] == $selected) {
            $option .= "selected=\"selected\" ";
            $recent_selected = true;
        }
        $option .= "value=\"" . $rec['place_fk'] . "\">" . $rec['place_name'] . "</option>\n";
        echo $option;
    }
    // insert divider
    echo "<option value=\"0\"> ---------- </option>\n";
    // see function filtered_places() in functions.sql
    // no interface for filter settings yet, use psql
    $handle = pg_query("SELECT place_id, get_place_name(place_id) AS place_name
                            FROM filtered_places() WHERE place_id > 0 ORDER BY place_name");
    while ($rec = pg_fetch_assoc($handle)) {
        $option = "<option ";
        if (!$recent_selected && $rec['place_id'] == $selected)
            $option .= "selected=\"selected\" ";
        $option .= "value=\"" . $rec['place_id'] . "\">" . $rec['place_name'] . "</option>\n";
        echo $option;
    }
    echo "</select>";
    echo "</td></tr>\n";
}

function select_tag_and_place() {}

function hidden_input($name, $value) {
    echo "<tr style=\"display:none\"><td><input type=\"hidden\" name=\"$name\" value=\"$value\" /></td></tr>\n";
}

function text_input($prompt, $size, $name, $value='', $trailer='', $tab='') {
    // print table row with input field
    $row = "<tr><td>$prompt</td><td><input type=\"text\" size=\"$size\" name=\"$name\"";
    if ($value)
        $row .= "value=\"" . $value . "\"";
    if ($tab)
        $row .= " tabindex=\"$tab\"";
    $row .= " />$trailer</td></tr>\n";
    echo $row;
}

function textarea_input($prompt, $rows, $cols, $name, $value='', $tab='') {
    // print table row with text area
    $row = "<tr><td>$prompt</td><td><textarea class=\"input\" rows=\"$rows\" cols=\"$cols\" name=\"$name\"";
    if ($tab)
        $row .= " tabindex=\"$tab\"";
    $row .= ">";
    if ($value)
        $row .= $value;
    $row .= "</textarea></td></tr>\n";
    echo $row;
}

function editarea_input($prompt, $rows, $cols, $name, $value='', $tab='') {
    // print table rows with Editarea
    echo "<tr><td>$prompt</td><td><textarea id=\"editarea_text\""
        . " class=\"input\" rows=\"$rows\" cols=\"$cols\" name=\"$name\"";
    if ($tab)
        echo " tabindex=\"$tab\"";
    echo ">";
    if ($value)
        echo $value;
    echo "</textarea></td></tr>\n";
    echo "<tr><td>Markup:</td><td>";
/*
    $els = array(   "birth","death","marriage","source","ref","name","date",
                    "place","age","father","mother","husband","wife","brother",
                    "sister","child","cause","witness","male","female",
                    "doctorsVisit"
                );
    sort($els);
    $els = array("p","ol","ul","li","name","place","note");
    foreach ( $els as $e ) {
        $row .= " <input type='button'
        onclick='editAreaLoader.insertTags(\"editarea_text\", \"<$e>\",
        \"</$e>\");' value='$e' />";
    }
*/
    $btn = "<input type='button' onclick='editAreaLoader.insertTags(\"editarea_text\",";
    echo $btn . " \"<p>\", \"</p>\");' value='p' />";
    echo $btn . " \"<ol>\", \"</ol>\");' value='ol' />";
    echo $btn . " \"<ul>\", \"</ul>\");' value='ul' />";
    echo $btn . " \"<li>\", \"</li>\");' value='li' />";
    echo $btn . " \"<a href=>\", \"</a>\");' value='a' />";
    echo $btn . " \"<note>\", \"</note>\");' value='note' />";
    echo $btn . " \"<name>\", \"</name>\");' value='name' />";
    // seems to be no way to insert quotes
    echo $btn . " \"<name role=child n=>\", \"</name>\");' value='child' />";
    echo $btn . " \"<name role=father n=>\", \"</name>\");' value='father' />";
    echo $btn . " \"<name role=mother n=>\", \"</name>\");' value='mother' />";
    echo $btn . " \"<place>\", \"</place>\");' value='place' />";
    echo $btn . " \"[p=|\", \"]\");' value='person' />";

    echo "</td></tr>\n";
}

function name_input() {
    global $_Name, $_Given, $_Surname, $_Patronym, $_Occupation, $_Toponym, $_Epithet;
    echo "<tr>\n";
    echo "<td>&nbsp;</td>";
    echo "<td colspan=\"2\">\n";
    echo "<fieldset><legend>$_Name</legend>\n";
    echo "<table>\n";
    echo "<tr><td>$_Given:</td>\n";
    echo "<td><input type=\"text\" size=\"35\" name=\"given\" tabindex=\"1\" /></td>\n";
    echo "<td>$_Surname:</td>\n";
    echo "<td><input type=\"text\" size=\"35\" name=\"surname\" tabindex=\"4\" /></td>\n</tr>\n";
    echo "<tr><td>$_Patronym:</td>\n";
    echo "<td><input type=\"text\" size=\"35\" name=\"patronym\" tabindex=\"2\" /></td>\n";
    echo "<td>$_Occupation:</td>\n";
    echo "<td><input type=\"text\" size=\"35\" name=\"occupation\" tabindex=\"5\" /></td>\n</tr>\n";
    echo "<tr><td>$_Toponym:</td>\n";
    echo "<td><input type=\"text\" size=\"35\" name=\"toponym\" tabindex=\"3\" /></td>\n";
    echo "<td>$_Epithet:</td>\n";
    echo "<td><input type=\"text\" size=\"35\" name=\"epithet\" tabindex=\"6\" /></td>\n</tr>\n";
    echo "</table>\n";
    echo "</fieldset></td>\n</tr>\n";
}

function source_num_input($prompt, $postkey, $postval) {
    // generalized input for source numbers, updates source text on the fly
    if (!$postval)
        $postval = get_last_selected_source();
    $source_text = get_source_text($postval);
    echo "<tr><td>$prompt</td><td><input type=\"text\" size=\"10\" ";
    // dynamic AJAX update of source text
    echo "name=\"$postkey\" value=\"$postval\" ";
    echo "onchange=\" get_source(this.value)\" />";
    echo "<span id=\"source\">$source_text</span></td></tr>\n";
}

function source_input() {
    // print source number and textarea input.
    global $_Source, $_Text;
    source_num_input($_Source, "source_id", 0);
    editarea_input("$_Text: ", 10, 100, 'source_text');
}

function participant_input($coprincipal) { // should be deprecated, see below
    // to avoid cluttering, this func allows for one coprincipal.
    // I really need an entirely new routine to handle 'history' events w/o principals.
    global $_With;
    echo "<tr><td>$_With</td><td><input type=\"text\" size=\"10\" value=\"$coprincipal\" ";
    echo "name=\"coprincipal\" onchange=\" get_name(this.value)\">";
    echo "<span id=\"name\">";
    echo ' ' . linked_name($coprincipal, '../family.php');
    echo "</span></td></tr>\n";
}

function person_id_input($person_id, $fieldname, $prompt='') {
    // generalised version of above func
    // input person_id, dynamically updates with linked name
    $person_id ? $pstr = $person_id : $pstr = '';
    echo "<tr><td>$prompt</td><td><input type=\"text\" size=\"10\" value=\"$pstr\" ";
    echo "name=\"$fieldname\" onchange=\" get_name(this.value)\">";
    echo "<span id=\"name\">";
    echo ' ' . linked_name($person_id, '../family.php');
    echo "</span></td></tr>\n";
}

function divider() {
    // just prints a <hr> inside the form table
    echo "<tr><td colspan=\"2\"><hr /></td></tr>\n";
}

function form_submit($value='OK') {
    // submit button
    echo "<tr><td>&nbsp;</td><td><input type=\"submit\" value=\"$value\" /></td></tr>\n";
}

function form_begin($name, $action) {
    // print form header
    echo "<form id=\"$name\" method=\"post\" action=\"$action\">\n";
    echo "<div>\n<table>\n";
}

function form_end() {
    // print form footer
    echo "</table>\n</div>\n</form>\n";
}

function checkbox($name, $trailer='', $checked = 0) {
    $str = "<tr><td>&nbsp;</td><td><input type=\"checkbox\" name=\"$name\"";
    if ($checked)
        $str .= " checked=\"checked\"";
    $str .= " /> $trailer</td></tr>\n";
    echo $str;
}

function radio_gender($gender) {
    // radio buttons for gender ($gender in (0,1,2,9))
    global $_Gender, $_Unknown, $_Male, $_Female, $_NslashA;
    echo "<tr><td>$_Gender:</td><td>\n";
    // echo "<fieldset>\n";
    // echo "<legend>$_Gender</legend>\n";
    printf ("<input type=\"radio\" name=\"gender\" %s value=\"0\" />$_Unknown\n",
        ($gender == 0)?'checked="checked"':'');
    printf ("<input type=\"radio\" name=\"gender\" %s value=\"1\" />$_Male\n",
        ($gender == 1)?'checked="checked"':'');
    printf ("<input type=\"radio\" name=\"gender\" %s value=\"2\" />$_Female\n",
        ($gender == 2)?'checked="checked"':'');
    printf ("<input type=\"radio\" name=\"gender\" %s value=\"9\" />$_NslashA\n",
        ($gender == 9)?'checked="checked"':'');
    // echo "</fieldset>\n";
    echo "</td></tr>\n";
}

function date_input($fdate='', $sdate='') {
    // form row with structured date input
    global $_before, $_say, $_ca, $_exact, $_after, $_between, $_or, $_from_to,
        $_Date, $_Sort_date;
    $fd_type[0] = $_before;
    $fd_type[1] = $_say;
    $fd_type[2] = $_ca;
    $fd_type[3] = $_exact;
    $fd_type[4] = $_after;
    $fd_type[5] = $_between;
    $fd_type[6] = $_or;
    $fd_type[7] = $_from_to;
    echo "<tr><td>$_Date:</td><td>";
    $fdate ? $ftype = substr($fdate, 8, 1) : $ftype = 3;
    $fdate ? $fdate_1 = trim_date(substr($fdate, 0, 8)) : $fdate_1 = '';
    $fdate ? $fdate_2 = trim_date(substr($fdate, 9, 8)) : $fdate_2 = '';
    echo "<input type=\"text\" size=\"10\" name=\"date_1\" value=\"$fdate_1\" />\n";
    echo "<select name = \"date_type\">\n";
    for ($i = 0; $i < 8; $i++) {
        $option = "<option";
        if ($i == $ftype)
            $option .= " selected=\"selected\"";
        $option .= " value=\"$i\">";
        $option .= $fd_type[$i] . "</option>\n";
        echo $option;
    }
    echo "</select>\n";
    echo "<input type=\"text\" size=\"10\" name=\"date_2\" value=\"$fdate_2\" />";
    echo " $_Sort_date: ";
    echo "<input type=\"text\" size=\"20\" name=\"sort_date\" value=\"$sdate\" />";
    echo "</td></tr>\n";
}

function fix_path($str) {
    // doctoring the path from link_expand()
    return str_replace('./family','../family', $str);
}

?>
