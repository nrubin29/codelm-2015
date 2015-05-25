<?php
    class Team {
        var $id, $division, $divisionString, $name, $members, $log, $score;
        
        function __construct($id) {
            $data = get_mysql()->query("select * from teams where id = $id")->fetch_assoc();
            $this->id = $data["id"];
            $this->division = $data["division"];
            $this->divisionString = $this->division == 0 ? "Demo" : ($this->division == 1 ? "Intermediate" : ($this->division == 2 ? "Advanced" : "null"));
            $this->name = $data["name"];
            $this->members = $data["members"];
            $this->log = $data["log"];
            $this->score = $data["score"];
        }

        function change_points($i) {
            get_mysql()->query("update teams set score = score + $i");
            $this->score += $i;
        }
        
        function log($problem, $uuid, $result) {
            $output = "$problem:" . date("h-i-s") . ":$uuid:$result;";
            $this->log .= $output;
            get_mysql()->query("update teams set log = '" . $this->log . "'");
        }
        
        function problems_solved() {
            $s = explode(";", $this->log);
            $result = array();

            $index = 0;
            for ($i = 0; $i < sizeof($s) - 1; $i++) {
                if (explode(":", $s[$i])[3] == "100") {
                    $result[$index++] = substr($s[$i], 0, strpos($s[$i], ":"));
                }
            }

            return $result;
        }

        function is_solved($problem) {
            return in_array($problem, $this->problems_solved());
        }
        
        function problems_total() {
            return sizeof(Problem::all_formatted($this));
        }

        public static function login($password) {
            $result = get_mysql()->query("select id from teams where password = '$password'");

            if ($result->num_rows > 0) {
                return $result->fetch_assoc()["id"];
            }

            else {
                return -1;
            }
        }
        
        static function get_teams($division) {
            $teams = array();
            
            foreach (get_mysql()->query("select id from teams where division = $division order by score") as $row) {
                array_push($teams, new Team($row["id"]));
            }
            
            return $teams;
        }
    }
    
    class Problem {
        var $id, $num, $division, $name, $question, $sample, $stub, $template, $correct;

        function __construct($id, $num, $division, $name, $question, $sample, $stub, $template, $correct) {
            $this->id = $id;
            $this->num = $num;
            $this->division = $division;
            $this->name = $name;
            $this->question = $question;
            $this->sample = $sample;
            $this->stub = $stub;
            $this->template = $template;
            $this->correct = $correct;
        }

        public static $all;
        
        static function setup() {
            if (!isset(self::$all)) {
                self::$all = array(
                    "1" => new Problem(
                        /* id */        1,
                        /* num */       1,
                        /* division */  0,
                        /* name */      "How do you do?",
                        /* question */  "Given parameter <code>name</code> of type <code>String</code>, return a nice greeting in the format <i>Hello, {name}!</i>",
                        /* sample */    array("Noah" => "Hello, Noah!", "Dog" => "Hello, Dog!"),
                        /* stub */      "public static String greet(String name) {
    
}",
                        /* template */  'import java.util.concurrent.ExecutionException;import java.util.concurrent.ExecutorService;import java.util.concurrent.Executors;import java.util.concurrent.Future;import java.util.concurrent.TimeUnit;import java.util.concurrent.TimeoutException;public class %name%{public static void main(String[]args){ExecutorService executor=Executors.newFixedThreadPool(4);Future<?>future=executor.submit(new Runnable(){@Override public void run(){System.out.println(greet("Noah"));System.out.println(greet("Dog"));System.out.println(greet("1234"));System.out.println(greet("%test%"));}});executor.shutdown();try{future.get(10,TimeUnit.SECONDS);}catch(TimeoutException e){future.cancel(true);System.exit(2);}catch(ExecutionException e){future.cancel(true);System.exit(1);}catch(Exception e){future.cancel(true);System.exit(3);}}%code%}',
                        /* correct */   array("Hello, Noah!", "Hello, Dog!", "Hello, 1234!", "Hello, %test%!")
                    ),
                    "2" => new Problem(
                        /* id */        2,
                        /* num */       2,
                        /* division */  0,
                        /* name */      "Even or odd?",
                        /* question */  "Given parameter <code>i</code> of type <code>int</code>, return <code>true</code> if the number is even and <code>false</code> if it is odd.",
                        /* sample */    array("0" => "<code>true</code>", "1" => "<code>false</code>", "2" => "<code>true</code>"),
                        /* stub */      "public static boolean isEven(int i) {
    
}",
                        /* template */  'import java.util.concurrent.ExecutionException;import java.util.concurrent.ExecutorService;import java.util.concurrent.Executors;import java.util.concurrent.Future;import java.util.concurrent.TimeUnit;import java.util.concurrent.TimeoutException;public class %name%{public static void main(String[]args){ExecutorService executor=Executors.newFixedThreadPool(4);Future<?>future=executor.submit(new Runnable(){@Override public void run(){System.out.println(isEven(0));System.out.println(isEven(1));System.out.println(isEven(2));System.out.println(isEven(999));System.out.println(isEven(1000));}});executor.shutdown();try{future.get(10,TimeUnit.SECONDS);}catch(TimeoutException e){future.cancel(true);System.exit(2);}catch(ExecutionException e){future.cancel(true);System.exit(1);}catch(Exception e){future.cancel(true);System.exit(3);}}%code%}',
                        /* correct */   array("true", "false", "true", "false", "true")
                    )
                );
            }
        }
        
        static function all_formatted($team) {
            $array = array();
            
            foreach (Problem::$all as $id => $problem) {
                if ($problem->division == $team->division) {
                    $array[$id] = array(
                        "id" => $problem->id,
                        "num" => $problem->num,
                        "name" => $problem->name,
                        "question" => $problem->question,
                        "sample" => $problem->sample,
                        "stub" => $problem->stub,
                        "solved" => $team->is_solved($problem->id)
                    );
                }
            }
            
            return $array;
        }
    }
    
    function get_mysql() {
        $conn = new mysqli("localhost", "user", trim(file_get_contents("../../../codelm.txt")), "codelm");

        if ($conn->connect_error) {
            die("Connection error: " . $conn->connect_error);
        }

        return $conn;
    }
    
    Problem::setup();
?>