<?php


function printStyle($message, $icon=NULL) {
    switch (strtolower($icon)) {
        case "ok":
            $preImg = "<img style='vertical-align:middle' src='images/task-complete.png' />";
        break;
        case "error":
            $preImg = "<img style='vertical-align:middle' src='images/task-reject.png' />";
        break;
        case "notice":
            $preImg = "<img style='vertical-align:middle' src='images/task-attempt.png' />";
        break;
        case NULL:
        default:
            $preImg = "";
    }

    echo "<div>$preImg<span style=''>$message</span></div>";
}


?>
