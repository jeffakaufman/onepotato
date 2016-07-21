<div class="row">
	<div class="col-md-10 col-md-offset-1">	
    	<form action="{{ url('user') }}/csr_note/{{ $user->id }}" method="POST" class="form-horizontal">
		     {{ csrf_field() }}
		<input type="hidden" name="user_id" value="{{ $user->id }}" />
		<div class="csrnotes">
			<div class="form-group" class="col-sm-12">
				<label for="note_text" >Notes</label>
				<textarea rows="4"  class="form-control" cols="75" name="note_text" id="note_text"></textarea>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-10 col-sm-3">
					<button type="submit" class="btn btn-default">
				    	<i class="fa fa-plus"></i> Add Note
				    </button>
				</div>
			</div>
			
			<div class="old_notes">	
				@foreach ($csr_notes as $csr_note)
				<div class="noteline">
					<span style="width:50px;">{{$csr_note->created_at}}</span>
					<span>{{$csr_note->note_text}}</span>
				</div>
				@endforeach
			</div>
        </div>
</div>
</form>