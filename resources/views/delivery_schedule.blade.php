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
     $weekNum = date("W") - date("W", strtotime(date("Y-m-01", time()))) + 1;

     if ($dayOfWeek > 0 && $dateComponents['mon'] != (int)date('n')) { 
        $calendar .= '</tr><tr>';
        $calendar .= '<td colspan="'.$dayOfWeek.'">&nbsp;</td>'; 
     } //else {
     //    $calendar .= '</tr>';
     // }
     
     $month = str_pad($month, 2, '0', STR_PAD_LEFT);

     //$today = date('Y-m-d');

     $active = '';
     $currentDay = 1;

     while ($currentDay <= $numberDays) {

        $currentDayRel = str_pad($currentDay, 2, '0', STR_PAD_LEFT);
        $date = $year.'-'.$month.'-'.$currentDayRel;
        if (strtotime($date) == strtotime("+4 days", strtotime(end($deliveryDates))) ) 
            $shortmonth = true;
        
        if ($dayOfWeek == 7) {

            $dayOfWeek = 0;
            // var_dump(date('Y-m-d H:i:s', strtotime('this week')));
            // var_dump(date('Y-m-d H:i:s', strtotime('+2 days', strtotime($date))));
            if (strtotime('this week') < strtotime('+2 days', strtotime($date)) && !isset($shortmonth))
                $calendar .= '</tr><tr>';
        }

        $tz = isset($_REQUEST['tz']) ? $_REQUEST['tz'] : 'America/Los_Angeles';

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone($tz));

        if ($date == $now->format('Y-m-d')) $active = 'active';
        else  $active = '';

        $marker = '';
        if (in_array($date,$deliveryDates) && in_array($date,$skipDates)) $marker = '<div class="fa fa-times-circle" aria-hidden="true"></div>';
        else if (in_array($date,$deliveryDates)) $marker = '<div class="fa fa-check-circle" aria-hidden="true"></div>';
        // var_dump(date('Y-m-d', strtotime("+4 days", strtotime(end($deliveryDates)))) );
        if (in_array($date,$deliveryDates))
            $calendar .= '<td class="day delivery '.$active.'" title="'.$date.'">'.$currentDay.$marker.'</td>';
        else if (strtotime('this week') < strtotime('+2 days', strtotime($date)) && strtotime($date) <= strtotime("+4 days", strtotime(end($deliveryDates))) )
            $calendar .= '<td class="day '.$active.'" title="'.$date.'">'.$currentDay.$marker.'</td>';
        
        $currentDay++;
        $dayOfWeek++;

     }

     if ($dayOfWeek != 7 && !isset($shortmonth)) { 
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
                <div class="subtitle alt">
                    @if (isset($trackingNumber))
                        Your next box will arrive on 
                            @for ($i = 0; $i < count($weeksMenus); $i++)
                                @if (!$weeksMenus[$i]->hold && strtotime($weeksMenus[$i]->date2) >= strtotime($startDate))
                                    {{ $weeksMenus[$i]->date}}, before 8pm.
                                    @break
                                @endif
                            @endfor
                        Tracking Number: {{ $trackingNumber }}
                    @else
                        <?php $nextDeliveryText = false; ?>
                        @for ($i = 0; $i < count($weeksMenus); $i++)
                            @if (!$weeksMenus[$i]->hold && strtotime($weeksMenus[$i]->date2) >= strtotime($startDate))
                                <?php $nextDeliveryText = $weeksMenus[$i]->date; ?>
                                @break
                            @endif
                        @endfor

                        @if($nextDeliveryText)
                            Your next scheduled delivery is {{$nextDeliveryText}}.
                        @else
                            You don’t have a scheduled delivery
                        @endif
                    @endif
                </div>
            </h1>
        </div>

    </div><!-- .row -->

    <div class="row">

        <div class="col-sm-3 hidden-xs">
            <?php

                $dateComponents = getdate();
                // $now = date('Y-m-d H:i:s');
                // $cutoff = date('Y-m-d H:i:s', strtotime('Wednesday this week 09:00:00'));
                // var_dump($now);
                // var_dump($cutoff);
                // if ($now > $cutoff) $weeks = 7;
                // else $weeks = 6;
                $deliveryDates = [];
                $skipDates = [];
                foreach ($weeksMenus as $weeksMenu) {
                    array_push($deliveryDates, $weeksMenu->date2);
                    if ($weeksMenu->hold) array_push($skipDates, $weeksMenu->date2);
                }
                // var_dump($deliveryDates);
                $month = [];
                $year = [];
                for ($i=0; $i<count($deliveryDates); $i++) {
                    $ddate=strtotime($deliveryDates[$i]);
                    $m = date('n',$ddate);
                    $y = date('Y',$ddate);
                    if (!in_array(date('n',$ddate), $month)) {
                        array_push($month, $m);
                        echo build_calendar($m,$y,$deliveryDates,$skipDates);
                    }
                }

            ?>
        </div>

        <div class="col-xs-12 col-sm-9 schedule">
        
        @foreach ($weeksMenus as $weeksMenu)
        <?php //var_dump($weeksMenu) ?>
            @if (strtotime($weeksMenu->date2) >= strtotime($startDate) )
            <div class="week">
                <h2><i class="fa @if ($weeksMenu->hold) fa-times-circle @else fa-check-circle @endif" aria-hidden="true"></i>{{ $weeksMenu->date }}
                    <span data-toggle="tooltip" data-placement="bottom" data-original-title="
                        @if ($weeksMenu->hold)
                            <span class='color-sec'>Skipped</span> - changeable by {{ $weeksMenu->deadline }} at 9 a.m." class="sidelink"
                        @else
                            <span class='color-prim'>Scheduled</span> - changeable by {{ $weeksMenu->deadline }} at 9 a.m.""
                        @endif
                    ><i class="icon icon-info-circled"></i></span>
                    @if ($weeksMenu->changeable == 'yes' && !$weeksMenu->hold )
                        <span class="plan_size">2 Adults
                            @if ($weeksMenu->children), {{ $weeksMenu->children }} 

                                @if ($weeksMenu->children == 1) Child
                                @else Children
                                @endif

                            @endif
                            <a href="#" class="change_children sidelink" data-date="{{ $weeksMenu->date2 }}" data-date2="{{ $weeksMenu->date3 }}" data-children="{{ $weeksMenu->children }}" data-toggle="modal" data-target="#changeChildren">(change)</a></span>
                    @endif
                    <div class="subtitle">
                        @if ($weeksMenu->changeable == 'yes' && !$weeksMenu->hold )

                            @if (count($weeksMenu->menus) > 0)
                                <a href="#" class="change_menu" @click="fetchWeekMenu('{{ $weeksMenu->date2 }}')" data-date="{{ $weeksMenu->date2 }}" data-date2="{{ $weeksMenu->date3 }}" data-dmenu="{{ $weeksMenu->menus }}" data-toggle="modal" data-target="#changeMenu">Change My Menu</a>
                            @endif
                            <span class="skip-btn">
                                <button disabled="disabled" type="button" class="btn btn-primary btn-skip" style="display:none;">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>SKIP THIS DELIVERY
                                </button>
                                <button type="button" onClick="location.href='/hold/{{ $userid }}/{{ $weeksMenu->date2 }}';" class="btn btn-primary btn-skip">SKIP THIS DELIVERY</button>
                            </span>
                        @elseif ($weeksMenu->changeable == 'yes' && $weeksMenu->hold)
                            <div class="unskip-btn">
                                <button disabled="disabled" type="button" class="btn btn-primary btn-unskip" style="display:none;">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>RECEIVE THIS DELIVERY
                                </button>
                                <button type="button" onClick="location.href='/hold/restart/{{ $userid }}/{{ $weeksMenu->date2 }}';" class="btn btn-primary btn-unskip">RECEIVE THIS DELIVERY</button>
                            </div>
                        @endif
                    </div>
                </h2>
                <div class="row">
                    @if (count($weeksMenu->menus) > 0) 
                        
                        @foreach ($weeksMenu->menus as $menu)
<?php $_menu = $menu->menu()->first(); ?>
                            <div class="col-xs-4">
                                @if($_menu->image)
                                    @if($_menu->pdf)
                                        <a href="{{$_menu->pdf}}" target="_blank"><img src="{{$_menu->image}}" /></a>
                                    @else
                                        <a href="#" data-toggle="modal" data-target="#imagemodal-{{ $_menu->id }}"><img src="{{$_menu->image}}" /></a>
                                    @endif
                                @else
                                <img height="100px" src="/img/foodpot.jpg"  class="center-block" />
                                @endif
                                <p class="caption">{{$_menu->menu_title}}<br/>
                                    <em>{{$_menu->menu_description}}</em>
                                </p>
                            </div>
                            <div id="imagemodal-{{ $_menu->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">{{ $_menu->menu_title }}</h4>
                                  </div>
                                  <div class="modal-body">
                                    <img src="{{ $_menu->image }}" id="imagepreview">
                                  </div>
                                </div>
                              </div>
                            </div>
                        @endforeach
                    
                    @elseif (count($weeksMenu->date) > 0)
                        <div class="col-xs-4">
                            <img height="100px" src="/img/foodpot.jpg"  class="center-block" />
                            <p class="caption">Still busy cooking</p>
                        </div>
                        <div class="col-xs-4">
                            <img height="100px" src="/img/foodpot.jpg"  class="center-block" />
                            <p class="caption">Still busy cooking</p>
                        </div>
                        <div class="col-xs-4">
                            <img height="100px" src="/img/foodpot.jpg"  class="center-block" />
                            <p class="caption">Still busy cooking</p>
                        </div>
                    @endif
                        <?php //var_dump($weeksMenu) ?>
                </div>
            </div>
            @endif
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
                                
                                <change-menu :fulllist="fulllist" is="change-menu"></change-menu>

                                <script type="x/templates" id="change-template">
                                    <h5 class="delivery_date padbottom"></h5>
                                    <div class="row">
                                        <div class="col-sm-4 meal text-center" v-bind:class="[meal.isNotAvailable ? '' : 'avail']" data-id="@{{ meal.id }}" v-for="meal in fullMenu">
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
                                </script>

                            </div>
                            <div class="modal-footer">

                                <p class="font16">Please select the three meals you'd like to receive.</p>
                                <a href="#" data-dismiss="modal">Cancel</a>
                                <button disabled="disabled" type="button" class="btn btn-primary" style="display:none;">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>Save changes
                                </button>
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
                        <div class="modal-body">

                            <div class="row padbottom">
                                <div class="col-sm-3" style="line-height: 42px"><b>Adults</b></div>
                                <div class="col-sm-9" style="line-height: 42px">2</div>
                            </div>
                            <div class="row padbottom">
                                <div class="col-sm-3"><b>Number of children</b></div>
                                <div class="col-sm-9">{!! Form::text('children', $weeksMenu->children, array('pattern' => '[0-9]*', 'class' => 'number')); !!}</div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">

                            <button disabled="disabled" type="button" class="btn btn-primary" style="display:none;">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>Save changes
                            </button>
                            <button type="button" data-user="{{ $userid }}" data-date="" data-children="{{$weeksMenu->children}}" class="btn btn-primary btn-children">Save changes</button>
                        </div>

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
        var deliveryDate = $(this).data('date2');
        var date_to_change = $(this).data('date');
        var numChildren = $(this).data('children');
        $('.delivery_date').text( deliveryDate );
        $('.btn-children').attr('data-date', date_to_change).attr('data-children', numChildren);
        $('#changeChildren input.number').val(numChildren);
    });
    $('.btn-children').click(function() {
        var userId = $(this).data('user');
        var deliveryDate = $(this).data('date');
        var numChildren = $(this).data('children');
        location.href='/plan/childchange/'+userId+'/'+numChildren+'/'+deliveryDate;
    });
    $('#changeChildren').on('click', '.numButton', function() {
        var numChildren = $('input[name=children]').val();
        $('.btn-children').attr('data-children', numChildren);
    });
    $('.btn-primary').click(function() {
        $(this).hide().prev('button').show();
    });
});
</script>
@endsection


