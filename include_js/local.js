(function() {
    this.EW = {};
}).call(this);

jQuery(function(){
    EW.modules = {};
    $.each(modules, function(index) {
        if (!EW.modules[this]) {
            EW.modules[this] = this;
            if (EW[EW.modules[this]]) {
                EW[EW.modules[this]].init();
            }
        }
    });

    /* Perdoret per te inicializu modulet pas ajax call */
    modules.push = function() {
        for (var i = 0; i < arguments.length; i++) {
            EW["ajax-modules"].init(arguments[i]);
        }
        return Array.prototype.push.apply(this, arguments);
    };
});


EW["mega-dropdown"] = {
    init: function() {
        var megaDropdownLinks = $('#navigation-area-lg');
        
        var hasTouch = $("html").hasClass("touch");
        var triggerEvent = hasTouch ? "click" : "mouseenter mouseleave";
//        if(hasTouch && megaDropdownLinks.is(":visible")){
//            megaDropdownLinks.find(".see-all-custom").addClass("show_this");
//        }
        $('.cd-dropdown-trigger-xs').on( "click", function(event) {
            event.stopPropagation();
            var hasTouch = $("html").hasClass("touch");
            if(hasTouch){
                event.preventDefault();
            }
            
            var $this = $(this).parent();

            var openItems = megaDropdownLinks.find('.cd-dropdown-trigger.dropdown-is-active').not($this);

            //megaDropdownLinks.find('.dropdown-is-active').trigger('click');
            if (openItems.length) {
                toggleNav(openItems);
            }
            toggleNav($this);
        });
        if(!hasTouch){
            /*large screen NO TOUCH*/
            $('.cd-dropdown-wrapper').on( "mouseenter mouseleave", function(event) {
                var hasTouch = $("html").hasClass("touch");

                var $this = $(this).find(".cd-dropdown-trigger"); /* large screen */
                var openItems = megaDropdownLinks.find('.cd-dropdown-trigger.dropdown-is-active').not($this);

                //megaDropdownLinks.find('.dropdown-is-active').trigger('click');
                if (openItems.length) {
                    toggleNav(openItems);
                }
                toggleNav($this);
            });
        }
        
        if(hasTouch){
            /* Large screen touch */
             $('.cd-dropdown-trigger').on( "click", function(event) {

                event.preventDefault();

                var $this = $(this); /* large screen */

                var openItems = megaDropdownLinks.find('.cd-dropdown-trigger.dropdown-is-active').not($this);

                //megaDropdownLinks.find('.dropdown-is-active').trigger('click');
                if (openItems.length) {
                    toggleNav(openItems);
                }
                toggleNav($this);
            });
        }

        /* visible only in large screen */
        var navTriggerDesktop = $(".cd-dropdown-trigger");
        $(window).on("resize", function() {
            if (navTriggerDesktop.is(":visible")) {
                $("#navigation-area-xs").find("nav.cd-dropdown.dropdown-is-active").removeClass("dropdown-is-active");
                $("body").removeClass('overflow_hidden');
            }
        });

        //close meganavigation
        $('.cd-dropdown .cd-close').on('click', function(event) {
            event.preventDefault();
            toggleNav($(this));
        });

        //on mobile - open submenu
         $(".has-children").children("a").on( "click" , function(event) {
             if(hasTouch || $(this).parents("#navigation-area-xs").is(":visible")){
                 event.preventDefault();
              }
            var selected = $(this);
            selected.next('ul').removeClass('is-hidden').end().parent('.has-children').parent('ul').addClass('move-out');
        });
        

        

        //on desktop - differentiate between a user trying to hover over a dropdown item vs trying to navigate into a submenu's contents
        var submenuDirection = (!$('.cd-dropdown-wrapper').hasClass('open-to-left')) ? 'right' : 'left';
        $('.cd-dropdown-content').menuAim({
            activate: function(row) {
                $(row).children().addClass('is-active').removeClass('fade-out');
                if ($('.cd-dropdown-content .fade-in').length === 0)
                    $(row).children('ul').addClass('fade-in');
            },
            deactivate: function(row) {
                $(row).children().removeClass('is-active');
                if ($('li.has-children:hover').length === 0 || $('li.has-children:hover').is($(row))) {
                    $('.cd-dropdown-content').find('.fade-in').removeClass('fade-in');
                    $(row).children('ul').addClass('fade-out');
                }
            },
            exitMenu: function() {
                $('.cd-dropdown-content').find('.is-active').removeClass('is-active');
                return true;
            },
            submenuDirection: submenuDirection
        });

        //submenu items - go back link
        $('.go-back').on('click', function() {
            var selected = $(this),
                    visibleNav = $(this).parent('ul').parent('.has-children').parent('ul');
            selected.parent('ul').addClass('is-hidden').parent('.has-children').parent('ul').removeClass('move-out');
        });

        function toggleNav($this) {
          
            var triggerDom = $this;
            var dropdownDom = $this.siblings('.cd-dropdown');
            var navIsVisible = (!dropdownDom.hasClass('dropdown-is-active')) ? true : false;
            dropdownDom.toggleClass('dropdown-is-active', navIsVisible);
            triggerDom.toggleClass('dropdown-is-active', navIsVisible);
            if ($this.hasClass("cd-dropdown-trigger-xs") || $this.hasClass("navigation_area_mobile_layer")) {
                $("body").toggleClass('overflow_hidden', navIsVisible);
            }
            if (!navIsVisible) {
                dropdownDom.one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function() {
                    dropdownDom.find('.has-children ul').addClass('is-hidden');
                    dropdownDom.find('.move-out').removeClass('move-out');
                    dropdownDom.find('.is-active').removeClass('is-active');
                });
            }
        }

        //IE9 placeholder fallback
        //credits http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
        if (!Modernizr.input.placeholder) {
            $('[placeholder]').focus(function() {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            }).blur(function() {
                var input = $(this);
                if (input.val() == '' || input.val() == input.attr('placeholder')) {
                    input.val(input.attr('placeholder'));
                }
            }).blur();
            $('[placeholder]').parents('form').submit(function() {
                $(this).find('[placeholder]').each(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                })
            });
        }
    }
};
//
//EW["medline-services_basket"] = {
//    init: function(){
//        
//        $.ajax({
//            url: "",
////            data :  apprcss=orderMedlineArticle&pmids
//    
//        })
//        
//    }
//   
//}
//
EW["orderform"] = {
    settings: {
        selector: '.controlitem'
    },
    init: function(u) {
        var module = this;
        var settings = module.settings;

        $('body').on('click.ControlItems', settings.selector, function(event) {
            var trigger = $(event.currentTarget);
            var target = $(trigger.attr('data-target'));
            if (target.length) {
                if (trigger.prop('checked')) {
                    target.prop('disabled', false);
                }else{
                    target.prop('disabled', true).val('');
                }
            }
            //console.log(trigger, trigger.prop('checked'), target);
        });

    }
}
EW["contact_post_nem"] = {
    init: function(u) {
        var pr = u && u instanceof jQuery ? u.find(".formular_contact_form") : $(".formular_contact_form");
        var a = $("form.formular_contact_form");
        a.each(function() {
            $(this).bind("submit", $.proxy(EW['contact_post_nem'].action, $(this)));
        });
        $(".clear_buton").bind("click", $.proxy(EW['contact_post_nem'].clear_input, $(this)));

    },
    action: function(event) {

        event.stopPropagation();
        event.preventDefault();
        var form = $(event.currentTarget);

        var c = $(event.currentTarget);
        var idstemp = c.attr("dt-idstemp");
        var id_nem = c.attr("dt-id_nem");
        var id_form_k = c.attr("dt-id_form_k");
        //$(form).validate();
        if ($(form).valid()) {
            //kushte nese duam qe te modifikojme vlaue perpara se ti bejme submit formes
            if (id_form_k == 0) {
                var val_check = "";
                $('.formular_contact_form :checkbox:checked').each(function(i) {

                    if (val_check != "")
                        val_check = val_check + " , " + $(this).val();
                    else
                        val_check = val_check + $(this).val();
                });
                $('.formular_contact_form input[name="other3_cont2"]').val(val_check);
            }
            else if (id_form_k == 3) {
                var val_check = "";
                $('.formular_contact_form :checkbox:checked').each(function(i) {

                    if (val_check != "")
                        val_check = val_check + " , " + $(this).val();
                    else
                        val_check = val_check + $(this).val();
                });
                $('.formular_contact_form input[name="other3_cont2"]').val(val_check);
            }
            contact_send(idstemp, id_nem);
            //contact_clearAll(id_nem);
        }
    },
    clear_input: function() {

        $('.formular_contact_form input[type="text"],.formular_contact_form select').each(function() {
            this.value = '';
        });
        $('.formular_contact_form textarea').each(function() {
            this.value = '';
        });
        $(".formular_contact_form input:radio,.formular_contact_form input:checkbox").attr("checked", false);
        return false;

    }
};

EW["video-item"] = {
    config: {
        videoId : 'videoItem'
    },
    init: function(){
        var _this = this;
        $("head").append('<link href="'+APP_URL+'include_css/video-js.4.12.css" rel="stylesheet">');
        $.getScript( APP_URL+"include_js/videojs.4.12.min.js", function( data, textStatus, jqxhr ) {
           _this.initializeVideo();
        });
    }, initializeVideo : function(){
         this.videoPlayer = videojs(this.config.videoId, {});
    }
};

//$.validator.setDefaults({
//    errorElement: "div"
//});


EW.validate = {
    init: function(b) {
        var a = b && b instanceof jQuery ? b.find(".validate") : $(".validate");
        a.each(function() {
            $(this).validate();
        })
    }
};


/* Promo modal */
EW.promo = {
    modalSelector: "#modalHome",
    modalLinkSelector: "a",
    
    storagenamespace: ( (typeof promoSI !== "undefined") ? "ironpromo"+promoSI : "ironpromo" ),
    storagevalue: "true",
    storage: "localStorage", // "localStorage","sessionStorage"
    init: function(){
        if( !this.checkStorageExistence() ){
            this.initModal();
        }
    },
    initModal: function(){
        
        $(this.modalSelector ).modal({
            backdrop : 'static',
            show: true,
            keyboard: false
        });
        
        this.promoSeenEvent();
       
    },
    promoSeenEvent: function(){
        $(this.modalSelector ).on("click", this.modalLinkSelector, function(){
            this.setStorage();
        }.bind(this));
        $(this.modalSelector ).bind('contextmenu', this.modalLinkSelector, function(e) {
            return false;
        }); 
    },
    checkStorageExistence: function(){
        if( window[this.storage].getItem( this.storagenamespace )
                && window[this.storage].getItem( this.storagenamespace ) == this.storagevalue ){
            return true;
        }else{
            return false;
        }
    },
    setStorage: function(){
        window[this.storage].setItem( this.storagenamespace, this.storagevalue );
    },
    deleteStorage: function(){
        window[this.storage].removeItem( this.storagenamespace );
    }
};


/* checks if it is an integer */
function isInt(value) {
  return !isNaN(value) && 
         parseInt(Number(value)) == value && 
         !isNaN(parseInt(value, 10));
}
/* Fe Calculator scripts */
    function f_reset()
    {
        var ist_hb = document.getElementById('ist_hb');
        ist_hb.value = "";
        var soll_hb = document.getElementById('soll_hb');
        soll_hb.value = "";
        var ist_fe = document.getElementById('ist_fe');
        ist_fe.value = "";
        var result = document.getElementById('result');
        result.value = "";
        ist_hb.focus();
    }
    
    function f_log()
    {
        var ist_hb = left_right_trim_log('ist_hb');
        var soll_hb = left_right_trim_log('soll_hb');
        var ist_fe = left_right_trim_log('ist_fe');
        if (ist_hb == "")
        {
            alert(alert_idomosdoshem_mesg);
            var theElement = document.getElementById('ist_hb');
            theElement.focus();
            return;
        }
        if (!isNumber_log('ist_hb'))
        {
            return;
        }
        /* checks if it is not an integer */
        if(!isInt(parseFloat(ist_hb))){
            alert(alert_not_integer);
            return;
        }
        if (parseFloat(ist_hb) < 30 || parseFloat(ist_hb) > 300)
        {
            alert(alert_not_in_range_30_300);
            var theElement = document.getElementById('ist_hb');
            theElement.focus();
            return;
        }
        if (soll_hb == "")
        {
            alert(alert_idomosdoshem_mesg);
            var theElement = document.getElementById('soll_hb');
            theElement.focus();
            return;
        }
        if (!isNumber_log('soll_hb'))
        {
            return;
        }
        /* checks if it is not an integer */
        if(!isInt(parseFloat(soll_hb))){
            alert(alert_not_integer);
            return;
        }
        if (parseFloat(soll_hb) < 30 || parseFloat(soll_hb) > 300)
        {
            alert(alert_not_in_range_30_300);
            var theElement = document.getElementById('soll_hb');
            theElement.focus();
            return;
        }
        if (ist_fe == "")
        {
            alert(alert_idomosdoshem_mesg);
            var theElement = document.getElementById('ist_fe');
            theElement.focus();
            return;
        }
        if (!isNumber_log('ist_fe'))
        {
            return;
        }
        if (parseFloat(ist_fe) <= 0)
        {
            alert(alert_isnumber_pozitiv_mesg);
            var theElement = document.getElementById('ist_fe');
            theElement.focus();
            return;
        }
        var result = (parseFloat(soll_hb) - parseFloat(ist_hb)) * 5 * 3.4 + (100 - parseFloat(ist_fe)) * 10;
        if (result < 0)
        {
            result = 0;
        }
        var theElementresult = document.getElementById('result');
        theElementresult.value = result;
    }
    
        function left_right_trim_log(id)
        {
            var theElement = document.getElementById(id);
            var fild_value = theElement.value;
            while (fild_value.charAt(0) == ' ')
            {
                fild_value = fild_value.substring(1, fild_value.length)
            }
            ;
            while (fild_value.charAt(fild_value.length - 1) == ' ')
            {
                fild_value = fild_value.substring(0, fild_value.length - 1)
            }
            ;
            theElement.value = fild_value;
            return fild_value;
        }

        function isNumber_log(id)
        {
            var theElement = document.getElementById(id);
            var s = theElement.value;
            if (isNaN(Math.abs(theElement.value)) && (s.charAt(0) != "#"))
            {
                if (isNumber_log.arguments.length < 1)
                    alert(alert_isnumber1_mesg);
                else
                {
                    for (var i = 0; (i <= s.length && s.charAt(i) != "."); )
                    {
                        if (s.charAt(i) == " ")
                        {
                            alert(alert_isnumber1_mesg);
                            theElement.focus();
                            return false;
                        }
                        if (
                                ((s.charAt(i) >= 0) && (s.charAt(i) <= 9)) ||
                                (s.charAt(i) == "," && i != 0 && i != s.length - 1) || (s.charAt(i) == "."))
                            i++;
                        else
                        {
                            alert(alert_isnumber1_mesg);
                            theElement.focus();
                            return false;
                        }
                    }
                    if (s.charAt(i) == ".")
                    {
                        for (i++; i <= s.length; )
                        {
                            if (((s.charAt(i) >= 0) && (s.charAt(i) <= 9)))
                                i++;
                            else
                            {
                                alert(alert_isnumber1_mesg);
                                theElement.focus();
                                return false;
                            }
                        }
                    }
                }
            }
            return true;
        }
/* End Fe Calculator scripts */

