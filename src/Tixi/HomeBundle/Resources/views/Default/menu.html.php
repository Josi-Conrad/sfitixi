<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 13.08.13
 * Time: 10:00
 **/
// src/Tixi/HomeBundle/Resources/views/Default/menu.html.php

// prefix
echo '<div id="menu-bar">';
echo '    <ul class="nav">';

// page variables
$myurl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
$items = count($menuitems);

//loop
foreach ($menuitems as $key => $menuitem):
 // calculate fully qualified URL and ID
    $route = $myurl.$menuitem["URL"];
    $id = 'fpm'.str_replace('/', '_', $menuitem["URL"] );

 // calculate level changes: down, same, up
    $thislevel = substr_count($menuitem["URL"],'/');
    if (($key + 1) >= $items) { $nextlevel = 1;
    } else { $nextlevel = substr_count($menuitems[$key+1]["URL"],'/');
    }

 // build list elements
    if ($nextlevel == $thislevel) {
        echo '<li><a href="'.$route.'" id="'.$id.'">'.$menuitem["CAPTION"].'</a></li>'; // same
    } elseif ($nextlevel > $thislevel) {
        echo '<li class="dropdown"><a href="'.$route.'" id="'.$id.'">'.$menuitem["CAPTION"].'</a>'; // down
        echo '<ul>'; // down
    } elseif ($nextlevel < $thislevel) {
        echo '<li><a href="'.$route.'" id="'.$id.'">'.$menuitem["CAPTION"].'</a></li>'; // up
        for ($i = 1; $i <= ($thislevel - $nextlevel); $i++) {
            echo '</ul></li>'; // up
        };
    }

endforeach;

// postfix
echo '  </ul></div>';
