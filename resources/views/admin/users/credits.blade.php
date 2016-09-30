
	

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
			<!--
			<div class="old_credits">	
				@foreach ($csr_notes as $csr_note)
				<div class="noteline">
					<span style="width:50px;">{{$csr_note->created_at}}</span>
					<span>{{$csr_note->note_text}}</span>
				</div>
				@endforeach
			</div>
			-->
			</form>
		</div>

    </div>


