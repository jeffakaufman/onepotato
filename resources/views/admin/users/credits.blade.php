
	

		<div class="panel panel-default ">
       	<div class="panel-heading"><h4>Credits</h4></div>
		<div class="panel-body">
				
    		<form action="{{ url('admin') }}/user_details/credit/{{ $user->id }}" method="POST" class="form-horizontal">
		     	{{ csrf_field() }}
				<input type="hidden" name="user_id" value="{{ $user->id }}" />
	
				<div class="form-group" class="col-sm-10">
					<label for="note_text" ></label>
					&nbsp;&nbsp;{{ Form::label('credit_amount', 'Credit Amount') }}
			        {{ Form::text('credit_amount') }}
					&nbsp;Amount: {{ Form::radio('credit_type', 'amount', true) }}
					&nbsp;Percent: {{ Form::radio('credit_type', 'percent', false) }}
					&nbsp;&nbsp;{{ Form::label('credit_description', 'Description') }}
			        {{ Form::text('credit_description') }}
			
					
				</div>
				<div class="form-group">
					<div class="col-sm-offset-9 col-sm-3">
							<button type="submit" class="btn btn-default">
					    		<i class="fa fa-plus"></i> Issue Credit
					    	</button>
					</div>
				</div>
			
			<div class="old_credits">
			
			<table>	
				<tr><td width="150"><strong>Date Applied</strong></td><td width="100"><strong>Amount</strong></td><td width="100"><strong>Percent</strong></td><td width="200"><strong>Description</strong></td></tr>
				@foreach ($credits as $credit)
				<tr><td>{{ $credit->date_applied}}</td><td>{{ number_format($credit->credit_amount / 100,2) }}</td><td>{{ $credit->credit_percent }}</td><td>{{ $credit->credit_description}}
				@endforeach
			</table>
			
			</div>
			
			</form>
		</div>

    </div>


