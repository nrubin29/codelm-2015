<html>
    <head>
        <title>CodeLM 2015 Judging Dashboard</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="CodeLM 2015 Judging Dashboard">
        <meta name="author" content="Noah Rubin">

        <link rel="stylesheet" href="css/bootstrap.min.css">
    </head>

    <body>
        <?php
            require_once("database.php");
        ?>

        <div class="container">
            <div class="page-header">
                <h1>CodeLM 2015 Judging Dashboard</h1>
            </div>

            <!-- Selection -->
            <div role="tabpanel">
                <ul class="nav nav-tabs nav-justified" role="tablist">
                    <li role="presentation"><a href="#demo" aria-controls="demo" role="tab" data-toggle="tab">Demo</a></li>
                    <li role="presentation"><a href="#intermediate" aria-controls="intermediate" role="tab" data-toggle="tab">Intermediate</a></li>
                    <li role="presentation"><a href="#advanced" aria-controls="advanced" role="tab" data-toggle="tab">Advanced</a></li>
                </ul>
            </div>

            <br>

            <div class="tab-content">
                <?php $divisions = array("demo", "intermediate", "advanced"); for ($i = 0; $i < sizeof($divisions); $i++) { ?>
                    <div role="tabpanel" class="tab-pane active" id="<?php echo $divisions[$i] ?>">
                        <?php $number = 1; foreach (Team::get_teams($i) as $team) { ?>
                            <div class="col-lg-1">
                                <h1><?php echo $number ?>.</h1>
                            </div>
                            <div class="col-lg-11">
                                <div class="panel panel-info">
                                    <div class="panel-body">
                                        <div class="col-lg-6">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Members</th>
                                                        <th>Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo $team->id ?></td>
                                                        <td><?php echo $team->name ?></td>
                                                        <td><?php echo $team->members ?></td>
                                                        <td><?php echo $team->score ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-lg-6">
                                            <ul class="list-group">
                                                <?php foreach (explode(";", $team->log) as $entry) { if ($entry == "") { break; } $data = explode(":", $entry); ?>
                                                    <li class="list-group-item"><a href="submissions/<?php echo $data[2] ?>.java">[<?php echo str_replace("-", ":", $data[1]) ?>] Problem <?php echo $data[0] ?> - <?php echo is_numeric($data[3]) ? $data[3] . "%" : $data[3] ?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $number++; } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>