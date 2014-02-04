/*
 * Plugin Name: Eugene Oh
 * Plugin URI: http://www.???.com
 * Description: Single-page video-player app
 * Author: Michael Kafka
 * Author URI: http://www.makfak.com
 * Version: 1.0
 */
;(function ( $, window, document, undefined ) {

    var History = window.History;

    var pluginName = 'EugeneOh';

    var Plugin = function ( element, options ) {
        this._name = pluginName;
        this.el = element;
        this.obj = $(element);
        this._options = options;
        this._id = (Date.parse(new Date()) + Math.round(Math.random() * 10000)).toString();

        this.init();
    };

    Plugin.prototype = {

        _defaults: {
            title_base : 'Eugene Young Oh'
        },

        video_container_id : 'yt_player',

        init: function (options) {
            var self = this;
            var data;

            this.opts = $.extend( {}, this._defaults, this._options );
            this._options = $.extend(true, {}, this.opts);

            this.render();
        },

        render : function (bindEvents) {
            var self = this;
            var view;
            var lastItemIndex;

            this._updateDeviceClass();
            this._bindEvents();
        },

        _bindEvents : function () {
            var self = this;

            Shadowbox.init({
                skipSetup: true
            }, function () {
                // wait a tick for Shadowbox's markup to be ready
                setTimeout(function () {
                    History.Adapter.trigger(window, 'statechange');
                }, 100);
            });

            this.obj.delegate('.video a', 'click', function(e) {
                self._mediaClick(e)
            });

            this.obj.delegate('.photo a', 'click', function(e) {
                self._mediaClick(e)
            });

            $(window).bind('resize.eyo', function(e) {
                self._updateDeviceClass(e);
            });

            History.Adapter.bind(window,'statechange',function() {
                var State = History.getState();
                self._matchHistoryState( State );
                // History.log(State.data, State.title, State.url);
            });
        },

        _mediaClick : function (e) {
            var $node = (e.target.tagName.toLowerCase() === 'a') ? $(e.target) : $(e.target).parents('a');

            e.preventDefault();
            e.stopPropagation();

            this._pushToHistory($node.attr('href'));
        },

        showEmbed : function (id) {
            var $node = $('#' + id).find('a');

            // photos are different
            if ($node.parents('.photo').length > 0) {
                this._showPhoto($node);
                return false;
            }

            if (this._isDesktop() || this._isTablet()) {
                this._embedDesktop($node);
            } else {
                this._embedPhone($node);
            }

            return false;
        },

        _embedPhone : function ($node) {
            // we have to do stupid stuff because YT doesn't bubble
            // 'fullscreenchange' to the parent window
            var existing = $('#' + this.video_container_id);

            if (existing.length > 0) {
                existing.remove();
            }

            // begin in earnest
            var self = this;
            var target = $('<div id="' + this.video_container_id + '"></div>')[0];
            var player;

            $node.parents('.video').append(target);
            
            setTimeout(function() {
                player = new YT.Player(self.video_container_id, {
                    videoId : $node.parents('.item').prop('id'),
                    width : '100%',
                    height : '100%',
                    playerVars : {
                        enablejsapi : 0, // disable JS api
                        iv_load_policy : 3, // don't show annotations
                        showinfo : 0 // don't show the toolbar at the top of the video
                    }
                });
            }, 0);
        },

        _embedDesktop : function ($node) {
            var ratio = {
                w : 480,
                h : 270
            };
            var win = {
                w : $(window).width(),
                h : $(window).height()
            };
            var video = {
                w : 0,
                h : 0
            };
            var self = this;

            $node = ($node.is('a')) ? $node : $node.parents('a');

            var url = $node.attr('data-url');

            if ( (win.w / win.h) > (ratio.w / ratio.h) ) {
                video.w = Math.floor((win.h * ratio.w) / ratio.h);
                video.h = win.h;
            } else {
                video.w = win.w;
                video.h = Math.floor((win.w * ratio.h) / ratio.w);
            }

            Shadowbox.open({
                content : url,
                title : $node.find('.caption').text(),
                player : 'iframe',
                width : video.w,
                height : video.h,
                options : {
                    onClose : function() {
                        self._pushToHistory('/');
                    }
                }
            });
        },

        _showPhoto : function ($node) {
            var self = this;

            $node = ($node.is('a')) ? $node : $node.parents('a');
            
            Shadowbox.open({
                content : $node.attr('data-url'),
                title : $node.find('.caption').text(),
                player : "img",
                options : {
                    onClose : function() {
                        self._pushToHistory('/');
                    }
                }
            });
        },

        _parseURLForHistory : function (url) {
            return url.match(/\/post\/([a-zA-Z0-9_-]{11})\/([a-zA-Z0-9_-]*)/);
        },

        _pushToHistory : function (url) {
            // History.replaceState( data, title, url );
            if (!url || url === '/') {
                History.replaceState({}, this.opts.title_base, History.getRootUrl());
                return false;
            }

            var match = this._parseURLForHistory(url);
            var state = History.getState();
            var new_url = match[0];
            var new_page_title = this.opts.title_base + ((match[2]) ? ' - ' + match[2] : '');
            
            if (state.data.id === match[1]) {
                return false;
            }

            History.replaceState({
                    id : match[1],
                    slug : match[2]
                },
                new_page_title,
                new_url
            );

            if (window._gaq) {
                window._gaq.push(['_trackPageview'], window.location.pathname + new_url);
            }
        },

        _matchHistoryState : function (state) {
            if (state.data.id) {
                this.showEmbed(state.data.id);
                return false;
            }

            if (!state.data.id && Shadowbox.isOpen()) {
                Shadowbox.close();
                return false;
            }

            var matches = this._parseURLForHistory(state.url);

            if ( matches && matches[1] ) {
                this.showEmbed(matches[1]);
            }

            return false;
        },

        _isPhone : function () {
            return (
                Modernizr.mq('only screen and (max-width: 480px)') ||
                Modernizr.mq('only screen and (min-width: 481px) and (max-width: 767px)')
            );
        },

        _isTablet : function () {
            return (
                Modernizr.mq('only screen and (min-width: 768px) and (max-width: 1024px)')
            );
        },

        _isDesktop : function () {
            return (
                Modernizr.mq('only screen and (min-width: 1025px) and (max-width: 1348px)') ||
                Modernizr.mq('only screen and (min-width: 1349px)')
            );
        },

        _updateDeviceClass: function (e) {
            var $body = $('body');
            this.device_class = this.device_class || '';

            $body.removeClass(this.device_class);

            if (this._isPhone()) {
                this.device_class = 'device-phone';
            } else if (this._isTablet()) {
                this.device_class = 'device-tablet';
            } else if (this._isDesktop()) {
                this.device_class = 'device-desktop';
            }

            $body.addClass(this.device_class);
        },

        _updateBodyClass : function (option, prefix, type) {
            var current = this.opts[option];
            type = type || current;

            $('body').removeClass(prefix + current).addClass(prefix + type);

            this.opts[option] = type;
        },

        version : '1.0',
    };

    $.fn[pluginName] = function ( options ) {
        options = options || {};
        return this.each(function () {
            if (!$.data(this, pluginName)) {
                var plugin = new Plugin( this, options );
                $.data(this, pluginName, plugin);
                window.EYO = plugin;
            }
        });
    };

})( jQuery, window, document );