@extends('spark::layouts.app')

@section('scripts')
<script>
var tz = '';
try {
    tz = moment.tz.guess();
} catch (e) {}
// use tz and pass it to php via ajax or in a hidden field
// index.php?tz=America/Toronto

var _defaultWhatsCookingWeek = false;
</script>

@if($defaultWeek)
<?php $redodate = date('Y-m-d', strtotime($defaultWeek)); ?>
<script type="text/javascript">
    _defaultWhatsCookingWeek = '{{$defaultWeek}}';
</script>
@endif
@endsection

@section('content')
<div id="whats_cooking" class="container">
    
    <div class="row">
        <div class="title text-center">
            <div class="weekNav prev disabled" @click="fetchNewMenu('prev')" data-week=""><i class="fa fa-chevron-left" aria-hidden="true"></i></div>
            <h1>What's Cooking the week of <span class="date"></span></h1>
            <div class="weekNav next active" @click="fetchNewMenu('next')" data-week=""><i class="fa fa-chevron-right" aria-hidden="true"></i></div>
        </div>
    </div><!-- .row -->
    <div class="row buttons padding">
        <div class="col-xs-12 filters text-center">
            <div id="veg" class="btn btn-outline veg">Vegetarian Box</div>
            <div id="omni" class="btn btn-outline omni">Omnivore Box</div>
        </div>
    </div><!-- .row -->
    <div class="row">
        
        <menu v-ref:menu transition="fade"></menu>

        <template id="menu-template">
                                
            <div id="menu">
                <div class="col-xs-12">
                    <div class="meal col-xs-12 col-sm-6 col-md-4" v-for="meal in getMenu" v-bind:class="{ 'veg':meal.isVegetarian, 'omni':meal.isOmnivore }" track-by="id">
                        <div class="inner">
                            
                            <div class="text-center">
                                <div v-if="meal.image">
                                    <a href="#" data-toggle="modal" data-target="#imagemodal-@{{ meal.id }}" v-if="clickable"><img :src="meal.image" alt="@{{ meal.menu_title }}" class="meal_image"></a>
                                    <img :src="meal.image" v-else alt="@{{ meal.menu_title }}" class="meal_image">
                                </div>
                                <img src="/img/foodpot.jpg" v-else alt="@{{ meal.menu_title }}">
                            </div>
                            <div class="text allpadding">
                                <h4>@{{ meal.menu_title }}</h4>
                                @{{ meal.menu_description }}
                                <div class="icons">
                                    <img src="/img/beef.png" v-show="meal.hasBeef" alt="Red Meat" title="Red Meat">
                                    <img src="/img/chicken.png" v-show="meal.hasPoultry" alt="Poultry" title="Poultry">
                                    <img src="/img/fish.png" v-show="meal.hasFish" alt="Fish" title="Fish">
                                    <img src="/img/lamb.png" v-show="meal.hasLamb" alt="Lamb" title="Lamb">
                                    <img src="/img/pork.png" v-show="meal.hasPork" alt="Pork" title="Pork">
                                    <img src="/img/shrimp.png" v-show="meal.hasShellfish" alt="Shellfish" title="Shellfish">
                                    <img src="/img/no_wheat.png" class="no" v-show="meal.hasNoGluten" alt="Gluten Free" title alt="Gluten Free">
                                    <img src="/img/no_nuts.png" class="no" v-show="meal.hasNuts" alt="Nut Free" title="Nut Free">
                                    <img src="/img/no_dairy.jpg" class="no" v-show="meal.noDairy" alt="Dairy Free" title="Dairy Free">
                                    <img src="/img/no_eggs.png" class="no" v-show="meal.noEgg" alt="Egg Free" title="Egg Free">
                                    <img src="/img/no_soy.png" class="no" v-show="meal.noSoy" alt="Soy Free" title="Soy Free">
                                    <img src="/img/oven.png" v-show="meal.oven" alt="Oven" title="Oven"> 
                                    <img src="/img/fry_pan.png" v-show="meal.stovetop" alt="Stovetop" title alt="Stovetop">
                                    <img src="/img/dutch_oven.png" v-show="meal.slowcooker" alt="Slow Cooker" title="Slow Cooker">
                                </div>
                            </div>

                            <input type="hidden" name="menus_id[@{{ meal.menu_delivery_date }}][]" value="@{{ meal.id }}" />
                            
                            <div id="imagemodal-@{{ meal.id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                              <div class="modal-dialog text-center">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">@{{ meal.menu_title }}</h4>
                                  </div>
                                  <div class="modal-body">
                                    <img :src="meal.image" id="imagepreview">
                                  </div>
                                </div>
                              </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
        </template>

    </div><!-- .row -->

</div>
<script>
$(function() {
    $('.filters').on('click','.btn-outline', function() {

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.meal.omni').show(); 
            $('.meal.veg').show();
        } else {
            $('.btn-outline').removeClass('active');
            $(this).addClass('active');

            if ($(this).hasClass('veg')) {
                $('.meal.omni').hide(); 
                $('.meal.veg').show(); 
            } else {
                $('.meal.veg').hide(); 
                $('.meal.omni').show();
            }
        }
        $('.meal:visible .inner').matchHeight();
    });
});
</script>
@endsection


