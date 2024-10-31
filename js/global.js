jQuery(function($) {        
        
        $('#projectguide a[href*="#"]:not([href="#"])').click(function () {
                if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                        var target = $(this.hash);
                        target = target.length ? target : $('#projectguide [name=' + this.hash.slice(1) + ']');
                        if (target.length) {
                                $('html, body').animate({
                                        scrollTop: target.offset().top
                                }, 300);
                                return false;
                        }
                }
        });  
  
        $('h2.pg-section-title').on('click',function(){          
                var object = $(this).parents('.pg-section').find('div.pg-deep-link');                
                if (object.hasClass('pg-hidden')){
                        object.removeClass('pg-hidden');                     
                }else{
                        object.addClass('pg-hidden');                     
                }
        });        
        
        $('ul.pg-main-nav.sub-onclick li a').on('click',function(){                          
                $(this).parent('li').children('.pg-main-nav-submenu').toggle();
        });
        
});