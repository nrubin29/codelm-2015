<html>
    <head>
        <title>CodeLM 2015 Dashboard</title>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2015">
        <meta name="author" content="Noah Rubin">
        
        <link rel="stylesheet" href="/codelm/css/bootstrap.min.css">
        <link rel="stylesheet" href="/codelm/css/jasny-bootstrap.min.css">
        <link rel="stylesheet" href="/codelm/css/codemirror.css">
        
        <style>
            .CodeMirror {
                border: 1px solid #eee;
                height: 200px;
            }
          
            .center {
                float: none;
                margin-left: auto;
                margin-right: auto;
            }
          
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                height: 60px;
                background-color: whitesmoke;
            }
            
            .navbar-toggle {
                float: left;
                margin-left: 15px;
            }

            .navmenu {
                z-index: 1;
            }

            .canvas {
                position: relative;
                left: 0;
                z-index: 2;
                min-height: 100%;
                padding: 50px 0 0 0;
                background: #fff;
            }

            @media (min-width: 0) {
                .navbar-toggle {
                    display: block; /* force showing the toggle */
                }
            }

            @media (min-width: 992px) {
                body {
                    padding: 0;
                }
                
                .navbar {
                    right: auto;
                    background: none;
                    border: none;
                }
                
                .canvas {
                    padding: 0;
                }
            }
        </style>
    </head>

    <body>
        <?php
            session_start();
            require_once("database.php");
            
            if (isset($_SESSION["team"])) {
                $team = new Team($_SESSION["team"]);
            }
        ?>
        
        <?php if (!isset($team)) { ?>
            <div class="container">
              <div class="col-lg-12">
                <div class="page-header">
                    <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2015</span> <small>Please enter your team password to log in.</small></h1>
                </div>
                <div class="col-lg-6 center">
                    <div class="row">
                    <div class="col-lg-12">
                      <div id="alert" class="alert alert-danger" role="alert">
                        <p>The team password you have entered is incorrect.</p>
                      </div>
                    </div>
                  </div>
                  
                  <form id="form">
                    <div class="form-group">
                      <input type="password" name="password" class="form-control input-lg">
                    </div>
                    <div class="form-group">
                      <input type="submit" value="Log In" class="btn btn-primary btn-lg center-block">
                    </div>
                  </form>
                </div>
              </div>
            </div>
        <?php } else { ?>
            <div class="navmenu navmenu-default navmenu-fixed-left" id="nav">
                <p class="navmenu-brand" style="margin-bottom: -10px; cursor: hand;"><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2015</span></p>
                <hr>
                <ul class="nav navmenu-nav">
                    <?php Problem::setup(); foreach (Problem::all_formatted($team) as $problem) { ?>
                        <li id="<?php echo $problem["id"] ?>"><a href="#" data-toggle="offcanvas" data-target="#nav" data-canvas="body"><?php echo $problem["id"] ?>. <?php echo $problem["name"] ?> <span class="badge pull-right"><span id="b<?php echo $problem["id"] ?>" style="margin-top: -2px;" class="glyphicon glyphicon-<?php echo $team->is_solved($problem["id"]) ? "ok" : "certificate"; ?>"></span></span></a></li>
                    <?php } ?>
                </ul>
                <hr>
                <a href="http://docs.oracle.com/javase/7/docs/api/" target="_blank" class="navmenu-brand" style="margin-top: -10px; font-size: 14px;">Open JavaDocs</a>
            </div>
            <div class="canvas">
                <div class="navbar navbar-default navbar-fixed-top">
                  <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#nav" data-canvas="body">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                </div>
                <div class="container">
                    <div class="col-lg-12">
                        <div class="page-header">
                            <h1 id="title"></h1>
                        </div>
                    </div>
                        
                    <div class="col-lg-12">
                        <div class="alert alert-info" id="alert">
                            <button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <p id="alertText"></p>
                        </div>
                    </div>
                            
                    <div class="col-lg-6">
                        <legend>Question</legend>
                        <p id="question"></p>
                    </div>
                    <div class="col-lg-6">
                        <legend>Sample Data</legend>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sample Input</th>
                                    <th>Correct Output</th>
                                </tr>
                            </thead>
                            <tbody id="tablebody">
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12">
                        <form method="post" action="/codelm/submit.php" id="submit">
                            <div class="form-group">
                                <textarea rows="10" name="code" id="code"></textarea>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="problem" id="problem" value="">
                            </div>
                                <input type="submit" id="submitButton" class="btn btn-primary btn-lg pull-right">
                        </form>
                    </div>
                </div>
                <br><br><br><br>
                <footer class="footer">
                    <div class="container">
                        <div class="col-lg-12">
                            <br/>
                            <p align="center" style="font-size: 24px; margin-top: -10px; cursor: hand;"><span class="label label-primary">Team <?php echo $team->id; ?>: <?php echo $team->divisionString ?> Division</span> <span class="label label-info" id="score">Score: <?php echo $team->score; ?></span> <span class="label label-success"><span id="solved"><?php echo sizeof($team->problems_solved()); ?></span>/<?php echo $team->problems_total(); ?> Problems Solved</span> <span class="label label-danger" id="countdown">Finished!</span></p>
                        </div>
                    </div>
                </footer>
            </div>
        <?php } ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="/codelm/js/bootstrap.min.js"></script>
        <script src="/codelm/js/jasny-bootstrap.min.js"></script>
        <script src="/codelm/js/codemirror.js"></script>
        <script src="/codelm/js/clike.js"></script>
        <script src="//raw.githubusercontent.com/mckamey/countdownjs/master/countdown.min.js"></script>
        <script>
            $(document).ready(function() {
                <?php if (!isset($team)) { ?>
                    $("#alert").hide();
                  
                    $("#form").submit(function(event) {
                        event.preventDefault();
                        
                        $("#alert").fadeOut();
            
                        $.post("login.php", $("#form").serialize(), function(data) {
                            if (data != -1) {
                                location.reload();
                            }
                            
                            else {
                                $("#alert").fadeIn().delay(5 * 1000).fadeOut();
                            }
                        });
                    });
                <?php } else { ?>
                    var data = <?php echo json_encode(Problem::all_formatted($team)); ?>;
                    var score = <?php echo $team->score; ?>;
                    var solved = <?php echo sizeof($team->problems_solved()); ?>;
                    var codes = {};
                    var currentID = -1;
                    
                    var codeMirror = CodeMirror.fromTextArea(code, { lineNumbers: true, mode: "text/x-java", matchBrackets: true, lines: 5 });
              
                    $("#alert").hide();
                    
                    $("li").click(function() {
                        $("li").removeClass("active");
                        $(this).addClass("active");
                        
                        $("#title").html(data[this.id]["name"] + " <small>Problem " + data[this.id]["id"] + "</small>");
                        $("#question").html(data[this.id]["question"]);
                        $("#problem").val(data[this.id]["id"]);
                        
                        if (data[this.id]["solved"]) {
                            $("#submitButton").prop("disabled", true).val("Solved");
                        }
                        
                        else {
                            $("#submitButton").prop("disabled", false).val("Submit");
                        }
                        
                        if (currentID != -1) {
                            codes[currentID] = codeMirror.getDoc().getValue();
                        }
                        
                        codeMirror.getDoc().setValue(codes[this.id] == null ? data[this.id]["stub"] : codes[this.id]);

                        $("#tablebody").html("");
                        
                        $.each(data[this.id]["sample"], function(key, value) {
                            $("#tablebody").append("<tr><td>" + key + "</td><td>" + value + "</td></tr>");
                        });
                        
                        currentID = this.id;
                    });
                    
                    $("li:first").click();
            
                    $("#submit").submit(function(event) {
                        event.preventDefault();
            
                        $("#submitButton").prop("disabled", true);
                        $("#alertText").text("Submitting...");
                        $("#alert").fadeIn();
            
                        $.post("/codelm/submit.php", $("#submit").serialize(), function(ret) {
                            var deltaPoints = -1;

                            if (ret == "closed") {
                                $("#alertText").html('<span style="color: red;">The competition is over. You may no longer submit answers.</span>');
                                deltaPoints = 0;
                            }

                            else if (ret == "compilation") {
                                $("#alertText").html('<span style="color: red;">Your answer did not compile. Please ensure it is valid code and runs without compilation errors.</span>');
                                deltaPoints = 0;
                            }
                            
                            else if (ret == "stacktrace") {
                                $("#alertText").html('<span style="color: red;">Your answer resulted in a stack trace. Please ensure it runs correctly.</span>');
                            }
                            
                            else if (ret == "toolong") {
                                $("#alertText").html('<span style="color: red;">Your answer took too long to run. Please ensure that there are no infinite loops and long recursion.</span>');
                            }
                            
                            else if (ret == "return") {
                                $("#alertText").html('<span style="color: red;">Your answer resulted in an unknown process return value. Please tell a judge.</span>');
                            }
                            
                            else if (ret == "toomuch") {
                                $("#alertText").html('<span style="color: red;">Your answer gave too many outputs. Please ensure that you have no extraneous print statements.</span>');
                            }
                            
                            else if (ret == "toofew") {
                                $("#alertText").html('<span style="color: red;">Your answer did not give enough outputs. This is probably an error unless you are doing problem 1. If you are not, tell a judge.</span>');
                            }
                          
                            else {
                                $("#alertText").text("You were correct for " + ret + "% of tests.");

                                if (ret == 100) {
                                    deltaPoints = 2;
                                    solved++;
                                }
                            }
                            
                            score += deltaPoints;
                            
                            $("#score").text("Score: " + score);
                            $("#solved").text(solved);
                            
                            if (deltaPoints == 2) {
                                data[currentID]["solved"] = true;
                                $("#submitButton").val("Solved");
                                $("#b" + currentID).removeClass("glyphicon-certificate").addClass("glyphicon-ok");
                            }
                            
                            else {
                                $("#submitButton").prop("disabled", false);
                            }
                        });
                    });
                    
                    var end = new Date(2015, 4, 28, 10, 30);
                    var cd = countdown(function(time) {
                        if (end.getTime() <= new Date().getTime()) {
                            $("#countdown").text("Finished!");
                            window.clearInterval(cd);
                        }
                        
                        else {
                            $("#countdown").text(time.toString() + " remaining");
                        }
                    }, end, countdown.DEFAULTS);

                    window.onbeforeunload = function() {
                        return "Your progress will be saved, but any code you have written will be deleted.";
                    };
                <?php } ?>
            });
        </script>
    </body>
</html>