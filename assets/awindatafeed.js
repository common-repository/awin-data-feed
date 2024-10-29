var AWDATAFEED = {
    init: function () {
        this.loadVerticalFeed();
        this.loadHorizontalFeed();
        this.loadHorizontalFeedShortCode();
        this.togglePrceRangeInout();
        this.resetForm();
        this.displayAnalytics();
        this.processAnalytics();
    },

    loadHorizontalFeed: function(){
        jQuery("#nextHorizontal").on('click', function () {
            jQuery.ajax({
                url: awindatafeed_params.ajaxurl,
                data:
                    jQuery('form#swFeedHorizontal').serialize(),
                success: function(response){
                    jQuery('#ajaxResponseHorizontal').html(response);
                    jQuery("#nextHorizontal").show();
                    jQuery("#nextHorizontal").html("&raquo;"); // »
                    AWDATAFEED.processAnalytics();
                },
                error: function(errorThrown) {
                    console.log(errorThrown);
                }
            })
        });

        jQuery('#nextHorizontal').trigger('click');
    },

    loadVerticalFeed: function() {
        jQuery("#nextVertical").on('click', function () {
            jQuery.ajax({
                url: awindatafeed_params.ajaxurl,
                data:
                    jQuery('form#swFeedVertical').serialize(),
                success: function(response){
                    jQuery('#ajaxResponseVertical').html(response);
                    jQuery("#nextVertical").show();
                    jQuery("#nextVertical").html('Next &raquo;')
                    AWDATAFEED.processAnalytics();
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            })
        });

        jQuery('#nextVertical').trigger('click');
    },

    loadHorizontalFeedShortCode: function() {
        jQuery("#nextHorizontalSc").on('click', function () {

            jQuery.ajax({
                url: awindatafeed_params.ajaxurl,
                data:
                    jQuery('form#swFeedHorizontalSc').serialize(),
                success: function(response){
                    jQuery('#ajaxResponseHorizontalSc').html(response);
                    jQuery("#nextHorizontalSc").show();
                    jQuery("#nextHorizontalSc").html("&raquo;");
                    AWDATAFEED.processAnalytics();
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            })
        });

        jQuery('#nextHorizontalSc').trigger('click');
    },

    togglePrceRangeInout: function() {
        if(jQuery("#maxPriceRange").is(':checked')) {
            jQuery(".range").attr("readonly", false);
        };
        jQuery("#maxPriceRange").focus(function () {
            jQuery(".range").attr("readonly", false);
        });
        jQuery(".maxPriceRadio").focus(function () {
            jQuery(".range").attr("readonly", true);
        });
    },

    processAnalytics: function () {
        jQuery("a[class^='trackImage']").on('click', function(e){
            var feedId = getFeedId(this.className);
            jQuery.ajax({
                url: awindatafeed_params.ajaxurl,
                data: {
                    'action': 'track_user_click',
                    'feedId': feedId
                },
                success: function(response){
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            })
        });

        var getFeedId = function (classNameString) {
            var id = classNameString.split("-");
            return id[1];
        }
    },

    resetForm: function() {
        jQuery("#resetFilters").on('click', function () {
            var form = jQuery('form#swFilters');
            form.find('input:text, input:password, input:file, select, textarea').val('');
            form.find('input:radio, input:checkbox')
                .removeAttr('checked').removeAttr('selected');
        });
    },

    displayAnalytics: function() {
        jQuery( "#reportButton" ).click(function() {
            jQuery( "#analytics" ).toggle('slow');
        });
    }
};

jQuery(document).ready(function() { AWDATAFEED.init(); });
