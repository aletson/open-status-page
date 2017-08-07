<?php
  $domain = 'Your domain name here';
  $apiKey = 'Your Uptime Robot API key';
  $twUser = 'Your twitter handle';
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, 'https://api.uptimerobot.com/getMonitors?apiKey=' . $apiKey . '&responseTimes=1&format=json');
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_USERAGENT, 'Open Status Page cURL');
  $json = curl_exec($curl);
  curl_close($curl);
  $json = str_replace('jsonUptimeRobotApi(', '', $json);
  $json = str_replace(')', '', $json);
  $return_obj = json_decode($json);
  $monitorData = $return_obj->monitors->monitor[0];
  $monitorData->responsetime = array_reverse($monitorData->responsetime);
?>
<html>
	<head>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha256-916EbMg70RQy9LHiGkXzG8hSg9EdNy97GazNG/aiY1w=" crossorigin="anonymous" />		
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha256-U5ZEeKfGNOja007MMD3YBI0A3OSZOQbeG6z2f2Y0hu8=" crossorigin="anonymous"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js" integrity="sha256-w8BXa9KXx+nmhO9N4hupvlLy+cAtqEarnB40DVJx2xA=" crossorigin="anonymous"></script>
		<script>
			window.twttr = (function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0],
				t = window.twttr || {};
				if (d.getElementById(id)) return t;
				js = d.createElement(s);
				js.id = id;
				js.src = "https://platform.twitter.com/widgets.js";
				fjs.parentNode.insertBefore(js, fjs);
				t._e = [];
				t.ready = function(f) {
					t._e.push(f);
				};
				return t;
			}(document, "script", "twitter-wjs"));
		</script>
		<title>Open Status Page for <?=$domain;?></title>

	</head>
	<body>
		<div class="container">
			<h2><?=$domain;?> monitoring and alerts</h2>
			<div class="row">
				<div class="col-md-6">
					<h3>Domain Uptime</h3>
					<canvas id="response-time-chart"></canvas>
					<?php
            $avgResp = 0;
            $count = 0;
            foreach($monitorData->responsetime as $thisResponse) {
              $avgResp += $thisResponse->value; $count++;
            }
            $avgResp = $avgResp / $count;
          ?>
					<h4 style="text-align: center">Average response time: <?=$avgResp;?> ms</h4>
					<canvas id="uptime-chart"></canvas>
					<h4 style="text-align: center"><?=$monitorData->alltimeuptimeratio;?>% uptime</h4>
				</div>
				<div class="col-md-6">		
					<h3>Maintenance announcements and updates</h3>
					<a class="twitter-timeline" data-tweet-limit="5" href="https://twitter.com/<?=$twUser;?>">Tweets by @<?=$twUser;?></a>
					<a class="twitter-follow-button" href="https://twitter.com/<?=$twUser;?>">Follow @<?=$twUser;?></a>
				</div>
			</div>
			<div id="footer">
        <p class="text-muted credit">Made with &hearts; by <a href="https://github.com/velinath/open-status-page">@velinath</a></p>
      </div>
		</div>
		<script>
		  var ctx = $('#response-time-chart');
		  var responseTimeChart = new Chart(ctx, {
  			type: 'line',
  			data: {
  				labels: [<?php foreach($monitorData->responsetime as $thisResponse) { echo '"' . $thisResponse->datetime . '",'; } ?>],
  				datasets: [{
  					label: "Response time (ms)",
  					data: [<?php foreach($monitorData->responsetime as $thisResponse) { echo $thisResponse->value . ',';} ?>],
  				}]
  			},
  			options: {
  				scales: {
  					yAxes: [{
  						ticks: {
  							beginAtZero: true
  						}
  					}]
  				}
  			}
  		});
  		var ctx2 = $('#uptime-chart');
  		var uptimeChart = new Chart(ctx2, {
  			type: 'doughnut',
  			data: {
  				labels: ['Uptime (%)', 'Downtime (%)'],
  				datasets: [{
  					data: [<?=$monitorData->alltimeuptimeratio;?>, <?=100 - $monitorData->alltimeuptimeratio;?>],
  					backgroundColor: ["#5cb85c", "#d9534f"]
  				}]
  			}
  		});
    </script>
	</body>
</html>
