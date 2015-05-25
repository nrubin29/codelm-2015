<?php
    define("canSubmit", true);

    if (!canSubmit) {
        echo("closed");
        return;
    }

    require_once("database.php");
    $problem = Problem::$all[strval($_POST["problem"])];

    $name = md5(rand());
    while (is_numeric(substr($name, 0, 1))) {
        $name = substr($name, 1);
    }

    $path = "submissions/$name.java";
    $file = fopen($path, "w");
    fwrite($file, str_replace("%name%", $name, str_replace("%code%", $_POST["code"], $problem->template)));
    fclose($file);

    $ignore = array();
    $ret = null;
    exec("cd submissions && javac $name.java", $ignore, $ret);
    if ($ret != null && $ret != 0) {
      echo("compilation");
      return;
    }
    
    $output = array();
    exec("cd submissions && java $name", $output, $ret);
    
    $result = "success";
    
    if ($ret != null && $ret != 0) {
        if ($ret == 1) {
            $result = "stacktrace";
        }
        
        else if ($ret == 2) {
            $result = "toolong";
        }
        
        else {
            $result = "return";
        }
    }
    
    if (sizeof($output) > sizeof($problem->correct)) {
        $result = "toomuch";
    }
    
    else if (sizeof($output) < sizeof($problem->correct)) {
        $result = "toofew";
    }

    session_start();
    $team = new Team($_SESSION["team"]);

    if ($result != "success") {
        echo($result);
        $team->log($problem->id, $name, $result);
        $team->change_points(-1);
        return;
    }
    
    $percent = 0;
    
    for ($i = 0; $i < sizeof($problem->correct); $i++) {
        if ($problem->correct[$i] == $output[$i]) {
          $percent += 1;
        }
    }
    
    $percent /= sizeof($problem->correct);
    $percent *= 100;

    $team->log($problem->id, $name, $percent);
    $team->change_points(($percent == 100) ? 2 : -1);
    
    echo($percent);
    
    /* This code will concatenate true or false to each given answer. This can be used for a more detailed response.
    $correct = true;

    for ($i = 0; $i < sizeof($problem->correct); $i++) {
        $c = $problem->correct[$i] == $output[$i];
        
        if (!$c) {
            $correct = false;
        }
      
        $output[$i] .= ":" . ($c ? "true" : "false");
    }
    
    session_start();
    $team = new Team($_SESSION["team"]);
    
    if ($correct) {
        $team->set_solved($problem->id, $name);
        $team->change_points(2);
        echo(json_encode($output));
    }
    
    else {
      $team->change_points(-1);
      echo($result);
    }
    */
?>