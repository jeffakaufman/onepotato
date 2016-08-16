@extends('spark::layouts.app')

@section('scripts')
<script>
var tz = '';
try {
    tz = moment.tz.guess();
} catch (e) {}
// use tz and pass it to php via ajax or in a hidden field
// index.php?tz=America/Toronto
</script>
@endsection

@section('content')
<?php
function build_calendar($month,$year,$deliveryDates,$skipDates) {

     $daysOfWeek = array('S','M','T','W','T','F','S');

     $firstDayOfMonth = mktime(0,0,0,$month,1,$year);

     // How many days does this month contain?
     $numberDays = date('t',$firstDayOfMonth);
     $dateComponents = getdate($firstDayOfMonth);

     $monthName = $dateComponents['month'];
     $dayOfWeek = $dateComponents['wday'];

     $calendar = '<table class="month">';
     $calendar .= '<caption class="month_name">'.$monthName . '</caption>';
     $calendar .= '<tr>';

     // Create the calendar headers

     foreach($daysOfWeek as $day) {
          $calendar .= '<td class="day_name">'.$day.'</td>';
     } 

     $currentDay = 1;

     $calendar .= "</tr><tr>";

     if ($dayOfWeek > 0) { 
          $calendar .= '<td colspan="'.$dayOfWeek.'">&nbsp;</td>'; 
     }
     
     $month = str_pad($month, 2, '0', STR_PAD_LEFT);

     //$today = date('Y-m-d');

     $active = '';
  
     while ($currentDay <= $numberDays) {

        if ($dayOfWeek == 7) {

            $dayOfWeek = 0;
            $calendar .= '</tr><tr>';

        }

        $currentDayRel = str_pad($currentDay, 2, '0', STR_PAD_LEFT);
          
        $date = $year.'-'.$month.'-'.$currentDayRel;

        $tz = isset($_REQUEST['tz']) ? $_REQUEST['tz'] : 'America/Los_Angeles';

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone($tz));
        
        if ($date == $now->format('Y-m-d')) $active = 'active';
        else  $active = '';

        $marker = '';
        if (in_array($date,$deliveryDates) && in_array($date,$skipDates)) $marker = '<div class="fa fa-times-circle" aria-hidden="true"></div>';
        else if (in_array($date,$deliveryDates)) $marker = '<div class="fa fa-check-circle" aria-hidden="true"></div>';
        
        if (in_array($date,$deliveryDates))
            $calendar .= '<td class="day delivery '.$active.'" title="'.$date.'">'.$currentDay.$marker.'</td>';
        else 
            $calendar .= '<td class="day '.$active.'" title="'.$date.'">'.$currentDay.$marker.'</td>';
 
        $currentDay++;
        $dayOfWeek++;

     }

     if ($dayOfWeek != 7) { 
          $remainingDays = 7 - $dayOfWeek;
          $calendar .= "<td colspan='$remainingDays'>&nbsp;</td>"; 
     }
     $calendar .= "</tr>";
     $calendar .= "</table>";

     return $calendar;

}

?> 
<div id="delivery_schedule" class="container">
    
    <div class="row">

        <div class="col-xs-12">
            <h1>Delivery Schedule
                <div class="subtitle alt">If scheduled, your next box will arrive on {{$weeksMenus[0]->date}}, before 8pm</div>
            </h1>
        </div>

    </div><!-- .row -->

    <div class="row">

        <div class="col-sm-3 hidden-xs">
            <?php

                $dateComponents = getdate();
                $deliveryDates = [];
                $skipDates = [];
                foreach ($weeksMenus as $weeksMenu) {
                    array_push($deliveryDates, $weeksMenu->date2);
                    if (count($weeksMenu->menus) == 0) array_push($skipDates, $weeksMenu->date2);
                }
                //var_dump($skipDates);
                $month = $dateComponents['mon'];                
                $year = $dateComponents['year'];
                $month2 = $dateComponents['mon'] + 1;    
                if ($month == 12)            
                    $year2 = $dateComponents['year'] + 1;
                else $year2 = $year;

                echo build_calendar($month,$year,$deliveryDates,$skipDates);
                echo build_calendar($month2,$year2,$deliveryDates,$skipDates);

            ?>
        </div>

        <div class="col-xs-12 col-sm-9 schedule">
        
        @foreach ($weeksMenus as $weeksMenu)
            <?php $toolate = false; 
                $ddate = new DateTime($weeksMenu->date2.' 09:00:00'); 
                $tz = isset($_REQUEST['tz']) ? $_REQUEST['tz'] : 'America/Los_Angeles';
                
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone($tz));
                
                $ddate->sub(new DateInterval('P7D'));
                if ($ddate < $now) $toolate = true; ?>
            <div class="week">
                <h2><i class="fa @if (count($weeksMenu->menus)) fa-check-circle @else fa-times-circle @endif" aria-hidden="true"></i>{{ $weeksMenu->date }}
                    <span class="plan_size">2 Adults
                        @if ($userProduct->productDetails()->ChildSelect), {{ $userProduct->productDetails()->ChildSelect }} Children @endif
                        <a href="#" class="change_children sidelink" data-date="{{ $weeksMenu->date }}" data-children="{{ $userProduct->productDetails()->ChildSelect }}" data-toggle="modal" data-target="#changeChildren">(change)</a></span>

                    <div class="subtitle">
                        @if (count($weeksMenu->menus) > 0 && !$toolate) 
                            <a href="#" class="change_menu" @click="fetchWeekMenu('{{ $weeksMenu->date2 }}')" data-date="{{ $weeksMenu->date2 }}" data-date2="{{ $weeksMenu->date3 }}" data-dmenu="{{ $weeksMenu->menus }}" data-toggle="modal" data-target="#changeMenu">Change My Menu</a>
                            {!! Form::open(array('url' => '/delivery-schedule')) !!}
                                {!! Form::hidden('date_to_skip', $weeksMenu->date2) !!}
                                {!! Form::submit('SKIP THIS DELIVERY', array('class' => 'btn btn-primary btn-skip')) !!}
                            {!! Form::close() !!}
                        @endif
                    </div>
                </h2>
                <div class="row">
                    @if (count($weeksMenu->menus) > 0) 

                        @foreach ($weeksMenu->menus as $menu)
                            <div class="col-xs-4">
                                @if($menu->menu()->first()->image)
                                <img src="{{$menu->menu()->first()->image}}" />
                                @else
                                <img height="100px" src="/img/foodpot.jpg"  class="center-block" />
                                @endif
                                <p class="caption">{{$menu->menu()->first()->menu_title}}<br/>
                                    <em>{{$menu->menu()->first()->menu_description}}</em>
                                </p>
                            </div>
                        @endforeach

                    @elseif (count($weeksMenu->all) > 0)

                        <delivery prefs="{{ $prefs }}" delivery="{{ $weeksMenu->date2 }}"></delivery>
                        
                        <template id="delivery-template">
                            <form method="POST" action="/delivery-schedule" role="form" accept-charset="UTF-8" class="unskip-btn">
                                {{ csrf_field() }}
                                <input name="date_to_unskip" type="hidden" value="{{ $weeksMenu->date2 }}">
                                <input name="menu_id[]" type="hidden" v-for="meal in filteredMenu" value="@{{ meal.id }}">
                                <button class="btn btn-primary btn-unskip" type="submit" :disabled="form.busy">
                                    <span v-if="form.busy">
                                        <i class="fa fa-btn fa-spinner fa-spin"></i>UNSKIP THIS DELIVERY
                                    </span>
                                    <span v-else>
                                        UNSKIP THIS DELIVERY
                                    </span>
                                </button>
                            </form>
                            
                            <div class="menu" class="col-xs-12" data-date="{{ $weeksMenu->date2 }}">
                                <div class="col-sm-4 text-center" v-for="meal in filteredMenu" data-menu="@{{ meal.id }}">

                                    <img :src="meal.image" v-if="meal.image" alt="@{{ meal.menu_title }}">
                                    <img src="/img/foodpot.jpg" v-else alt="@{{ meal.menu_title }}">
                                    
                                    <p class="caption">@{{ meal.menu_title }}<br>
                                        <em>@{{ meal.menu_description }}</em>
                                    </p>
                                </div>
                            </div>
                        </template>
                    @endif
                        <?php //var_dump($weeksMenu) ?>
                </div>
            </div>
        @endforeach

            <div id="changeMenu" class="modal fade" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST" action="/delivery-schedule" role="form" accept-charset="UTF-8">
                                {{ csrf_field() }}
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">SELECT MEALS</h4>
                            </div>
                            <div class="modal-body">
                                
                                <change-menu :fulllist="fulllist"></change-menu>

                                <template id="change-template">
                                    <h5 class="delivery_date padbottom"></h5>
                                    <div class="row">
                                        <div class="col-sm-4 meal text-center" data-id="@{{ meal.id }}" v-for="meal in fullMenu">
                                            <div v-if="meal.isNotAvailable == 1" class="not_avail">No longer available</div>
                                            <img :src="meal.image" v-if="meal.image" alt="@{{ meal.menu_title }}">
                                            <img src="/img/foodpot.jpg" v-else alt="@{{ meal.menu_title }}">
                                            <p class="caption">@{{ meal.menu_title }}<br>
                                                <em>@{{ meal.menu_description }}</em>
                                            </p>
                                        </div>
                                    </div>
                                    <input name="date_to_change" type="hidden" value="">
                                    <input name="menu_id[]" class="menu_id menu_id-0" type="hidden" value="">
                                    <input name="menu_id[]" class="menu_id menu_id-1" type="hidden" value="">
                                    <input name="menu_id[]" class="menu_id menu_id-2" type="hidden" value="">
                                </template>

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
                                <div class="col-sm-9">{!! Form::text('children', $userProduct->productDetails()->ChildSelect, array('pattern' => '[0-9]*', 'class' => 'number')); !!}</div>
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
$(function() {
    $('.change_menu').click(function () {
        var deliveryDate = $(this).data('date');
        var deliveryDate2 = $(this).data('date2');
        
        $('.delivery_date').text( deliveryDate2);
        $('#changeMenu input[name="date_to_change"]').val(deliveryDate);
        var menus = $(this).data('dmenu');
        for (i=0; i<menus.length; i++) {
            $('#changeMenu input.menu_id-'+i).val(menus[i].menus_id);
        }
    });
    $('.change_children').click(function () {
        var deliveryDate = $(this).data('date');
        $('.delivery_date').text( deliveryDate2 );
    });
});
</script>
@endsection


