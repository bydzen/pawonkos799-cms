

(function($) {
    function bulkChangeOrders(status) {
        $.ajax({
            url: woocos_ajax_object.ajaxurl,
            method: "POST",
            data: {
                action: 'woocos_bulk_change_orders_ajaxPost',
                status: status,
            },
            success: function(result) {
                $('.woocos-deactivation-popup').find('.bulk-change-section').html(result.toString().substring(0, result.length - 1));
                $('#initiate-safe-deactivation').removeClass('disabled');
                $('#skip-safe-deactivation').parent().remove();
                $('.woocos-show-more-orders').click(function(e){
                    e.preventDefault();
                    $(this).parent().find('.more-orders').removeClass('hidden');
                    $(this).remove();
                })
            }
        })
    }
    function addModal(){
        var link = $('#the-list').find('tr[data-slug="custom-order-statuses-for-woocommerce"]').find('.deactivate').find('a').attr('href');
        $.ajax({
            url: woocos_ajax_object.ajaxurl,
            method: "POST",
            data: {
                action: 'woocos_setup_deactivation_form_ajaxPost',
                deactivation_link: link,
            },
            success: function(result){
                $('#wpwrap').append(result.toString());
                $('.woocos-show-more-orders').click(function(e){
                    e.preventDefault();
                    $(this).parent().find('.more-orders').removeClass('hidden');
                    $(this).remove();
                })
                $('.woocos-deactivation-popup').find('#bulk-change').change(function(){
                    $('.woocos-deactivation-popup').find('#bulk-change-button').removeClass('disabled');
                })
                $('#initiate-safe-deactivation').parent().click(function(e){
                    if($('#bulk-change').length > 0){
                        e.preventDefault();
                    }
                })
                $('.close-woocos-popup').click(function(){
                    $('.woocos-deactivation-popup').addClass('hidden');
                })
                $('.woocos-deactivation-popup').find('#bulk-change-button').click(function(){
                    var status = $('.woocos-deactivation-popup').find('#bulk-change').val();
                    if (status === null){
                        return;
                    } else {
                        bulkChangeOrders(status);
                    }
                })
            },
            error: function(msg){
            }
        })
        
        
    }
    $(document).ready(function(){

        addModal();

        $('#the-list').find('tr[data-slug="custom-order-statuses-for-woocommerce"]').find('.deactivate').find('a').click(function(e){
            if($('.woocos-deactivation-popup').length > 0) {
                e.preventDefault();
                $('.woocos-deactivation-popup').removeClass('hidden');
            }
        })
        
        
    })

})(jQuery);
