@extends('spark::layouts.app')

@section('scripts')
<script>
    // these are labels for the days of the week
days_labels = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

// these are human-readable month name labels, in order
monthNames = ['January', 'February', 'March', 'April',
                     'May', 'June', 'July', 'August', 'September',
                     'October', 'November', 'December'];
deliveryStatus = [true, true, false, true, true, true, false, true, true];

function Calendar(month, year) {
  this.month = month;
  this.year  = year;
  this.html = '';
}

Calendar.prototype.generateHTML = function(){

  // get first day of month
  var firstDay = new Date(this.year, this.month, 1);
  var startingDay = firstDay.getDay();

  // find number of days in month
  var dd = new Date(this.year, this.month+1, 0);
  var monthLength = dd.getDate();
//console.log(this.month);
  
  // do the header
  var monthName = monthNames[this.month];
  var html = '<table class="month">';
  html += '<tr><th class="month_name" colspan="7">';
  html +=  monthName;
  html += '</th></tr>';
  html += '<tr>';
  for(var i = 0; i <= 6; i++ ){
    html += '<td class="day_name">';
    html += days_labels[i];
    html += '</td>';
  }
  html += '</tr><tr class="week week1" data-week="1">';

  var started = false;
  var blank = true;
  // fill in the days
  var day = 1;
  // this loop is for is weeks (rows)
  var weeks = Math.ceil((monthLength + startingDay) / 7);
  today = current_date.getDate() + 7;
  current_month = current_date.getMonth() + 1;
  this.month = this.month + 1;
  //console.log(current_month);
  //console.log(current_month);
  for (var i = 0; i < weeks; i++) {
    // this loop is for weekdays (cells)
    //html += '<tr class="week week'+i+'">';
    for (var j = 0; j <= 6; j++) { 

        if (day <= monthLength && (i > 0 || j >= startingDay)) blank = false;

        if (day == today && current_month == this.month && !blank)
            html += '<td class="day day'+day+' active" data-date="'+this.month+'-'+day+'">';
        else if ( (day == 1 && !blank) || (day <= monthLength && started) )
            html += '<td class="day day'+day+'" data-date="'+this.month+'-'+day+'">';
        else if (!started || day > monthLength)
            html += '<td>';

        if (day <= monthLength && (i > 0 || j >= startingDay)) {
            started = true;
            html += day;
            day++;
        } else {
            started = false;
        }
        if (j == 2 && !blank && (day <= monthLength || j >= startingDay) && deliveryStatus[i]) {
            html += '<div class="fa fa-check-circle" aria-hidden="true"></div>';
        } else if (j == 2 && !blank && (day <= monthLength || j >= startingDay) && !deliveryStatus[i]) {
            html += '<div class="fa fa-times-circle" aria-hidden="true"></div>';
        }
        html += '</td>';
    }
    var x = i + 2;
    if (x > weeks) html += '</tr>';
    else html += '</tr><tr class="week week'+x+'" data-week="'+x+'">';
    //html += '</tr><tr>';
  }
  html += '</table>';

  this.html = html;
}

Calendar.prototype.getHTML = function() {
  return this.html;
}

$(function() {
    $('.change_menu, .change_children').click(function () {
        var deliveryMonth = $(this).data('month') - 1;
        var deliveryDay = $(this).data('day');
        var deliveryYear = $(this).data('year');
        $('.delivery_date').text( monthNames[deliveryMonth] + ' ' + deliveryDay + ', ' + deliveryYear );
    });
    $('.subtitle').on('click', '.btn-skip', function(){
        var deliveryMonth = $(this).data('month');
        var deliveryDay = $(this).data('day');
        var skipDay = deliveryMonth + '-' + deliveryDay;
        $('table.month .day[data-date='+skipDay+']').find('.fa').removeClass('fa-check-circle').addClass('fa-times-circle');
        $(this).parents('.week').find('h2 .fa').removeClass('fa-check-circle').addClass('fa-times-circle');
        $(this).text('UNSKIP THIS DELIVERY').removeClass('btn-skip').addClass('btn-unskip');
        $('.change_menu').hide();
        return true;
    });
    $('.subtitle').on('click', '.btn-unskip', function(){
        var deliveryMonth = $(this).data('month');
        var deliveryDay = $(this).data('day');
        var deliveryYear = $(this).data('year');
        var unskipDay = deliveryMonth + '-' + deliveryDay;
        $('table.month .day[data-date='+unskipDay+']').find('.fa').removeClass('fa-times-circle').addClass('fa-check-circle');
        $(this).parents('.week').find('h2 .fa').removeClass('fa-times-circle').addClass('fa-check-circle');
        $(this).text('SKIP THIS DELIVERY').removeClass('btn-unskip').addClass('btn-skip');
        $('<a href="#" class="change_menu" data-month="'+deliveryMonth+'" data-day="'+deliveryDay+'" data-year="'+deliveryYear+'" data-toggle="modal" data-target="#changeMenu">Change My Menu</a>').insertBefore( $(this) );
        return true;
    });
    $('#changeMenu .meal').each(function() {
        if ($(this).hasClass('select')) {
            $(this).click(function() {
                $('#changeMenu .meal').removeClass('selected');
                $(this).removeClass('select').addClass('selected');
            });
        }
    });
});
</script>
@endsection

@section('content')
<div class="container">
    
    <div class="row">

        <div class="col-xs-12">
            <h1>Delivery Schedule</h1>
            <div class="font16" style="position: absolute; right: 0; top: 34px;">If scheduled, your next box will arrive on {{$weeksMenus[0]->date}}, before 8pm</div>
        </div>

    </div><!-- .row -->

    <div class="row">

        <div class="col-sm-3">
            <script type="text/javascript">
                var thisMonth, nextMonth, thisYear, nextYear;
                current_date = new Date(); 
                thisMonth = current_date.getMonth();
                thisYear = current_date.getFullYear();
                
                if (thisMonth == 12) {
                    nextMonth = 1;
                    nextYear = nextYear + 1;
                } else {
                    nextMonth = thisMonth + 1;
                    nextYear = thisYear;
                }
                var cal = new Calendar(thisMonth,thisYear);
                var cal2 = new Calendar(nextMonth,nextYear);
                cal.generateHTML();
                cal2.generateHTML();
                document.write(cal.getHTML());
                document.write(cal2.getHTML());
            </script>
        </div>

        <div class="col-sm-9 schedule">
        
        @foreach ($weeksMenus as $weeksMenu)
            @if (count($weeksMenu->menus) > 0) 
            <div class="week">
                <?php $status = 'on' ?>
                <h2><i class="fa @if ($status == 'off') fa-times-circle @else fa-check-circle @endif" aria-hidden="true"></i>{{ $weeksMenu->date }}
                    <span class="plan_size">{{ $userProduct->productDetails()->FamilySize }} <a href="#" class="change_children sidelink" data-month="8" data-day="3" data-year="2016" data-toggle="modal" data-target="#changeChildren">(change)</a></span>
                    <div class="subtitle">
                        @if ($status == 'shipped')<div class="shipped"><i class="icon icon-truck"></i> Shipped</div>@endif
                        @if ($status == 'on')
                            <a href="#" class="change_menu" data-month="8" data-day="3" data-year="2016" data-whatscooking="{{ $weeksMenu->all }}" data-toggle="modal" data-target="#changeMenu">Change My Menu</a>
                        @endif
                        @if ($status == 'off')
                            <button class="btn btn-primary btn-unskip" data-month="8" data-day="3" data-year="2016">UNSKIP THIS DELIVERY</button>
                        @elseif ($status != 'shipped')
                            <button class="btn btn-primary btn-skip" data-month="8" data-day="3" data-year="2016">SKIP THIS DELIVERY</button>
                        @endif
                    </div>
                </h2>
	                <div class="row">
                	@foreach ($weeksMenu->menus as $menu)
                    	<div class="col-sm-4">
                    		@if($menu->menu()->first()->image)
                    	    <img src="{{$menu->menu()->first()->image}}" />
                    	    @else
                    	    <img height="100px" src="/img/foodpot.jpg"  class="center-block" />
							@endif
                    	    <p class="caption">{{$menu->menu()->first()->menu_title}}<br/>
                    	    	<i>{{$menu->menu()->first()->menu_description}}</i></p>
                    	</div>
                	@endforeach
    	            </div>
            </div>
            @else
            <div class="week">
                <?php $status = 'on' ?>
                <h2><i class="fa @if ($status == 'off') fa-times-circle @else fa-check-circle @endif" aria-hidden="true"></i>  {{ $weeksMenu->date }}
                    <span class="plan_size">{{ $userProduct->productDetails()->FamilySize }} <a href="#" class="change_children sidelink" data-month="8" data-day="3" data-year="2016" data-toggle="modal" data-target="#changeChildren">(change)</a></span>
                    <div class="subtitle">
                        @if ($status == 'shipped')<div class="shipped"><i class="icon icon-truck"></i> Shipped</div>@endif
                        @if ($status == 'on')
                            <a href="#" class="change_menu" data-month="8" data-day="3" data-year="2016" data-toggle="modal" data-target="#changeMenu">Change My Menu</a>
                        @endif
                        @if ($status == 'off')
                            <button class="btn btn-primary btn-unskip" data-month="8" data-day="3" data-year="2016">UNSKIP THIS DELIVERY</button>
                        @elseif ($status != 'shipped')
                            <button class="btn btn-primary btn-skip" data-month="8" data-day="3" data-year="2016">SKIP THIS DELIVERY</button>
                        @endif
                    </div>
                </h2>
                <div class="row">
                    <div class="col-sm-4">
                        <img src="/img/foodpot.jpg"  class="center-block" />
                        <p class="caption">Still Cooking!</p>
                    </div>
                    <div class="col-sm-4">
                        <img src="/img/foodpot.jpg" class="center-block" />
                        <p class="caption">Still Cooking!</p>
                    </div>
                    <div class="col-sm-4">
                        <img src="/img/foodpot.jpg" class="center-block"  />
                        <p class="caption">Still Cooking!</p>
                    </div>
                </div>
            </div>
    	    @endif
        @endforeach           

            <div id="changeMenu" class="modal fade" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">SELECT MEALS</h4>
                        </div>
                        <div class="modal-body">
                            
                            <h5 class="delivery_date padbottom"></h5>
                            <div class="row">
                                <div class="col-sm-4 meal select">
                                    <img src="/img/op_06-Salmon-Cooked_0037_f1.jpg" />
                                    <p class="caption">Salmon in Parchment with Brown Rice</p>
                                </div>
                                <div class="col-sm-4 meal"><div class="not_avail">No longer available</div>
                                    <img src="/img/op_08-Skewers_0044_p1.jpg" />
                                    <p class="caption">Persian Chicken Kebabs with Quinoa and Cucumber, Tomato and Avocado Salad</p>
                                </div>
                                <div class="col-sm-4 meal selected">
                                    <img src="/img/op_03-Chicken_0020_p.jpg" />
                                    <p class="caption">Roast Chicken with Mediterranean Chickpea Fries</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 meal select">
                                    <img src="/img/vegetariantaquitoshires-008.jpg" />
                                    <p class="caption">Vegetarian Taquitos with Rice and Cucumber Avocado Salsa</p>
                                </div>
                                <div class="col-sm-4 meal select">
                                    <img src="/img/gingerbeefsatay-012.jpg" />
                                    <p class="caption">Ginger Beef Satay with Coconut Rice</p>
                                </div>
                                <div class="col-sm-4 meal select">
                                    <img src="/img/gnocchipesto-009.jpg" />
                                    <p class="caption">Gnocchi with Pesto and Zucchini Coins</p>
                                </div>
                            </div>

                            <div id="test">test</div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div id="changeChildren" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">DETAILS FOR <span class="delivery_date"></span></h4>
                        </div>
                     
                        <form method="POST" action="" accept-charset="UTF-8" class="meals">
                        
                                {{ csrf_field() }}
                    
                        <input type="hidden" name="user_id" value="" />
                        <input type="hidden" name="update_type" value="meals" />
                        <div class="modal-body">

                            <div class="row padbottom">
                                <div class="col-sm-3" style="line-height: 42px"><b>Adults</b></div>
                                <div class="col-sm-9" style="line-height: 42px">2</div>
                            </div>
                            <div class="row padbottom">
                                <div class="col-sm-3"><b>Number of children</b></div>
                                <div class="col-sm-9">{!! Form::text('children', '2', array('pattern' => '[0-9]*', 'class' => 'number')); !!}</div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

        </div>

    </div><!-- .row -->

</div>
<script>
$('#changeMenu').on('show.bs.modal', function(e) {
    
    $("#changeMenu #test").text( $(e.relatedTarget).data('whatscooking'); );
</script>
@endsection


