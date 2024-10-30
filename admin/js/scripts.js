
jQuery(document).ready(function($) {
    $('#webservice_options').submit(function( event ) {
        var radiozonesValues = [];
        $('.radiozones:checked').each(function() {
            if(isInArray($(this).val(), radiozonesValues)){
                alert('Selecione métodos diferentes nas zonas.');
                event.preventDefault();
            }
            radiozonesValues.push($(this).val());
        });
    });

    function isInArray(value, array) {
        return array.indexOf(value) > -1;
    }
})