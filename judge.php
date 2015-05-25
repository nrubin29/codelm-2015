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
        <div class="col-lg-12">
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
              <?php foreach (Team::get_teams(0) as $team) { ?>
                <div class="panel panel-info">
                  <div class="panel-heading">Team <?php echo $team->id; ?></div>
                  <div class="panel-body">
                    <p>Score: <?php echo $team->score; ?></p>
                  </div>
                </div>
              <?php } ?>
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
      </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>