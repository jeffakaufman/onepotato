@extends('spark::layouts.app-admin', ['menuitem' => 'dashboard'])

@section('page_header')
    <h1>
        <center>Dashboard</center>
    </h1>

@endsection

@section('content')
 
<home :recipes="recipes" inline-template>
    <div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading"><strong></strong></div>
					<div class="panel-body">
							<div class="row" >
								<div  class="col-md-9 col-md-offset-1">
									<canvas id="revenue"></canvas>
								</div>
							</div>
							<div class="row" >
								<div  class="col-md-6">
									<canvas id="subcriberSummary"></canvas>
								</div>
								<div  class="col-md-6">
									<canvas id="subBreakdown"></canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</home>

<script>
var ctx2 = document.getElementById("subcriberSummary");
var myChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [ @foreach ($weeklySummaries as $week) "{{date('M d, Y',strtotime($week->start_date))}}", @endforeach ],
        datasets: [{
            label: 'Recurring',
            data: [ @foreach ($weeklySummaries as $week) {{$week->recurringSubCount}}, @endforeach ],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
        },{
            label: 'New',
            data:  [ @foreach ($weeklySummaries as $week) {{$week->newSubCount}}, @endforeach ],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        },{
            label: 'Skips',
            data:  [ @foreach ($weeklySummaries as $week) {{$week->skips}}, @endforeach ],
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
var ctx = document.getElementById("subBreakdown");
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [ @foreach ($subs as $i=>$sub) "{{$i}}", @endforeach ],
        datasets: [{
            label: 'Incomplete',
            data: [{{ $subReport["incomplete"]->today }},{{ $subReport["incomplete"]->yesterday }},{{ $subReport["incomplete"]->lastWeek }},{{ $subReport["incomplete"]->lastMonth }},{{ $subReport["incomplete"]->last90 }}],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
        },{
            label: 'New',
            data: [{{ $subReport["active"]->today }},{{ $subReport["active"]->yesterday }},{{ $subReport["active"]->lastWeek }},{{ $subReport["active"]->lastMonth }},{{ $subReport["active"]->last90 }}],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        },{
            label: 'Cancelled',
            data: [{{ $subReport["inactive-cancelled"]->today }},{{ $subReport["inactive-cancelled"]->yesterday }},{{ $subReport["inactive-cancelled"]->lastWeek }},{{ $subReport["inactive-cancelled"]->lastMonth }},{{ $subReport["inactive-cancelled"]->last90 }}],
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
var ctx = document.getElementById("revenue");
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [ @foreach ($revenue as $week) "{{date('M d',strtotime('Y'.substr($week->week,0,4).'W'.substr($week->week,4).'+2 days'))}}", @endforeach ],
        datasets: [{
            label: 'Chargeable',
            data: [@foreach ($revenue as $week) {{ money_format ('%i',$week->amountToCharge) }}, @endforeach],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
        },{
            label: 'Actual',
            data: [@foreach ($revenue as $week) {{ money_format ('%i',$week->amountCharged)}}, @endforeach],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>
@endsection


//$week->week.'+2 days'
