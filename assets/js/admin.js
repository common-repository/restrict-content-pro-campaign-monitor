/*global jQuery, document*/
/*jslint newcap: true*/
jQuery(document).ready(function ($) {

    $(".toggle-password").on('click',function() {
        // $( this ).toggleClass("dashicons dashicons-hidden");
        var inputName = $( this ).attr( 'toggle' ).toString();
        var inputObject = $( document.getElementById( inputName ) );

        if ( $( inputObject ).attr( "type" ) === "password") {
            $( inputObject ).attr( "type", "text" );
            $( this ).removeClass("dashicons dashicons-hidden");
            $( this ).addClass("dashicons dashicons-visibility");
        } else {
            $( inputObject ).attr( "type", "password" );
            $( this ).removeClass("dashicons dashicons-visibility");
            $( this ).addClass("dashicons dashicons-hidden");
        }

    });
});
