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
            get_mysql()->query("update teams set score = score + $i where id = " . $this->id);
            $this->score += $i;
        }
        
        function log($problem, $uuid, $result) {
            $output = "$problem:" . date("h-i-s") . ":$uuid:$result;";
            $this->log .= $output;
            get_mysql()->query("update teams set log = '" . $this->log . "' where id = " . $this->id);
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

        public static function register($data) {
            get_mysql()->query("insert into teams (division, password, name, members, log) values (" . $data["division"] . ", '" . $data["password"] . "', '" . $data["name"] . "', '" . $data["members"] . "', '')");
            return Team::login($data["password"]);
        }
        
        static function get_teams($division) {
            $teams = array();
            
            foreach (get_mysql()->query("select id from teams where division = $division order by score DESC") as $row) {
                array_push($teams, new Team($row["id"]));
            }
            
            return $teams;
        }
    }
    
    class Problem {
        var $id, $num, $divisions, $name, $question, $sample, $stub, $template, $correct;

        function __construct($id, $divisions, $name, $question, $sample, $stub, $template, $correct) {
            $this->id = $id;
            $this->divisions = $divisions;
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
                        /* divisions */ array(1),
                        /* name */      "AB Rectangle",
                        /* question */  "Given a width and height, print out a square that has a border of <code>'a'</code>s and is filled with <code>'b'</code>s. You will print the rectangle line by line and not return anything.",
                        /* sample */    array("<code>1</code>, <code>1</code>" => "a", "<code>2</code>, <code>2</code>" => "aa<br>aa", "<code>3</code>, <code>3</code>" => "aaa<br>aba<br>aaa", "<code>4</code>, <code>5</code>" => "aaaa<br>abba<br>abba<br>abba<br>aaaa"),
                        /* stub */      "public static void rect(int width, int height) {\n\t\n}",
                        /* template */  'import java.util.concurrent.ExecutionException;import java.util.concurrent.ExecutorService;import java.util.concurrent.Executors;import java.util.concurrent.Future;import java.util.concurrent.TimeUnit;import java.util.concurrent.TimeoutException;public class %name%{public static void main(String[]args){ExecutorService executor=Executors.newFixedThreadPool(4);Future<?>future=executor.submit(new Runnable(){@Override public void run(){rect(1,1);rect(2,2);rect(3,3);rect(4,5);rect(7,9);rect(10,10);}});executor.shutdown();try{future.get(10,TimeUnit.SECONDS);}catch(TimeoutException e){future.cancel(true);System.exit(2);}catch(ExecutionException e){future.cancel(true);System.exit(1);}catch(Exception e){future.cancel(true);System.exit(3);}}%code%}',
                        /* correct */   array("a", "aa<br>aa", "aaa<br>aba<br>aaa", "aaaa<br>abba<br>abba<br>abba<br>aaaa", "aaaaaaa<br>abbbbba<br>abbbbba<br>abbbbba<br>abbbbba<br>abbbbba<br>abbbbba<br>abbbbba<br>aaaaaaa", "aaaaaaaaaa<br>abbbbbbbba<br>abbbbbbbba<br>abbbbbbbba<br>abbbbbbbba<br>abbbbbbbba<br>abbbbbbbba<br>abbbbbbbba<br>abbbbbbbba<br>aaaaaaaaaa")
                    ),
                    "2" => new Problem(
                        /* id */        2,
                        /* divisions */ array(1),
                        /* name */      "Combination Lock",
                        /* question */  "You are attempting to crack a safe with a simple 6-digit combination lock. This combination lock has multiple answers according to the following pattern:<br><br><ul><li>The first and fourth values must be both even or both odd.</li><li>The fifth and sixth values must add to the third value.</li><li>The second value must be at most 2 away from the third value.</li></ul><br>Given a 6-digit guess, you must determine whether it matches the above pattern or not. Note that you can use %10 to get the far right number and /10 to remove the far right number.<br><br>",
                        /* sample */    array("<code>479236</code>" => "<code>true</code>", "<code>197216</code>" => "<code>false</code>", "<code>357524</code>" => "<code>false</code>", "<code>639854</code>" => "<code>false</code>"),
                        /* stub */      "public static boolean crack(int guess) {\n\t\n}",
                        /* template */  'import java.util.concurrent.ExecutionException;import java.util.concurrent.ExecutorService;import java.util.concurrent.Executors;import java.util.concurrent.Future;import java.util.concurrent.TimeUnit;import java.util.concurrent.TimeoutException;public class %name%{public static void main(String[]args){ExecutorService executor=Executors.newFixedThreadPool(4);Future<?>future=executor.submit(new Runnable(){@Override public void run(){System.out.println(crack(479236));System.out.println(crack(197216));System.out.println(crack(357524));System.out.println(crack(639854));System.out.println(crack(176942));System.out.println(crack(820324));System.out.println(crack(697852));System.out.println(crack(920385));}});executor.shutdown();try{future.get(10,TimeUnit.SECONDS);}catch(TimeoutException e){future.cancel(true);System.exit(2);}catch(ExecutionException e){future.cancel(true);System.exit(1);}catch(Exception e){future.cancel(true);System.exit(3);}}%code%}',
                        /* correct */   array("true", "false", "false", "false", "true", "false", "true", "false")
                    ),
                    "3" => new Problem( // TODO: Template, correct.
                        /* id */        3,
                        /* divisions */ array(1),
                        /* name */      "Tic-Tac-Toe Winner",
                        /* question */  "Given a two-dimensional array of characters representing a valid tic-tac-toe board, you must determine which player, if either won. You will return the character of the winning player (<code>'x'</code> or <code>'o'</code>), <code>'t'</code> if the game is a tie, or <code>' '</code> if the game is not yet over. There will never be a situation in which both players have won.",
                        /* sample */    array("<code>{</br>&nbsp;&nbsp;{ ‘x’, ‘o’, ‘x’ },<br>&nbsp;&nbsp;{ ‘o’, ‘x’, ‘o’ },<br>&nbsp;&nbsp;{ ‘x’, ‘o’, ‘o’ }<br>}</code>" => "<code>true</code>", "<code>197216</code>" => "<code>false</code>", "<code>357524</code>" => "<code>false</code>", "<code>639854</code>" => "<code>false</code>"),
                        /* stub */      "public static char winner(char[][] board) {\n\t\n}",
                        /* template */  'import java.util.concurrent.ExecutionException;import java.util.concurrent.ExecutorService;import java.util.concurrent.Executors;import java.util.concurrent.Future;import java.util.concurrent.TimeUnit;import java.util.concurrent.TimeoutException;public class %name%{public static void main(String[]args){ExecutorService executor=Executors.newFixedThreadPool(4);Future<?>future=executor.submit(new Runnable(){@Override public void run(){System.out.println(crack(479236));System.out.println(crack(197216));System.out.println(crack(357524));System.out.println(crack(639854));System.out.println(crack(176942));System.out.println(crack(820324));System.out.println(crack(697852));System.out.println(crack(920385));}});executor.shutdown();try{future.get(10,TimeUnit.SECONDS);}catch(TimeoutException e){future.cancel(true);System.exit(2);}catch(ExecutionException e){future.cancel(true);System.exit(1);}catch(Exception e){future.cancel(true);System.exit(3);}}%code%}',
                        /* correct */   array("x", "o", "t", " ")
                    ),
                    "16" => new Problem(
                        /* id */        16,
                        /* divisions */ array(0),
                        /* name */      "Even or odd?",
                        /* question */  "Given parameter <code>i</code> of type <code>int</code>, return <code>true</code> if the number is even and <code>false</code> if it is odd.",
                        /* sample */    array("0" => "<code>true</code>", "1" => "<code>false</code>", "2" => "<code>true</code>"),
                        /* stub */      "public static boolean isEven(int i) {\n\t\n}",
                        /* template */  'import java.util.concurrent.ExecutionException;import java.util.concurrent.ExecutorService;import java.util.concurrent.Executors;import java.util.concurrent.Future;import java.util.concurrent.TimeUnit;import java.util.concurrent.TimeoutException;public class %name%{public static void main(String[]args){ExecutorService executor=Executors.newFixedThreadPool(4);Future<?>future=executor.submit(new Runnable(){@Override public void run(){System.out.println(isEven(0));System.out.println(isEven(1));System.out.println(isEven(2));System.out.println(isEven(999));System.out.println(isEven(1000));}});executor.shutdown();try{future.get(10,TimeUnit.SECONDS);}catch(TimeoutException e){future.cancel(true);System.exit(2);}catch(ExecutionException e){future.cancel(true);System.exit(1);}catch(Exception e){future.cancel(true);System.exit(3);}}%code%}',
                        /* correct */   array("true", "false", "true", "false", "true")
                    ),
                    "17" => new Problem(
                        /* id */        17,
                        /* divisions */ array(0),
                        /* name */      "How do you do?",
                        /* question */  "Given parameter <code>name</code> of type <code>String</code>, return a nice greeting in the format <i>Hello, {name}!</i>",
                        /* sample */    array("Noah" => "Hello, Noah!", "Dog" => "Hello, Dog!"),
                        /* stub */      "public static String greet(String name) {\n\t\n}",
                        /* template */  'import java.util.concurrent.ExecutionException;import java.util.concurrent.ExecutorService;import java.util.concurrent.Executors;import java.util.concurrent.Future;import java.util.concurrent.TimeUnit;import java.util.concurrent.TimeoutException;public class %name%{public static void main(String[]args){ExecutorService executor=Executors.newFixedThreadPool(4);Future<?>future=executor.submit(new Runnable(){@Override public void run(){System.out.println(greet("Noah"));System.out.println(greet("Dog"));System.out.println(greet("1234"));System.out.println(greet("%test%"));}});executor.shutdown();try{future.get(10,TimeUnit.SECONDS);}catch(TimeoutException e){future.cancel(true);System.exit(2);}catch(ExecutionException e){future.cancel(true);System.exit(1);}catch(Exception e){future.cancel(true);System.exit(3);}}%code%}',
                        /* correct */   array("Hello, Noah!", "Hello, Dog!", "Hello, 1234!", "Hello, %test%!")
                    ),
                );
            }
        }
        
        static function all_formatted($team) {
            $array = array();
            
            foreach (Problem::$all as $id => $problem) {
                if (in_array($team->division, $problem->divisions)) {
                    $array[$id] = array(
                        "id" => $problem->id,
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