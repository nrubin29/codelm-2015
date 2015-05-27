<?php
    require_once("database.php");
    session_start();

    $team = Team::login($_POST["password"]);

    if ($team != -1) {
        $_SESSION["team"] = $team;
    }

    echo($team);
?>