@extends('spark::layouts.app-admin', ['menuitem' => 'dashboard'])

@section('page_header')
    <h1>
        What's Shipping {{ $thisTuesday }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection

@section('content')
<home :recipes="recipes" inline-template>
    <div class="container">

@foreach($reportData['bigGroups'] as $bgData)
			<div class="row">
				<div class="col-md-9">
					<div class="panel panel-default">
						<div class="panel-heading"><strong>{{$bgData['name']}}</strong></div>
						<div class="panel-body">
							@foreach ($bgData['groups'] as $gData)
								<div class="row" style="font-size:small;color:white;background-color:black;padding-left: 10px">
									<strong>
										{{$gData['name']}}
									</strong>
								</div>
								<table id="boxes" class="table table-striped table-hover table-order-column" width="100%" cellspacing="0">
									@foreach ($gData['products'] as $pData)
										<tr>
											<td>{{$pData['name']}}</td>
											<td class="text-right">{{$pData['count']}}</td>
										</tr>
									@endforeach
								</table>
							@endforeach
						</div>
					</div>
				</div>
			</div>
@endforeach

	</div>
</div>

</home>
@endsection



