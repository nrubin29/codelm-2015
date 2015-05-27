<html>
    <head>
        <title>CodeLM 2015 Registration</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2015 Registration">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="css/bootstrap.min.css">

        <style>
            .center {
                float: none;
                margin-left: auto;
                margin-right: auto;
            }
        </style>
    </head>

    <body>
        <?php
            require_once("database.php");
        ?>

        <div class="container">
            <div class="page-header">
                <h1><span style="color: gray;">Code</span><span style="color: rgb(128, 0, 0);">LM</span> <span style="color: gray;">2015</span> Registration <small>For official use only.</small></h1>
            </div>

            <div class="col-lg-6 center">
                <div id="alert" class="alert alert-info" role="alert"></div>
                <form id="form">
                    <div class="form-group">
                        <input type="number" name="division" placeholder="Division" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Name" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="members" placeholder="Members" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Register" class="btn btn-lg btn-primary center-block">
                    </div>
                </form>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#alert").hide();

                $("#form").submit(function(event) {
                    event.preventDefault();

                    $("#alert").fadeOut();

                    $.post("register.php", $("#form").serialize(), function(data) {
                        if (data != -1) {
                            $("#alert").text("Team registered under number " + data + " with password " + $('input[name="password"]').val() + ".");
                            $("input").val("");
                            $('input[type="submit"]').val("Register");
                        }

                        else {
                            $("#alert").text("Registration failed: " + data);
                        }

                        $("#alert").fadeIn();
                    });
                });
            });
        </script>
    </body>
</html>