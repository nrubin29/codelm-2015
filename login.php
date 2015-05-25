<?php
    require_once("database.php");
    session_start();
    
    if (Team::exists($_POST["team"])) {
        $_SESSION["team"] = $_POST["team"];
        echo("true");
    }
    
    else {
        echo("false");
    }
?>