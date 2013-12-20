/**
 * Profile 
 * 
 */
 $(document).ready(function(){
    $('#update-subscription').submit(function(){
        if($('#update-payment').data('card') == 'none') {
            event.preventDefault();
            $('#payment-empty-modal').modal();
            return false;
        }
    });

    $('#dismiss-payment-warning').click(function(){
        return initStripe();
    });

    $('#payment-button').click(function(){
        return initStripe();
    });

    function initStripe() {
        var token = function(res){
            var $input = $('<input type=hidden name=paymentToken />').val(res.id);
            $('#update-payment').append($input).submit();
        };
        StripeCheckout.open({
            key: $('#update-payment').attr('data-key'),
            address:false,
            currency:'usd',
            name:'BlueRidge',
            panelLabel:'Update Payment Method',
            token:token
        });
        return false;
    }
});