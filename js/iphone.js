


var data = $.getJSON( "responsev3.json", function( data ) {
    console.log( data );

    var size = Object.keys( data ).length;
    var r1   = randRange( size - 1 );
    for ( var i = 0; i < size; i++ ) {
        var obj = data[ i ];
        for ( var key in obj ) {
            var attrName  = key;
            var attrValue = obj[ key ];
            console.log( "data[", i, "][", attrName, "] => ", attrValue );
        }
    }



    console.log("Today's random number is: ", r1);
    $("<div id=\"review-date-inner\">"+data[r1]['date']+"</div>").insertAfter("#review-date");
    $("<div id='reviewer-infobox'><span id='person'>" + data[r1]['name'] +  ", </span><span id='location'>" + data[r1]['city'] + "</span></div>").insertAfter("#reviewer-name");
    $("<img src =\""+data[r1]['avatar']+"\" id='avatar'>").insertAfter("#reviewer-avatar");
    $("<img id=\"stars\" src ='img/YelpStars5.png' >").insertAfter("#star-rating");
    $("<p class=\"review-body\">"+data[r1]['content']+"</p>").insertAfter("#review-content");
});


var notDone = true;
var clicked = false;
window.setTimeout(function() { $('div#wrapper' ).css('visibility', 'visible')}, 1500);

$(document ).ready(function() {
    $( document ).scroll( function () {
                              if ( notDone ) {
                                  window.setTimeout(function() {
//        $( "<div id=\'call-screen\'></div>" ).insertAfter( $( "#visible-screen" ) );

                                      $( "#broken" ).remove();

                                      $( 'div#inner-iphone-text' ).html( "iPhone Repair <br><span id=\'caller-info\'>" +
                                                                         " Mobile</span></div><div id=\'caller-photo\'>" +
                                                                         "<img src=\"img/me-circlehead.gif\" " +
                                                                         " width=\'90px\' height=\'90px\'></div>"
                                      );
                                      $( 'div#iphone' ).css( { "animation": "ring .5s 0s infinite linear" } );
                                      $( '#top-bar' ).css( {
                                                               'visibility': 'visible',
                                                               'z-index': '990'
                                                           }
                                      );

                                      $( "<div id=\'green-button\'></div>" ).insertAfter( $( "#call-screen" ) );
                                      window.setTimeout( function () {
                                                             if ( !clicked ) {
                                                                 $( "<div id=\'modal\'></div>" ).insertBefore( $( "#callbox" ) );
                                                             }
                                                         }, 1500
                                      ); }, 1000);
                                  notDone =
                                      false;
                              }
                          }
    );

});

$( 'div#call-screen' ).hover( function () {
    $( "div#green-button, div#modal" ).click( function () {
                                                  clicked =
                                                      true;
                                                  $( '#outer' ).css( 'opacity', '0' );
                                                  $( "div#iphone" ).removeAttr( 'style' );
                                                  $( '#screen' ).css( {
                                                                          'background': 'linear-gradient(coral, lightblue)',
                                                                          'opacity': '1'
                                                                      }
                                                  );

                                                  $( 'div#call-screen' ).css( {
                                                                                  'animation': 'jello 1s',
                                                                                  'transform' : 'scaleX(0.01) scaleY(0.01)',
                                                                                  'opacity': '0'
                                                                              }
                                                  );

                                                  $( '#callbox' ).remove();
                                                  $( '#green-button' ).remove();
                                                  $( '#home' ).css( 'visibility', 'visible' );
                                                  $( '#modal' ).remove();


                                              }
    );
});
