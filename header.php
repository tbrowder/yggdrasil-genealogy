<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:TEI="http://www.tei-c.org/ns/1.0" xml:lang="en" lang="en">
<!--
/***************************************************************************
 *   header.php                                                            *
 *   Yggdrasil: Common page header and sidebar menu                        *
 *                                                                         *
 *   Copyright (C) 2006-2011 by Leif B. Kristensen <leif@solumslekt.org>   *
 *   All rights reserved. For terms of use, see LICENSE.txt                *
 ***************************************************************************/
-->
<head>
<?php echo ("<title>$title</title>\n"); ?>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
<meta name="Author" content="Leif Biberg Kristensen" />
<link rel="stylesheet" type="text/css" href="default.css" />
<link rel="shortcut icon" href="http://localhost/~leif/yggdrasil/favicon.ico" />

<script type="text/javascript" src="scripts.js"></script>
</head>
<?php
require_once "./langs/$language.php";
echo "<body lang=\"$_lang\"";
if (isset($form) && isset($focus)) // place cursor
    echo " onload=\"document.forms['$form'].$focus.focus()\"";
echo ">\n";

// start timer
function getmicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

if (!isset($static))
    $time_start = getmicrotime();

function menu_item($url, $label, $title="") {
    $str = "<tr><td class=\"nav\"><a href=\"$url\"";
    if ($title)
        $str .= " title=\"$title\"";
    $str .= ">$label</a></td></tr>\n";
    return $str;
}

echo "<!-- Navigation Sidebar -->\n";
echo "<table class=\"nav\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

// common items
echo menu_item("./index.php", $_Index);
echo menu_item("./couples.php", $_Search_for_couples);
echo menu_item("./source_search.php", $_Search_for_sources);
echo menu_item("./source_manager.php", $_Sources);
echo menu_item("./place_manager.php", $_Places);
echo menu_item("./tag_manager.php", $_Event_types);
echo menu_item("./forms/user_settings.php", $_User_settings);
echo menu_item("./forms/person_insert.php", $_Add_person);

// source manager items
if ($source_manager) {
    // show/hide links for deletion
    if (fetch_val("SELECT show_delete FROM user_settings WHERE username = current_user") == 'f') {
        $show_delete = false;
        echo menu_item("./forms/source_toggle_sd.php?node=$self", "$_Show $_deletion_links");
    }
    else {
        $show_delete = true;
        echo menu_item("./forms/source_toggle_sd.php?node=$self", "$_Hide $_deletion_links");
    }
    echo menu_item("./spt_manager.php", $_Source_types);
    echo menu_item($_SERVER['PHP_SELF'] . "?node=" . $props['parent_id'], $_Up);
    echo menu_item($_SERVER['PHP_SELF'] . "?node=" . $props['prev_page'], "&lt; $_Previous", $title_prev);
    echo menu_item($_SERVER['PHP_SELF'] . "?node=" . $props['next_page'], "$_Next &gt;", $title_next);
}

// if $person is set, show family, pedigree, descendants
if ($person) {
    if (!$family)
        echo menu_item("./family.php?person=$person", $_Family);
    if (!$pedigree)
        echo menu_item("./pedigree.php?person=$person", $_Pedigree);
    if (!$descendants)
        echo menu_item("./descendants.php?person=$person", $_Descendants);
}

// this section will show up only in the family view
if ($family) {
    echo menu_item("./forms/event_insert.php?person=$person", $_Add_event);
    echo menu_item("./forms/person_insert.php?person=$person&amp;addspouse=true", $_Add_spouse);
    if ($gender == 1)
        $par = 'father';
    if ($gender == 2)
        $par = 'mother';
    if (isset($par)) {
        echo menu_item("./forms/person_insert.php?$par=$person", $_Add_child);
    }
    echo menu_item("./forms/person_merge.php?person=$person", "$_Merge ...");

    // "< Previous"
    $query = ("SELECT MAX(person_id) FROM persons WHERE person_id < $person");
    $prev_person = fetch_val($query);
    if (!$prev_person) {    // wrap around
        $query = ("SELECT MAX(person_id) FROM persons");
    }
    $prev_person = fetch_val($query);
    echo menu_item("./family.php?person=$prev_person", "&lt; $_Previous", get_name_and_lifespan($prev_person));
    // "Next >"
    $query = ("SELECT MIN(person_id) FROM persons WHERE person_id > $person");
    $prev_person = fetch_val($query);
    if (!$prev_person) {    // wrap around
        $query = ("SELECT MIN(person_id) FROM persons");
    }
    $next_person = fetch_val($query);
    echo menu_item("./family.php?person=$next_person", "$_Next &gt;", get_name_and_lifespan($next_person));
    // Toggle is_public
    if (fetch_val("SELECT is_public($person)") == 'f')
        echo menu_item("./forms/person_toggle_pf.php?person=$person", "$_Mark_as $_Public");
    else
        echo menu_item("./forms/person_toggle_pf.php?person=$person", "$_Mark_as $_Private");
    // Toggle dead_child
    if (fetch_val("SELECT dead_child($person)") == 'f')
        echo menu_item("./forms/person_toggle_dcf.php?person=$person", "$_Died_young");
    else
        echo menu_item("./forms/person_toggle_dcf.php?person=$person", "$_Not_died_young");
}
echo "</table>\n";
echo "<!-- End of Navigation Sidebar -->\n";
?>
