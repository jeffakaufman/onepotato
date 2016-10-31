@extends('spark::layouts.app')

@section('scripts')
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css"/>
@endsection

@section('content')
<div id="hero">
  <div id="hero-text">
    <h2>Family Meals <span>made easy</span>
      <div class="subtitle">Organic, pre-prepped ingredients and family friendly recipes with special child pricing delivered weekly.</div>
    </h2>
    <button type="button" class="btn btn-primary" onclick="location.href='/register';">
      Get Started
    </button>
  </div>
</div>
<div class="container">
  <h3 class="slogan">The One Potato <span>difference</span></h3>

  <div class="slick slick1">
    <div class="item">
      <img src="/img/topslide1.jpg">
      <div class="text inline">
        See<br>
        how<br>
        <span>easy...</span>
      </div>
    </div>
    <div class="item"><img src="/img/topslide2.jpg"></div>
  </div>

  <div class="row cards">

    <div class="col-xs-12 col-sm-4 card">
      <img src="/img/card1.jpg">
      <div class="text">
        <h3>Family Friendly</h3>
        <div class="list">
          <div class="item">
            <div class="icon"><img src="/img/icon-pot.svg"></div>
            <div class="text"><img src="/img/logo-weelicious.png">-tested recipes</div>
          </div>
          <div class="item">
            <div class="icon"><img src="/img/icon-silverware.svg"></div>
            <div class="text">Low pricing for kids saves you money</div>
          </div>
          <div class="item">
            <div class="icon"><img src="/img/icon-cookie.svg"></div>
            <div class="text">Free cookie dough in every box!</div>
          </div>
        </div>
      </div>
    </div><!--.card-->

    <div class="col-xs-12 col-sm-4 card">
      <img src="/img/card2.jpg">
      <div class="text">
        <h3>Organic & Sustainable</h3>
        <div class="list">
          <div class="item">
            <div class="icon"><img src="/img/icon-carrot.svg"></div>
            <div class="text">Organic, non-GMO seasonal produce</div>
          </div>
          <div class="item">
            <div class="icon"><img src="/img/icon-wheat.svg"></div>
            <div class="text">Ingredients sourced from the country’s top farms</div>
          </div>
          <div class="item">
            <div class="icon"><img src="/img/icon-box.svg" style="width: 20px;"></div>
            <div class="text">Recyclable and reusable packaging</div>
          </div>
        </div>
      </div>
    </div><!--.card-->

    <div class="col-xs-12 col-sm-4 card">
      <img src="/img/card3.jpg">
      <div class="text">
        <h3>Quick & Convenient</h3>
        <div class="list">
          <div class="item">
            <div class="icon"><img src="/img/icon-clock.svg"></div>
            <div class="text">Meals ready in 30 minutes or less</div>
          </div>
          <div class="item">
            <div class="icon"><img src="/img/icon-knife.svg"></div>
            <div class="text">Fresh ingredients arrived pre-chopped and pre-measured in a refrigerated box</div>
          </div>
          <div class="item">
            <div class="icon"><img src="/img/icon-truck.svg"></div>
            <div class="text">FREE delivery</div>
          </div>
        </div>
      </div>
    </div><!--.card-->

  </div>
</div><!--.container-->


<div id="menu">
  <div class="title">
    <h3>See what our <span>families</span> are <span>loving</span></h3>
    Quick, nutritious and fresh recipes<br>
    that appeal to the whole family<br>
    <a href="/whats-cooking" class="btn btn-secondary" type="button">See This Week's Menu</a>
  </div>
  <div class="menu_slider">
    {{--@if (count($currentMenu[0]) > 0) 
      @foreach ($currentMenu[0] as $menu)
      <div class="meal">
        <div class="bg" style="background-image: url('{{$menu->image}}');">
          <div class="title">
            <h3>See what our <span>families</span> are <span>loving</span></h3>
            Quick, nutritious and fresh recipes<br>
            that appeal to the whole family<br>
            <a href="/whats-cooking" class="btn btn-secondary" type="button">See This Week's Menu</a>
          </div>
          <div class="caption">
              <h4>{{$menu->menu_title}} {{$menu->menu_description}}</h4>
          </div>
        </div>
      </div>
      @endforeach
    @endif--}}
    <div class="meal">
        <div class="caption">
            <h4>Chilaquiles with Tomatillo Sauce</h4>
        </div>
        <img sizes="(min-width: 40em) 80vw, 100vw" srcset="/img/meals/chilaquiles-s.jpg 400w, /img/meals/chilaquiles-m.jpg 800w, /img/meals/chilaquiles.jpg 1200w" alt="Chilaquiles with Tomatillo Sauce">
    </div><!--.meal-->
    <div class="meal">
        <div class="caption">
            <h4>Chicken Fajitas with Black Beans and Spanish Rice</h4>
        </div>
        <img sizes="(min-width: 40em) 80vw, 100vw" srcset="/img/meals/fajitas-s.jpg 400w, /img/meals/fajitas-m.jpg 800w, /img/meals/fajitas.jpg 1200w" alt="Chicken Fajitas with Black Beans and Spanish Rice">
    </div><!--.meal-->
    <div class="meal">
        <div class="caption">
            <h4>Pepperoni Pizza with Greek Salad</h4>
        </div>
        <img sizes="(min-width: 40em) 80vw, 100vw" srcset="/img/meals/gfpepperonipizza-s.jpg 400w, /img/meals/gfpepperonipizza-m.jpg 800w, /img/meals/gfpepperonipizza.jpg 1200w" alt="Pepperoni Pizza with Greek Salad">
    </div><!--.meal-->
    <div class="meal">
        <div class="caption">
            <h4>Slow Roasted Brisket and Potato Cheese Pierogi with Autumn Salad</h4>
        </div>
        <img sizes="(min-width: 40em) 80vw, 100vw" srcset="/img/meals/pierogi-s.jpg 400w, /img/meals/pierogi-m.jpg 800w, /img/meals/pierogi.jpg 1200w" alt="Slow Roasted Brisket and Potato Cheese Pierogi with Autumn Salad">
    </div><!--.meal-->
    <div class="meal">
        <div class="caption">
            <h4>Roast Chicken with Spring Vegetables and Polenta Fries</h4>
        </div>
        <img sizes="(min-width: 40em) 80vw, 100vw" srcset="/img/meals/roastedchicken-s.jpg 400w, /img/meals/roastedchicken-m.jpg 800w, /img/meals/roastedchicken.jpg 1200w" alt="Roast Chicken with Spring Vegetables and Polenta Fries">
    </div><!--.meal-->
    <div class="meal">
        <div class="caption">
            <h4>Crispy Salmon Rice Bowls</h4>
        </div>
        <img sizes="(min-width: 40em) 80vw, 100vw" srcset="/img/meals/salmonbowls-s.jpg 400w, /img/meals/salmonbowls-m.jpg 800w, /img/meals/salmonbowls.jpg 1200w" alt="Crispy Salmon Rice Bowls">
    </div><!--.meal-->
    <div class="meal">
        <div class="caption">
            <h4>Vegetarian Chili with Cheesy Cornbread</h4>
        </div>
        <img sizes="(min-width: 40em) 80vw, 100vw" srcset="/img/meals/vegetarianchili-s.jpg 400w, /img/meals/vegetarianchili-m.jpg 800w, /img/meals/vegetarianchili.jpg 1200w" alt="Vegetarian Chili with Cheesy Cornbread">
    </div><!--.meal-->
   
  </div><!--.slider-->
</div>

<div style="background: #daf8fa;">
  <div class="container">

    <h3 class="slogan green">See why we’re <span>responsible</span></h3>

    <div class="slick slick2">
      <div class="item">
        <img src="/img/bottomslide1.jpg">
        <div class="text">
          <h4>good for your family...</h4>
          Sourcing the highest quality organic produce is part of One Potato mission and the essential ingredient for everyone of our recipes. That’s why we work with some of the country’s best farms and purveyors of fish, meat and poultry who share our values of sustainability and humane treatment of animals.
        </div>
      </div>
      <div class="item">
        <img src="/img/bottomslide2.jpg">
        <div class="text">
          <h4>...and our planet</h4>
          In addition to working with farmers that treat our soil, water and animals with respect and care, One Potato makes every effort to ensure that the packaging is as environmentally responsible as possible. Everything we ship in is recyclable and/or biodegradable. Our boxes are made from recyclable, biodegrable 98% post-consumer cardboard, our insulation is made from recycled denim jeans (really!) and completely recyclable, our ice packs and containers are also completely recyclable.  <b><a href="/recycling">Read more...</a></b>
        </div>
      </div>
    </div>
    
  </div><!--.container -->
</div>

<div class="hero3">
  <img src="/img/hero3.jpg">
  <div class="text">
    <h4>We cook with <img src="/img/logo-weelicious.png"></h4>
    Many of our recipes come from the pages of One Potato co-founder Catherine McCord's best selling Weelicious cookbooks and website. Families around the world rely on Catherine for delicious, easy, and healthy family meals.
  </div>
</div>

<div class="container">
  <h3 class="slogan">See what our <span>families</span> are <span>saying</span></h3>
  <div class="row testimonials">

    <div class="col-xs-12 col-sm-4 testimonial">
      <img src="/img/testimonial1.jpg">
      <div class="text">
        <div class="quote open-quote">&#8220;</div>
        “Another winner from @onepotatobox! This is the spring vegetable pasta with bruschetta. We seriously haven’t struck out yet and I love introducing Grayson to new flavors and foods!”
        <div class="byline">&#8212;@eatliverun</div>
        <div class="quote close-quote">&#8221;</div>
      </div>
    </div><!--.testimonial-->

    <div class="col-xs-12 col-sm-4 testimonial">
      <img src="/img/testimonial2.jpg">
      <div class="text">
        <div class="quote open-quote">&#8220;</div>
        “My pickiest eater just devoured 2 servings of Arepas with Slow Cooked Pulled Chicken from #onepotatobox WITH SLAW. He says it was the best meal ever. I’m in shock with happiness.”

        <div class="byline">&#8212;@ramonarose</div>
        <div class="quote close-quote">&#8221;</div>
      </div>
    </div><!--.testimonial-->

    <div class="col-xs-12 col-sm-4 testimonial">
      <img src="/img/testimonial3.jpg">
      <div class="text">
        <div class="quote open-quote">&#8220;</div>
        So excited because our first @onepotatobox by @weelicious arrived today! Kids picked pizza balls for dinner, so that’s what’s up first! Between moving, working two full time jobs and being a mama and wife I needed this in my life!

        <div class="byline">&#8212;@zozubaby</div>
        <div class="quote close-quote">&#8221;</div>
      </div>
    </div><!--.testimonial-->

  </div>
</div><!--.container-->

<div id="footer-action">
  <div class="container">
    <div class="col-md-8 col-md-offset-2">
      <h4>Let’s cook <span>together</span>!</h4>
      Try One Potato today with no commitment.<br>
      Skip or cancel anytime.<br>
      <button type="button" class="btn btn-secondary" onclick="location.href='/register';">
        Get Started
      </button>
    </div>
  </div>
</div>
<script src="/js/picturefill.min.js" async></script>
@endsection