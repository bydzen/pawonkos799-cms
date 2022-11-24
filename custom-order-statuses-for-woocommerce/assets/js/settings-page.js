

(function($) {
    
    // Converts string to slug
    function randomString(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
          result += characters.charAt(Math.floor(Math.random() * 
     charactersLength));
       }
       return result;
    }
    function convertToSlug(Text) {

        var output = Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        if (output.length == 0) {
            output = randomString(10);
        }
        console.log(output);
        return output;
    }
    $(document).ready(function(){

        //Disables automatic slug generation
        $('#new-woocos-slug').click(function(){
            $(this).removeClass('unedited');
        })
        // If slug exceeds 10 characters, crops it (slugs need to be 20 characters at max, but slug extensions used in other parts of the  plugin limits it to 10)
        $("#new-woocos-slug").keyup(function(e){
            var slug = convertToSlug($(this).val());
            if (slug.length > 17) {
                slug = slug.substring(0, 17);
                $('#new-woocos-slug-message').css('display', 'block');
            }
            $(this).val(slug);
        })
        // Automatically generates slug
        $('#new-woocos-title').keyup(function(e){
            if(!$('#new-woocos-slug').hasClass('unedited')) {
                return;
            }
            var slug = convertToSlug($(this).val());
            if (slug.length > 17) {
                slug = slug.substring(0, 17);
                $('#new-woocos-slug-message').css('display', 'block');
            }
            $('#new-woocos-slug').val(slug);
        })
        // Removes custom order status if available
        $('.remove-woocos-item').click(function(){
            var item_index = $(this).parent().parent().parent().data('index');
            var item_title = $(this).parent().parent().find('h4').html();
            var item = $(this).parent().parent().parent();
            $.ajax({
                url: woocos_ajax_object.ajaxurl,
                method: "POST",
                data: {
                    action: 'remove_woocos_item_ajaxPost',
                    index: item_index,
                    title: item_title,
                },
                success: function(result){
                    if(!result.includes('Break')){
                        item.remove();
                    }
                    $('.woocos-removed-message').find('p').html(result.toString().substring(0, result.length - 1).replace('Break', ''));
                },
                error: function(msg){
                }
            })
        })
        // Allows to update status if some edits were made
        $('.woocos-status-options input').change(function(){
            $(this).parent().parent().find('.woocos-update-item').removeClass('disabled')
        })
        const { __, _x, _n, sprintf } = wp.i18n;
        // Expands and minimzes custom order status settings item
        $('.woocos-expand-status').click(function(){
            $(this).parent().parent().parent().find('.woocos-status-options').toggleClass('expanded');
            $(this).toggleClass('expanded');
            var expanded = null;
            if ($(this).parent().parent().parent().find('.woocos-status-options').hasClass('expanded')){
                // $(this).html(__('Minimize', 'custom-order-statuses-for-woocommerce'))
                expanded = 1;
            } else {
                // $(this).html(__('Expand', 'custom-order-statuses-for-woocommerce'))
            }
            var button_text = $(this);
            var item_index = $(this).parent().parent().parent().find('form').data('index');
            $.ajax({
                url: woocos_ajax_object.ajaxurl,
                method: "POST",
                data: {
                    action: 'expand_woocos_item_ajaxPost',
                    index: item_index,
                    expanded: expanded,
                },
                success: function(result){
                    button_text.html(result.toString().substring(0, result.length - 1));
                    
                },
                error: function(res) {
                }
            })
        })
        // Updates custom order status
        $('.woocos-update-item').click(function(e){
            e.preventDefault();
            var update_form = $(this).parent().parent();
            var title = update_form.find('.woocos-title-input').val()
            if(title.length === 0){
                alert('Title for custom order status cannot be empty');
                return;
            }
            var bulk = 0;
            if (update_form.find('.woocos-bulk-input').is(':checked')) {
                bulk = 1;
            }
            var allow_emails = update_form.find('.woocos-allow-emails').prop('checked');
            var item_index = update_form.data('index');

            var html_title = update_form.parent().parent().find('h4');
            $.ajax({
                url: woocos_ajax_object.ajaxurl,
                method: 'POST',
                data: {
                    action: 'update_woocos_item_ajaxPost',
                    index: item_index,
                    new_title: title,
                    new_bulk: bulk,
                    new_emails: allow_emails ? 1 : null
                },
                success: function(result){
                    html_title.html(title);
                    update_form.find('.woocos-update-message').find('p').html(result.toString().substring(0, result.length - 1));
                },
                error: function(msg){ 
                }
            })
        })
    });
    
    

})(jQuery);
