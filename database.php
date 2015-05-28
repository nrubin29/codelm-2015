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
        var $id, $num, $divisions, $name, $question, $sample, $stub, $tests, $correct, $special;

        function __construct($id, $divisions, $name, $question, $sample, $stub, $tests, $correct, $special = "") {
            $this->id = $id;
            $this->divisions = $divisions;
            $this->name = $name;
            $this->question = $question;
            $this->sample = $sample;
            $this->stub = $stub;
            $this->tests = $tests;
            $this->correct = $correct;
            $this->special = $special;
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
                        /* tests */     'rect(1, 1); rect(2, 2); rect(3, 3); rect(4, 5); rect(7, 9); rect(10, 10);',
                        /* correct */   array("a", /**/ "aa", "aa", /**/ "aaa", "aba", "aaa", /**/ "aaaa", "abba", "abba", "abba", "aaaa", /**/ "aaaaaaa", "abbbbba", "abbbbba", "abbbbba", "abbbbba", "abbbbba", "abbbbba", "abbbbba", "aaaaaaa", /**/ "aaaaaaaaaa", "abbbbbbbba", "abbbbbbbba", "abbbbbbbba", "abbbbbbbba", "abbbbbbbba", "abbbbbbbba", "abbbbbbbba", "abbbbbbbba", "aaaaaaaaaa")
                    ),
                    "2" => new Problem(
                        /* id */        2,
                        /* divisions */ array(1),
                        /* name */      "Combination Lock",
                        /* question */  "You are attempting to crack a safe with a simple 6-digit combination lock. This combination lock has multiple answers according to the following pattern:<br><br><ul><li>The first and fourth values must be both even or both odd.</li><li>The fifth and sixth values must add to the third value.</li><li>The second value must be at most 2 away from the third value.</li></ul><br>Given a 6-digit guess, you must determine whether it matches the above pattern or not. Note that you can use %10 to get the far right number and /10 to remove the far right number.",
                        /* sample */    array("<code>479236</code>" => "<code>true</code>", "<code>197216</code>" => "<code>false</code>", "<code>357524</code>" => "<code>false</code>", "<code>639854</code>" => "<code>false</code>"),
                        /* stub */      "public static boolean crack(int guess) {\n\t\n}",
                        /* tests */     'System.out.println(crack(479236)); System.out.println(crack(197216)); System.out.println(crack(357524)); System.out.println(crack(639854)); System.out.println(crack(176942)); System.out.println(crack(820324)); System.out.println(crack(697852)); System.out.println(crack(920385));',
                        /* correct */   array("true", "false", "false", "false", "true", "false", "true", "false")
                    ),
                    "3" => new Problem(
                        /* id */        3,
                        /* divisions */ array(1),
                        /* name */      "Tic-Tac-Toe Winner",
                        /* question */  "Given a two-dimensional array of characters representing a valid tic-tac-toe board, you must determine which player, if either won. You will return the character of the winning player (<code>'x'</code> or <code>'o'</code>), <code>'t'</code> if the game is a tie, or <code>'g'</code> if the game is not yet over. There will never be a situation in which both players have won.",
                        /* sample */    array("<code>{</br>&nbsp;&nbsp;{ 'x', 'o', 'x' },<br>&nbsp;&nbsp;{ 'o', 'x', 'o' },<br>&nbsp;&nbsp;{ 'x', 'o', 'x' }<br>}</code>" => "x", "<code>{</br>&nbsp;&nbsp;{ 'x', ' ', 'o' },<br>&nbsp;&nbsp;{ ' ', ' ', 'o' },<br>&nbsp;&nbsp;{ 'x', 'x', 'o' }<br>}</code>" => "o", "<code>{</br>&nbsp;&nbsp;{ 'x', 'o', 'x' },<br>&nbsp;&nbsp;{ 'o', 'x', 'o' },<br>&nbsp;&nbsp;{ 'o', 'x', 'o' }<br>}</code>" => "t", "<code>{</br>&nbsp;&nbsp;{ ' ', 'o', ' ' },<br>&nbsp;&nbsp;{ ' ', 'x', ' ' },<br>&nbsp;&nbsp;{ 'o', 'x', ' ' }<br>}</code>" =>  "g"),
                        /* stub */      "public static char winner(char[][] board) {\n\t\n}",
                        /* tests */     "System.out.println(winner(new char[][] { new char[] { 'x', 'x', 'x' }, new char[] { 'o', 'x', 'o' }, new char[] { 'x', 'o', 'o' } })); System.out.println(winner(new char[][] { new char[] { 'o', 'x', 'o' }, new char[] { 'o', 'x', 'o' }, new char[] { 'x', 'o', 'o' } })); System.out.println(winner(new char[][] { new char[] { 'o', 'o', 'x' }, new char[] { 'o', 'x', 'o' }, new char[] { 'x', 'o', 'x' } })); System.out.println(winner(new char[][] { new char[] { 'o', ' ', ' ' }, new char[] { ' ', 'x', 'o' }, new char[] { ' ', ' ', 'x' } }));",
                        /* correct */   array("x", "o", "t", "g")
                    ),
                    "4" => new Problem(
                        /* id */        4,
                        /* divisions */ array(1),
                        /* name */      "Magic Square",
                        /* question */  "A magic square is a grid of numbers where all verticals, horizontals, and diagonals add to be the same number. The following is a 3x3 square where all lines sum to 15:<br><br>6 1 8<br>7 5 3<br>2 9 4<br><br>Given a two-dimension array of integers, return the number to which each line sums, or -1 if the input does not represent a magic square.",
                        /* sample */    array("<code>{</br>&nbsp;&nbsp;{ 6, 1, 8 },<br>&nbsp;&nbsp;{ 7, 5, 3 },<br>&nbsp;&nbsp;{ 2, 9, 4 }<br>}</code>" => "<code>15</code>", "<code>{</br>&nbsp;&nbsp;{ 6 }<br>}</code>" => "<code>6</code>", "<code>{</br>&nbsp;&nbsp;{ 1, 4 },<br>&nbsp;&nbsp;{ 2, 5 }<br>}</code>" => "<code>-1</code>"),
                        /* stub */      "public static int magicNumber(int[][] puzzle) {\n\t\n}",
                        /* tests */     "System.out.println(magicNumber(new int[][] { new int[] { 6, 1, 8 }, new int[] { 7, 5, 3 }, new int[] { 2, 9, 4 } })); System.out.println(magicNumber(new int[][] { new int[] { 6 } })); System.out.println(magicNumber(new int[][] { new int[] { 1, 4 }, new int[] { 2, 5 } })); System.out.println(magicNumber(new int[][] { new int[] { 7, 12, 1, 14 }, new int[] { 2, 13, 8, 11 }, new int[] { 16, 3, 10, 5 }, new int[] { 9, 6, 15, 4 } })); System.out.println(magicNumber(new int[][] { new int[] { 11, 24, 7, 20, 3 }, new int[] { 4, 12, 25, 8, 16 }, new int[] { 17, 5, 13, 21, 9 }, new int[] { 10, 18, 1, 14, 22 }, new int[] { 23, 6, 19, 2, 15 } }));",
                        /* correct */   array("15", "6", "-1", "34", "65")
                    ),
                    "5" => new Problem(
                        /* id */        5,
                        /* divisions */ array(1),
                        /* name */      "Odd Element Out",
                        /* question */  "Given an array of integers, return a new array with all elements to the immediate right of odd numbers removed. Each odd number will only remove one other number. Note that removed odd numbers still count.",
                        /* sample */    array("<code>{ 2, 4, 6 }</code>" => "<code>{ 2, 4, 6 }</code>", "<code>{ 2, 3, 4 }</code>" => "<code>{ 2, 3 }</code>", "<code>{ 3, 5, 2 }</code>" => "<code>{ 3 }</code>"),
                        /* stub */      "public static int[] oddOut(int[] array) {\n\t\n}",
                        /* tests */     "System.out.println(Arrays.toString(oddOut(new int[] { 2, 4, 6 }))); System.out.println(Arrays.toString(oddOut(new int[] { 2, 3, 4 }))); System.out.println(Arrays.toString(oddOut(new int[] { 3, 5, 2 }))); System.out.println(Arrays.toString(oddOut(new int[] { 4, 2, 5, 6 }))); System.out.println(Arrays.toString(oddOut(new int[] { 3, 5, 2, 4 }))); System.out.println(Arrays.toString(oddOut(new int[] { 5, 2, 5, 7, 2 })));",
                        /* correct */   array("[2, 4, 6]", "[2, 4]", "[3]", "[4, 2, 5]", "[3, 4]", "[5, 5]")
                    ),
                    "6" => new Problem(
                        /* id */        6,
                        /* divisions */ array(1, 2),
                        /* name */      "Roman Numerals",
                        /* question */  'Given a String that contains one number in roman numeral form, return that number as an int. You may assume that all numbers are valid. Here is a chart of roman numerals:<table class="table"><thead><tr><th>Roman</th><th>Decimal</th></thead><tbody><tr><td>I</td><td>1</td></tr><tr><td>V</td><td>5</td></tr><tr><td>X</td><td>10</td></tr><tr><td>L</td><td>50</td></tr><tr><td>C</td><td>100</td></tr><td>D</td><td>500</td></tr><tr><td>M</td><td>1,000</td></tbody></table>',
                        /* sample */    array("I" => "<code>1</code>", "IV" => "<code>4</code>", "XXIV" => "<code>26</code>", "DCXV" => "<code>615</code>"),
                        /* stub */      "public static int toInt(String roman) {\n\t\n}",
                        /* tests */     'System.out.println(toInt("I")); System.out.println(toInt("IV")); System.out.println(toInt("XXIV")); System.out.println(toInt("DCXV")); System.out.println(toInt("V")); System.out.println(toInt("XXVIII")); System.out.println(toInt("MMXV"));',
                        /* correct */   array("1", "4", "26", "615", "5", "28", "2015")
                    ),
                    "7" => new Problem(
                        /* id */        7,
                        /* divisions */ array(1, 2),
                        /* name */      "Time Formatter",
                        /* question */  'Given a time in seconds, print a formatted time in hours, minutes, and seconds in the following format:<br><br># hour(s) # minute(s) # second(s)<br><br>Where each # is replaced by the number of that unit. If a number is 0 (for example, less than one hour), omit that unit. If the number is 1, ensure that the unit is singular and not plural. The number 185 is \"3 minutes 5 seconds\" because there are 0 hours, 3 minutes, and 5 seconds.',
                        /* sample */    array("<code>90</code>" => "1 minute 30 seconds", "<code>3600</code>" => "1 hour", "<code>121</code>" => "2 minutes 1 second", "<code>3662</code>" => "1 hour 1 minute 2 seconds"),
                        /* stub */      "public static String formatTime(int time) {\n\t\n}",
                        /* tests */     "System.out.println(formatTime(90)); System.out.println(formatTime(3600)); System.out.println(formatTime(121)); System.out.println(formatTime(3662)); System.out.println(formatTime(108183)); System.out.println(formatTime(120)); System.out.println(formatTime(1));",
                        /* correct */   array("1 minute 30 seconds", "1 hour", "2 minutes 1 second", "1 hour 1 minute 2 seconds", "3 hours 3 minutes 3 seconds", "2 minutes", "1 second")
                    ),
                    "8" => new Problem(
                        /* id */        8,
                        /* divisions */ array(1, 2),
                        /* name */      "Special Substitution Cypher",
                        /* question */  "A substitution cypher is an easy way to encrypt information. Every letter is replaced with the letter after it (a is replaced with b, b with c, etc.), and z is replaced with a. This cypher isn’t terribly secure, so we will make a few modifications to it.<br><br>Given a message and an operation (either <code>'e'</code> for encrypt or <code>'d'</code> for decryption), you must encrypt or decrypt the given message. The steps below will encrypt the message. Following them in reverse will decrypt the message.<br><br><ol><li>Reverse the string.</li><li>Remove all vowels from the string. They do not need to be added back in the decryption phase.</li><li>Replace all instances of a space with an underscore (_).</li><li>Apply the substitution cypher as specified above.</li></ol>",
                        /* sample */    array("hello world, <code>'e'</code>" => "emsx_mmi", "emsx_mmi, <code>'d'</code>" => "hll wrld"),
                        /* stub */      "public static String cypher(String input, char operation) {\n\t\n}",
                        /* tests */     'System.out.println(cypher("hello world", \'e\')); System.out.println(cypher("emsx_mmi", \'d\')); System.out.println(cypher("code lm", \'e\')); System.out.println(cypher("io_t_no_zn", \'d\')); System.out.println(cypher("zilch zip zero", \'e\'));',
                        /* correct */   array("emsx_mmi", "hll wrld", "nm_ed", "my nm s nh", "sa_qa_idma")
                    ),
                    "9" => new Problem(
                        /* id */        9,
                        /* divisions */ array(1, 2),
                        /* name */      "Simple Calculator",
                        /* question */  "Given an equation consisting of two numbers and an operator (addition, subtraction, multiplication, division, modulo, or power) you must compute the output. Return <code>Double.NaN</code> if the equation includes division or modulo by 0.",
                        /* sample */    array("1 + 1" => "<code>2</code>", "3 - 12" => "<code>-9</code>", "3 * 4" => "<code>12</code>", "81 / 9" => "<code>9</code>", "10 % 3" => "<code>1</code>", "2 ^ 3" => "<code>8</code>"),
                        /* stub */      "public static double calculate(String equation) {\n\t\n}",
                        /* tests */     'System.out.println(calculate("5 + 1")); System.out.println(calculate("30 - 24")); System.out.println(calculate("3 * 12")); System.out.println(calculate("1 / 0")); System.out.println(calculate("54 / 8")); System.out.println(calculate("21 % 2")); System.out.println(calculate("0 % 0")); System.out.println(calculate("9 ^ 2"));',
                        /* correct */   array("6", "6", "36", "NaN", "6.75", "1", "NaN", "81")
                    ),
                    "10" => new Problem(
                        /* id */        10,
                        /* divisions */ array(1, 2),
                        /* name */      "Array Operations",
                        /* question */  "Given an operation and two arrays of ints (not necessarily of equal size), you must return the result of that operation in the form of an array. Note that the result could be an empty array. The operations are as follows:<br><br>Union Operator (<code>'u'</code>): Return an array that containing all values from both arrays with no repetition.<br>Intersection Operator (<code>'i'</code>): Returns an array containing all values that are in both arrays.<br>Difference Operator (<code>'d'</code>): Returns an array containing all values that are in the first array but not the second.<br>Reverse Difference Operator (<code>'r'</code>): Returns an array containing all values that are in the second array but not the first.",
                        /* sample */    array("<code>'u'</code>, <code>{ 1, 2, 3 }</code>, <code>{ 2, 3, 4, 5 }</code>" => "<code>{ 1, 2, 3, 4, 5 }</code>", "<code>'i'</code>, <code>{ 1, 2, 3 }</code>, <code>{ 2, 3, 4, 5 }</code>" => "<code>{ 2, 3 }</code>", "<code>'d'</code>, <code>{ 1, 2, 3 }</code>, <code>{ 2, 3, 4, 5 }</code>" => "<code>{ 1 }</code>", "<code>'r'</code>, <code>{ 1, 2, 3 }</code>, <code>{ 2, 3, 4, 5 }</code>" => "<code>{ 4, 5 }</code>"),
                        /* stub */      "public static int[] applyOperation(char operation, int[] a, int[] b) {\n\t\n}",
                        /* tests */     "System.out.println(Arrays.toString(applyOperation('u', new int[] { 2, 6, 4 }, new int[] { 5, 1, 4 }))); System.out.println(Arrays.toString(applyOperation('i', new int[] { 2, 5, 3, 1 }, new int[] { 7, 5, 2, 1, 8 }))); System.out.println(Arrays.toString(applyOperation('d', new int[] { 3, 5, 2 }, new int[] { 3, 5, 2 }))); System.out.println(Arrays.toString(applyOperation('d', new int[] { 2, 5, 3 }, new int[] { 2, 6, 4 }))); System.out.println(Arrays.toString(applyOperation('r', new int[] { 3, 6, 8, 1, 7 }, new int[] { 8, 5, 2, 4, 6 })));",
                        /* correct */   array("[2, 6, 4, 5, 1]", "[2, 5, 1]", "[]", "5, 3", "[5, 2, 4]")
                    ),
                    "11" => new Problem(
                        /* id */        11,
                        /* divisions */ array(2),
                        /* name */      "Binary-Decimal Converter",
                        /* question */  'We normally count using the decimal system, where a number goes to the next place every power of 10. Computers count using the binary system, where a number goes to the next place at powers of 2.<br><br>Consider the decimal number 163. We could fill in a table to represent the number like so:<br><br><table class="table"><tr><th>Place</th><td>100</td><td>10</td><td>1</td><tr><th>Count</th><td>1</td><td>6</td><td>3</td></tr></table>To convert this number to binary, we could fill in the table below:<br><br><table class="table"><tr><th>Place</th><td>128</td><td>64</td><td>32</td><td>16</td><td>8</td><td>4</td><td>2</td><td>1</td></tr><tr><th>Count</th><td>1</td><td>0</td><td>1</td><td>0</td><td>0</td><td>0</td><td>1</td><td>1</td></tr></table>To convert back to decimal, simply multiply each count by its place and add these numbers.<br><br>Knowing this information, you must now write a converter that takes in a number and the type of that number (<code>\'d\'</code> for decimal and <code>\'b\'</code> for binary) and converts it to the other type. You may assume that all numbers are valid and the largest number would be 11111111 or 255.',
                        /* sample */    array("<code>163</code>, <code>'d'</code>" => "<code>10100011</code>", "<code>10100011</code>, <code>'b'</code>" => "<code>163</code>"),
                        /* stub */      "public static int convert(int number, char operation) {\n\t\n}",
                        /* tests */     "System.out.println(convert(29, 'd')); System.out.println(convert(100101, 'b')); System.out.println(convert(250, 'd')); System.out.println(convert(11100011, 'b'));",
                        /* correct */   array("11101", "37", "11111010", "227")
                    ),
                    "12" => new Problem(
                        /* id */        12,
                        /* divisions */ array(2),
                        /* name */      "Safe Cracking Probability",
                        /* question */  "You are attempting to crack a safe, but you aren’t completely sure what the combination is. You will be given a String containing your guess. If you don’t know a particular digit, you will see a guess in its place in the form [x,y] denoting that that particular digit is between x and y inclusive. You must compute the probability of cracking the safe given your guess and the correct answer. The guess will be the first parameter as a String. The answer will be the second parameter as an integer.",
                        /* sample */    array("12345, <code>12345</code>" => "<code>100</code>", "98765, <code>12345</code>" => "<code>0</code>", "12[0,9]45, <code>12345</code>" => "<code>10</code>", "12[0,4]45, <code>12345</code>" => "<code>20</code>", "12[0,1]4[0,1], <code>12041</code>" => "<code>25</code>", "12[0,1]4[0,1], <code>12340</code>" => "<code>0</code>"),
                        /* stub */      "public static int crack(String guess, int correct) {\n\t\n}",
                        /* tests */     'System.out.println(crack("1739[0,2]", 17390)); System.out.println(crack("[6,7]38[3,5]9", 63849)); System.out.println(crack("[0,4][0,9][3,7][4,6][7,9]", 25658)); System.out.println(crack("29495", 29039)); System.out.println(crack("82[0,2]89", 82989));',
                        /* correct */   array("33", "15", "0", "0", "0")
                    ),
                    "13" => new Problem(
                        /* id */        13,
                        /* divisions */ array(2),
                        /* name */      "Special Sort",
                        /* question */  'A Person class has been defined. Given an ArrayList of type Person, sort the ArrayList by age (youngest to oldest) then by name (A-Z).<br><br>Click <a href="files/Person.java" target="_blank">here</a> to view the Person class. You may paste it directly into your Eclipse class (don\'t make a new file) for testing, but please don\'t upload it with your submission.',
                        /* sample */    array("<code>[</br>&nbsp;&nbsp;Person(\"Noah\", 16),<br>&nbsp;&nbsp;Person(\"Eli\", 15)<br>]</code>" => "<code>[</br>&nbsp;&nbsp;Person(\"Eli\", 15),<br>&nbsp;&nbsp;Person(\"Noah\", 16)<br>]</code>", "<code>[</br>&nbsp;&nbsp;Person(\"David\", 16),<br>&nbsp;&nbsp;Person(\"Noah\", 16)<br>]</code>" => "<code>[</br>&nbsp;&nbsp;Person(\"David\", 16),<br>&nbsp;&nbsp;Person(\"Noah\", 16)<br>]</code>", "<code>[</br>&nbsp;&nbsp;Person(\"Noah\", 16),<br>&nbsp;&nbsp;Person(\"Nathan\", 16)<br>]</code>" => "<code>[</br>&nbsp;&nbsp;Person(\"Nathan\", 16),<br>&nbsp;&nbsp;Person(\"Noah\", 16)<br>]</code>", "<code>[</br>&nbsp;&nbsp;Person(\"Noah\", 16),<br>&nbsp;&nbsp;Person(\"Max\", 16),<br>&nbsp;&nbsp;Person(\"Gabe\", 15),<br>&nbsp;&nbsp;Person(\"Dan\", 18)<br>]</code>" => "<code>[</br>&nbsp;&nbsp;Person(\"Gabe\", 15),<br>&nbsp;&nbsp;Person(\"Max\", 16),<br>&nbsp;&nbsp;Person(\"Noah\", 16),<br>&nbsp;&nbsp;Person(\"Dan\", 18)<br>]</code>"),
                        /* stub */      "public static List<Person> sort(List<Person> people) {\n\t\n}",
                        /* tests */     'System.out.println(sort(Arrays.asList(new Person("Dave", 34), new Person("Mark", 37)))); System.out.println(sort(Arrays.asList(new Person("Steve", 12), new Person("Jeremy", 13), new Person("Jake", 13)))); System.out.println(sort(Arrays.asList(new Person("Mark", 12), new Person("Markie", 12), new Person("Marcus", 12), new Person("Marco", 12))));',
                        /* correct */   array("[Dave, Mark]", "[Steve, Jake, Jeremy]", "[Mark, Markie, Marco, Marcus]"),
                        /* special */   "static class Person{private String name;private int age;public Person(String name,int age){this.name=name;this.age=age;}public String getName(){return name;}public int getAge(){return age;}@Override public String toString(){return name;}}"
                    ),
                    "14" => new Problem(
                        /* id */        14,
                        /* divisions */ array(2),
                        /* name */      "Shipment Order",
                        /* question */  "A shipment-tracking program has recently broken and needs to be fixed. However, the program is poorly written, so it won’t be as easy as you think.<br><br>You will be given three arrays of equal length representing the shipment data. The first array will contain the items being shipped. The second array will contain the times in 12-hour format. The last array will tell you whether each time is am or pm. Your task is to return an array containing the items being shipped in order from earliest to latest.",
                        /* sample */    array("<code>{ \"iPhone 6S\", \"MacBook Ultra\", \"iPad Pro\" },<br>{ 1250, 609, 900 },<br>{ \"pm\", \"am\", \"pm\" }</code>" => "<code>{ \"MacBook Ultra\", \"iPad Pro\", \"iPhone 6S\" }</code>"),
                        /* stub */      "public static String[] order(String[] items, int[] times, String[] amPM) {\n\t\n}",
                        /* tests */     'System.out.println(Arrays.toString(order(new String[] { "Hamburger", "Fries", "Soda" }, new int[] { 524, 1203, 305 }, new String[] { "pm", "am", "pm" }))); System.out.println(Arrays.toString(order(new String[] { "Sandwich", "Chips", "Drink" }, new int[] { 643, 353, 1029 }, new String[] { "pm", "pm", "am" }))); System.out.println(Arrays.toString(order(new String[] { "Egg", "Cheese" }, new int[] { 800, 800 }, new String[] { "pm", "am" })));',
                        /* correct */   array("[Fries, Soda, Hamburger]", "[Drink, Chips, Sandwich]", "[Cheese, Egg]")
                    ),
                    "15" => new Problem(
                        /* id */        15,
                        /* divisions */ array(2),
                        /* name */      "Maze Solver",
                        /* question */  "You will be given a maze like this:<br><br>o––––<br>o–o–o<br>–oo–o<br>–o–oo<br><br>Your task is to determine, starting at (0, 0), whether the maze can be solved. The maze is solvable if each o has at least one neighbor (vertical, horizontal, diagonal) and that moving from o to o will eventually land you at the bottom right of the array. Note that if (0, 0) is not an o, the maze is unsolvable. Return <code>true</code> if the maze is solvable, <code>false</code> if not.",
                        /* sample */    array('<code>{</br>&nbsp;&nbsp;"o––––",<br>&nbsp;&nbsp;"o–o–o",<br>&nbsp;&nbsp;"–oo–o",<br>&nbsp;&nbsp;"–oo–o"<br>}</code>' => "<code>true</code>"),
                        /* stub */      "public static boolean isSolvable(String[] maze) {\n\t\n}",
                        /* tests */     'System.out.println(isSolvable(new String[] { "o----", "o-o-o", "-oo-o", "-o-oo" })); System.out.println(isSolvable(new String[] { "o-o-o", "oo-oo", "--oo-", "oo-oo" })); System.out.println(isSolvable(new String[] { "-o-o-", "o---o", "-o-oo", "-ooo-" })); System.out.println(isSolvable(new String[] { "o--o-", "-o--o", "---oo", "--ooo" })); System.out.println(isSolvable(new String[] { "o-o--", "-o-o-", "o---o", "-o--o" }));',
                        /* correct */   array("true", "true", "false", "false", "true")
                    ),
                    "16" => new Problem(
                        /* id */        16,
                        /* divisions */ array(0),
                        /* name */      "Even or odd?",
                        /* question */  "Given parameter <code>i</code> of type <code>int</code>, return <code>true</code> if the number is even and <code>false</code> if it is odd.",
                        /* sample */    array("0" => "<code>true</code>", "1" => "<code>false</code>", "2" => "<code>true</code>"),
                        /* stub */      "public static boolean isEven(int i) {\n\t\n}",
                        /* tests */     'System.out.println(isEven(0)); System.out.println(isEven(1)); System.out.println(isEven(2)); System.out.println(isEven(999)); System.out.println(isEven(1000));',
                        /* correct */   array("true", "false", "true", "false", "true")
                    ),
                    "17" => new Problem(
                        /* id */        17,
                        /* divisions */ array(0),
                        /* name */      "How do you do?",
                        /* question */  "Given parameter <code>name</code> of type <code>String</code>, return a nice greeting in the format <i>Hello, {name}!</i>",
                        /* sample */    array("Noah" => "Hello, Noah!", "Dog" => "Hello, Dog!"),
                        /* stub */      "public static String greet(String name) {\n\t\n}",
                        /* tests */     'System.out.println(greet("Noah")); System.out.println(greet("Dog")); System.out.println(greet("1234")); System.out.println(greet("%test%"));',
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