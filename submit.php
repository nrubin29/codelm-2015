<?php
    define("canSubmit", true);

    if (!canSubmit) {
        echo("closed");
        return;
    }

    require_once("database.php");
    session_start();
    $problem = Problem::$all[strval($_POST["problem"])];
    $team = new Team($_SESSION["team"]);

    $name = md5(rand());
    while (is_numeric(substr($name, 0, 1))) {
        $name = substr($name, 1);
    }

    $path = "submissions/$name.java";
    $file = fopen($path, "w"); // If there's an error, mkdir submissions and chmod it to 077.
    fwrite($file, str_replace("%special%", $problem->special, str_replace("%tests%", $problem->tests, str_replace("%name%", $name, str_replace("%code%", $_POST["code"], "import java.util.concurrent.ExecutionException;import java.util.concurrent.ExecutorService;import java.util.concurrent.Executors;import java.util.concurrent.Future;import java.util.concurrent.TimeUnit;import java.util.concurrent.TimeoutException;import java.util.Arrays;import java.util.ArrayList;import java.util.List;public class %name%{%special%public static void main(String[]args){ExecutorService executor=Executors.newFixedThreadPool(4);Future<?>future=executor.submit(new Runnable(){@Override public void run(){%tests%}});executor.shutdown();try{future.get(10,TimeUnit.SECONDS);}catch(TimeoutException e){future.cancel(true);System.exit(2);}catch(ExecutionException e){future.cancel(true);System.exit(1);}catch(Exception e){future.cancel(true);System.exit(3);}}%code%}")))));
    fclose($file);

    $ignore = array();
    $ret = null;
    exec("cd submissions && javac $name.java", $ignore, $ret);
    if ($ret != null && $ret != 0) {
      echo("compilation");
      $team->log($problem->id, $name, "compilation");
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
    $percent = round($percent, 2);

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