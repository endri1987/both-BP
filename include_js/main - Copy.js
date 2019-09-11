(function () {
    this.EW = {};
}).call(this);


jQuery(function () {
/**
 * General config settings
 */
EW.config = {
    settings: {
        bodySmallClass: 'body-small',
        bodyMiniNavbarClass: 'mini-navbar',
        bodyShowNavbarClass: 'show-navbar',
        bodyShowNavbarFrontClass: 'show-navbar-front',
        bodyFixedFidebarClass: 'fixed-sidebar',
        brakepoint: 769,
        sidemenuselector: '#side-menu',
        zoneFrontClass: 'ecc-zone',
        zoneMyCMEClass: 'mycme-zone',
        zoneAuthoringClass: 'authoring-zone',
        zoneLecturePreviewClass: 'lecture-view',
        navigationTopHeight: 50,
        headerInitialClass : 'headroom',
        headerPinnedClass : 'headroom--pinned',
        headerUnpinnedClass : 'headroom--unpinned',
        headerTopClass : 'headroom--top',
        headerNotTopClass : 'headroom--not-top',
        bodyShowNavbarLectureClass: 'show-navbar-lecture',
        navbarLectureBrakepoint: 1201
    },
    errors: {
        errorstatus: 'An error ocured',
        successstatus: 'Successfully completed'
    },
    html: {
        close: '<a href="#dialog" class="di-close"><i class="fa fa-times "></i><span class="icon close"></span></a>',
        loader: '<div class="loader" title="Please wait while the content loads..."><span class="access">Please wait while the content loads...</span></div>',
        "loader-sm": '<div class="ajax-loader loader-sm" title="Please wait while the content loads..."><span class="sr-only">Please wait while the content loads...</span><i class="loader-icon fa fa-spinner fa-spin"></i></div>',
        "loader-md": '<div class="ajax-loader loader-md" title="Please wait while the content loads..."><span class="sr-only">Please wait while the content loads...</span><i class="loader-icon fa fa-spinner fa-2x fa-spin"></i></div>',
        "loader-lg": '<div class="ajax-loader loader-lg" title="Please wait while the content loads..."><span class="sr-only">Please wait while the content loads...</span><i class="loader-icon fa fa-spinner fa-4x fa-spin"></i></div>'
    },
    integers: {
        reveal: 200,
        geoTimeout: 20000
    }
};

/**
 * External dependecies libraries
 * Initialize dependecies loader
 */
dependencies.init({
    'bowser': {
        url: 'bowser/bowser.min.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.select2'
    },
    'lodash': {
        url: '',
        type: 'script',
        loaded: false,
        definition: 'Lodash'
    },
    'datatable': {
        url: 'dataTables/dtbundle.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.DataTable'
    },
    'videojs': {
        url: 'videojs/video.js',
        type: 'script',
        loaded: false,
        definition: 'videojs'
    },
    'validate': {
        url: 'validate/jquery.validate.min.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.validate'
    },
    'serializeobject': {
        url: 'jquery.serializeobject/jquery.serialize-object.min.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.serializeObject'
    },
    'gmap3': {
        url: 'jquery.gmap3/gmap3.min.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.gmap3'
    },
    'gmaps': {
        url: 'gmaps/gmaps.js',
        type: 'script',
        loaded: false,
        definition: 'GMaps'
    }
});

/**
 * Handle registered modules for initialization
 */
EW.modules = {};
$.each(modules, function (index) {
    if (!EW.modules[this]) {
        EW.modules[this] = this;
        if (EW[EW.modules[this]]) {
            var module = EW[EW.modules[this]];
            if (typeof module.dependencies !== 'undefined') {
                $.when.apply(null, dependencies.arrayFunct(module.dependencies))
                .done(function(){
                    module.init();
                })
            }else{
                module.init();
            }
        }
    }
});
/* Perdoret per te inicializu modulet pas ajax call */
modules.push = function () {
    for (var i = 0; i < arguments.length; i++) {
        EW["ajax-modules"].init(arguments[i]);
    }
    return Array.prototype.push.apply(this, arguments);
};

EW["ajax-modules"] = {
    init: function (pushedModule) {
        if (EW[pushedModule]) {
            if (typeof EW[pushedModule].dependencies !== 'undefined') {
                $.when.apply(null, dependencies.arrayFunct(EW[pushedModule].dependencies))
                .done(function(){
                    EW[pushedModule].init();
                })
            }else{
                EW[pushedModule].init();
            }
        }
    }
};
});

/**
 * Page Setup
 */

EW.pagesetup = {
    init: function(){
        //console.log('EW.pagesetup');
        var pagesetup = this;

        /**
         * Call here all modules that need to be initialized on page load...
         */
        //Akkordeon
        $('.frame_40').each(function(i,e) {

            //console.log($(e).attr('id'));

            $(e).click(function() {
                $(e).toggleClass('offen');
            });
        });

        //suche
        $('#buttonsuchmaske')
            .click(function() {
                $('#suche').show();
                setTimeout(function(){
                    $('#suchmaske').toggleClass('aktiv');
                }, 10);
            });

        $(document).scroll(function() {
            $('#suchmaske').removeClass('aktiv');
            $('#loginContainer').removeClass('aktiv');
        });

        //Login Module Area
        $('#buttonLogin')
            .click(function() {
                $('#dropdownMenuLink').show();
                setTimeout(function(){
                    $('#loginContainer').toggleClass('aktiv');
                }, 10);
            });


        $('body').on('click', '[data-menu-toggle]', function(event) {
            event.preventDefault();
            var trigger = $(event.currentTarget);
            var action = trigger.attr('data-menu-toggle');
            if (action === 'open') {
                $('body').addClass('menu-open');
            }else if (action === 'close') {
                $('body').removeClass('menu-open');
            }
        });

        $('#suchmaske').on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(e) {
            var searchbox = $(e.target);
            if (!searchbox.hasClass('aktiv')) {
                $('#suche').hide();
            }else{
                searchbox.find('.poleSearch').focus();
            }

        });
    }
};
EW["ajax-module"] = {
    settings: {
        linkselector: '[data-ajax-link="true"]',
        formselector: '[data-ajax-form="true"]',
        selector: '[data-ajax="true"]',
        selectorLong: '[data-ajax-module="true"]',
        targetattr: 'data-target',
        scrolltobeforeattr: 'data-scrollto-before',
        scrolltoafterattr: 'data-scrollto-after',
        notifyattr: 'data-ajax-notify',
        notifyconfirmattr: 'data-notify-confirm',
        notifycustomconfirmattr: 'data-notify-custom-confirm',
        notifystatusattr: 'data-notify-status',
        notifysuccessattr: 'data-notify-success',
        notifyerrorattr: 'data-notify-error',
        refreshattr: 'data-refresh',
        triggereventbeforeattr: 'data-trigger-event-before',
        triggereventafterattr: 'data-trigger-event-after',
        triggereventaftersuccessattr: 'data-trigger-event-after-success',
        eventhandlerattr: 'data-event-handler',
        dependentparams: 'data-dependant-params',
        triggereventbeforens: 'ajaxmodulebefore',
        triggereventafterns: 'ajaxmoduleafter',
        triggercallback: 'data-trigger-after',
        triggeronceattr: 'data-trigger-once',
        triggeronceflag: 'data-trigger-once-flag',
        triggertoggleattr: 'data-trigger-toggle',
        triggertoggleflag: 'data-trigger-toggle-flag',
        refresheditorattr: '[data-refresh-editor="true"]',
        hastimerattr: 'data-has-timer',
        hastimerstopattr: 'data-timer-stop',
        hastimerremoteattr: 'data-has-timer-remote',
        hastimernewattr: 'data-timer-new',
        hastimerreportattr: 'data-timer-report',
        hascustomurlattr: 'data-url-custom',
        showLoaderattr: 'data-ajax-loader',
        showLoaderReplaceattr: 'data-ajax-loader-replace="true"',
        loadingClass: 'loading',
        loaderTemplate: '<div class="loader-indicator"><div class="loader-indicator-icon"><i class="fa fa-fw fa-2x fa-spin fa-cog"></i></div></div>',
        checkEditToggle: 'data-enable-edit="true"',
        notifyIconConfirm: 'fa fa-question fa-2x fa-fw',
        notifyIconLoading: 'fa fa-cog fa-spin fa-2x fa-fw',
        notifyIconSuccess: 'fa fa-check fa-2x fa-fw',
        notifyIconError: 'fa fa-exclamation-triangle fa-2x fa-fw',
        resetFormAttr: 'data-reset-form="true"',
        disablePreventDefaultAttr: '[data-prevent-default="false"]',
        triggerAutomatically: '[data-ajax-trigger="onload"]'
    },
    triggerevent: function(eventns){
        var settings = EW["ajax-module"].settings;
        if (typeof eventns !== 'undefined') {
            $(document).trigger(EW['ajax-module'].settings.triggereventafterns, {
                namespace: eventns
            })
        }
    },
    init: function (mod) {
        var settings = EW["ajax-module"].settings;
        $('body').off('click.AjaxModule');
        $('body').on('click.AjaxModule', 'a' + settings.selector + ', a' + settings.selectorLong+', button' + settings.selector+', button' + settings.selectorLong, EW["ajax-module"].submit);

        $('body').off('submit.AjaxModule');
        $('body').on('submit.AjaxModule', 'form' + settings.selector + ', form' + settings.selectorLong, EW["ajax-module"].submit);

        $('body').off('change.AjaxModule');
        $('body').on('change.AjaxModule', 'select' + settings.selector + ', select' + settings.selectorLong, EW["ajax-module"].submit);

        $('body').off('input.AjaxModule');
        $('body').on('input.AjaxModule', 'input[type="text"]' + settings.selector + ', input[type="text"]' + settings.selectorLong, EW["ajax-module"].submit);

        $(document).off(settings.triggereventafterns);
        $(document).on(settings.triggereventafterns, function(event, data){
            if (typeof data !== 'undefined' && typeof data.namespace !== 'undefined' && data.namespace !== '') {
                $('['+ settings.eventhandlerattr +'='+data.namespace+']').each(function(index, el) {
                    var element = $(el);
                    if (element.is('a')) {
                        element.trigger('click');
                    }
                    else if (element.is('form')) {
                        element.trigger('submit');
                    }
                });
            }
        });

        $(document).off(settings.triggereventbeforens);
        $(document).on(settings.triggereventbeforens, function(event, data){
            if (typeof data !== 'undefined' && typeof data.namespace !== 'undefined' && data.namespace !== '') {
                $('['+ settings.eventhandlerattr +'='+data.namespace+']').each(function(index, el) {
                    var element = $(el);
                    if (element.is('a')) {
                        element.trigger('click');
                    }
                    else if (element.is('form')) {
                        element.trigger('submit');
                    }
                });
            }
        });

        /**
         * Handle items that are configured to be triggered automatically
         */
        $(settings.triggerAutomatically).each(function(index, el) {
            var item = $(el);
            if (item.is('form')) {
                item.trigger('submit');
            }else{
                item.trigger('click');
            }
        });
    },
    handleDependandParams : function(triggerelement){
        var trigger = triggerelement instanceof jQuery ? triggerelement : $(triggerelement);
        var params = {};
        if (!trigger.is('[data-dependant-params]')) {
            return '';
        }
        var items = $.parseJSON(trigger.attr('data-dependant-params'));
        if (!items.length) {
            return '';
        }
        $(items).each(function(index, item) {
            var elements = $(item.element);

            // Skip if element is not found
            if (!elements.length) {
                return;
            }
            elements.each(function(index, el) {
                var element = $(el);
                //console.log('dependant params element item', element);
                var itemdata = '';
                // Handle form
                console.log("Handle dependant params");
                if (element.is('form')){
                    itemdata = element.serialize();
                    //console.log(itemdata);
                }

                // Handle form elements
                if (element.is('input') || element.is('select') || element.is('textarea')) {
                    itemdata = element.val();
                }
                // Handle html non-form elements
                else{
                    itemdata = element.html();
                }
                if (typeof item.name !== 'undefined') {
                    params[item.name] = itemdata;
                }else{
                    if (element.is('[name]')) {
                        params[element.attr('name')] = itemdata;
                    }
                }
            });
        });


        params = $.param( params );

        return params;
    },
    submit: function (event) {
        var trigger = $(event.currentTarget);
        if (trigger.is(EW['ajax-module'].settings.disablePreventDefaultAttr) === false) {
            event.preventDefault();
        }
        //console.log(trigger);
        // //////console.log('teest')
        // EW["ajax-module"].load(trigger);
        var confirmStack = {
            'dir1': 'down',
            'dir2': 'right',
            'modal': true
        }
        if (trigger.is('[' + EW['ajax-module'].settings.notifyconfirmattr + ']')) {

            var notifyConfirmConfig = {confirm: true};
            /*var notifyConfirmCancelBtn = {
                text: 'Cancel'
            }*/

            if (trigger.is('[' + EW['ajax-module'].settings.notifycustomconfirmattr + ']')) {
                var customConfirmConfig = JSON.parse(trigger.attr(EW['ajax-module'].settings.notifycustomconfirmattr));
                if (customConfirmConfig.length) {
                   notifyConfirmConfig.buttons = customConfirmConfig;
                }
                console.log(notifyConfirmConfig, customConfirmConfig);
            }else{
                /*notifyConfirmConfig.buttons = [
                    {
                        text: 'OK',
                        addClass: 'btn-primary',
                        click: function(e, notice){
                            EW["ajax-module"].load(trigger);
                        }
                    }
                ]*/
            }

            //notifyConfirmConfig.buttons.push(notifyConfirmCancelBtn);

            var notify = new PNotify({
                title: trigger.attr(EW['ajax-module'].settings.notifyconfirmattr),
                icon: EW["ajax-module"].settings.notifyIconConfirm,
                hide: false,
                confirm: notifyConfirmConfig,
                history: {
                    history: false
                },
                buttons: {
                    closer: false,
                    sticker: false
                },
                addclass: 'stack-modal',
                stack: confirmStack
            })
            .get().on('pnotify.confirm', function (e, notice) {
                    EW["ajax-module"].load(trigger);
                }).on('pnotify.cancel', function () {
                    return;
                });
        } else {
            EW["ajax-module"].load(trigger);
        }
    },
    load: function (trigger, thetarget, loadCallback) {
        var url = EW["get-url"].geturl(trigger);
        var target = thetarget;

        if (typeof thetarget == 'undefined') {
            //var target = thetarget;
            target = trigger.is('[' + EW["ajax-module"].settings.targetattr + ']') ? $(trigger.attr(EW["ajax-module"].settings.targetattr)) : false;
        }
        /*
         * //////console.log('ajax-module-submitted', trigger); //////console.log('url',
         * url); //////console.log('target', target);
         */


        if (trigger.hasClass('fake-call')){
            return;
        }

        var notify = false;
        if (trigger.is('[' + EW['ajax-module'].settings.notifyattr + ']')) {
            notify = new PNotify({
                title: trigger.attr(EW['ajax-module'].settings.notifyattr),
                icon: EW["ajax-module"].settings.notifyIconLoading,
                delay: 800000,
                hide: false,
                buttons: {
                    closer: false,
                    sticker: false
                }
            });
        }

        if (url) {

            // Check if trigger has been configured to be called only once and stop loading if already called
            if (trigger.is('[' + EW['ajax-module'].settings.triggeronceattr + ']') && trigger.is('[' + EW['ajax-module'].settings.triggeronceflag + ']')) {
                return;
            }

            // Check if trigger has been configured to be called on toggle mode
            if (trigger.is('[' + EW['ajax-module'].settings.triggertoggleattr + ']') && trigger.is('[' + EW['ajax-module'].settings.triggertoggleflag + ']')) {
                trigger.removeAttr(EW['ajax-module'].settings.triggertoggleflag);
                return;
            }

            if (trigger.is('[' + EW['ajax-module'].settings.hastimerattr + ']') && trigger.is('[' + EW['ajax-module'].settings.hastimerreportattr + ']')) {
                var timervisual = $(trigger.attr(EW['ajax-module'].settings.hastimerattr));
                //var spentTimeValue = timervisual.data('timer') - timervisual.TimeCircles().getTime();
                //var spentTimeValue = timervisual.TimeCircles().getTime();
                var spentTimeValue = EW['asesment-timer'].getSpentTime(timervisual);

                trigger.find(trigger.attr(EW['ajax-module'].settings.hastimerreportattr)).val(Math.round(spentTimeValue));
                /*////console.log( trigger.find(trigger.attr(EW['ajax-module'].settings.hastimerreportattr)));
                ////console.log( trigger.find(trigger.attr(EW['ajax-module'].settings.hastimerreportattr)).val());
                ////console.log(Math.round(spentTimeValue));*/
            }

            var method = 'POST';
            if (trigger.is('form') && trigger.attr('method') == 'POST') {
                method = 'POST';
            }
            var formdata = '';
            if (trigger.is('form')) {
                formdata = trigger.serialize();
            }else if (trigger.is('select')) {
                formdata = trigger.attr('name') + '=' +  trigger.val();
            }else if (trigger.is('input')) {
                formdata = trigger.attr('name') + '=' +  trigger.val();
            }



            if (trigger.is('[' + EW['ajax-module'].settings.dependentparams + ']')) {
                if (formdata !== '') {
                    formdata += '&' + EW["ajax-module"].handleDependandParams(trigger);
                }else{
                    formdata = EW["ajax-module"].handleDependandParams(trigger);
                }
            }

            /*Jon*/
            var simpleEditAuthoring = session.GetValue('simpleEditAuthoring');
            var simpleModePreview   = session.GetValue('simpleModePreview');
            if (typeof simpleEditAuthoring !== 'undefined' && simpleEditAuthoring === 't' && typeof simpleModePreview !== 'undefined' && simpleModePreview === 'yes'){
                formdata += "&smpe=y"
            }

            url = trigger.is('[' + EW['ajax-module'].settings.hascustomurlattr + ']') ? url : _ajx + url;


            var ajaxmodule = $.ajax({
                url: url,
                method: method,
                data: formdata,
                beforeSend: function (xhr) {
                    /**
                     * Trigger another event before ajax call if data attribute is provided
                     * Data attribute value is the event to be triggered
                     */
                    if (trigger.is('[' + EW['ajax-module'].settings.triggereventbeforeattr + ']')) {
                        var triggereventbeforeattr = trigger.attr(EW['ajax-module'].settings.triggereventbeforeattr);
                        if (triggereventbeforeattr !== '') {
                            $(document).trigger(EW['ajax-module'].settings.triggereventbeforens, {
                                element: trigger,
                                namespace: triggereventbeforeattr
                            })
                        }
                    }

                    /*if (trigger.is('['+EW['ajax-module'].settings.showLoaderattr+']')) {
                        var ajaxloader = EW.config.html[trigger.attr(EW['ajax-module'].settings.showLoaderattr)];
                        if (typeof ajaxloader === 'undefined') {
                            ajaxloader = EW.config.html['loader-md'];
                        }

                        if (trigger.is('['+EW['ajax-module'].settings.showLoaderReplaceattr+']')) {
                            target.html(ajaxloader);
                        }else{
                            target.append(ajaxloader);
                        }

                    }*/

                    /**
                     * Scroll to element before ajax call if attribute data-scrollto is found.
                     * Set value to [target] for scrollign to the target element specified byt data-target attribute
                     * Set a selector if you wnat to scroll to anoter element
                     */
                    if (trigger.is('[' + EW['ajax-module'].settings.scrolltobeforeattr + ']')) {
                        // Scroll to target element if attribute value is [target]
                        if (trigger.attr(EW['ajax-module'].settings.scrolltobeforeattr) === 'target') {
                            $.scrollTo(target, 400);
                        }
                        // Scroll to another element if attribute value is a valid jQuery selector
                        else if ($(trigger.attr(EW['ajax-module'].settings.scrolltobeforeattr)).length) {
                            $.scrollTo($(trigger.attr(EW['ajax-module'].settings.scrolltobeforeattr)), 400);
                        }

                    }

                    /**
                     * Add loading class
                     */
                    if (target) {
                        if (target.find('.loader-indicator').length === 0 && (trigger.is('[' + EW['ajax-module'].settings.showLoaderattr + ']') || target.is('[' + EW['ajax-module'].settings.showLoaderattr + ']'))) {
                            $(EW['ajax-module'].settings.loaderTemplate).appendTo(target);
                            //console.log('create template', EW['ajax-module'].settings.loaderTemplate);
                        }
                        target.addClass(EW['ajax-module'].settings.loadingClass);
                    }

                    if (trigger.is('[' + EW['ajax-module'].settings.hastimerattr + ']')) {
                        var timervisual = $(trigger.attr(EW['ajax-module'].settings.hastimerattr));
                        //timervisual.TimeCircles().stop();
                        //timervisual.countdown('pause');
                    }
                },
                success: function (data) {
                    var newdata;
                    if (typeof EW["get-url"].parseurl(url)[1] === 'undefined') {
                        newdata = $(data);
                    }else{
                        var selector = '#' + EW["get-url"].parseurl(url)[1];
                        newdata = $(data).filter(selector);
                        if (!newdata.length) {
                            newdata = $(data).find(selector);
                        }
                    }

                    /*////console.log('selector: '+ selector);
                    ////console.log(newdata);*/
                    if (notify) {

                        if (newdata.is('[' + EW['ajax-module'].settings.notifystatusattr + ']') && newdata.attr(EW['ajax-module'].settings.notifystatusattr) == 'false') {
                            var notifyoptions = {
                                icon: EW["ajax-module"].settings.notifyIconError,
                                type: 'error',
                                delay: 800,
                                hide: true,
                                buttons: {
                                    closer: false,
                                    sticker: false
                                }
                            };
                            if (trigger.is('[' + EW['ajax-module'].settings.notifyerrorattr + ']')) {
                                notifyoptions.title = trigger.attr(EW['ajax-module'].settings.notifyerrorattr);
                            } else {
                                notifyoptions.title = EW.config.errors.errorstatus;
                            }
                        } else {
                            var notifyoptions = {
                                icon: EW["ajax-module"].settings.notifyIconSuccess,
                                type: 'success',
                                delay: 800,
                                hide: true,
                                buttons: {
                                    closer: false,
                                    sticker: false
                                }
                            };
                            if (trigger.is('[' + EW['ajax-module'].settings.notifysuccessattr + ']')) {
                                notifyoptions.title = trigger.attr(EW['ajax-module'].settings.notifysuccessattr);
                            } else {
                                notifyoptions.title = EW.config.errors.successstatus;
                            }
                        }
                        // //////console.log('u kry notify')
                        notify.update(notifyoptions);
                    }

                    /**
                     * Reset form data if attribute is found
                     */
                    if (trigger.is('form') && trigger.is('['+ EW["ajax-module"].settings.resetFormAttr +']')) {
                        trigger[0].reset();
                    }

                    if (newdata.is('[' + EW['ajax-module'].settings.hastimerattr + ']')) {
                        /**
                         * Check if newdata has timer and resume oo pause acordingly.
                         */
                        //$(newdata.attr(EW['ajax-module'].settings.hastimerattr)).TimeCircles().start();
                        if (newdata.is('[' + EW['ajax-module'].settings.hastimerstopattr + ']')) {
                            $(newdata.attr(EW['ajax-module'].settings.hastimerattr)).countdown('pause');
                            PNotify.removeAll();
                        }else{
                            $(newdata.attr(EW['ajax-module'].settings.hastimerattr)).countdown('resume');
                        }
                    }

                    // Check if trigger has been configured to be called only once and add flag.
                    if (trigger.is('[' + EW['ajax-module'].settings.triggeronceattr + ']')) {
                        /**
                         * Case when trigger is configured to be triggert only once.
                         * Sets attribut flag to true
                         */
                        trigger.attr(EW['ajax-module'].settings.triggeronceflag, 'true');

                    }

                    // Check if trigger has been configured to be called on toggle mode and add flag.
                    if (trigger.is('[' + EW['ajax-module'].settings.triggertoggleattr + ']')) {
                        /**
                         * Case when trigger is configured to be triggered on toggle mode.
                         * Sets attribut flag to true
                         */
                        trigger.attr(EW['ajax-module'].settings.triggertoggleflag, 'true');

                    }

                    if (trigger.is('[' + EW['ajax-module'].settings.refreshattr + ']')) {
                        /**
                         * Case when trigger is used to refresh all page
                         */
                        GoTo('thisPage?event=none.rfr()');

                    }else if(trigger.is(EW['ajax-module'].settings.refresheditorattr)){
                        /**
                         * Case when trigger is used to update data of CKEDITOR element
                         * Make sure that targeteditor is a ckeditor instance
                         */
                        EW.ckeditor.setData(target, newdata.html());
                    } else if(target){
                        target.html(newdata);
                        $(window).trigger('ew.ContentRevealed');
                    }


                    //console.log(target);

                    /**
                     * Enable edit mode on loaded content if attribut "checkEditToggle" is present on trigger
                     */
                    if (trigger.is('[' + EW['ajax-module'].settings.checkEditToggle + ']')) {
                        EW["toggle-edit-mode"].showEdit(target);
                    }

                    /**
                     * Trigger another element as a callback if data attribute is provuided
                     * Data attribute value is a normal jQuery selector
                     */
                    if (trigger.is('[' + EW['ajax-module'].settings.triggercallback + ']')) {
                        var triggerafter = $(trigger.attr(EW['ajax-module'].settings.triggercallback));
                        //console.log(triggerafter);
                        if (triggerafter.length) {
                            if (triggerafter.is('form')) {
                                triggerafter.submit();
                            }
                            else if (triggerafter.is('.datatable')) {
                                //console.log('is datatable');
                                triggerafter.trigger('reload');
                            }
                            else{
                                triggerafter.trigger('click');
                            }
                        }
                        //$(trigger.attr(EW['ajax-module'].settings.triggercallback)).trigger('click');
                    }

                    /**
                     * Trigger another event if data attribute is provided
                     * Data attribute value is the event to be triggered
                     */
                    if (trigger.is('[' + EW['ajax-module'].settings.triggereventafterattr + ']')) {
                        var triggereventafterattr = trigger.attr(EW['ajax-module'].settings.triggereventafterattr);
                        if (triggereventafterattr !== '') {
                            $(document).trigger(EW['ajax-module'].settings.triggereventafterns, {
                                element: trigger,
                                namespace: triggereventafterattr
                            })
                        }
                    }

                    if (trigger.is('[' + EW['ajax-module'].settings.triggereventaftersuccessattr + ']')) {
                        if (newdata.is('[' + EW['ajax-module'].settings.notifystatusattr + ']') && newdata.attr(EW['ajax-module'].settings.notifystatusattr) == 'true') {
                            var triggereventaftersuccessattr = trigger.attr(EW['ajax-module'].settings.triggereventaftersuccessattr);
                            if (triggereventaftersuccessattr !== '') {
                                $(document).trigger(EW['ajax-module'].settings.triggereventafterns, {
                                    element: trigger,
                                    namespace: triggereventaftersuccessattr
                                })
                            }
                        }
                    }

                    /**
                     * Autofocus on element
                     */
                    if (target !== false && typeof target !== 'undefined') {
                        target.find('[autofocus]:first').focus();
                    }

                    /**
                     * Scroll to element after ajax call if attribute data-scrollto is found.
                     * Set value to [target] for scrollign to the target element specified byt data-target attribute
                     * Set a selector if you wnat to scroll to anoter element
                     */
                    if (trigger.is('[' + EW['ajax-module'].settings.scrolltoafterattr + ']')) {
                        // Scroll to target element if attribute value is [target]
                        if (trigger.attr(EW['ajax-module'].settings.scrolltoafterattr) === 'target') {
                            $.scrollTo(target, 400);
                        }
                        // Scroll to another element if attribute value is a valid jQuery selector
                        else if ($(trigger.attr(EW['ajax-module'].settings.scrolltoafterattr)).length) {
                            $.scrollTo($(trigger.attr(EW['ajax-module'].settings.scrolltoafterattr)), 400);
                        }

                    }

                    /**
                     * Remove loading class
                     */
                    if (target) {
                        target.removeClass(EW['ajax-module'].settings.loadingClass);
                    }

                    if (typeof loadCallback !== 'undefined') {
                        loadCallback(newdata);
                    }

                }
            });

            return ajaxmodule;
        }
    }
};





/**
 * Bootstrpa Dialog Module
 */
EW.modal = {
    modal: '',
    header: '',
    title: '',
    content: '',
    footer: '',
    relatedTarget: null,
    size: {
        sm: 'modal-sm',
        md: 'modal-md',
        lg: 'modal-lg'
    },
    defaults: {
        title: 'Modal title',
        animationIn: 'bounceInDown',
        animationOut: 'bounceOutUp'
    },
    settings: {
        selector: '[data-bs-modal="true"]',
        modalselector: '#ecc-bs-modal',
        modalstyleselector: 'data-modal-style',
        dimissattr: '[data-dismiss-modal]',
        targetaatr: 'data-target',
        titledefault: 'Modal title',
        animateInattr: 'data-modal-animate-in',
        animateOutattr: 'data-modal-animate-out',
        loadingClass: 'modal-loading'
    },
    init: function (mod) {
        var module = this;
        var settings = module.settings;
        var defaults = module.defaults;

        var modules = $(settings.selector, mod);
        $('body').on('click', 'a'+settings.selector, module.show);
        $('body').on('submit', 'form'+settings.selector, module.show);

        $('body').on('input change', 'form[data-monitor="true"], form[data-monitor="true"] input:not([data-bypas-monitor]), form[data-monitor="true"] textarea:not([data-bypas-monitor]), form[data-monitor="true"] select:not([data-bypas-monitor])', function (event) {
            event.preventDefault();
            var item = $(event.currentTarget);
            if (item.closest(settings.modalselector).length) {
                module.modal.data('changed', true);
            }
        });

        //$('body').prepend(settings.markup);

        module.modal = $(settings.modalselector);
        module.modal.data('changed', false);
        //console.log(module.modal);

        // Handle Header and Title defaults
        module.header = $('.modal-header:first', module.modal);
        module.title = $('.modal-title', module.header);

        // Handle Body defaults
        module.contentWrapper = $('.modal-content:first', module.modal);
        module.content = $('.modal-body:first', module.modal);

        // Handle footer defaults
        module.footer = $('.modal-footer:first', module.modal);
        module.modal = module.modal.modal({
            show: false
        });

        /*//console.log(EW.dialog.modal);
        //console.log(EW.dialog.header);
        //console.log(EW.dialog.title);
        //console.log(EW.dialog.footer);*/

        // Attach event handlers
        module.modal.on('show.bs.modal', function (event) {
            module.update();
        });

        module.modal.on('shown.bs.modal', function (event) {
        });

        module.modal.on('hide.bs.modal', function (event) {
            if (module.modal.data('changed') === true) {
                var confirmhide = confirm("Are you sure you want to discard changes?");
                if (confirmhide) {
                    module.hide();
                    module.modal.data('changed', false);
                }else{
                    return false;
                }
            }else{
                module.hide();
                module.modal.data('changed', false);
            }

        });

        module.modal.on('hidden.bs.modal', function (event) {
            ////console.log('hidden.bs.modal');
            module.reset();
        });


        //EW.dialog.show();
    },
    show: function(e){
        var module = EW.modal;
        var settings = module.settings;
        var defaults = module.defaults;

        //console.log('show');
        e.preventDefault();
        var trigger = $(e.currentTarget);
        ////console.log(trigger[0]);
        ////console.log(EW.dialog.relatedTarget[0]);
        if(! typeof  module.relatedTarget !== null){
            if(trigger[0] == module.relatedTarget){
                //console.log('yes');
            }
        }
        module.relatedTarget = $(trigger);
        //console.log(module.modal);
        if(!module.modal.data('bs.modal').isShown){
            module.modal.modal('show');

            // Trigger slidepause event for lecture presentation player. This is to be removed to avoid dependencies.
            $(document).trigger('slidePause');
        }else{
            //module.update();
        }
        module.load();

    },
    update: function(newdata){
        var module = this;
        var settings = module.settings;
        var defaults = module.defaults;

        var trigger = module.relatedTarget;
        var modaltitle = '';

        if (typeof newdata !== 'undefined') {
            if (newdata.title !== undefined && newdata.title !== '') {
                modaltitle = newdata.title;
            }else{
                if(trigger.is('[data-title]')){
                    modaltitle = trigger.attr('data-title');
                }else{
                    modaltitle = trigger.attr('title');
                }
            }
        }else{
            if(trigger.is('[data-title]')){
                modaltitle = trigger.attr('data-title');
            }else{
                modaltitle = trigger.attr('title');
            }
        }

        if(trigger.is('[data-modal-footer="true"]')){
            module.footer.show();
        }

        if (trigger.is('[data-modal-size]')) {
            module.modal.find('.modal-dialog:first')
            .removeClass(module.size.sm + ' ' + module.size.md + ' ' + module.size.lg)
            .addClass(trigger.attr('data-modal-size'));
        }else{
            module.modal.find('.modal-dialog:first').removeClass(module.size.sm + ' ' + module.size.md + ' ' + module.size.lg)
            .addClass(trigger.attr(module.size.md));
        }

        if (trigger.is('[data-modal-style="compact"]')) {
           //console.log(module.content);
           //console.log(module.modal);
           module.modal.removeClass('inmodal')
           module.modal.addClass('compact-modal')
        }else{
           //console.log(module.content);
           //console.log(module.modal);
           module.modal.addClass('inmodal')
           module.modal.removeClass('compact-modal')
        }

        if (trigger.is('[' + settings.animateInattr + ']')) {
            module.contentWrapper.attr('class', 'modal-content animated ' + trigger.attr(settings.animateInattr));
        }

        module.title.text(modaltitle);
    },
    hide: function(){
        var module = this;
        var settings = module.settings;
        var defaults = module.defaults;

        var trigger = module.relatedTarget;

        /*if (trigger.is('[' + settings.animateOutattr + ']')) {
            module.contentWrapper.attr('class', 'modal-content animated ' + trigger.attr(settings.animateOutattr));
        }else{
            module.contentWrapper.attr('class', 'modal-content animated ' + defaults.animationOut);
        }*/

        if (module.modal.data('refreshOnDismiss') === true) {
            //location.reload(true);
            GoTo('thisPage?event=none.rfr()');
        }

    },
    reset: function(){
        var module = this;
        var settings = module.settings;
        var defaults = module.defaults;

        module.title.text(defaults.title);
        module.content.empty();
        module.contentWrapper.attr('class', 'modal-content animated ' + defaults.animationIn);
        module.footer.hide();
        module.relatedTarget = null;
    },
    load: function(){
        var module = this;
        var settings = module.settings;
        var defaults = module.defaults;

        module.modal.data('changed', false);

        //console.log('show');
        var trigger = module.relatedTarget;
        if(trigger.is('[data-modal-ajax="true"]')){

            /**
             * Ajax loaded content case
             */

            if (trigger.is('[' + settings.targetaatr + ']')) {
                //console.log('first case');
                EW["ajax-module"].load(trigger, $(trigger.attr(settings.targetaatr)));

                // Close modal if attribute is found
                if (trigger.is(settings.dimissattr)) {
                    module.modal.modal('hide');
                }

            }else{
                //console.log('second case');
                module.modal.addClass(settings.loadingClass);
                //EW["ajax-module"].load(trigger, module.content);
                EW["ajax-module"].load(trigger, module.content, function(newdata){

                    // Close modal if attribute is found
                    if (trigger.is(settings.dimissattr)) {
                        module.modal.modal('hide');
                    }

                    //console.log(newdata, $(newdata).find('[data-modal-title]:first'));
                    var newtitle = $(newdata).find('[data-modal-title]:first');
                    if (newtitle.length && newtitle.attr('data-modal-title') !== '') {
                        //module.title.text(newtitle.attr('data-modal-title'));
                        module.update({title:newtitle.attr('data-modal-title')});
                    }

                    //console.log(newdata, $(newdata).find('[data-refresh-ondismiss="true"]'));
                    if ($(newdata).find('[data-refresh-ondismiss="true"]').length) {
                        module.modal.data('refreshOnDismiss', true);
                    }

                    module.modal.removeClass(settings.loadingClass);

                });
                //console.log('module.content', module.content)
            }

        }
        else{

            /**
             * Onpage static content case
             */
            var triggercontent = trigger.find('.modal-content-holder');
            if (triggercontent.length) {
                module.content.html(triggercontent.html());
            }

        }



    }
};


/**
 * Form Validation module
 */
EW.validate = {
    dependencies: ['validate'],
    init: function (b) {
        var a = $(".validate", b);
        a.each(function () {
            ////console.log(a)
            var form = $(this);
            var config = EW.utilities.parseConfig($(this).find('[type="text/jquery-validate-config"]:first'));
            config.ignore = [':disabled'];
            if (form.is('[data-validation-message="alert"]')) {
                config.showErrors = function(errorMap, errorList){
                    return;
                }
            }
            $(this).validate(config);
        });
    }
};

/**
 * Scrool to module
 */
EW.scrollto = {
    init: function (b) {
        var a = $("[data-scroll-to]:first", b);
        //$(window).scrollTop(a.offset().top);
        //console.log(a.offset().top > $(window).scrollTop(), a.offset().top, $(window).scrollTop());
        if (a.length && a.offset().top > $(window).scrollTop()) {
            setTimeout(function(){ $(window).scrollTop(a.offset().top); }, 1);
        }
    }
};

/**
 * Bootstrap Tooltip Module
 */
EW.tooltip = {
    init: function (b) {
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });
    }
};


/**
 * Initialize Bootstrap Popovers
 */
EW['enable-popover'] = {
    popovertemplate: '<div class="popover" role="tooltip"><button type="button" class="close popover-close-btn" data-dismiss="popover" aria-label="Close"><span aria-hidden="true">&times;</span></button><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
    init: function (b) {
        //console.log('enable-popover');
        var options = {
            html: true,
            template: EW['enable-popover'].popovertemplate,
            selector: '[data-toggle=popover][data-trigger!=hover], .has-popover[data-trigger!=hover]',
            //container: 'body',
            viewport: {selector: 'body', padding: 0},
            title: function () {
                return $(this).attr('data-title');
            },
            content: function () {
                var popovercontent = $(this).find('.popover-content-holder:first');
                if (popovercontent.length) {
                    popovercontent = popovercontent.html();
                } else {
                    popovercontent = '';
                }

                return popovercontent;
            }
        };
        // $('[data-toggle="popover"]').popover(options);
        $('body').popover(options);

        options.selector = '[data-toggle=popover][data-trigger=hover], .has-popover[data-trigger=hover]';
        options.trigger = 'hover';
        $("html").popover(options);

        $('body').on('click', '.popover-close-btn', function (e) {
            var targetId = $(this).parents('.popover:first');
            targetId = targetId.attr('id');
            var target = $('[aria-describedby="' + targetId + '"]');
            target.popover('hide');
            //////console.log('click herepopover');
        });

        $('body').on('click', '[data-toggle="popover"], .has-popover', function (e) {
            $('[data-toggle="popover"], .has-popover').not(this).popover('hide');
            //////console.log('click herepopover');
        });

        $('body').on('focus focusin', '[data-toggle="popover"], .has-popover', function (e) {
            //////console.log('focusin herepopover');
            var trigger = $(e.currentTarget);
            if (!trigger.is('[data-popover-close="manually"]')) {
                $(this).popover('hide');
            }
        });

        $('body').on('focusout blur', '[data-toggle="popover"], .has-popover', function (e) {
            // ////console.log('focusout herepopover');
            var trigger = $(e.currentTarget);
            if (!trigger.is('[data-popover-close="manually"]')) {
                $(this).popover('hide');
            }
        });


    }
};


/**
 * Dropdown replacement of Bootstrap Dropdown
 */
EW['handle-dropdown'] = {
    settings: {
        /**
         * Use this class to make any link a dropdown toggle
         * @type {class}
         */
        selector: '.ew-dropdown-toggle',
        /**
         * Class for the parent holding a dropd-down button. A drop-down toggle button should always be inside an element with this class
         * @type {String}
         */
        parentselector: '.ew-dropdown',
        openclass: 'open',
        backdropclass: 'ew-dropdown-backdrop',
        backdroptemplate: '<div></div>',
        /**
         * Use this class on the dro-down-menu element if you want to make it dissmisable when clicking on links or buttons within it.
         * @type {String}
         */
        dismissableselector: '.ew-dropdown-menu-dismissable'
    },
    init: function () {
        var module = this;

        /**
         * Attach event handler for dropdown trigger element
         */
        $('body').on('click', module.settings.selector, function(event) {
            event.preventDefault();
            var trigger = $(event.currentTarget);
            var parent = trigger.parents(module.settings.parentselector + ':first');

            if (typeof trigger.data('hasbackdrop') === 'undefined' || trigger.data('hasbackdrop') === false) {
                module.open(trigger);
            }else{
                module.close(trigger);
            }
        });

        /**
         * Attach event handler for backdrop element
         */
        $('body').on('click touchstart', '.'+module.settings.backdropclass, function(event) {
            event.preventDefault();
            var backdrop = $(event.currentTarget);
            var trigger = backdrop.data('trigger');
            trigger.trigger('click');
        });

        /**
         * Attach event handler for all links elements inside a drop-down menu that should be disamssible
         */
        var dismissableselector = module.settings.dismissableselector + ' a, ' + module.settings.dismissableselector + ' button';
        $('body').on('click', dismissableselector, function(event) {
            var dropdownmenu = $(event.currentTarget).parents(module.settings.dismissableselector + ':first');
            var backdrop = dropdownmenu.siblings('.' + module.settings.backdropclass + ':first');
            backdrop.trigger('click');
        });
    },
    open: function(trigger){
        var module = this;
        var parent = trigger.parents(module.settings.parentselector + ':first');
        if (parent.length) {
            var backdrop = $(module.settings.backdroptemplate);
            backdrop.addClass(module.settings.backdropclass);
            backdrop = backdrop.insertAfter(trigger);

            parent.addClass(module.settings.openclass);
            backdrop.data('trigger', trigger);
            trigger.data('hasbackdrop', backdrop);
        }
    },
    close: function(trigger){
        var module = this;
        var parent = trigger.parents(module.settings.parentselector + ':first');
        if (parent.length) {
            parent.removeClass(module.settings.openclass);
            backdrop = trigger.data('hasbackdrop');
            backdrop.remove();
            trigger.data('hasbackdrop', false);
        }
    }
};



/**
 * Embeded video player
 */
EW['video-player-embed'] = {
    dependencies: ['videojs'],
    settings: {
        selector: '[data-module="view-video-embed"]',
        mediaUrlAttr: 'data-media-url',
        posterUrlAttr: 'data-poster-url',
        defaultPoster: APP_URL + 'graphics/embed-player-default-poster.jpg',
        identifierPrefix: 'embed-player-',
        playerWrapperHtml: '<div class="player-embed-wrapper"></div>',
        playerHtml: '<video class="video-js vjs-default-skin IIV" controls preload="none" width="100%" height="100%" playsinline></video>'
    },
    init: function(wrapper) {
        var module = this;
        var settings = this.settings;
        var items = $(settings.selector, wrapper);
        items.each(function(index, el) {
            module.setup(el);
        });
        //console.log(items);
    },
    setup: function(item){
        var module = this;
        var settings = this.settings;
        var container = item instanceof jQuery ? item : $(item);
        //console.log(player);

        // Check if Media URL is provided, otherwise return.
        if (container.is('['+settings.mediaUrlAttr+']') === false || container.attr(settings.mediaUrlAttr) === '') {
            //console.log('No media url found');
            return;
        }

        var playerWrapper = $(settings.playerWrapperHtml).appendTo(container);
        var player = $(settings.playerHtml);
        var identifier = settings.identifierPrefix + chance.natural({min: 1, max: 100000});

        player.attr('id', identifier);
        player = player.appendTo(playerWrapper);
        //console.log(container);

        var source = container.attr(settings.mediaUrlAttr);

        if (container.is('['+settings.posterUrlAttr+']') && container.attr(settings.posterUrlAttr) !== '') {
            //poster = container.attr(settings.posterUrlAttr);
            player.attr('poster', container.attr(settings.posterUrlAttr));
        }


        player = videojs(identifier, {}, function () {
            //console.log(player);
            var playerinstance = this;
            var playingTriggerDelay = 5;
            var lastplaytime;
            playerinstance.on('timeupdate', function(){
                if (!playerinstance.paused()) {
                    var currentTime = playerinstance.currentTime();
                    var sessionPlayTime = Math.ceil(currentTime);
                    if (sessionPlayTime && (sessionPlayTime != lastplaytime) && (sessionPlayTime%playingTriggerDelay === 0)) {
                        lastplaytime = sessionPlayTime;
                        $(document).trigger('ewvideoplayer.playing');
                    }
                }
                playerinstance.trigger('loadstart');
            });
            playerinstance.on('ended', function(){
                playerinstance.trigger('loadstart');
            });
            var newplayerdom = $('video[id*='+identifier+']');
            enableInlineVideo(newplayerdom[0]);
            newplayerdom.addClass('IIV');
        });

        var checksource = source.substring(0, 4);
        if (checksource == 'rtmp') {
            // ////console.log('is rtmp: '+checksource);
            player.src({type: "rtmp/mp4", src: source});
        } else {
            // ////console.log('not rtmp: '+checksource);
            var sourceuni = source.indexOf('?') !== -1 ? source + '&uni=' + UNI : source + '?uni=' + UNI;
            player.src({src: sourceuni});
        }

    }
}


/**
 * Header slideshow
 */
EW["header-slideshow"] = {
    init: function(wrapper) {
        var slideitems = $('#backgroundImage .sliderBackground');
        var itemscount = slideitems.length;
        slideitems.hide();
        slideitems.first().show();
        var currentitem = 0;
        var slideinterval = setInterval(function() {
            slideitems.eq(currentitem)
                .fadeOut(500, function(){
                    if (currentitem === (itemscount-1)) {
                        currentitem = 0;
                    }else{
                        currentitem = currentitem + 1;
                    }
                    slideitems.eq(currentitem).fadeIn(100)
                })
        },  5000);
    }
}

/**
 * Google Maps Module
 */

EW["gmap"] = (function(){
    var module = this;
    var dependencies = ['gmaps','lodash'];
    var settings = {
        selector: '[data-module="gmap"]',
        mapstyles: {
            'monochrome-grey': [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#4f595d"},{"visibility":"on"}]}]
        },
        defaultmapconfig: function(){
            //address:"Zurich, Switzerland",
            var config = {
                //center: [47.3769, 8.5417],
                zoom: 10,
                mapTypeId: 'roadmap',
                mapTypeControl: true,
                mapTypeControlOptions: {
                  style: 2
                },
                navigationControl: true,
                scrollwheel: true,
                streetViewControl: true,
            }
            return config;
        },
        /**
         * defaultmarkersconfig is extended with custom configuration. Here is defined the default marker icon
         * "markersconfig": {
         *      "type": {
         *           "icon": "http://maps.google.com/mapfiles/marker_green.png"
         *       }
         *   },
         */
        defaultmarkersconfig: function(){
            var markersconfig = {
                default: {
                    icon: APP_URL + 'graphics/map-marker.png'
                }
            }
            return markersconfig;
        }
    }

    function init(wrapper){
        var items = $(settings.selector, wrapper);
        items.each(function(index, el) {
            creategmaps($(el));
        });
    }

    function creategmaps(item){
        var customconfig = EW.utilities.parseConfig(item.find('[type="text/x-config"]:first'));
        if (typeof customconfig.mapconfig !== 'undefined') {
            var mapconfig = $.extend({}, settings.defaultmapconfig(), customconfig.mapconfig);
        }else{
            var mapconfig = $.extend({}, settings.defaultmapconfig());
        }

        //console.log(mapconfig);

        if (typeof mapconfig.styles === 'string' && settings.mapstyles[mapconfig.styles] !== 'undefined') {
            mapconfig.styles = settings.mapstyles[mapconfig.styles];
        }

        var markersPromise = $.Deferred();
        var markersconfig = $.extend({}, settings.defaultmarkersconfig(), Lodash.get(customconfig, 'markersconfig', {}));
        var markers = false;
        if (typeof customconfig.markers !== 'undefined') {
            markers = customconfig.markers;
        }

        if (Lodash.isArray(markers)) {
            /**
             * List of markers provided in config
             */
            if (markers.length === 1) {
                //mapconfig.center = markers[0].position;
                //mapconfig.lat = markers[0].position[0];
                //mapconfig.lng = markers[0].position[1];
            }
            markersPromise.resolve(markers, true);
        }
        else if (Lodash.isPlainObject(markers)) {
            /**
             * Single markers provided in config
             */
            mapconfig.center = markers.position;
            markersPromise.resolve(markers, false);
        }
        else if (Lodash.isString(markers)) {
            /**
             * Markers URL provided to load
             */
            $.getJSON(markers, function(response, textStatus) {
                 if (textStatus === 'success') {
                    if (response.errorcode === 0) {
                         markersPromise.resolve(response.data, true);
                    }
                }
            });
        }

        mapconfig.div = item[0];
        mapconfig.lat = 47.3769;
        mapconfig.lng = 8.5417;

        var mapitem = new GMaps(mapconfig);

        $.when(markersPromise).done(function(markers, multi){
            if (multi) {
                Lodash.forEach(markers, function(marker){
                    /**
                     * Handle marker icon if no icon attribute is defined
                     */
                    if (typeof marker.icon === 'undefined') {
                        // Check if {type} property is defined.
                        if (Lodash.has(marker, 'type')) {
                            // Assign icon based on {type.icon} property if defined in {markersconfig} property othrwise assign default icon.
                            marker.icon = Lodash.get(markersconfig, marker.type+'.icon', markersconfig.default.icon);
                        }else{
                            // Assign default icon if no {type} property is defined
                            marker.icon = markersconfig.default.icon;
                        }
                    }

                    if (Lodash.has(marker, 'infoWindow.url')) {
                        marker.click = function(e){
                            var infoWindow = e.infoWindow;
                            infoWindow.close();
                            //console.log(e);
                            loadInfoWindow(infoWindow, function(response){
                                infoWindow.setContent(response);
                                infoWindow.open(e.map, e)
                            });
                        }
                    }
                })
                mapitem.addMarkers(markers);
                if (markers.length > 1) {
                    mapitem.fitZoom();
                }else{
                    mapitem.setCenter(markers[0].lat, markers[0].lng);
                }
            }else{
                /**
                 * Check if there are coordinates and address attribute has been provided
                 */
                //console.log('markers not defined', markers.lat, markers.lng);
                if ((typeof markers.lat === 'undefined' || typeof markers.lng === 'undefined') && typeof markers.address !== 'undefined') {
                    //console.log('markers not defined');
                    GMaps.geocode({
                        address: markers.address,
                        callback: function(results, status){
                            //console.log(results, status);
                            if (status == 'OK') {
                                var latlng = results[0].geometry.location;
                                mapitem.setCenter(latlng.lat(), latlng.lng());
                                markers.lat = latlng.lat();
                                markers.lng = latlng.lng();
                                mapitem.addMarker(markers);
                            }
                        }
                    })
                }else{
                    mapitem.addMarker(markers);
                    mapitem.setCenter(markers.lat, markers.lng);
                }
            }
        });
        //console.log(mapitem);
    }

    function loadInfoWindow(infoWindow, callback){
        return $.ajax({
            url: infoWindow.url
        })
        .done(callback)
        .fail(function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        });
    }

    return {
        dependencies: dependencies,
        settings: settings,
        init: init,
        create: creategmaps
    }
})(EW);


////////////////////// UTILITIES /////////////////////////
/**
 * Equal Height Layoutsd
 */
EW["equal-heights"] = {
    multiSelectorSplitter: ", ", /**/
    resizeDelay : 150,
    selector: "[data-equal-heights]",
    init: function() {
        var _this = this;
        this.equalizer();
        this.debouncer();
        $(window).smartresize(function(){
            _this.equalizer();
        });
    },
    equalizer: function() {
        var _this = this;
        /* data-equal-container loop */
        $(this.selector).each(function() {
            var b = $(this),
                    c = b.data("equal-heights");
            /* if data-equal-heights is not provided */
            if (!c) {
                c = "> li";
            }
            c = c.split(_this.multiSelectorSplitter);
          
            /* selector loop */
            $(c).each(function(index, element) {
                var items = b.find(element);
                items.css({"height": "auto"});
                var a = 0;
                /**/
                items.each(function(d) {
                    var e = $(this);
                    if (e.height() > a) {
                        a = e.height();
                    }
                });
                items.height(a);
            });
        });
    },
    debouncer: function() {
        var _this = this;
        (function($, sr) {

            // debouncing function from John Hann
            // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
            var debounce = function(func, threshold, execAsap) {
                var timeout;

                return function debounced() {
                    var obj = this, args = arguments;
                    function delayed() {
                        if (!execAsap)
                            func.apply(obj, args);
                        timeout = null;
                    }
                    ;

                    if (timeout)
                        clearTimeout(timeout);
                    else if (execAsap)
                        func.apply(obj, args);

                    timeout = setTimeout(delayed, threshold || _this.resizeDelay);
                };
            };
            // smartresize
            jQuery.fn[sr] = function(fn) {
                return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
            };

        })(jQuery, 'smartresize');
    }
};


/**
 * Gather Usage ANALYTICS
 */
 EW.analytics = {
    settings: {
        path: 'prg_at.php?',
        unikey: 'uni=',
        idElCkey: '&idElC=',
        cidkey: '&cid=',
        ciskey: '&cis=',
        firstkey: '&frst=1',
        downloadkey: '&frst=2'
    },
    worker: false,
    init: function(){

        var analytics = this;
        if (typeof save_extended_stat !== 'undefined' && save_extended_stat === 'no') {
            return;
        }

        if (typeof ANALYTICS_TIMER !== 'undefined' && window.Worker) {
            // Start ANALYTICS WORKER
            //console.log('Start ANALYTICS WORKER');
            EW.analytics.worker = new Worker(APP_URL + 'include_js/workers/analytics.worker.js');
            EW.analytics.worker.postMessage({
                type: 'SETSETTINGS',
                data: {
                    path: 'prg_at.php?',
                    unikey: 'uni='+session.GetValue('uni'),
                    idElCkey: '&idElC='+session.GetValue('contentId'),
                    cidkey: '&cid=',
                    ciskey: '&cis=',
                    firstkey: '&frst=1',
                    downloadkey: '&frst=2',
                    interval: ANALYTICS_TIMER
                }
            });
        }

        /**
         * Subscribe to ew.onDocumentOpen event
         */
        $(document).on("ew.analytics.onDocumentOpen", analytics.open);
        /**
         * Subscribe to ew.onDocumentOpenOnce event
         */
        $(document).on("ew.analytics.onDocumentOpenOnce", analytics.openonce);
        /**
         * Subscribe to ew.onDocumentClose event
         */
        $(document).on("ew.analytics.onDocumentClose", analytics.close);
        /**
         * Subscribe to ew.onDocumentDownload event
         */
        $(document).on("ew.analytics.onDocumentDownload", analytics.download);
        /**
         * Subscribe to ew.onDocumentUpdateTime event
         */
        $(document).on("ew.analytics.onDocumentUpdateTime", analytics.updatetime);
        /**
         * General event handler for download item links
         */
        $(document).on("click", '.download-item', function(evt){
            var trigger = $(evt.currentTarget);
            if (trigger.is('[data-id]')) {
                analytics.trigger('download', trigger.attr('data-id'), trigger);
            }
        });

        /**
         * Init analytics for current page
         */
        analytics.trigger('open', session.GetValue('contentId'));
        if (typeof ANALYTICS_TIMER !== 'undefined') {
            var analyticsinterval = setInterval(function(){
                    analytics.trigger('update', session.GetValue('contentId'));
                }, ANALYTICS_TIMER);
        }


    },
    trigger: function(type, documentid, triggerTarget){

        if (typeof save_extended_stat !== 'undefined' && save_extended_stat === 'no') {
            return;
        }

        /**
         * Helper function to trigger analytics events
         * @type {[string]} - Mandatory - Argument to define type of event to trigger.
         * @documentid {[string]} - Mandatory - ID of document for which analytics event will triggered.
         * @triggerTarget {[object]} - Optional - Element/Object which trigered the event. If not provided [document] object will be passed.
         */
        var relatedTarget = document;
        if (jQuery.type( type ) === "undefined") {
            return;
        }
        if (jQuery.type( documentid ) === "undefined") {
            return;
        }
        if (jQuery.type( triggerTarget ) !== "undefined") {
            relatedTarget = triggerTarget instanceof jQuery ? triggerTarget[0] : triggerTarget;
        }

        if (type === 'open') {
            $.event.trigger({
                type: "ew.analytics.onDocumentOpen",
                message: 'Document Opened',
                documentid: documentid,
                relatedTarget: relatedTarget,
                time: new Date()
            });
        }
        if (type === 'openonce') {
            $.event.trigger({
                type: "ew.analytics.onDocumentOpenOnce",
                message: 'Document Opened',
                documentid: documentid,
                relatedTarget: relatedTarget,
                time: new Date()
            });
        }
        else if (type === 'close') {
            $.event.trigger({
                type: "ew.analytics.onDocumentClose",
                message: 'Document Closed',
                documentid: documentid,
                relatedTarget: relatedTarget,
                time: new Date()
            });
        }
        else if (type === 'download') {
            $.event.trigger({
                type: "ew.analytics.onDocumentDownload",
                message: 'Document Downloaded',
                documentid: documentid,
                relatedTarget: relatedTarget,
                time: new Date()
            });
        }
        else if (type === 'update') {
            $.event.trigger({
                type: "ew.analytics.onDocumentUpdateTime",
                message: 'Document time updated',
                documentid: documentid,
                relatedTarget: relatedTarget,
                time: new Date()
            });
        }
    },
    open: function(evt){
        var settings = EW.analytics.settings;

        var idElC = session.GetValue('contentId');
        if (session.GetValue('idElC')) idElC = session.GetValue('idElC');
        if (session.GetValue('idRef')) idElC = session.GetValue('idRef');

        if (EW.analytics.worker === false) {
            // Report the old way
            var url = APP_URL + settings.path + settings.unikey + session.GetValue('uni')+  settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid + settings.firstkey;
            EW.analytics.submit(url);
        }else{
            // Report to Worker
            EW.analytics.worker.postMessage({
                type: 'OPEN',
                data: {
                    message: 'Document opened',
                    openurl: APP_URL + settings.path + settings.unikey + session.GetValue('uni')+  settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid + settings.firstkey,
                    updateurl: APP_URL + settings.path + settings.unikey + session.GetValue('uni') + settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid,
                    documentid: evt.documentid,
                    time: new Date()
                }
            });
        }
    },
    openonce: function(evt){
        var settings = EW.analytics.settings;

        var idElC = session.GetValue('contentId');
        if (session.GetValue('idElC')) idElC = session.GetValue('idElC');
        if (session.GetValue('idRef')) idElC = session.GetValue('idRef');

        if (EW.analytics.worker === false) {
            // Report the old way
            var url = APP_URL + settings.path + settings.unikey + session.GetValue('uni')+  settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid + settings.firstkey;
            EW.analytics.submit(url);
        }else{
            // Report to Worker
            EW.analytics.worker.postMessage({
                type: 'OPENONCE',
                data: {
                    message: 'Document opened - record once',
                    openurl: APP_URL + settings.path + settings.unikey + session.GetValue('uni')+  settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid + settings.firstkey,
                    updateurl: APP_URL + settings.path + settings.unikey + session.GetValue('uni') + settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid,
                    documentid: evt.documentid,
                    time: new Date()
                }
            });
        }
    },
    close: function(evt){
        // Report to Worker
        EW.analytics.worker.postMessage({
            type: 'CLOSE',
            data: {
                message: 'Document opened',
                documentid: evt.documentid,
                time: new Date()
            }
        });
    },
    download: function(evt){
        var settings = EW.analytics.settings;
        /*//console.log('download | ew.onDocumentUpdateTime');
        //console.log(evt);*/
        var idElC = session.GetValue('contentId');
        if (session.GetValue('idElC')) idElC = session.GetValue('idElC');

        if (EW.analytics.worker === false) {
            // Report the old way
            var url = APP_URL + settings.path + settings.unikey + session.GetValue('uni')+  settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid + settings.downloadkey;
            EW.analytics.submit(url);
        }else{
            // Report to Worker
            EW.analytics.worker.postMessage({
                type: 'DOWNLOAD',
                data: {
                    message: 'Document Downloaded',
                    downloadurl: APP_URL + settings.path + settings.unikey + session.GetValue('uni')+  settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid + settings.downloadkey,
                    documentid: evt.documentid,
                    time: new Date()
                }
            });
        }

    },
    updatetime: function(evt){
        var settings = EW.analytics.settings;
        if (EW.analytics.worker !== false) {
            // THis is not used if workers are supported
            return;
        }
        var idElC = session.GetValue('contentId');
        if (session.GetValue('idElC')) idElC = session.GetValue('idElC');


        var url = APP_URL + settings.path + settings.unikey + session.GetValue('uni') + settings.idElCkey + idElC + settings.ciskey + session.GetValue('contentId') + settings.cidkey + evt.documentid;
        EW.analytics.submit(url);
    },
    submit: function(url){
        if (EW.analytics.worker !== false) {
            // Double check if workers are supported
            return;
        }
        if (jQuery.type( url ) === "undefined") {
            //console.log('Analytics cannot submit. URL not provided.');
            return;
        }
        ////console.log('submit', url);
        $.get(url);
    }
};

/**
 * Utilities
 */
/***************************************************************************************************************/

/**
 * Utility for parsing URLs from elements
 */
EW["get-url"] = {
    urlattr: 'data-url',
    geturl: function (element) {
        var urlattr = "data-url";
        var url = "";
        if (element.is('[' + urlattr + ']')) {
            url = element.attr(urlattr);
        }

        if (url === "") {
            if (element.is('a')) {
                url = element.attr('href');
            } else if (element.is('form')) {
                url = element.attr('action');
            } else {
                url = false;
            }
        }

        return url;
    },
    parseurl: function (theurl) {
        var spliturl = theurl.split('#');
        return spliturl;
    }
};



EW.utilities = {
    elements: {},
    caching: function(){
        this.elements.headerBanner = $('#header');
    },
    localStorageSupport: function(){
        var confirm = false;
        try {
            //console.log('check if exists');
            if ('localStorage' in window && window['localStorage'] !== null && window['localStorage'] !== null){
                localStorage.setItem("available",true);
                localStorage.removeItem("available");
                //console.log('is open');
                return true;
            }
        } catch (e) {
            //console.log('is blocked');
            return false;
        }
    },
    checkModalSize: function(currWidth, currHeight){
        var size = {
            width: currWidth,
            height: currHeight
        };

        if (typeof size.width === 'undefined' || $(window).width() <= size.width) {
            size.width = $(window).width() - 20;
        }

        if (typeof size.height === 'undefined' || $(window).height() <= size.height) {
            size.height = $(window).height() - 76;
        }

        return size;
    },
    calculateheight: function(element){
        if ($('body').hasClass(EW.config.settings.headerTopClass)) {
            var topValue = Math.abs(EW.utilities.elements.headerBanner.height() - $(window).scrollTop() + EW.config.settings.navigationTopHeight);
            element.css({
                'top': parseInt(topValue)+'px'
            });
            //console.log("topValue", topValue);
        }else{
            var topValue = Math.abs(EW.config.settings.navigationTopHeight);
            element.css({
                'top': parseInt(topValue)+'px'
            });
            //console.log("topValue", topValue);
        }
    },
    parseConfig: function(customOptions){
        var config = {};
        if (customOptions.length) {
            var configtext = customOptions.text();
            config = JSON.parse(configtext);
        }

        return config;
    }
};