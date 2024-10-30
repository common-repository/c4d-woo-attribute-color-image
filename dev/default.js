jQuery(function($){
    $(document).ready(function(){
      $('body').on('click', '.c4d-woo-aci__color .c4d-woo-aci__color_item', function(index){
        var id = $(this).attr('data-id'),
        parent = $(this).parents('.c4d-woo-aci__color').attr('data-id'),
        image = $('.c4d-woo-aci__image[data-id='+ parent +'] [data-id='+ id + ']');
        $(this).toggleClass('active').siblings().removeClass('active');
        image.html($('<img/>', {
          src: image.attr('data-src'),
          alt: image.attr('data-alt')
        })).toggleClass('active').siblings().removeClass('active');
      });
    });
});
