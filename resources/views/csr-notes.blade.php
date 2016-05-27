	<form action="{{ url('user') }}/csr_note/{{ $user->id }}" method="POST" class="form-horizontal">
		     {{ csrf_field() }}
	<input type="hidden" name="user_id" value="{{ $user->id }}" />
<div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default csrnotes">
                <div class="panel-heading">Notes</div>
				
                <div class="panel-body">
						<div class="form-group">
							<label for="note_text" class="col-sm-3 control-label">Note</label>

				            <div class="col-sm-6">
				               	<textarea rows="4" cols="50" name="note_text"></textarea>
				            </div>
				       </div>
						 <div class="form-group">
				                <div class="col-sm-offset-3 col-sm-6">
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
        </div>
    </div>

</div>
</form>