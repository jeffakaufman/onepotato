
	

		<div class="panel panel-default ">
       	<div class="panel-heading"><h4>Credits</h4></div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-12" id="">
					<form action="{{ url('admin') }}/user_details/credit/{{ $user->id }}" method="POST" >
		     		{{ csrf_field() }}
		     			<div class="row">
							<div class="form-group col-sm-2">
								{{ Form::label('credit_amount', 'Amount', ['class' => 'control-label']) }}
			    	    		{{ Form::number('credit_amount', null,array_merge(['class' => 'form-control'])) }}
							</div>
							<div class="form-group col-sm-1">
								{{ Form::label('credit_type', 'Type', ['class' => 'control-label']) }}
								{{ Form::select('credit_type', array('amount' => '$','percent'=>'%'), array_merge(['class' => 'form-control'])) }}
							</div>
							<div class="form-group col-sm-8">
								{{ Form::label('credit_description', 'Description', ['class' => 'control-label']) }}
					    	   	{{ Form::text('credit_description', null,array_merge(['class' => 'form-control'])) }}
							</div>
						</div>
		     			<div class="row">
							<div class="col-sm-12" id="">
								<button type="submit" class="btn btn-default col-md-offset-9">
				    				<i class="fa fa-plus"></i> Issue Credit
				    			</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="row" style="margin-top:5px">
				<div class="col-sm-12" id="">
					<table class="table table-striped table-cell-border table-compact" width="40%" cellspacing="0">	
						<tr>
							<th width="10%">Date</th>
							<th width="15%" class="text-right">Amount</th>
							<th>Description</th>
						</tr>
						@foreach ($credits as $credit)
						<tr>
							<td>{{ date('n/j/y',strtotime($credit->created_at))}}</td>
							<td class="text-right">
								@IF ($credit->credit_percent) {{ $credit->credit_percent }}%/ @ENDIF
								${{ number_format($credit->credit_amount / 100,2) }} </td>
							<td>{{ $credit->credit_description}}</td>
						@endforeach
						</tr>
					</table>
				</div>
			</div>
    </div>


