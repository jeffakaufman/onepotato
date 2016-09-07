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
<div id="whats_cooking" class="container">
    
    <div class="row">
        <div class="col-sm-5">
            <h1>What's Cooking</h1>
        </div>
        <div class="col-sm-7">
            <div class="weekPager">
                <div class="date"></div>
                <div class="weekNav prev" @click="fetchNewMenu('prev')" data-week=""><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></div>
                <div class="weekNav next" @click="fetchNewMenu('next')" data-week=""><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></div>
            </div>
        </div>
    </div><!-- .row -->
    <div class="row buttons padding">
        <div class="col-xs-12">
            <div class="btn btn-outline veg">Vegetarian</div>
            <div class="btn btn-outline omni">Omnivore</div>
        </div>
    </div><!-- .row -->
    <div class="row">
        
        <menu v-ref:menu transition="fade"></menu>

        <template id="menu-template">
                                
            <div id="menu">

                <div class="meal col-xs-4 text-center" v-for="meal in getMenu" v-bind:class="{ 'veg':meal.isVegetarian, 'omni':meal.isOmnivore }" track-by="id">
                    <div v-if="meal.image">
                        <a href="#" data-toggle="modal" data-target="#imagemodal-@{{ meal.id }}" v-if="clickable"><img :src="meal.image" alt="@{{ meal.menu_title }}" class="meal_image"></a>
                        <img :src="meal.image" v-else alt="@{{ meal.menu_title }}" class="meal_image">
                    </div>
                    <img src="/img/foodpot.jpg" v-else alt="@{{ meal.menu_title }}">
                    <div class="font16 padding text-center">@{{ meal.menu_title }} <em>@{{ meal.menu_description }}</em></div>

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
            
        </template>

    </div><!-- .row -->

</div>
<script>
$(function() {
    $('.btn-outline').click(function() {
        $('.btn-outline').removeClass('active');
        $(this).addClass('active');

        if ($(this).hasClass('veg')) {

            $('.meal.omni').hide(); 
            $('.meal.veg').show(); 
        
        } else {
            $('.meal.veg').hide(); 
            $('.meal.omni').show();
        }
    });
});
</script>
@endsection


