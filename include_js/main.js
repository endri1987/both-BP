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
    'lodash': {
        url: '',
        type: 'script',
        loaded: false,
        definition: 'Lodash'
    },
    'bowser': {
        url: '',
        type: 'script',
        loaded: false,
        definition: 'Lodash'
    },
    'pnotify': {
        url: 'pnotify/pnotify.min.js',
        type: 'script',
        loaded: false,
        definition: 'PNotify'
    },
    'pnotifycss': {
        url: 'pnotify/pnotify.min.css',
        type: 'css',
        loaded: false,
        definition: 'pnotifycss'
    },
    'datatable': {
        url: 'dataTables/dtbundle.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.DataTable'
    },
    'jqueryui': {
        url: 'jqueryui/1.12.1/jquery-ui.min.js',
        type: 'script',
        loaded: false,
        definition: '$.ui'
    },
    'jqueryuicss': {
        url: 'jqueryui/1.12.1/jquery-ui.min.css',
        type: 'css',
        loaded: false,
        definition: '$.ui'
    },
    'select2': {
        url: 'select2/select2.full.min.js',
        type: 'script',
        loaded: false,
        definition: 'videojs'
    },
    'videojs': {
        url: 'videojs/video.js',
        type: 'script',
        loaded: false,
        definition: 'videojs'
    },
    'videojscss': {
        url: 'videojs/video.css',
        type: 'css',
        loaded: false,
        definition: 'videojscss'
    },
    'validate': {
        url: 'validate/jquery.validate.min.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.validate'
    },
    'inputmask': {
        url: 'jquery.inputmask/jquery.inputmask.bundle.min.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.inputmask'
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
    },
    'summernote': {
        url: 'summernote/summernote.min.js',
        type: 'script',
        loaded: false,
        definition: '$.summernote'
    },
    'summernotecss': {
        url: 'summernote/summernote.css',
        type: 'css',
        loaded: false,
        definition: 'summernotecss'
    },
    'jcrop': {
        url: 'jcrop/jquery.Jcrop.min.js',
        type: 'script',
        loaded: false,
        definition: '$.fn.Jcrop'
    },
    'jcropcss': {
        url: 'jcrop/jquery.Jcrop.min.css',
        type: 'css',
        loaded: false,
        definition: 'jcropcss'
    },
    'dropzone': {
        url: 'dropzone/dropzone.min.js',
        type: 'script',
        loaded: false,
        definition: 'Dropzone'
    },
    'slick': {
        url: 'slick/slick.min.js',
        type: 'script',
        loaded: true,
        definition: '$.fn.slick'
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
		
    $('.btn-search-navi').on('click',function()
    {
        if($('#navbarsExampleCenteredNav').attr('aria-expanded') === 'true'){
            $('#navbarsExampleCenteredNav').removeClass('show');
        }
        $('.search-popup').toggleClass("open-search-input animated fadeInUp");
        $('.btn-search-navi .fa-search').toggleClass("fa-remove");
        $('.btn-search-navi').toggleClass("active");
        return false;
    });
	
	$('body').click(function(e) {
		var container = $('[data-menu-toggle]');
        var targetParents = $(e.target).parents('.search-popup');
		if ($(e.target).parents(container)) {
			if(targetParents.length == 0 && $('.search-popup').hasClass("open-search-input animated fadeInUp")){
                $('.search-popup').toggleClass("open-search-input animated fadeInUp");
                $('.btn-search-navi .fa-search').toggleClass("fa-remove");
                $('.btn-search-navi').toggleClass("active");
			}
		}
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			if($('#navbarsExampleCenteredNav').attr('aria-expanded') === 'true'){
				$('[data-menu-toggle]').trigger('click');
			}
		}
	});


    }
};


EW["ajax-module"] = {
    dependencies: ['pnotify','pnotifycss'],
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
        modalselector: '#ew-bs-modal',
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
    settings: {
        defaultconfigs: {
            smartadmin: {
                highlight: function(element){
                    $(element).closest('label').addClass('state-error');
                },
                unhighlight: function(element) {
                    $(element).closest('label').removeClass('state-error');
                },
                errorElement: 'div',
                errorClass: 'note note-error',
                errorPlacement: function(error, element){
                    if(element.parents('.input-group:first').length) {
                        error.insertAfter(element.parents('.input-group:first'));
                    }  else if (element.parents('.inline-group:first').length) {
                        error.insertAfter(element.parents('.inline-group:first'));
                    }  else if(element.parents('.checkbox:first').length) {
                        error.insertAfter(element.parents('.checkbox:first'));
                    } else if(element.parents('.radio:first').length) {
                        error.insertAfter(element.parents('.radio:first'));
                    } else if (element.parent('label').length) {
                        error.insertAfter(element.parent('label'));
                    } else if (element.is('.kw-list-controller') && element.next('.select2').length) {
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            }
        }
    },
    init: function (b) {
        var module = this;
        var a = $(".validate", b);
        a.each(function () {
            var form = $(this);
            var config = EW.utilities.parseConfig($(this).find('[type="text/jquery-validate-config"]:first'));

            if (typeof config.default !== 'undefined' && typeof module.settings.defaultconfigs[config.default] !== 'undefined') {
                config = $.extend({}, config, module.settings.defaultconfigs[config.default]);
            }

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
 * Form Validation module
 */
EW.inputmask = {
    dependencies: ['inputmask'],
    settings: {
        selector: 'data-inputmask'
    },
    init: function (wrapper) {
        var module = this;
        var items = $('[' + module.settings.selector + ']', wrapper);
        items.each(function () {
            var item = $(this);
            item.inputmask();
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

        /*$('body').on('focus focusin', '[data-toggle="popover"], .has-popover', function (e) {
            //////console.log('focusin herepopover');
            var trigger = $(e.currentTarget);
            if (!trigger.is('[data-popover-close="manually"]')) {
                $(this).popover('hide');
            }
        });*/

        /*$('body').on('focusout blur', '[data-toggle="popover"], .has-popover', function (e) {
            // ////console.log('focusout herepopover');
            var trigger = $(e.currentTarget);
            if (!trigger.is('[data-popover-close="manually"]')) {
                $(this).popover('hide');
            }
        });*/


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
 * Datatables
 */

EW.datatable = {
    dependencies: ['datatable','serializeobject'],
    dataTableContainerSelector: ".datatable-container",
    selectSelector: ".datatable-custom-filter",
    searchSelector: ".datatable-custom-search",
    filterFormSelector: ".dt-filterform:first",
    dateFromSelector: ".datefrom-filter:first",
    dateToSelector: ".dateto-filter:first",
    init: function (module) {
     
        var _this = this;
        var items = $('.datatable', module);

        // ////console.log('again');

        if (items.length) {
            //console.log('datatable', items)
            items.each(function (index, elem) {

                // ////console.log(elem);
                var item = $(elem);
                item.on('reload', function(event) {
                    event.preventDefault();
                    datatableitem.ajax.reload(null, false);
                });

                if ( item.parents('.dataTables_wrapper').length ) {
                    return;
                }

                var tableOptions = {
                    paging: false,
                    dom: 't',
                    searching: false,
                    info: false,
                    ordering: false
                }

                var customOptions = item.closest(_this.dataTableContainerSelector).find('[type="text/chart-config"]:first');

                if (customOptions.length) {
                    var configtext = customOptions.text();
                    tableOptions = $.extend({}, tableOptions, JSON.parse(configtext));
                }

                //console.log(tableOptions)


                /*if (typeof tableOptions.dom === 'undefined') {
                    tableOptions.dom = '<"dt-top"f>{table}<"dt-bottom"ip>';
                }*/

                tableOptions.initComplete = function (e, settings, json) {
                    if (item.is('[data-select-items]')) {
                        $('body').on('change', item.attr('data-select-items'), function (e) {
                            var checkAllBtn = $(e.currentTarget);
                            if (checkAllBtn.prop('checked')) {
                                item.find('tbody input[type="checkbox"]').prop('checked', true);
                            } else {
                                item.find('tbody input[type="checkbox"]').prop('checked', false);
                            }
                        });

                    }
                    //console.log('has hidden-initialization', item.is('.hidden-initialization'));
                    if (item.is('.dt-hidden-initialization')) {
                        item.show();
                    }

                };
                //var configtext = chartconfig.html();
                //var configtext = chartconfig.prop('inerHTML');
                tableOptions.language = {"emptyTable": "No matching records found "}

                var parentForm = item.parents('form:first');
                var responsedata;

                if (typeof tableOptions.ajaxsettings !== 'undefined') {
                    tableOptions.ajax = function(data, callback, settings){
                        var requestUrl = tableOptions.ajaxsettings.url;

                        var filterdata = $.extend(true, {}, data, item.closest(_this.dataTableContainerSelector).find(_this.filterFormSelector).serializeObject());
                        //console.log(item.closest(_this.dataTableContainerSelector).find(_this.filterFormSelector), item.closest(_this.dataTableContainerSelector).find(_this.filterFormSelector).serializeObject());
                        //dt-filterform
                        $.post(requestUrl, filterdata, function(response){
                            returneddata = typeof response === 'string' ? JSON.parse(response) : response;
                            responsedata = returneddata.data;
                            console.log(typeof response);
                            callback({
                                recordsTotal: returneddata.data.recordsTotal,
                                recordsFiltered: returneddata.data.recordsFiltered,
                                // here I'd do something like totalTranslated : returneddata.totalTranslated ?
                                data: returneddata.data.list,
                            });
                        });
                    }
                }

                /**
                 * Check if footer callback is set in configuration
                 */
                if (typeof tableOptions.footerCallback !== 'undefined' && tableOptions.footerCallback !== '') {

                    var footerCallbackVal = tableOptions.footerCallback;

                    tableOptions.footerCallback = function(tfoot, data, start, end, display){
                        // Check if data should be manipulated by a global function. Put [FUNCTION:functionname] as value of data property
                        // where functionname is the function responsible for the manipulation
                        if (footerCallbackVal.indexOf('FUNCTION:') > -1) {
                            var funcname = footerCallbackVal.split(':')[1];
                            window[funcname](responsedata, tfoot, data, start, end, display)
                        }

                    };
                }

                /**
                 * Check if draw callback is set in configuration
                 */
                if (typeof tableOptions.drawCallback !== 'undefined' && tableOptions.drawCallback !== '') {

                    var drawCallbackVal = tableOptions.drawCallback ;

                    tableOptions.drawCallback  = function(settings){
                        // Check if data should be manipulated by a global function. Put [FUNCTION:functionname] as value of data property
                        // where functionname is the function responsible for the manipulation
                        if (drawCallbackVal.indexOf('FUNCTION:') > -1) {
                            var funcname = drawCallbackVal.split(':')[1];
                            window[funcname](settings)
                        }

                    };
                }

                /**
                 * Check if data should be manipulated by a global function. Put [FUNCTION:functionname] as value of data property
                 * where functionname is the function responsible for the manipulation
                 */
                if (typeof tableOptions.columns !== 'undefined'){
                    $(tableOptions.columns).each(function(index, el) {
                        if (typeof el.data !== 'undefined' && el.data.indexOf('FUNCTION:') > -1) {
                            var funcname = el.data.split(':')[1];
                            el.data = window[funcname];
                        }
                    });
                }

                var datatableitem = item.DataTable(tableOptions);
                item.on('draw.dt', function(event) {
                    $(window).trigger('ew.ContentRevealed');
                });

                var customFilter = item.closest(_this.dataTableContainerSelector).find(_this.selectSelector);
                //console.log('customFilter', customFilter);
                customFilter.each(function(index, el) {
                    var customFilterElem = $(el);
                    _this.handleFilter(item, customFilterElem);
                    customFilterElem.change( function() {
                        datatableitem.draw();
                    });
                    customFilterElem.trigger('change');
                });

                item.closest(_this.dataTableContainerSelector).on('keyup', _this.searchSelector, function(event) {
                    event.preventDefault();
                    datatableitem.search($(event.currentTarget).val()).draw();
                    //console.log($(event.currentTarget).val());
                });

                if (item.closest(_this.dataTableContainerSelector).find(_this.dateFromSelector).length && item.closest(_this.dataTableContainerSelector).find(_this.dateToSelector).length) {
                    _this.handleDateFilter(item, item.closest(_this.dataTableContainerSelector).find(_this.dateFromSelector), item.closest(_this.dataTableContainerSelector).find(_this.dateToSelector));
                    item.closest(_this.dataTableContainerSelector).on('keyup change', _this.dateFromSelector+','+_this.dateToSelector, function(event) {
                        event.preventDefault();
                        datatableitem.draw();
                    });
                }

                item.on('draw.dt', function (e, settings) {});
                item.on('search.dt', function (e, settings) {});

                /**
                 * Hande row click if configured
                 */
                if (typeof tableOptions.onrowclick !== 'undefined' && tableOptions.onrowclick !== '') {
                    item.on('click', 'tr', function(event) {
                        event.preventDefault();
                        var data = datatableitem.row( this ).data();
                        if (typeof window[tableOptions.onrowclick] !== 'undefined') {
                            window[tableOptions.onrowclick](data);
                        }
                    });
                }

                /**
                 * Hande row click if configured
                 */
                if (typeof tableOptions.onrowdoubleclick !== 'undefined' && tableOptions.onrowclick !== '') {
                    item.on('dblclick', 'tr', function(event) {
                        event.preventDefault();
                        var data = datatableitem.row( this ).data();
                        if (typeof window[tableOptions.onrowclick] !== 'undefined') {
                            window[tableOptions.onrowclick](data);
                        }
                    });
                }


            });
        }
    },
    handleFilter: function(item, selectDom){
        var _this = this;

        $.fn.dataTableExt.afnFiltering.push(
                function(settings, data, dataIndex) {
                    if (settings.nTable.id == item[0].id) {
                        var valLw = selectDom.val().toLowerCase();

                        var dataLw = data[0].toLowerCase();

                        if (!valLw || dataLw.indexOf(valLw) !== -1)
                        {
                            return true;
                        }
                        return false;
                    }else{
                        return true;
                    }
                }
        );
    },
    handleDateFilter: function(item, fromEl, toEl){
        $.fn.dataTableExt.afnFiltering.push(
            function( settings, aData, iDataIndex, aRawData ) {
                if (settings.nTable.id == item[0].id) {
                    var iFini = fromEl.val();
                    var iFfin = toEl.val();
                    var fromDataSel = fromEl.attr('data-datatable-filter-from');
                    var toDataSel = toEl.attr('data-datatable-filter-to');
                    var iStartDateCol = 6;
                    var iEndDateCol = 7;

                    //console.log(iFini,iFfin,fromDataSel,toDataSel,iStartDateCol,iEndDateCol);

                    var colPos = fromEl.is('[data-datatable-filter-col]') ? fromEl.attr('data-datatable-filter-col') : 2;

                    //var startDate =
                    //console.log(fromEl.val(), toEl.val(), oSettings, aData, iDataIndex);
                    //console.log(aData[colPos]);
                    //console.log(whatelse);

                    iFini=iFini.substring(6,10) + iFini.substring(3,5)+ iFini.substring(0,2);
                    iFfin=iFfin.substring(6,10) + iFfin.substring(3,5)+ iFfin.substring(0,2);

                    //console.log(iFini, iFfin);

                    var datofini = $(aRawData[colPos]).find(fromDataSel).text().substring(6,10) + $(aRawData[colPos]).find(fromDataSel).text().substring(3,5) + $(aRawData[colPos]).find(fromDataSel).text().substring(0,2);
                    var datoffin = $(aRawData[colPos]).find(toDataSel).text().substring(6,10) + $(aRawData[colPos]).find(toDataSel).text().substring(3,5) + $(aRawData[colPos]).find(toDataSel).text().substring(0,2);
                    //var datofini = aData[colPos].substring(6,10) + aData[colPos].substring(3,5) + aData[colPos].substring(0,2);
                    //var datoffin = aData[colPos].substring(6,10) + aData[colPos].substring(3,5) + aData[colPos].substring(0,2);
                    //console.log('fromDataSel:'+fromDataSel, 'toDataSel:'+toDataSel);
                    //console.log('datofini:'+datofini, 'datoffin:'+datoffin);

                    if ( iFini === "" && iFfin === "" )
                    {
                        //console.log('iFini === "" && iFfin === "" ');
                        return true;
                    }
                    else if ( iFini <= datofini && iFfin === "")
                    {
                        //console.log('iFini <= datofini && iFfin === ""');
                        return true;
                    }
                    else if ( iFfin >= datoffin && iFini === "")
                    {
                        //console.log('iFfin >= datoffin && iFini === ""');
                        return true;
                    }
                    else if (iFini <= datofini && iFfin >= datoffin)
                    {
                        //console.log('iFini <= datofini && iFfin >= datoffin');
                        return true;
                    }
                    return false;
                }else{
                    return true;
                }
            }
        );
    },
    isDataTable:function ( nTable ){
        var settings = $.fn.dataTableSettings;
        for ( var i=0, iLen=settings.length ; i<iLen ; i++ )
        {
            if ( settings[i].nTable == nTable )
            {
                return true;
            }
        }
        return false;
    }
};



/**
 * Keywords Controllers
 */
EW["kw-list-controller"] = {
    settings: {
        selector: '.kw-list-controller'
    },
    dependencies: ['select2'],
    init: function (wrapper) {
        var module = this;
        var items = $(module.settings.selector, wrapper);

        /*if (items.length) {
            if (!module.dependencies.select2.status) {
                $.getScript(APP_URL + 'plugins/select2/' + module.dependencies.select2.path, function (data, textStatus, jqxhr) {
                    module.dependencies.select2.status = true;
                    module.activate(items);
                });
            } else {
                module.activate(items);
            }

        }*/
        module.activate(items);

    },
    activate: function (items) {

        items.each(function (index, el) {
            var item = $(el);
            //if(item.data('select2') !== undefined){

            if (item.is('[data-selected-options]')) {
                var selectedvalues = item.attr('data-selected-options').split(',')
                //console.log(selectedvalues);
                $(selectedvalues).each(function(index, value) {
                   // console.log(value);
                    item.find('option[value="'+value+'"]').prop('selected', true);
                });
            }

            if(item.is('[data-allow-new-tags]') && item.attr('data-allow-new-tags') == 'true'){
                item.select2({
                    allowClear: true,
                    placeholder: "Type to select or add new and press Enter to select",
                    //selectOnClose: true,
                    tags: true
                }).on('change', function() {        //jquery validate triggers only on blur
                    $(this).trigger('blur');
                });
            }else{
                item.select2({
                    allowClear: true,
                    //selectOnClose: true,
                    placeholder: "Type to select"
                }).on('change', function() {        //jquery validate triggers only on blur
                    $(this).trigger('blur');
                });
            }


        });
    }
};



/**
 * Embeded video player
 */
EW['video-player-embed'] = {
    dependencies: ['videojs','videojscss'],
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
    dependencies: ['slick'],
    init: function(wrapper) {
        var slideitems = $('#bannerArea .sliderBackground');
        var itemscount = slideitems.length;
        /*slideitems.hide();
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
        },  5000);*/
        var bannerSlider = $('.header-promo-listwrapper');
        bannerSlider.on('beforeChange', function(event, slick, currentSlide, nextSlide){
            var nextitem = $(slick.$slides[nextSlide]);
            nextitem.children().css( 'background-image', 'url(' + nextitem.find('img:first').attr('data-src') + ')' );
        });
        bannerSlider.slick({
            lazyLoad: 'ondemand',
            infinite: true,
            speed: 500,
            fade: true,
            cssEase: 'linear',
            autoplay: true,
            autoplaySpeed: 7000,
            arrows: false,
            dots: true
        });
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
            'monochrome-grey': [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#085c4d"},{"visibility":"on"}]}],
            'monochrome-green': [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#085c4d"},{"visibility":"on"}]}],
            'monochrome-blue': [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#094375"},{"visibility":"on"}]}]
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
                    icon: APP_URL + 'graphics/ham-map-marker.png'
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


/////////////////////////  REFERENCES PREVIEW MODULES //////////////////////////////////

/**
 * Viewer Gateway
 */
EW['viewer-gateway'] = (function () {

    var settings = {
        selector: '[data-view]',
        urlcheckFILEID: APP_URL + 'ajxDt.php?uni=' + UNI + '&apprcss=getReferenceInfo&file_id=',
        urlcheckCID: APP_URL + 'ajxDt.php?uni=' + UNI + '&apprcss=getReferenceInfo&CID=',
        urlcheckFILENAME: APP_URL + 'ajxDt.php?uni=' + UNI + '&apprcss=getReferenceInfo&file_name='
    }

    $('body').on('click.AssetViewer', settings.selector, handleclick);

    function handleclick(event){
        event.preventDefault();
        var trigger = $(event.currentTarget);

        var url = trigger.is('[data-url]') ? trigger.attr('data-url') : trigger.attr('href');
        var referenceobject = getReferenceType(url);

        if (referenceobject === false) {
            return;
        }

        $.ajax({
            url: referenceobject.checkurl + referenceobject.reference,
            type: 'POST',
            dataType: 'json'
        })
        .always(function(responsedata, textStatus, jqXHR) {
            if (textStatus === 'success') {
                if (responsedata.errorcode === 0) {
                    callViewer(responsedata.data, trigger);
                }
            }
        });

    }

    function callViewer(data, trigger){
        //if (data['ico_type'] === 'video' || data['ico_type'] === 'image') {
        if (data['ico_type'] === 'image') {
            trigger.attr({
                'data-width': data['file_width'],
                'data-height': data['file_height']
            });
        }

        if (data['ico_type'] === 'pdf') {
            EW['pdf-viewer'].openviewer(trigger);
        }
        else if (data['ico_type'] === 'video' || data['ico_type'] === 'audio') {
            EW['video-player'].openviewer(trigger);
        }
        else if (data['ico_type'] === 'image') {
            EW['image-html-viewer'].openviewer(trigger);
        }
    };

    function getReferenceType(url){
        var returnobject = false;

        var referencetype = false;
        var reference = false;
        var checkurl = false;

        if (url.indexOf('file_id=') !== -1) {
            referencetype = 'fileid';
            checkurl = settings.urlcheckFILEID;
            var ref = url.split('file_id=')[1].split('&')[0];
            reference = ref;
        }
        else if (url.indexOf('cid=') !== -1) {
            referencetype = 'cid'
            checkurl = settings.urlcheckCID;
            var ref = url.split('cid=')[1].split('&')[0];
            reference = ref;
        }
        else {
            referencetype = 'cid'
            checkurl = settings.urlcheckFILENAME;
            reference = url.split('/').pop();
        }

        if ( referencetype !== false && reference !== false && checkurl !== false ) {
            returnobject = {
                referencetype: referencetype,
                reference: reference,
                checkurl: checkurl
            }
        }

        return returnobject;

    }

    return {
        init: function(){
            //init function for compatibility ini case pushed in modules.
        },
        getreferencetype: getReferenceType
    }
}());


/**
 * References Video Player Module
 */

EW['video-player'] = {
    dependencies: ['videojs','videojscss','bowser','jqueryui','jqueryuicss'],
    settings: {
        playerhtml: '<video id="video-player" class="video-js vjs-default-skin" controls preload="none" width="100%" height="100%"></video>',
        modalwidth: 802
    },
    player: null,
    init: function (b) {
        var settings = this.settings;
        var modalwin = $('#video-modal').length ? $('#video-modal') : $('<div id="video-modal"></div>').appendTo('body');
        var modaliconvideo = '<i class="fa fa-fw fa-file-video-o" class="text-info" style="margin-right: 5px;"></i>';
        var modaliconaudio = '<i class="fa fa-fw fa-file-video-o" class="text-info" style="margin-right: 5px;"></i>';
        var defaultTitle = 'View Video';

        // modalbody.html(playerhtml);

        /*
         * var player = videojs("video-player", {}, function(){ //
         * ////console.log('player initialized'); });
         */

        var mwidth = EW.utilities.checkModalSize(settings.modalwidth).width;
        var mheight = (mwidth * 9) / 16;
        //console.log(mwidth);
        modalwin.dialog({
            autoOpen: false,
            width: mwidth,
            height: mheight,
            show: {effect: "drop", direction: "up", duration: 500},
            hide: {effect: "drop", direction: "up", duration: 500},
            dialogClass: 'dialog-compact dialog-video dialog-reference',
            resizable: true,
            open: function (event, ui) {
                // player.play();

                // Pause Presentation player
                $(document).trigger('slidePause');

                if (modalwin.data('trigger')) {
                    var trigger = modalwin.data('trigger');
                    var newsource = EW['video-player'].getSource(trigger);
                    EW['video-player'].createPlayer(modalwin, newsource.src, newsource.time);
                }
            },
            beforeClose: function (event, ui) {
                EW['video-player'].destroyPlayer();
            },
            close: function (event, ui) {
                //console.log(modalwin.data('trigger'));
                //console.log(modalwin.data('documentId'));
                EW.analytics.trigger('close', modalwin.data('documentId'), modalwin.data('trigger'));
                modalwin.data('trigger', false);
                modalwin.data('documentId', false);

                clearInterval(modalwin.data('analyticsinteravl'));
                modalwin.data('analyticsinteravl', false);
            }
        });

        $(window).on('resize', function(event) {
            event.preventDefault();
            if ( modalwin.dialog('option', 'width') > $(window).width()) {
                modalwin.dialog('option', 'width', EW.utilities.checkModalSize(settings.modalwidth).width);
                modalwin.dialog('option', 'height', (modalwin.dialog('option', 'width') * 9) / 16);
            }
        });

        modalwin.data('analyticsinteravl', false);
        modalwin.data('trigger', false);
        modalwin.data('documentId', false);

        if (modalwin.length) {
            modalwin.data("uiDialog")._title = function (title) {
                title.html(this.options.title);
            };
        }


        //$('body').on('click', 'a.view-video, a[data-view="video"], a.view-audio, a[data-view="audio"]', function (e) {
        $('body').on('click', 'a.view-video, a.view-audio', function (e) {
            e.preventDefault();
            var trigger = $(e.currentTarget);


        });

        function openViewer(triggerElement){
            var trigger = triggerElement instanceof jQuery ? triggerElement : $(triggerElement);

            var modalicon = modaliconvideo;
            if (trigger.hasClass('view-audio') || trigger.is('[data-view="audio"]')) {
                modalicon = modaliconaudio;
            }

            var newtitle = defaultTitle;
            if (trigger.is('[data-title]')) {
                newtitle = trigger.attr('data-title');
            } else if (trigger.is('[title]')) {
                newtitle = trigger.attr('title');
            }

            if (trigger.is('[data-width]'))     mwidth = trigger.attr('data-width');
            if (trigger.is('[data-height]'))    mheight = trigger.attr('data-height');

            var newsource = EW['video-player'].getSource(trigger);

            if (bowser.tablet === true || bowser.mobile === true) {
                if (modalwin.dialog("isOpen")) {
                    modalwin.dialog('close');
                }
                var newwin = window.open(APP_URL + 'plugins/videojs/index.html?file=' + newsource.src, '_blank');
                if(newwin.document) { // if loaded
                    newwin.document.title = newtitle; // set title
                } else { // if not loaded yet
                    setTimeout(function(){
                        newwin.document.title = newtitle;
                    }, 1000); // check in another 10ms
                }

                newwin.addEventListener('load', function(event){
                    //console.log(newwin);
                    //console.log('Popup PDF LOADED', event);
                    $(document).trigger('EW.ViewerLoaded', {viewer:newwin, type:'popup', referenceType:'internal'});
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }, false);
                newwin.onbeforeunload = function(e){
                    EW.analytics.trigger('close', trigger.attr('data-id'), newwin);
                }

                return;
            }

            if (!modalwin.dialog("isOpen")) {
                if (trigger.is('[data-dialog-apendto]')) {
                    modalwin.dialog({appendTo: trigger.attr('data-dialog-apendto')});
                } else {
                    modalwin.dialog({appendTo: 'body'});
                }
            }

            if (!modalwin.dialog("isOpen")) {
                modalwin.dialog({title: modalicon + newtitle, width: mwidth, height: mheight});
                modalwin.data('trigger', trigger);
                modalwin.dialog('open');
            } else {
                modalwin.dialog({title: modalicon + newtitle});
                if (EW['video-player'].getSource(modalwin.data('trigger')) != EW['video-player'].getSource(trigger)) {
                    EW['video-player'].loadSource(newsource.src, newsource.time);
                }
                modalwin.data('trigger', trigger);
            }

            // Close previously open document
            if (modalwin.data('documentId')) {
                EW.analytics.trigger('close', modalwin.data('documentId'));
            }

            // Set new document ID
            if (trigger.is('[data-id]')) {
                modalwin.data('documentId', trigger.attr('data-id'));
            }else{
                modalwin.data('documentId', false);
            }

            clearInterval(modalwin.data('analyticsinteravl'));
            modalwin.data('analyticsinteravl', false);

            if (trigger.is('[data-id]')) {
                if (EW.analytics.worker !== false) {
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }else{
                    if (typeof ANALYTICS_TIMER !== 'undefined') {
                        modalwin.data({
                            'analyticsinteravl': setInterval(function(){
                                EW.analytics.trigger('update', trigger.attr('data-id'), trigger);
                            }, ANALYTICS_TIMER)
                        });
                    }
                }

            }
        }

        this.openviewer = openViewer;

    },
    createPlayer: function (modalwin, source, time) {
        var settings = EW['video-player'].settings;
        if (EW['video-player'].player === null) {
            modalwin.html(settings.playerhtml);
            EW['video-player'].player = videojs("video-player", {}, function () {
                //  ////console.log('player initialized');
                var playerinstance = this;
                var playingTriggerDelay = 5;
                var lastplaytime;

                setTimeout(function () {
                    // ////console.log(EW['video-player'].player);
                    EW['video-player'].player.on('timeupdate', function(){
                        if (!EW['video-player'].player.paused()) {
                            var currentTime = EW['video-player'].player.currentTime();
                            var sessionPlayTime = Math.ceil(currentTime);
                            if (sessionPlayTime && (sessionPlayTime != lastplaytime) && (sessionPlayTime%playingTriggerDelay === 0)) {
                                lastplaytime = sessionPlayTime;
                                $(document).trigger('ewvideoplayer.playing');
                            }
                        }
                    });
                    EW['video-player'].loadSource(source, time);
                }, 500);
                // EW['video-player'].player.src({ type: "rtmp/mp4", src:
                // source });
                // EW['video-player'].player.play();
            });
        }

    },
    destroyPlayer: function () {
        if (EW['video-player'].player !== null) {
            EW['video-player'].player.dispose();
            EW['video-player'].player = null;
        }
    },
    loadSource: function (source, time) {
        if (EW['video-player'].player !== null) {
            var checksource = source.substring(0, 4);
            if (checksource == 'rtmp') {
                // ////console.log('is rtmp: '+checksource);
                EW['video-player'].player.src({type: "rtmp/mp4", src: source});
            } else {
                // ////console.log('not rtmp: '+checksource);
                EW['video-player'].player.src({src: source});
            }

            EW['video-player'].player.play();

            if (time !== null) {
                //////console.log('HAS TIMER: '+time);
                setTimeout(function () {
                    EW['video-player'].player.currentTime(time);
                    // ////console.log('HAS TIMER: '+time);
                }, 1000);
            }

        }
    },
    getSource: function (item) {
        var src = null;
        /*if (item.is('[data-url]')) {
            src = item.attr('data-url');
        } else if (item.is('[href]')) {
            src = item.attr('href');
        }*/
        if (item.is('[href]') && item.attr('href') !== '') {
            src = item.attr('href');
        }

        var timer = null;
        if (item.is('[data-start-time]')) {
            timer = EW['video-player'].convertTime(item.attr('data-start-time'));
        }

        var source = {
            src: src,
            time: timer
        };

        source.src = source.src.indexOf('?') !== -1 ? source.src + '&uni=' + uniqueid : source.src + '?uni=' + uniqueid;

        return source;
    },
    convertTime: function (timer) {
        var times = timer.split(":");
        var minutes = times[0];
        var seconds = times[1];
        seconds = parseInt(seconds, 10) + (parseInt(minutes, 10) * 60);
        return seconds;
    }
};

/**
 * Vide Player with Virtual Slides
 * Makes use of Presentation Player
 */
EW['view-virtual-slide'] = {
    dependencies: ['bowser','jqueryui','jqueryuicss'],
    settings: {
        modalwidth: 1057,
        modalheight: 700
    },
    init: function (b) {
        var settings = this.settings;
        var modalwin = $('#virtual-slide-video').length ? $('#virtual-slide-video') : $('<div id="virtual-slide-video"></div>').appendTo('body');
        var modalicon = '<i class="fa fa-play fa-fw text-info" class="text-info" style="margin-right: 5px;"></i>';
        var defaultTitle = 'View Video';

        // Initialize modal dialog
        var modalwidth = EW.utilities.checkModalSize(settings.modalwidth).width;

        var modalheight = modalwidth / 2;
        modalwin.dialog({
            autoOpen: false,
            width: modalwidth,
            height: modalheight,
            show: {effect: "drop", direction: "up", duration: 500},
            hide: {effect: "drop", direction: "up", duration: 500},
            dialogClass: 'dialog-compact dialog-reference',
            minWidth: 965,
            minHeight: 430,
            open: function () {
                $(document).trigger('slidePause');
            },
            close: function (event, ui) {
                modalwin.empty();
            }
        });

        $(window).on('resize', function(event) {
            event.preventDefault();
            if ( modalwin.dialog('option', 'width') > $(window).width()) {
                modalwin.dialog('option', 'width', EW.utilities.checkModalSize(settings.modalwidth).width);
                modalwin.dialog('option', 'height', modalwin.dialog('option', 'width') / 2);
            }
        });

        if (modalwin.length) {
            modalwin.data("uiDialog")._title = function (title) {
                title.html(this.options.title);
            };
        }

        $('body').on('click', 'a.view-virtual-slide', function (e) {
            e.preventDefault();
            var trigger = $(e.currentTarget);

            var newtitle = defaultTitle;
            if (trigger.is('[data-title]')) {
                newtitle = trigger.attr('data-title');
            } else if (trigger.is('[title]')) {
                newtitle = trigger.attr('title');
            }

            modalwin.dialog({title: modalicon + newtitle});

            if (!modalwin.dialog("isOpen")) {
                if (trigger.is('[data-dialog-apendto]')) {
                    modalwin.dialog({appendTo: trigger.attr('data-dialog-apendto')});
                } else {
                    modalwin.dialog({appendTo: 'body'});
                }
            }

            var key = "";
            var url = "";
            var id = "";

            if (trigger.is('[data-key]'))   key = trigger.attr('data-key');
            if (trigger.is('[data-url]'))   url = trigger.attr('data-url');
            if (trigger.is('[data-id]'))    id = trigger.attr('data-id');

            var theurl = "";
            if (key == "URL" && url !== "") {
                theurl = url;
            } else {
                theurl = _ajx + '&apprcss=videoVS&cis=' + trigger.attr('data-id');
            }

            if (bowser.tablet === true || bowser.mobile === true) {
                if (modalwin.dialog("isOpen")) {
                    modalwin.dialog('close');
                }
                var viewer = window.open(theurl, '_blank');

                viewer.addEventListener('load', function(event){
                    //console.log(viewer);
                    //console.log('Popup PDF LOADED', event);
                    viewer.location.reload();
                    $(document).trigger('EW.ViewerLoaded', {viewer:viewer, type:'popup', referenceType:'internal'});
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }, false);
                viewer.onbeforeunload = function(e){
                    EW.analytics.trigger('close', trigger.attr('data-id'), viewer);
                }

                return;
            }

            var viewer = '<iframe id="refViewer" class="iframe-scroll" src="' + theurl + '" style="border: 0;" scrolling="no" width="100%" height="100%" allowfullscreen></iframe>';
            modalwin.html(viewer);
            if (!modalwin.dialog("isOpen")) {
                modalwin.dialog('open');
            }

        });
    }
};

/**
 * External References Viewer
 */
 
EW['ref-link-viewer'] = {
    dependencies: ['bowser','jqueryui','jqueryuicss'],
    settings: {
        modalwidth: 800
    },
    init: function (b) {
        var settings = this.settings;
        var modalwin = $('#ref-modal').length ? $('#ref-modal') : $('<div id="ref-modal"></div>').appendTo('body');
        var modalicon = '<i class="fa fa-file-text-o fa-fw text-info" class="text-info" style="margin-right: 5px;"></i>';
        var defaultTitle = 'View Reference';

        // Initialize modal dialog
        var modalwidth = EW.utilities.checkModalSize(settings.modalwidth).width;
        var modalheight = EW.utilities.checkModalSize().width;
        modalwin.dialog({
            autoOpen: false,
            width: modalwidth,
            height: modalheight,
            show: {effect: "drop", direction: "up", duration: 500},
            hide: {effect: "drop", direction: "up", duration: 500},
            dialogClass: 'dialog-compact dialog-reference',
            open: function () {
                $(document).trigger('slidePause');
            },
            close: function (event, ui) {
                modalwin.empty();

                //console.log(modalwin.data('trigger'));
                //console.log(modalwin.data('documentId'));
                EW.analytics.trigger('close', modalwin.data('documentId'), modalwin.data('trigger'));
                modalwin.data('trigger', false);
                modalwin.data('documentId', false);
                clearInterval(modalwin.data('analyticsinteravl'));
                modalwin.data('analyticsinteravl', false);
            }
        });

        $(window).on('resize', function(event) {
            event.preventDefault();
            if ( modalwin.dialog('option', 'width') > $(window).width()) {
                modalwin.dialog('option', 'width', EW.utilities.checkModalSize(settings.modalwidth).width);
            }
            if ( modalwin.dialog('option', 'height') > $(window).height()) {
                modalwin.dialog('option', 'height', EW.utilities.checkModalSize().height);
            }
        });
        
        modalwin.data('analyticsinteravl', false);
        modalwin.data('trigger', false);
        modalwin.data('documentId', false);
//" data-popup-viewer
        if (modalwin.length) {
            modalwin.data("uiDialog")._title = function (title) {
                title.html(this.options.title);
            };
        }

        $('body').on('click', 'a.view-link-ref, a[data-popup-viewer]', function (e) {
            e.preventDefault();
            var trigger = $(e.currentTarget);


            /**
             * DIRTY TRICK FOR DEMO ONLY
             * Eshte perdorur per te hapur regjistrimin nga nje additional resource.
             * Item ne fjale duhet qe te kete identifier te tipit URL me vlere "call-registration"
             */
            if (trigger.attr('data-url') === 'http://call-registration') {
                if (modalwin.dialog("isOpen")) {
                    modalwin.dialog('close');
                }
                $('#register-trigger').trigger('click');
                return;
            }
            //register-trigger

            var newtitle = defaultTitle;
            if (trigger.is('[data-title]')) {
                newtitle = trigger.attr('data-title');
            } else if (trigger.is('[title]')) {
                newtitle = trigger.attr('title');
            }

            modalwin.dialog(
                {title: modalicon + newtitle}
            );




            modalwin.empty();
            var width = settings.modalwidth;;
            if (trigger.is('[data-width]') && trigger.attr('data-width')!="" && trigger.attr('data-width')>0){
                width = Number( trigger.attr('data-width') ) + 20;
            }
            var height = width/2;
            if (trigger.is('[data-height]') && trigger.attr('data-height')!="" && trigger.attr('data-height')>0){
                height = Number( trigger.attr('data-height') ) + 50;
            }
            var checksize = EW.utilities.checkModalSize(width, height);

            if (width == checksize.width &&  height == checksize.height) {
                //console.log('smaller', neWidth, newHeight);
                modalwin.dialog('option', 'width', width);
                modalwin.dialog('option', 'height', height);
            }

        //  console.log("width:"+trigger.attr('data-width')+" - "+width+";height:"+trigger.attr('data-height')+" - "+height);
            if (!modalwin.dialog("isOpen")) {
                if (trigger.is('[data-dialog-apendto]')) {
                    modalwin.dialog({appendTo: trigger.attr('data-dialog-apendto')});
                } else {
                    modalwin.dialog({appendTo: 'body'});
                }
            }

            var key = "";
            var url = "";
            var id = "";

            if (trigger.is('[data-key]')){
                key = trigger.attr('data-key');
            }
            if (trigger.is('[data-url]')){
                url = trigger.attr('data-url');
            }

            if (trigger.is('[data-popup-viewer]')){
                id = trigger.attr('data-popup-viewer');
            } else if (trigger.is('[data-id]')){
                id = trigger.attr('data-id');
            }

            var theurl = "";
            if (key == "URL" ) {
                theurl = url;
                if (theurl.indexOf('https://') === -1 || url.indexOf('google') > -1 || url.indexOf('amazon') > -1 || url.indexOf('abebooks') > -1) {
                    window.open(theurl);
                    EW.analytics.trigger('openonce', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                    return;
                }
            } else {
                if (trigger.is('[data-prc]')){
                        theurl = _ajx + '&apprcss='+trigger.attr('data-prc')+'&cis='+id;
                } else  theurl = _ajx + '&apprcss=getReference&cis='+id;

                if (trigger.is('[data-aid]')){
                    theurl = theurl + "&aid="+ trigger.attr('data-aid');
                }
                if (trigger.is('[data-pid]')){
                    theurl = theurl + "&itemId="+ trigger.attr('data-pid');
                }
            }

            if (bowser.tablet === true || bowser.mobile === true) {
                if (modalwin.dialog("isOpen")) {
                    modalwin.dialog('close');
                }

                var viewer = window.open(theurl, '_blank');

                viewer.addEventListener('load', function(event){
                    //console.log(viewer);
                    //console.log('Popup PDF LOADED', event);
                    $(document).trigger('EW.ViewerLoaded', {viewer:viewer, type:'popup', referenceType:'internal'});
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }, false);
                viewer.onbeforeunload = function(e){
                    EW.analytics.trigger('close', trigger.attr('data-id'), viewer);
                }

                return;
            }

            var viewer = '<iframe id="refViewer" class="iframe-scroll" src="' + theurl + '" style="border: 0;" width="100%" height="100%" allowfullscreen></iframe>';

            modalwin.html(viewer);
            if (!modalwin.dialog("isOpen")) {
                modalwin.dialog('open');
            }

            modalwin.data('trigger', trigger);

            // Close previously open document
            if (modalwin.data('documentId')) {
                EW.analytics.trigger('close', modalwin.data('documentId'));
            }

            // Set new document ID
            if (trigger.is('[data-id]')) {
                modalwin.data('documentId', trigger.attr('data-id'));
            }else{
                modalwin.data('documentId', false);
            }

            clearInterval(modalwin.data('analyticsinteravl'));
            modalwin.data('analyticsinteravl', false);

            if (trigger.is('[data-id]')) {
                // Trigger OPEN event
                if (EW.analytics.worker !== false) {
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }else{
                    if (typeof ANALYTICS_TIMER !== 'undefined') {
                        modalwin.data({
                            'analyticsinteravl': setInterval(function(){
                                // Trigger Update event
                                EW.analytics.trigger('update', trigger.attr('data-id'), trigger);
                            }, ANALYTICS_TIMER)
                        });
                    }
                }
            }

        });
    }
};



EW['ref-viewer'] = {
    dependencies: ['bowser','jqueryui','jqueryuicss'],
    settings: {
        modalwidth: 800
    },
    init: function (b) {
        var settings = this.settings;
        var modalwin = $('#ref-modal').length ? $('#ref-modal') : $('<div id="ref-modal"></div>').appendTo('body');
        var modalicon = '<i class="fa fa-file-text-o fa-fw text-info" class="text-info" style="margin-right: 5px;"></i>';
        var defaultTitle = 'View Reference';

        // Initialize modal dialog
        var modalwidth = EW.utilities.checkModalSize(settings.modalwidth).width;
        var modalheight = EW.utilities.checkModalSize().height;

        //console.log('modalheight',modalheight);
        modalwin.dialog({
            autoOpen: false,
            width: modalwidth,
            height: modalheight,
            show: {effect: "drop", direction: "up", duration: 500},
            hide: {effect: "drop", direction: "up", duration: 500},
            dialogClass: 'dialog-compact dialog-reference',
            open: function () {
                $(document).trigger('slidePause');
            },
            close: function (event, ui) {
                modalwin.empty();

                //console.log(modalwin.data('trigger'));
                //console.log(modalwin.data('documentId'));
                EW.analytics.trigger('close', modalwin.data('documentId'), modalwin.data('trigger'));
                modalwin.data('trigger', false);
                modalwin.data('documentId', false);
                clearInterval(modalwin.data('analyticsinteravl'));
                modalwin.data('analyticsinteravl', false);
            }
        });

        $(window).on('resize', function(event) {
            event.preventDefault();
            if ( modalwin.dialog('option', 'width') > $(window).width()) {
                modalwin.dialog('option', 'width', EW.utilities.checkModalSize(settings.modalwidth).width);
            }
            if ( modalwin.dialog('option', 'height') > $(window).height()) {
                modalwin.dialog('option', 'height', EW.utilities.checkModalSize().height);
            }
        });

        modalwin.data('analyticsinteravl', false);
        modalwin.data('trigger', false);
        modalwin.data('documentId', false);

        if (modalwin.length) {
            modalwin.data("uiDialog")._title = function (title) {
                title.html(this.options.title);
            };
        }

        $('body').on('click', 'a.view-ref', function (e) {
            e.preventDefault();
            var trigger = $(e.currentTarget);


            /**
             * DIRTY TRICK FOR DEMO ONLY
             * Eshte perdorur per te hapur regjistrimin nga nje additional resource.
             * Item ne fjale duhet qe te kete identifier te tipit URL me vlere "call-registration"
             */
            if (trigger.attr('data-url') === 'http://call-registration') {
                if (modalwin.dialog("isOpen")) {
                    modalwin.dialog('close');
                }
                $('#register-trigger').trigger('click');
                return;
            }
            //register-trigger

            var newtitle = defaultTitle;
            if (trigger.is('[data-title]')) {
                newtitle = trigger.attr('data-title');
            } else if (trigger.is('[title]')) {
                newtitle = trigger.attr('title');
            }

            modalwin.dialog({title: modalicon + newtitle});

            if (!modalwin.dialog("isOpen")) {
                if (trigger.is('[data-dialog-apendto]')) {
                    modalwin.dialog({appendTo: trigger.attr('data-dialog-apendto')});
                } else {
                    modalwin.dialog({appendTo: 'body'});
                }
            }

            var key = "";
            var url = "";
            var id = "";

            //luetra 30/07/2015


            if (trigger.is('[data-key]')){
                key = trigger.attr('data-key');
            }
            if (trigger.is('[data-url]')){
                url = trigger.attr('data-url');
            }
            if (trigger.is('[data-id]')){
                id = trigger.attr('data-id');
            }

            var theurl = "";
            if (key == "URL" ) {
                theurl = url;
                if (theurl.indexOf('https://') === -1 || url.indexOf('google') > -1 || url.indexOf('amazon') > -1 || url.indexOf('abebooks') > -1) {
                    var viewer = window.open(theurl);
                    EW.analytics.trigger('openonce', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                    return;
                }
            } else {
                theurl = _ajx + '&apprcss=getReference&cis='+trigger.attr('data-id');
                if (trigger.is('[data-aid]')){
                    theurl = theurl + "&aid="+ trigger.attr('data-aid');
                }
            }


            if (bowser.tablet === true || bowser.mobile === true) {
                if (modalwin.dialog("isOpen")) {
                    modalwin.dialog('close');
                }

                var viewer = window.open(theurl);

                viewer.addEventListener('load', function(event){
                    //console.log(viewer);
                    //console.log('Popup PDF LOADED', event);
                    $(document).trigger('EW.ViewerLoaded', {viewer:viewer, type:'popup', referenceType:'internal'});
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }, false);
                viewer.onbeforeunload = function(e){
                    EW.analytics.trigger('close', trigger.attr('data-id'), viewer);
                }

                return;
            }

            var viewer = '<iframe id="refViewer" class="iframe-scroll" src="' + theurl + '" style="border: 0;" width="100%" height="100%" allowfullscreen></iframe>';

            //modalwin.html(viewer);
            viewer = $(viewer);
            modalwin.html(viewer);
            //console.log(viewer);

            viewer.on('load', function(event) {
                event.preventDefault();
                //console.log('PDF LOADED', viewer.contents());
                $(document).trigger('EW.ViewerLoaded', {viewer:viewer[0], type:'iframe', referenceType:'internal'});
            });

            if (!modalwin.dialog("isOpen")) {
                modalwin.dialog('open');
            }

            modalwin.data('trigger', trigger);

            // Close previously open document
            if (modalwin.data('documentId')) {
                EW.analytics.trigger('close', modalwin.data('documentId'));
            }

            // Set new document ID
            if (trigger.is('[data-id]')) {
                modalwin.data('documentId', trigger.attr('data-id'));
            }else{
                modalwin.data('documentId', false);
            }

            clearInterval(modalwin.data('analyticsinteravl'));
            modalwin.data('analyticsinteravl', false);

            if (trigger.is('[data-id]')) {
                // Trigger OPEN event
                if (EW.analytics.worker !== false) {
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }else{
                    if (typeof ANALYTICS_TIMER !== 'undefined') {
                        modalwin.data({
                            'analyticsinteravl': setInterval(function(){
                                // Trigger Update event
                                EW.analytics.trigger('update', trigger.attr('data-id'), trigger);
                            }, ANALYTICS_TIMER)
                        });
                    }
                }
            }

        });
    }
};

/**
 * PDF Viewer
 */
EW['pdf-viewer'] = {
    dependencies:['bowser','jqueryui','jqueryuicss'],
    settings: {
        modalwidth: 800
    },
    init: function (b) {
        var settings = this.settings;
        var modalwin = $('#pdf-modal').length ? $('#pdf-modal') : $('<div id="pdf-modal"></div>').appendTo('body');
        var modalicon = '<i class="fa fa-fw fa-file-pdf-o" class="text-info" style="margin-right: 5px;"></i>';
        var defaultTitle = 'View PDF';

        // Initialize modal dialog
        var modalwidth = EW.utilities.checkModalSize(settings.modalwidth).width;
        var modalheight = EW.utilities.checkModalSize().height;

        modalwin.dialog({
            autoOpen: false,
            width: modalwidth,
            height: modalheight,
            show: {effect: "drop", direction: "up", duration: 500},
            hide: {effect: "drop", direction: "up", duration: 500},
            dialogClass: 'dialog-compact dialog-reference',
            create: function(event, ui){
                modalwin.addClass('ui-dialog-content-beforeopen');
            },
            open: function () {
                $(document).trigger('slidePause');
            },
            close: function (event, ui) {
                modalwin.empty();

                if (!modalwin.hasClass('ui-dialog-content-beforeopen')) {
                    modalwin.addClass('ui-dialog-content-beforeopen');
                }

                //console.log(modalwin.data('trigger'));
                //console.log(modalwin.data('documentId'));
                EW.analytics.trigger('close', modalwin.data('documentId'), modalwin.data('trigger'));
                modalwin.data('trigger', false);
                modalwin.data('documentId', false);
                clearInterval(modalwin.data('analyticsinteravl'));
                modalwin.data('analyticsinteravl', false);
            },
            resizeStart: function(event, ui){
                if (modalwin.hasClass('ui-dialog-content-beforeopen')) {
                    modalwin.removeClass('ui-dialog-content-beforeopen');
                }
            }
        });

        $(window).on('resize', function(event) {
            event.preventDefault();
            if ( modalwin.dialog('option', 'width') > $(window).width()) {
                modalwin.dialog('option', 'width', EW.utilities.checkModalSize(settings.modalwidth).width);
            }
            if ( modalwin.dialog('option', 'height') > $(window).height()) {
                modalwin.dialog('option', 'height', EW.utilities.checkModalSize().height);
            }
        });

        modalwin.data('analyticsinteravl', false);
        modalwin.data('trigger', false);
        modalwin.data('documentId', false);

        if (modalwin.length) {
            modalwin.data("uiDialog")._title = function (title) {
                title.html(this.options.title);
            };
        }


        //$('body').on('click', 'a[data-view="pdf"], a.view-pdf', function (e) {
        $('body').on('click', 'a.view-pdf', function (e) {
            e.preventDefault();
            var trigger = $(e.currentTarget);
            openViewer(trigger);
        });


        function openViewer(triggerElement){
            var trigger = triggerElement instanceof jQuery ? triggerElement : $(triggerElement);

            var newtitle = defaultTitle;
            if (trigger.is('[data-title]')) {
                newtitle = trigger.attr('data-title');
            } else if (trigger.is('[title]')) {
                newtitle = trigger.attr('title');
            }

            modalwin.dialog({title: modalicon + newtitle});

            if (!modalwin.dialog("isOpen")) {
                if (trigger.is('[data-dialog-apendto]')) {
                    modalwin.dialog({appendTo: trigger.attr('data-dialog-apendto')});
                } else {
                    modalwin.dialog({appendTo: 'body'});
                }
            }

            //var fileurl = EW["get-url"].geturl(trigger);
            var fileurl = trigger.attr('href');
            var fileprotocol = fileurl.substring(0, 4);

            if (fileprotocol != 'http') {
                fileurl = APP_URL + fileurl;
            }

            fileurl = encodeURIComponent(fileurl);

            if (bowser.tablet === true || bowser.mobile === true) {
                if (modalwin.dialog("isOpen")) {
                    modalwin.dialog('close');
                }
                var viewer = window.open(APP_URL + 'plugins/pdfjs/web/viewer.html?file=' + fileurl, '_blank');

                viewer.addEventListener('load', function(event){
                    //console.log(viewer);
                    //console.log('Popup PDF LOADED', event);
                    $(document).trigger('EW.ViewerLoaded', {viewer:viewer, type:'popup', referenceType:'internal'});
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }, false);
                viewer.onbeforeunload = function(e){
                    console.log('PDF close event', e);
                    EW.analytics.trigger('close', trigger.attr('data-id'), viewer);
                }

                return;
            }

            var viewer = '<iframe id="pdfViewer" class="iframe-scroll" src="' + PDFVIEWER_PLUGIN_URL + '?file=' + fileurl + '" style="border: 0;" width="100%" height="100%" allowfullscreen></iframe>';
            viewer = $(viewer);
            modalwin.html(viewer);
            //console.log(viewer);

            viewer.on('load', function(event) {
                event.preventDefault();
                //console.log('PDF LOADED', viewer.contents());
                $(document).trigger('EW.ViewerLoaded', {viewer:viewer[0], type:'iframe', referenceType:'internal'});
            });


            if (!modalwin.dialog("isOpen")) {
                modalwin.dialog('open');
            }

            modalwin.data('trigger', trigger);

            // Close previously open document
            if (modalwin.data('documentId')) {
                EW.analytics.trigger('close', modalwin.data('documentId'));
            }

            // Set new document ID
            if (trigger.is('[data-id]')) {
                modalwin.data('documentId', trigger.attr('data-id'));
            }else{
                modalwin.data('documentId', false);
            }

            clearInterval(modalwin.data('analyticsinteravl'));
            modalwin.data('analyticsinteravl', false);

            if (trigger.is('[data-id]')) {
                if (EW.analytics.worker !== false) {
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }else{
                    if (typeof ANALYTICS_TIMER !== 'undefined') {
                        modalwin.data({
                            'analyticsinteravl': setInterval(function(){
                                EW.analytics.trigger('update', trigger.attr('data-id'), trigger);
                            }, ANALYTICS_TIMER)
                        });
                    }
                }
            }
        }

        this.openviewer = openViewer;

    }
};

/**
 * Image Viewer
 */
EW['image-html-viewer'] = {
    settings: {
        modalwidth: 600,
        modalMaxHeight: 600
    },
    init: function (b) {
        var settings = this.settings;
        var modalwin = $('#image-html-modal').length ? $('#image-html-modal') : $('<div id="image-html-modal"></div>').appendTo('body');
        var modalicon = '<i class="fa fa-fw fa-picture-o" class="text-info" style="margin-right: 5px;"></i>';
        var defaultTitle = 'Item Preview';

        // Initialize modal dialog
        var modalWidth = EW.utilities.checkModalSize(settings.modalwidth).width;
        var modalheight = EW.utilities.checkModalSize(settings.modalwidth, settings.modalMaxHeight).height;
        //console.log('modalheight', modalheight);
        modalwin.dialog({
            autoOpen: false,
            width: modalWidth,
            height: modalheight,
            //minHeight: 320,
            maxHeight: 600,
            show: {effect: "drop", direction: "up", duration: 500},
            hide: {effect: "drop", direction: "up", duration: 500},
            dialogClass: 'dialog-compact image-html-viewer dialog-reference',
            create: function (event, ui) {
                //////console.log(event);
                //////console.log(ui);
            },
            open: function (event, ui) {
                $(document).trigger('slidePause');

                //console.log(modalwin.data('trigger'));
                //console.log(modalwin.data('documentId'));
                EW.analytics.trigger('close', modalwin.data('documentId'), modalwin.data('trigger'));
                modalwin.data('trigger', false);
                modalwin.data('documentId', false);
                clearInterval(modalwin.data('analyticsinteravl'));
                modalwin.data('analyticsinteravl', false);
            },
            close: function (event, ui) {
                modalwin.empty();
            }
        });

        $(window).on('resize', function(event) {
            event.preventDefault();
            if ( modalwin.dialog('option', 'width') > $(window).width()) {
                modalwin.dialog('option', 'width', EW.utilities.checkModalSize(settings.modalwidth).width);
            }
        });

        modalwin.data('analyticsinteravl', false);
        modalwin.data('trigger', false);
        modalwin.data('documentId', false);

        if (modalwin.length) {
            modalwin.data("uiDialog")._title = function (title) {
                title.html(this.options.title);
            };
        }

        //$('body').on('click', 'a.view-image, a[data-view="image"]', function (e) {
        $('body').on('click', 'a.view-image', function (e) {
            e.preventDefault();
            var trigger = $(e.currentTarget);

        });

        function openViewer(triggerElement){
            var trigger = triggerElement instanceof jQuery ? triggerElement : $(triggerElement);

            var newtitle = defaultTitle;
            if (trigger.is('[data-title]')) {
                newtitle = trigger.attr('data-title');
            } else if (trigger.is('[title]')) {
                newtitle = trigger.attr('title');
            }
            // modalwin.dialog({title: newtitle});
            modalwin.dialog({title: modalicon + newtitle});

            if (!modalwin.dialog("isOpen")) {
                if (trigger.is('[data-dialog-apendto]')) {
                    modalwin.dialog({appendTo: trigger.attr('data-dialog-apendto')});
                } else {
                    modalwin.dialog({appendTo: 'body'});
                }
            }

            //var fileurl = EW["get-url"].geturl(trigger);
            var fileurl = trigger.attr('href');
            var fileprotocol = fileurl.substring(0, 4);

            if (fileprotocol != 'http') {
                fileurl = APP_URL + fileurl;
            }

            modalwin.empty();

            var imageWidth = '';
            var imageHeight = '';
            if (trigger.is('[data-image-width]')) {
                imageWidth = Number( trigger.attr('data-image-width') ) + 20;
            }else{
                imageWidth = 300 + 20;
            }
            if (trigger.is('[data-image-height]')) {
                imageHeight = Number( trigger.attr('data-image-height') ) + 50;
            }else{
                imageHeight = 300 + 50;
            }
            //var viewer = '<div class="image-html-wrapper"><img src="' + fileurl + '" width="100%" style="width:100%;" ></div>'
            var viewer = $('<div class="image-html-wrapper"><div><img src="' + fileurl + '"></div></div>');
            var checksize = EW.utilities.checkModalSize(imageWidth, imageHeight);

            if (imageWidth == checksize.width &&  imageHeight == checksize.height) {
                //console.log('smaller', neWidth, newHeight);
                modalwin.dialog('option', 'width', imageWidth);
                modalwin.dialog('option', 'height', imageHeight);
            }
            if (imageWidth < imageHeight) {
                modalwin.dialog('option', 'height', checksize.height);
                viewer.addClass('image-wide');
                modalwin.dialog('option', 'width', imageWidth);
            }

            viewer.appendTo(modalwin);

            //modalwin.html(viewer);
            if (!modalwin.dialog("isOpen")) {
                modalwin.dialog('open');
            }

            modalwin.data('trigger', trigger);

            // Close previously open document
            if (modalwin.data('documentId')) {
                EW.analytics.trigger('close', modalwin.data('documentId'));
            }

            // Set new document ID
            if (trigger.is('[data-id]')) {
                modalwin.data('documentId', trigger.attr('data-id'));
            }else{
                modalwin.data('documentId', false);
            }

            clearInterval(modalwin.data('analyticsinteravl'));
            modalwin.data('analyticsinteravl', false);

            if (trigger.is('[data-id]')) {
                if (EW.analytics.worker !== false) {
                    EW.analytics.trigger('open', trigger.attr('data-id'), trigger);
                    EW.refreshbasket();
                }else{
                    if (typeof ANALYTICS_TIMER !== 'undefined') {
                        modalwin.data({
                            'analyticsinteravl': setInterval(function(){
                                EW.analytics.trigger('update', trigger.attr('data-id'), trigger);
                            }, ANALYTICS_TIMER)
                        });
                    }
                }

            }
        }

        this.openviewer = openViewer;

    }
};

/**
 * MC Standard module
 */
EW.mcmodule = {
    settings: {
        tabsmenucontainerselector: '.mc-tabs-menu',
        tabsmenuselector: '.dropdown-item',
        tabscontainerselector: '.mc-nav-tabs',
        tabitemselector: '.nav-item'
    },
    init: function () {
        var module = this;
        var settings = module.settings;
        $('body').on('click', settings.tabsmenucontainerselector + ' ' + settings.tabsmenuselector, function(event) {
            event.preventDefault();
            var menuitem = $(event.currentTarget);
            var container = menuitem.parents(settings.tabsmenucontainerselector + ':first');
            var targetselector = menuitem.attr('href');
            var navitem = $('[data-target="' + targetselector + '"]');
            var navitemcontainer = navitem.parents(settings.tabscontainerselector + ':first');
            // Show respective tab
            navitem.trigger('click');
            // Hide previous tab and show current one
            navitemcontainer.find(settings.tabitemselector).hide();
            navitem.show();
            // Set active class on clicked menu item
            container.find('.active').removeClass('active');
            menuitem.addClass('active');
        });

        // Init first active item
        $(settings.tabsmenucontainerselector + ' ' + settings.tabsmenuselector + '.active').trigger('click');
    }
}


/**
 * Datepicker
 */
EW.datepicker = {
    settings: {
        selector: '.datepicker'
    },
    init: function () {
        var module = this;
        var items = $(module.settings.selector);
        items.each(function(index, el) {
            var item = $(el);
            pickeroptions = {
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: "dd.mm.yy",
                closeText: 'Close'
            }

            if (item.is('[data-datepicker-max]') && item.attr('data-datepicker-max') !== '' && $(item.attr('data-datepicker-max')).length) {
                var toPicker = $(item.attr('data-datepicker-max'));
                pickeroptions.maxDate = toPicker.val();
                toPicker.on('change', function(event) {
                    event.preventDefault();
                    item.datepicker( "option", "maxDate", toPicker.val() );
                });
            }
            if (item.is('[data-datepicker-min]') && item.attr('data-datepicker-min') !== '' && $(item.attr('data-datepicker-min')).length) {
                var fromPicker = $(item.attr('data-datepicker-min'));
                pickeroptions.minDate = fromPicker.val();
                fromPicker.on('change', function(event) {
                    event.preventDefault();
                    item.datepicker( "option", "minDate", fromPicker.val() );
                });
            }
            item.datepicker(pickeroptions);
        });
    }
};


/**
 * Summernote WYSIWYG editor
 */
EW["summernote"] = {
    dependencies: ['summernote','summernotecss'],
    multiSelectorSplitter: ", ", /**/
    resizeDelay : 150,
    selector: "[data-summernote]",
    init: function() {
        var module = this;
        var items = $(module.selector);
        console.log('summernote', items);
        items.each(function(index, item) {
            module.activate($(item));
        });
    },
    activate: function(item){
        var module = this;
        var editor = item.find('textarea:first');

        var defaultconfig = {
            height: 150,
            maxHeight: 500,
            shortcuts: false,
            disableDragAndDrop: true,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']]
            ],
            popover: {
                image: []
            }
        }

        var customOptions = item.find('[type="text/summernote-config"]:first');
        if (customOptions.length) {
            var configtext = customOptions.text();
            editorOptions = $.extend({}, defaultconfig, JSON.parse(configtext));
        }

        if (editor.length) {
            editor.summernote(defaultconfig);
        }
    }
};



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
        console.log('equal-heights');
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

/**
 * Member Photo Module
 */
/**
 * Summernote WYSIWYG editor
 */
EW["changememberphoto"] = {
    dependencies: ['jcrop','jcropcss','dropzone'],
    init: function() {
      
		var module = this;
		//go back to user "Meine Personalien"
		var pageToLoadPrm =_uniqueid;				
		//call dropzone init
		EW["changememberphoto"].dropzoneInit(module);
		
		//submit button event listener
		EW["changememberphoto"].submitFinalPhotoButton(module.myDropzone);   
		   
		$( "#goBack" ).on( "click", function() {			  
			if(_go_back_to_main_page=='yes'){
					var $this 		= $(this);
					var href 		= $this.attr('href');
					window.location.href = href; //causes the browser to refresh and load the requested url
			}
		});
	},
	 ShowErrorMessage: function(mesg,type){

				var ifsuccess = (type === "success") ? true : false;
                //var bgcolor = (ifsuccess) ? "#f0fff0" : "#fff0f0";
                var icons = (ifsuccess) ? EW["ajax-module"].settings.notifyIconSuccess : EW["ajax-module"].settings.notifyIconError;
                var type_t = (ifsuccess) ? "success" : "error";  //"notice", "info", "success", or "error".
				
				notify = new PNotify({
					title: mesg,
					icon: icons,
					delay: 2000,
					type:type_t,
					hide: true,
					buttons: {
						closer: false,
						sticker: false
					}
				});
    },
	execute_crop: function(){
	
		//crop image
		// Create variables (in this scope) to hold the API and image size
			var jcrop_api;
			var boundx;
			var boundy;

			//center selection
			x = $('.img_big').width()/4 ;
			y = $('.img_big').height()/4 ;
			x1 = x + $('.img_big').width()/4;
			y1 = y + $('.img_big').width()/4;

            var naturalwidth = $('#previewImg').get(0).naturalWidth;
            var naturalheight = $('#previewImg').get(0).naturalHeight;

           // console.log($('#uploaded_photo').width(), $('#uploaded_photo').width() / (naturalwidth/naturalheight));

			var initjcrop = function(){
                $('#previewImg').Jcrop({
                            aspectRatio: 8/10,                                  //If you want to keep aspectRatio
                            onChange: EW.changememberphoto.updatePreview,       //Called when the selection is moving  [you're referring to the named function, not calling it]
                            onSelect: EW.changememberphoto.updatePreview,       //Called when selection is completed
                            boxWidth: 10000,                                    //Maximum width you want for your bigger images
                            boxHeight: $('#uploaded_photo').width() / (naturalwidth/naturalheight),                                     //Minimum width you want for your bigger images
                            trueSize: [naturalwidth, naturalheight],
                            setSelect:   [ x, y, x1, y1 ]                       //put selection on the center of the photo

                },function(){
                  // Use the API to get the real image size
                  var bounds = this.getBounds();
                  this.boundx = bounds[0];
                  this.boundy = bounds[1];
                  // Store the API in the jcrop_api variable
                  jcrop_api = this;

                  // Move the preview into the jcrop container for css positioning
                  //$('#preview-pane').appendTo(jcrop_api.ui.holder);
                });
            }

            initjcrop();



            $(window).on('resize', function(event) {
                event.preventDefault();
                jcrop_api.destroy();

                initjcrop();

            });
	},
	updatePreview: function(c){
		//get coordinates
		$("#x_mbmPic").val(c.x);   
		$("#y_mbmPic").val(c.y);
		$("#x2_mbmPic").val(c.x2);
		$("#y2_mbmPic").val(c.y2);
		$("#w_mbmPic").val(c.w);   //width i selektimit
		$("#h_mbmPic").val(c.h);   //height i selektimit

		if (parseInt(c.w) > 0)
		{

			xsize   = $('#preview-pane .preview-container').width()
			ysize   = $('#preview-pane .preview-container').height();


			var rx = xsize / c.w;
			var ry = ysize / c.h;

			$('#preview-pane .preview-container img').css({
				width: Math.round(rx * this.boundx) + 'px',
				height: Math.round(ry * this.boundy) + 'px',
				marginLeft: '-' + Math.round(rx * c.x) + 'px',
				marginTop: '-' + Math.round(ry * c.y) + 'px'
			});
		}
	},
	dropzoneInit: function(module){
		
		
		params	= EW["changememberphoto"].GetParamForm();
		
		Dropzone.autoDiscover = false;
		var myDropzone = new Dropzone("#mydropzone", { // Make the whole body a dropzone
		  url: _APP_URL+"gateway_upload_member_photo.php?uni="+_uni+"&ses_u="+_ses_u+"&apprcss=uploadTemporaryFile"+params, // Set the url
		  paramName: "member_photo", // The name that will be used to transfer the file
		  thumbnailWidth: 120,  	//madhesia e duhur qe te shfaqet sic duhet ne dropzone
		  thumbnailHeight: 120,		//madhesia e duhur qe te shfaqet sic duhet ne dropzone
		  parallelUploads: 1,
		  maxFiles: 1,
		  addRemoveLinks: true,
		  maxFilesize: 5,  //5 MB
		  dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> '+_dropFile+' </span><span>&nbsp&nbsp<h4 class="display-inline"> '+_orClick+'</h4></span>',    
		  dictResponseError: _errorUploadingFile,
		  acceptedFiles: 'image/*', //select only image
		  init: function() {
				
				this.on('sending', function(file, xhr, formData){ //Called just before each file is sent
					EW["changememberphoto"].JcropBeforeUploadPhoto('withLoader');
				});
				this.on('success', function( file, resp ){ //The file has been uploaded successfully. Gets the server response as second argument.
					EW["changememberphoto"].ShowImageUploaded(resp); //show the image to the user so that he can crop it
				});	
				this.on("removedfile", function (file) {
					EW["changememberphoto"].RemovePhotoUploaded(); //Called whenever a file is removed from the list
				});
				
		  },
		  dictFallbackMessage 				: _dictFallbackMessage, 			
		  dictFallbackText 					: _dictFallbackText, 				
		  dictFileTooBig 					: _dictFileTooBig1 +' ({{filesize}}MiB). '+ _dictFileTooBig2 + ' {{maxFilesize}}MiB.', 		  			  
		  dictInvalidFileType 				: _dictInvalidFileType,			
		  dictResponseError	 				: _dictResponseError + ' {{statusCode}} code.',	 			
		  dictCancelUpload 					: _dictCancelUpload, 				
		  dictCancelUploadConfirmation 		: _dictCancelUploadConfirmation, 	
		  dictRemoveFile 					: _dictRemoveFile, 				
		  dictMaxFilesExceeded 				: _dictMaxFilesExceeded, 			
		  
		 
		});
		
	

		module.myDropzone = myDropzone;
	},
	ShowImageUploaded: function(resp){
		
		var widthImg    = "";
		var heightImg   = "";
									
		//destroy iframe after upload
		$('#postBeforeCropiframe').remove();
		dimensionArray  = resp.split("####");

		if(dimensionArray[0]=="OK"){
			widthImg        = dimensionArray[1];
			heightImg       = dimensionArray[2];

			$("#wAfterCrop").val(widthImg); 
			$("#hAfterCrop").val(heightImg);

			var randomNr 	= Math.random();
			var var_src 	= "";
			var_src 		= _APP_URL+"preview_el.php?idBTmp="+_idBTmp+"&foto_member_upload=Y&randomNr="+randomNr;

			//destroy element and create again----------------------------------------------------------------------------------------------------
				//destroy big image
					if($( ".img_big" ).length){
						$('.img_big').remove();
						$('.img_big').attr('src', '');

					}
					if($( ".preview-container" ).length){
						$( ".preview-container" ).remove();
						$('.img_preview').attr('src', '');
					}
					if($( ".jcrop-holder" ).length){
						$('.jcrop-holder img').remove();
					}

				//append new uploaded image tag to the right big image container
					$('.uploaded_photo').append('<img id="previewImg" src="'+var_src+'" alt="your image"  class="jcrop-preview previewImgage initial_photo img_big" width="'+widthImg+'" height="'+heightImg+'">');
					$('.img_big').hide();
				//append new uploaded image tag to the right preview image container
					$('.preview_photo_cnt').append('<div id="preview-pane">');
					$('#preview-pane').append('<div class="preview-container">');
					$('.preview-container').append('<img src="'+var_src+'"  class="jcrop-preview previewImgage up_img img_preview" width="'+widthImg+'" height="'+heightImg+'">');

				//destroy element and create again----------------------------------------------------------------------------------------------------
					$('.img_big').on('load', function(){
						$('#loader').hide();
						 EW["changememberphoto"].execute_crop(); //function to execute the image crop
					});


			//give to the uploaded_photo height
			$('.uploaded_photo').height($('.jcrop-holder').height());


		}else{//ERROR
				var message="";
				messageM        =dimensionArray[1];//error message
				if(messageM=="mess1")
				message=_mesg_error_1_mesg;
				if(messageM=="mess2")
					message=_mesg_error_2_mesg;
				if(messageM=="mess3")
					message=_mesg_error_3_mesg;
				if(messageM=="mess4")
					message=_mesg_error_4_mesg;
				if(messageM=="mess5")
					message=_mesg_error_5_mesg;
				if(messageM=="mess6")
					message=_mesg_error_6_mesg;

				 mesg       = message;


				ifsuccess   =false;
				EW["changememberphoto"].ShowErrorMessage(mesg,ifsuccess)

				//destroy big image
				if($( ".img_big" ).length)
					$('.img_big').remove();
				if($( ".preview-container" ).length)
					$( ".preview-container" ).remove();

			$('#loader').hide();
		}	
	},
	JcropBeforeUploadPhoto: function(type){ 
			
			if(type=="withLoader")
				$('#loader').show();
			
			//pastrohen clasat error te shtuara gjate validimit te inputit te uploadid
			$('.input-file').removeClass('error');
			$("label.error_label").remove();

			//destroy and recreate  Jcrop ----------------------------------------
				if ($('#previewImg').data('Jcrop')) {
				// if need destroy jcrop or its created dom element here
				$('#previewImg').data('Jcrop').destroy();
				}
				//hide them
				$('.img_big').remove();
				$('.img_preview').remove();
			//destroy and recreate  Jcrop ----------------------------------------
	},
	submitFinalPhotoButton: function(myDropzone){ 
			//on submit form 
			$('#formsubmit').on("click", function() {
					 
					params	= EW["changememberphoto"].GetParamForm();
					//duhet bere nje kontroll nese useri nuk ka bere upload asnje imazh te dal mesazh errori
						if (!myDropzone.files || !myDropzone.files.length) {//nuk ka file per momentin mos bej submit
							mesg	= _imageRequired;
							ifsuccess   =false;
							EW["changememberphoto"].ShowErrorMessage(mesg,ifsuccess)
						} else {
									 $.ajax({
											type: "post",
											url: _APP_URL+"gateway_upload_member_photo.php?uni="+_uni+"&ses_u="+_ses_u+"&apprcss=uploadFinalFile"+params,
											success: function(data) {    
												//destroy Jcrop image and go back								
												$("#postiframe").remove();//shaktrroje qe te mos vi dy here
												if(data=="goBack"){//if everything happend ok then go back to the main profile, to do so trigger a click event
														if ($("#goBack").length){
															$( "#goBack" ).trigger( "click" );
														}else{
															location.reload();
															mesg       = _saveSuccesfull;
															ifsuccess  ="success";
															EW["changememberphoto"].ShowErrorMessage(mesg,ifsuccess);
														}
												}else if(data!="goBack" && data!=""){
													//popoUp notify
														 if(data=="mess1")
															message=_mesg_error_1_mesg;
														if(data=="mess2")
															message=_mesg_error_2_mesg;
														if(data=="mess3")
															message=_mesg_error_3_mesg;
														if(data=="mess4")
															message=_mesg_error_4_mesg;
														if(data=="mess5")
															message=_mesg_error_5_mesg;
														if(data=="mess6")
															message=_mesg_error_6_mesg;

														 mesg       = message;
														 ifsuccess  =false;
														 EW["changememberphoto"].ShowErrorMessage(mesg,ifsuccess);

												}
											}
									})
					}
			});
	},
	RemovePhotoUploaded: function(){ 
		EW["changememberphoto"].JcropBeforeUploadPhoto('withoutLoader');
		params	= EW["changememberphoto"].GetParamForm();
		//bej nje ajax call qe te fshish te fshish fushat me imazhin e uploaduar qe tashme eshte fshire
		 $.ajax({
				type: "post",
				url: _APP_URL+"gateway_upload_member_photo.php?uni="+_uni+"&ses_u="+_ses_u+"&apprcss=deleteTemporaryFiledsPhoto"+params,
				success: function(data) {    		
				}
		})
		
	},
	GetParamForm: function(){ 
		//prepare the element to be post-----------------------------
			var id_member	= $('#id_member').val();
			var org_main	= $('#org_main').val();
			var idstemp		= $('#idstemp').val();
			var ses_userid	= $('#ses_userid').val();
			var lang		= $('#lang').val();
			var x_mbmPic	= $('#x_mbmPic').val();
			var y_mbmPic	= $('#y_mbmPic').val();		
			var x2_mbmPic	= $('#x2_mbmPic').val();
			var y2_mbmPic	= $('#y2_mbmPic').val();
			var w_mbmPic	= $('#w_mbmPic').val();
			var h_mbmPic	= $('#h_mbmPic').val();
			var wAfterCrop	= $('#wAfterCrop').val();
			var hAfterCrop	= $('#hAfterCrop').val();
		//prepare the element to be post-----------------------------
			
		var params	= "&id_member="+id_member+"&org_main="+org_main+"&idstemp="+idstemp+"&ses_userid="+ses_userid+"&lang="+lang+"&x_mbmPic="+x_mbmPic+"&y_mbmPic="+y_mbmPic+"&x2_mbmPic="+x2_mbmPic;
		params 		= params + "&y2_mbmPic="+y2_mbmPic+"&w_mbmPic="+w_mbmPic+"&h_mbmPic="+h_mbmPic+"&wAfterCrop="+wAfterCrop+"&hAfterCrop="+hAfterCrop;
		return params;
	}
	
};	





		