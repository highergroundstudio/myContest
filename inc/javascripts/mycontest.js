jQuery(document).ready(function($, $masonrycontainer, voteKey, mcb, cn, h, warn, novote) {

    // Enable strict mode
    "use strict";

    // Share button popups
    $('.share-button-popup').click(function(event, pd){
        pd = $(this).data();
        window.open(pd.url, pd.popname, "toolbar=0,status=0,width="+pd.w+",height="+pd.h);
    });

    // Setup the toast position
    toastr.options.positionClass = "toast-bottom-right";

    // Setup the masonry container
    $masonrycontainer = $('.myContest-entries');

    // Intialize masonry
    $masonrycontainer.masonry({
        itemSelector : '.myContest-entry',
        isAnimated: !Modernizr.csstransitions
    });

    // Reload when the images are loaded
    $masonrycontainer.imagesLoaded(function(){
        $masonrycontainer.masonry( 'reload' );
    });

    // Toggle share links wrapper
    $('.meta-share').click(function(event) {
        event.preventDefault();
        $(this).siblings(".share-links").slideToggle(200,function() {
            // Animation complete.
            $masonrycontainer.masonry( 'reload' );
        });
        return false
    });

    // Retina ready
    $('img.retina').retinaData();

    // Move and Display the magnify hover as we have js
    mcb = $('.mycontestbox');

    // Add the hover
    $( '.entryImg' ).each( function() {
        var img = $(this), imgW = img.width(), imgH = img.height();
        img.parent().append('<div class="cn" style="position:absolute;width:'+imgW+'px;height:'+imgH+'px;"><div class="icon-zoomin" style="top:'+((imgH / 2) - 16)+'px;left:'+((imgW / 2) - 16)+'px;"></div></div>');
    });

    // Setup myconstestbox
    mcb.myContestbox({
        beforeLoad: function() {
            var el, id = $(this.element).data('title-id');

            // New line
            this.title += '<br />'
            if (id) {
                el = $('#' + id);
            
                if (el.length) {
                    this.title += el.html();
                }
            }
        }
    });

    // Check the vote
    checkVoteAll();
    

    $('.myContest-votes-button').live('click', function(event) {

        // Stop from going to link
        event.preventDefault();

        var link = $(this), postid = null;

        // Setup our postid
        postid = link.parents('.myContest-entries').data('postid');

        voteKey = "mycont" + postid;  
        // console.debug(voteKey)


        // Check if user can vote
        if ( checkVote(postid) === false ) return false;            

        // Setup vars
        var count = link.children('.myContest-count'),// Get the count container
            id = link.data("entry-id"),
            postfix = link.find('.myContest-postfix').text(),
            data = {
                action: 'my-contest',
                myContestNonce : my_contest.myContestNonce,
                entry_id:id,
                post_id:postid,
                postfix:postfix
            }

        // Start the spinner
        // Only allow one click
        link.spin("small").attr("disabled", "disabled");

        // Hide the count
        count.hide();            

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(my_contest.ajaxurl, data, function(response) {

            // Successful!
            
            // Get dates
            var cDate = new Date(),
                eDate = new Date(),
                // How long till next vote (minutes)
                eTime = ( my_contest.expire * 60 * 1000 );

            eDate.setTime( eDate.getTime() + eTime );

            // console.debug("Your currrent date is: " + cDate.getTime())
            // console.debug("Your expire date is: " + eDate.getTime())
            // console.debug("my_contest.expire = " + my_contest.expire)
            // console.debug("Your expiration time is: " + ((eDate.getTime() - cDate.getTime()) / 60 / 1000) )

            // Save our data
            // v = entry id
            // e = expire time
            // n = number of votes
            amplify.store( voteKey, { v: id, e: eDate.getTime(), i: postid } );

            // Update our vote count
            if( $('.myContest-entries').hasClass('votenoshow') ){
                $('.'+id).html(my_contest.votedforthis);
            }else{
                 $('.'+id).html(response+"&nbsp"+my_contest.votes);
            }

        }).fail(function() {
            // Vote unsuccessful error
            toastr.error(my_contest.voteUnsuccessful); 
            // Re-enable link
            link.removeAttr("disabled");
        }).always(function() { 
            // Always stop the spinner and show the count
            count.show();
            link.spin(false);
            checkVoteAll();
            $masonrycontainer.masonry( 'reload' );
        })

        return false;
    });


    function checkVoteAll(warn) {

        $( '.myContest-entries' ).each(function( index ) {

            // Setup our postid
            var localpostid = $(this).data('postid'), 
                localvoteKey = "mycont" + localpostid,
                lthis = $(this).children('.myContest-entry');
            
            // console.debug("Running checkVote for key: " + localvoteKey)

            // Setup vars
            var localvoteValue = amplify.store( localvoteKey ),
                cDate = new Date();

            // console.debug(localvoteValue)

            // Make sure we have a value to check 
            if(localvoteValue){

                // Get the vote value
                var eDate = localvoteValue.e;


                // console.debug( eDate > cDate.getTime() )
                // console.debug("Your currrent date is: " + cDate.getTime())
                // console.debug("Your expire date is: " + eDate)
                // console.debug("Your expiration time is: " + ((eDate - cDate.getTime()) / 60 / 1000) + " minutes" )


                // Check if the user can vote again
                if( eDate > cDate.getTime() ){

                    // console.debug(lthis);

                    // $( lthis ).each(function( i ) {
                    //     $(this).children('.myContest-votes-button').addClass('disabled').removeClass('active').attr('disabled', true)
                    // })

                    // If we are not showing the vote count
                    if($(this).hasClass('votenoshow')){
                        lthis.children('.vote-holder').children('.myContest-votes-button').addClass('disabled').removeClass('active').attr('title',my_contest.alreadyVoted).html(my_contest.alreadyVoted).attr('disabled', true);
                        lthis.children('.vote-holder').children('.'+localvoteValue.v).addClass('voted').removeClass('disabled').attr('title',my_contest.votedforthis).html(my_contest.votedforthis);
                    }else{
                        lthis.children('.vote-holder').children('.myContest-votes-button').addClass('disabled').removeClass('active').attr('disabled', true);
                        lthis.children('.vote-holder').children('.'+localvoteValue.v).addClass('voted').removeClass('disabled');
                    }

                    // Reload masonry
                    $masonrycontainer.masonry( 'reload' );
                    return
                }
            }else{
                // Reload masonry
                $masonrycontainer.masonry( 'reload' );
            }
            
        }); // end each
        
    } // end votecheckAll

    function checkVote(postid) {

            // Setup our postid
            var localpostid = postid, 
                localvoteKey = "mycont" + localpostid;
            
            // console.debug("Running checkVote for key: " + localvoteKey);

            // Setup vars
            var localvoteValue = amplify.store( localvoteKey ),
                cDate = new Date();

            // console.debug(localvoteValue);

            // Make sure we have a value to check 
            if(localvoteValue){

                // Get the vote value
                var eDate = localvoteValue.e;


                // console.debug( eDate > cDate.getTime() )
                // console.debug("Your currrent date is: " + cDate.getTime())
                // console.debug("Your expire date is: " + eDate)
                // console.debug("Your expiration time is: " + ((eDate - cDate.getTime()) / 60 / 1000) + " minutes" )


                // Check if the user can vote again
                if( eDate > cDate.getTime() ){
                    checkVoteAll();
                    // Warning
                    toastr.warning(my_contest.alreadyVoted);
                    // Reload masonry
                    $masonrycontainer.masonry( 'reload' );
                    return false;
                }
            }else{
                // Reload masonry
                $masonrycontainer.masonry( 'reload' );
            }
        
    } // end votecheck

});