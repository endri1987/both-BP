/*
/* =Module Name=   					=Useful(1-5)=    	=Description=
 * EW["datepicker"] 						(5)			=> Jquery UI
 * EW["social-media"] 						(2)			=> Lifestream Plugin for social media
 * EW["imagegallery_slideshow"] 			(2)			=> Gallerific gallery
 * EW["youtube-thumbnail-generator"]   		(5)			=> Creates youtube thumbnail from video URL
 * EW["font-change"]  						(2)			=> Changes body font
 * EW["make-visible"]  						(3)			=> Removes class invisible wherever it finds it after 0.6 seconds
 * EW["normalize-pagers"]  								=> 
 * EW["photo-modal"]  						(2)			=> Put image on pop-up dialog with colorbox
 * EW["banner"]  										=> Give to banner a certain functionality
 * EW["lazy-load"]  	 					(3)			=> Images are downloaded when they are visible or when they become visible inside the viewport, lazy loads images with class="lazy" requires jail.js plugin
 * EW["ie-fix"]     						(1)  		=> bug fix for ie
 */


(function() {
    EW["datepicker"] = {
        template: '<div id="datepickerWrapper"><div id="datepickerHeader"><div id="datepickerClose">X</div></dic><div id="datepickerBody"></div></div>',
        init: function() {
            var context = this;
            this.$datepicker = $("#datepicker");
            this.$datepicker.on("click", "a", function(e) {
                e.preventDefault();
//                e.stopPropagation();
            });
            this.$datepicker.on("click", "#datepickerClose", function(e) {
                $("body").removeClass("datepicker_show");
                e.preventDefault();
            });
            this.$datepicker.datepicker({
                inline: true,
                regional: "al",
                beforeShowDay: context.handleEvents,
                onSelect: function(date, inst) {
                    var popUpArray = [];
                    $.map(eventzz, function(val, i) {
                        if (date.indexOf(val.date) !== -1) {
                            popUpArray.push(val);
                        }
                    });
                    context.datePickerPopUp(popUpArray);
                },
                onChangeMonthYear: function(year, month) {

                }
            });
        }, handleEvents: function(calendarDate) {
//            for (var i = 0; i < eventzz.length; i++) {
//                var tempDate = eventzz[i].date.split("/");
//                if (calendarDate.getDate() === tempDate[0] * 1
//                        && calendarDate.getMonth() + 1 === tempDate[1] * 1
//                        && calendarDate.getFullYear() === tempDate[2] * 1) {
//                    return [true, eventzz[i].className, eventzz[i].title];
//                }
//            }
            return [false, '', ''];
        }, datePickerPopUp: function(popUpArray) {
            var context = this;
            var thisTemplate = '';
            var dateFormated;
            $.map(popUpArray, function(val, i) {
                dateFormated = val.dateFormated;
                thisTemplate += '<div class="pad_v alphaY clearfix"> <div class="grid_50 datepicker_title">' + val.title.split("//")[0] + '</div> <div class="grid_50 datepicker_desc">' + val.title.split("//")[1] + '</div></div>';
            });
            if (context.$datepicker.find("#datepickerWrapper").length < 1) {
                context.$datepicker.append(context.template);
            }
            thisTemplate += '<div class="datepicker_date">' + dateFormated + '</div>'
            context.$datepicker.find("#datepickerBody").html(thisTemplate).closest("body").addClass("datepicker_show");
        }
    };
})(EW);

(function() {
    EW["social-media"] = {
        social_list_index: 0,
        social_list: [{
                count: 0,
                limit: 1,
                selector: "#socialFb",
                list: [
                    {
                        service: 'facebook_page',
                        user: '272118479618581',
                        template: {
                            wall_post: '<div class="lifestream_icon_facebook"></div>Post on wall  <a href="${link}">${title}</a>'
                        }
                    }
                ]
            },
            {
                count: 0,
                limit: 1,
                selector: "#socialTw",
                list: [
                    {
                        service: 'twitter',
                        user: 'ediramaal',
                        template: {
                            posted: '<div class="lifestream_icon_twitter"></div>{{html tweet}}'
                        }
                    }
                ]
            },
            {
                count: 0,
                limit: 1,
                selector: "#socialYou",
                list: [
                    {
                        service: 'youtube',
                        user: 'TopChannelAlbania',
                        template: {
                            uploaded: '<div class="lifestream_icon_youtube"></div>Uploaded  <a href="${video.player.default}" ' +
                                    'title="${video.description}">${video.title}</a>',
                            favorited: 'favorited <a href="${video.player.default}" ' +
                                    'title="${video.description}">${video.title}</a>'
                        }
                    }
                ]
            }],
        init: function() {
            var temp = '<div id="socialFb"> </div><div id="socialTw"> </div><div id="socialYou"> </div>';
            $("#socialStream").append(temp);
            var context = this;

            this.stream();

        }, stream: function() {
            var context = this;
            var objj = context.social_list[context.social_list_index];
            var selector = context.social_list[context.social_list_index].selector;

            Date.prototype.toISO8601 = function(date) {
                var pad = function(amount, width) {
                    var padding = "";
                    while (padding.length < width - 1 && amount < Math.pow(10, width - padding.length - 1))
                        padding += "0";
                    return padding + amount.toString();
                }
                var date = date ? date : new Date();
                var offset = date.getTimezoneOffset();
                return date.toDateString()
                        + "  at " + pad(date.getHours(), 2)
                        + ":" + pad(date.getMinutes(), 2);
            };

            $(selector).lifestream({
                limit: objj.limit,
                list: objj.list,
                feedloaded: function() {
                    $(selector).find("li").each(function() {
                        var element = $(this),
                                date = new Date(element.data("time"));
                        element.append(' <abbr class="timeago" title="' + date.toISO8601(date) + '">' + date.toISO8601(date) + "</abbr>");
                    });
                    if (context.social_list_index < context.social_list.length) {
                        context.social_list_index = context.social_list_index + 1;
                        context.stream();
                    }
                }
            });
        }
    };
})(EW);

/* CREATES THUMBNAIL OF VIDEO */
(function() {
    EW["youtube-thumbnail-generator"] = {
        init: function() {
            var context = this;
            $("body").find(".video_thumb .video_img").each(function() {
                var jThis = $(this);
                var imgSrc = EW["youtube-id"].init(jThis.attr("src"));
                var imgThumb = "http://img.youtube.com/vi/" + imgSrc + "/0.jpg";
                jThis.attr("src", imgThumb);
            });
            
            $(".video_thumb").on("click", "img", function(e) {
                EW["video-load"].setSrc(EW["video-load"].splitSrc($(this)));
                e.stopPropagation();
            });
        }
    };
})(EW);


(function() {
    EW["font-change"] = {
        init: function() {
            $(".sub_menu_font_size").on("click", "a", function(e) {
                var jThis = $(this);
                var htmlDom = $("html");
                var fontSize = parseInt(htmlDom.css("font-size").split("px")[0], 10);
                this.setSize(htmlDom, jThis, fontSize);

                e.preventDefault();
            });
        }, setSize: function(htmlDom, jThis, fontSize) {
            if (jThis.hasClass("font_small") && fontSize > 8) {
                htmlDom.css("font-size", (fontSize - 2));
            }
            else if (jThis.hasClass("font_normal")) {
                htmlDom.css("font-size", 14);
            }
            else if (jThis.hasClass("font_large") && fontSize < 18) {
                htmlDom.css("font-size", (fontSize + 2));
            }
        }
    };
})(EW);

(function() {
    EW["banner"] = {
        init: function() {
            var context = this;
            this.bannerMain = $(".banner_main");

            this.setImg();

            $(".banner_list").on("click", "li a", function(e) {
                var jThis = $(this);
                var imgSrc = jThis.closest("li").addClass("banner_active_item").siblings().removeClass("banner_active_item");
                context.setImg();
                e.preventDefault();
            });

        }, setImg: function() {
            var context = this;
            $("#bannerArea").find(".banner_active_item").each(function() {
                var jThis = $(this);
                var imgSrc = jThis.attr("data-img-src");
                context.bannerMain.find("img").attr("src", imgSrc);
            });
        }
    };
})(EW);

(function() {
    EW["make-visible"] = {
        init: function() {
            setTimeout(function() {
                $("body").find(".invisible").each(function() {
                    $(this).removeClass("invisible");
                });
            }, 600);
        }
    };
})(EW);

(function() {
    EW["lazy-load"] = {
        init: function() {
            if ($("html").hasClass("ie7")) {
                EW.ie7browser = true;
            }
            if (!EW.ie7browser) {
                setTimeout(function() {
                    $('img.lazy').jail({
                        offset: 400
                    });
                }, 500);
            } else {

                $("body").find("img.lazy").each(function() {
                    var jThis = $(this);
                    jThis.attr("src", jThis.attr("data-src"));
                });
            }
        }
    };
})(EW);


(function() {
    EW["photo-modal"] = {
        init: function() {
            $(".modal_gallery").find("img").each(function() {
                var jThis = $(this);
                var imgSrc = (jThis.attr("class").indexOf("lazy") !== -1) ? jThis.attr("data-src") : jThis.attr("src");
                jThis.attr("href", imgSrc).addClass("gallery");
            });
            $("img.gallery").colorbox({
                rel: "gallery",
                transition: "fade",
                opacity: 0.5,
                photo: "true"
            });
        }
    };
})(EW);


(function() {
    EW["ie-fix"] = {
        init: function() {
            if (typeof EW.ie7browser !== undefined && EW.ie7browser === true) {
                this.check();
            }

        },
        check: function() {
            this.main_navigation_space();
            if ($("[class*='media_ratio']").is(":visible")) {
                this.media_ratio();
            }
        },
        main_navigation_space: function() {
            var navMain = $(".first_level_nav");
            var paddingLR = 0;
            var elements = navMain.find("> li.nav_n1");
            var elementsNr = elements.length;
            var navWidth = navMain.width();
            /*14 is the font size*/
            paddingLR = Math.floor((navWidth - (elementsNr * 100)) / (2 * elementsNr)) / 14;
            elements.each(function() {
                $(this).find("a.nav_n1").css({"padding-left": paddingLR + "em",
                    "padding-right": paddingLR + "em"});
            });
            setTimeout(function() {
                $(".iehide").removeClass("iehide");
            }, 600);
        },
        media_ratio: function() {
            $("body").find("[class*='media_ratio']").each(function() {
                var jThis = $(this);
                jThis.find("img").height(jThis.outerHeight()).addClass("visible");
            });
        }
    };
})(EW);