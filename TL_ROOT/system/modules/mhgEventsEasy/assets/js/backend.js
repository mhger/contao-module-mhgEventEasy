var EventsEasy = new Class({
    Implements: [Options],
    options: {
        mode: 'inject',
        delay: 500
    },
    container: null,
    newsSection: null,
    newsHandle: null,
    isCollapsed: null,
    newsSectionLoaded: false,
    initialize: function (options) {
        var self = this;
        this.setOptions(options);

        this.newsSection = document.getElementById('tl_navigation').getElements('.eventseasy_toggle')[0];
        this.container = document.getElementById('eventseasy');

        // get state
        this.isCollapsed = (this.newsSection.hasClass('eventseasy_collapsed')) ? true : false;

        // check if the event/calendar section content is loaded or if it is going to be loaded via ajax on the next click
        this.newsSectionLoaded = Boolean($$('#tl_navigation a.calendar').length);

        // initialize eventseasy again when someone toggles the section
        this.newsSection.addEvent('click', function () {
            // update state
            self.isCollapsed = !self.isCollapsed;
            self.init();
        });

        window.addEvent('ajax_change', function () {
            self.newsSectionLoaded = true;
            self.init();
        });

        this.init();
    },
    init: function () {
        var self = this;

        // only launch eventseasy if expanded and the data doesn't need to be loaded via ajax first
        if (!this.isCollapsed && this.newsSectionLoaded) {
            this.newsHandle = $$('#tl_navigation a.calendar')[0].getParent('li');
            this.container.inject(this.newsHandle);
            this.container.removeClass('eventseasy_doNotLaunch');
        } else {
            this.container.addClass('eventseasy_doNotLaunch');
            return;
        }

        // Set item to display: block when everything is done
        this.container.addClass('ready');
    }
});