@extends('spark::layouts.app')

@section('content')
<home :menu="menu" inline-template>
    <div class="container">
        <!-- Application Dashboard -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Menu</div>

                    <div class="panel-body">
						<form action="http://onepotato.app/admin/services/invoice" method="POST">
						
							<textarea></textarea>
							<button type="submit" name="submit" value="submit" />
					
						</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
