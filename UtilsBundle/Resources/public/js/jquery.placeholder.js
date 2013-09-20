(function( $ ) {
  $.fn.placeholder = function() {
    var placeholderSupported = !!( 'placeholder' in document.createElement('input') );

    this.each(function () {
        if (!placeholderSupported)
        {
            $(this).find("[placeholder]").focus(function()
            {
                if ($(this).val() == $(this).attr('placeholder'))
                {
                    $(this).removeClass("placeholderActive");
                    $(this).val("");
                }
            });

            $(this).find("[placeholder]").blur(function()
            {
                if ($(this).val() == "")
                {
                    $(this).addClass("placeholderActive");
                    $(this).val($(this).attr('placeholder'));
                }
            });

            $(this).find("[placeholder]").blur();

            $(this).find("form:has([placeholder])").submit(function(){
                $('.placeholderActive', this).val('');
            });
        }
    });
  };
})( jQuery );

$(document).ready(function()
{
    $(document).placeholder();
});
