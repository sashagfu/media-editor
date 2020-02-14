import $ from 'jquery';

// Chain of Responsibility pattern. Creates base class that can be overridden.
if (typeof Object.create !== 'function') {
    Object.create = function objectCreate(obj) {
        function F() {
        }

        F.prototype = obj;
        return new F();
    };
}

const iframeIsReady = function iframeIsReady(callback) {
    // Listen for Global YT player callback
    if (typeof window.YT === 'undefined' && typeof window.loadingPlayer === 'undefined') {
        // Prevents Ready Event from being called twice
        window.loadingPlayer = true;

        // Creates deferred so, other players know when to wait.
        window.dfd = $.Deferred();
        window.onYouTubeIframeAPIReady = function onYouTubeIframeAPIReady() {
            window.onYouTubeIframeAPIReady = null;
            window.dfd.resolve('done');
            callback();
        };
    } else if (typeof YT === 'object') {
        callback();
    } else {
        window.dfd.done(() => {
            callback();
        });
    }
};

const loadAPI = function loadAPI(callback) {
    // Load Youtube API
    let tag = document.createElement('script');
    let head = document.getElementsByTagName('head')[0];

    if (window.location.origin === 'file://') {
        tag.src = 'http://www.youtube.com/iframe_api';
    } else {
        tag.src = '//www.youtube.com/iframe_api';
    }

    head.appendChild(tag);

    // Clean up Tags.
    head = null;
    tag = null;

    iframeIsReady(callback);
};

// YTPlayer Object
const YTPlayer = {
    player: null,

    // Defaults
    defaults: {
        ratio: 16 / 9,
        videoId: 'LSmgKRx5pBo',
        mute: true,
        repeat: true,
        width: $(window).width() + 40,
        playButtonClass: 'YTPlayer-play',
        pauseButtonClass: 'YTPlayer-pause',
        muteButtonClass: 'YTPlayer-mute',
        volumeUpClass: 'YTPlayer-volume-up',
        volumeDownClass: 'YTPlayer-volume-down',
        start: 0,
        pauseOnScroll: false,
        fitToBackground: true,
        playerVars: {
            iv_load_policy: 3,
            modestbranding: 1,
            autoplay: 1,
            controls: 0,
            showinfo: 0,
            wmode: 'opaque',
            branding: 0,
            autohide: 0,
        },
        events: null,
    },

    /**
     * @function init
     * Intializes YTPlayer object
     */
    init: function init(node, userOptions) {
        const self = this;

        self.userOptions = userOptions;

        self.$body = $('body');
        self.$node = $(node);
        self.$window = $(window);

        // Setup event defaults with the reference to this
        self.defaults.events = {
            onReady(e) {
                self.onPlayerReady(e);

                // setup up pause on scroll
                if (self.options.pauseOnScroll) {
                    self.pauseOnScroll();
                }

                // Callback for when finished
                if (typeof self.options.callback === 'function') {
                    self.options.callback.call(this);
                }
            },
            onStateChange(e) {
                if (e.data === 1) {
                    self.$node.find('img').fadeOut(400);
                    self.$node.addClass('loaded');
                } else if (e.data === 0 && self.options.repeat) {
                    // video ended and repeat option is set true
                    self.player.seekTo(self.options.start);
                }
            },
        };

        self.options = $.extend(true, {}, self.defaults, self.userOptions);
        self.options.height = Math.ceil(self.options.width / self.options.ratio);
        self.ID = (new Date()).getTime();
        self.holderID = `YTPlayer-ID-${self.ID}`;

        if (self.options.fitToBackground) {
            self.createBackgroundVideo();
        } else {
            self.createContainerVideo();
        }
        // Listen for Resize Event
        self.$window.on(`resize.YTplayer${self.ID}`, () => {
            self.resize(self);
        });

        loadAPI(self.onYouTubeIframeAPIReady.bind(self));

        self.resize(self);

        return self;
    },

    /**
     * @function pauseOnScroll
     * Adds window events to pause video on scroll.
     */
    pauseOnScroll: function pauseOnScroll() {
        const self = this;
        self.$window.on(`scroll.YTplayer${self.ID}`, () => {
            const state = self.player.getPlayerState();
            if (state === 1) {
                self.player.pauseVideo();
            }
        });
        self.$window.scrollStopped(() => {
            const state = self.player.getPlayerState();
            if (state === 2) {
                self.player.playVideo();
            }
        });
    },
    /**
     * @function createContainerVideo
     * Adds HTML for video in a container
     */
    createContainerVideo: function createContainerVideo() {
        const self = this;

        /* jshint multistr: true */
        let $YTPlayerString = $(`<div id="ytplayer-container${self.ID}" >\
                                    <div id="${self.holderID}" class="ytplayer-player"></div> \
                                    </div> \
                                    <div id="ytplayer-shield" class="ytplayer-shield"></div>`);

        self.$node.append($YTPlayerString);
        self.$YTPlayerString = $YTPlayerString;
        $YTPlayerString = null;
    },

    /**
     * @function createBackgroundVideo
     * Adds HTML for video background
     */
    createBackgroundVideo: function createBackgroundVideo() {
        /* jshint multistr: true */
        const self = this;
        let $YTPlayerString = $(`<div id="ytplayer-container${self.ID}" class="ytplayer-container background">\
                                    <div id="${self.holderID}" class="ytplayer-player"></div>\
                                    </div>\
                                    <div id="ytplayer-shield" class="ytplayer-shield"></div>`);

        self.$node.append($YTPlayerString);
        self.$YTPlayerString = $YTPlayerString;
        $YTPlayerString = null;
    },

    /**
     * @function resize
     * Resize event to change video size
     */
    resize: function resize(self) {
        // var self = this;
        let container = $(window);

        if (!self.options.fitToBackground) {
            container = self.$node;
        }

        const width = container.width() + 40;
        const height = container.height();
        let pWidth; // player width, to be defined
        let pHeight; // player height, tbd
        let $YTPlayerPlayer = $(`#${self.holderID}`);

        // when screen aspect ratio differs from video, video must center and underlay one dimension
        if (width / self.options.ratio < height) {
            pWidth = Math.ceil(height * self.options.ratio); // get new player width
            $YTPlayerPlayer.width(pWidth).height(height).css({
                left: (width - pWidth) / 2,
                top: 0,
            }); // player width is greater, offset left; reset top
        } else { // new video width < window width (gap to right)
            pHeight = Math.ceil(width / self.options.ratio); // get new player height
            $YTPlayerPlayer.width(width).height(pHeight).css({
                left: 0,
                top: (height - pHeight) / 2,
            }); // player height is greater, offset top; reset left
        }

        $YTPlayerPlayer = null;
        container = null;
    },

    /**
     * @function onYouTubeIframeAPIReady
     * @ params {object} YTPlayer object for access to options
     * Youtube API calls this function when the player is ready.
     */
    onYouTubeIframeAPIReady: function onYouTubeIframeAPIReady() {
        const self = this;
        self.player = new window.YT.Player(self.holderID, self.options);
    },

    /**
     * @function onPlayerReady
     * @ params {event} window event from youtube player
     */
    onPlayerReady: function onPlayerReady(e) {
        if (this.options.mute) {
            e.target.mute();
        }
        e.target.playVideo();
    },

    /**
     * @function getPlayer
     * returns youtube player
     */
    getPlayer: function getPlayer() {
        return this.player;
    },

    /**
     * @function destroy
     * destroys all!
     */
    destroy: function destroy() {
        const self = this;

        self.$node
            .removeData('yt-init')
            .removeData('ytPlayer')
            .removeClass('loaded');

        self.$YTPlayerString.remove();

        $(window).off(`resize.YTplayer${self.ID}`);
        $(window).off(`scroll.YTplayer${self.ID}`);
        self.$body = null;
        self.$node = null;
        self.$YTPlayerString = null;
        self.player.destroy();
        self.player = null;
    },
};

// Scroll Stopped event.
$.fn.scrollStopped = function(callback) {
    const $this = $(this);
    const self = this;
    $this.scroll(() => {
        if ($this.data('scrollTimeout')) {
            clearTimeout($this.data('scrollTimeout'));
        }
        $this.data('scrollTimeout', setTimeout(callback, 250, self));
    });
};

// Create plugin
$.fn.YTPlayer = function jYTPlayer(options) {
    return this.each(function() {
        const el = this;

        $(el).data('yt-init', true);
        const player = Object.create(YTPlayer);
        player.init(el, options);
        $.data(el, 'ytPlayer', player);
    });
};
