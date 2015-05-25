<html>
    <head>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <title>CodeLM 2015 Judging Dashboard</title>
        
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
              <h1>CodeLM 2015 Judging Dashboard</h1>
          </div>

          <!-- Selection -->
          <div role="tabpanel">
              <ul class="nav nav-tabs nav-justified" role="tablist">
                  <li role="presentation" class="active"><a href="#demo" aria-controls="demo" role="tab" data-toggle="tab">Demo</a></li>
                  <li role="presentation"><a href="#intermediate" aria-controls="intermediate" role="tab" data-toggle="tab">Intermediate</a></li>
                  <li role="presentation"><a href="#advanced" aria-controls="advanced" role="tab" data-toggle="tab">Advanced</a></li>
              </ul>
          </div>

          <br>

          <div class="tab-content">
              <!-- Demo -->
              <div role="tabpanel" class="tab-pane active" id="demo">
                  <?php $i = 1; foreach (Team::get_teams(0) as $team) { ?>
                      <div class="col-lg-1">
                          <h1><?php echo $i ?>.</h1>
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
                  <?php $i++; } ?>
              </div>

              <!-- Intermediate -->
              <div role="tabpanel" class="tab-pane active" id="intermediate">
                  <?php foreach (Team::get_teams(1) as $team) { ?>
                      <div class="panel panel-info">
                          <div class="panel-heading">Team <?php echo $team->id; ?></div>
                          <div class="panel-body">
                              <p>Score: <?php echo $team->score; ?></p>
                          </div>
                      </div>
                  <?php } ?>
              </div>

              <!-- Advanced -->
              <div role="tabpanel" class="tab-pane active" id="advanced">
                  <?php foreach (Team::get_teams(2) as $team) { ?>
                      <div class="panel panel-info">
                          <div class="panel-heading">Team <?php echo $team->id; ?></div>
                          <div class="panel-body">
                              <p>Score: <?php echo $team->score; ?></p>
                          </div>
                      </div>
                  <?php } ?>
              </div>
          </div>
      </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>