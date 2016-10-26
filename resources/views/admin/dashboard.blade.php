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
			<div class="col-md-9">
				<div class="panel panel-default">
					<div class="panel-heading"><strong></strong></div>
					<div class="panel-body">
							<div class="row" >
								<div  class="col-md-9 col-md-offset-1">
									<canvas id="subcriberSummary"></canvas>
								</div>
							</div>
							<div class="row" >
								<div  class="col-md-9 col-md-offset-1">
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
            label: 'Active Subscribers',
            data: [ @foreach ($weeklySummaries as $week) {{$week->totalSubs}}, @endforeach ],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
        },{
            label: 'New Subscribers',
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
            label: 'Incomplete Signups',
            data: [ @foreach ($subs as $i=>$sub) {{$subs[$i][0]->statusTotal}}, @endforeach],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
        },{
            label: 'Active Subscribers',
            data:  [ @foreach ($subs as $i=>$sub) @if (isset($subs[$i][1])){{$subs[$i][1]->statusTotal}}, @endif @endforeach],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        },{
            label: 'Cancelled',
            data:  [ @foreach ($subs as $i=>$sub) @if (isset($subs[$i][2])){{$subs[$i][2]->statusTotal}}, @endif @endforeach],
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        },{
            label: 'New Signups',
            data:  [ @foreach ($subs as $i=>$sub) @if (isset($newSubs[$i])) {{ $newSubs[$i]->new }} @else 0 @endif, @endforeach ],
            backgroundColor: 'rgba(56, 110, 2, 0.2)',
            borderColor: 'rgba(56, 110, 2, 1)',
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



