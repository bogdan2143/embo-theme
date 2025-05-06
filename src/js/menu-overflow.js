(function($){
  var $navbarEnd  = $('.navbar-end'),
      $moreToggle = $navbarEnd.find('.more-toggle'),
      $moreDrop   = $moreToggle.find('.navbar-dropdown');

  function redistributeMenu(){
    var winW = $(window).width();
    if(winW < 1024){
      $moreDrop.children().insertBefore($moreToggle);
      $moreToggle.hide().removeClass('active');
      $moreDrop.hide();
      return;
    }

    var availableW = $navbarEnd.innerWidth() - $moreToggle.outerWidth(true),
        usedW      = 0;

    $moreDrop.children().insertBefore($moreToggle);
    $navbarEnd.children('a.navbar-item').show();
    $moreDrop.empty();

    $navbarEnd.children('a.navbar-item').not('.more-toggle').each(function(){
      usedW += $(this).outerWidth(true);
      if( usedW > availableW ){
        $(this).appendTo($moreDrop);
      }
    });

    if( $moreDrop.children().length ){
      $moreToggle.show();
    } else {
      $moreToggle.hide();
    }
  }

  $moreToggle.on('click', function(e){
    e.preventDefault();
    // переключаем класс и видимость списка
    $(this).toggleClass('active');
    $moreDrop.toggle();
  });

  var resizeTimer;
  $(window).on('load resize', function(){
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function(){
      redistributeMenu();
      // прячем дроп, если он открыт после перерасчёта
      $moreToggle.removeClass('active');
      $moreDrop.hide();
    }, 100);
  });
})(jQuery);