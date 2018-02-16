var EventEasy = new Class({
    Implements: [Options],
    options: {
        mode: 'inject',
        delay: 500
    },
    container: null,
    eventSection: null,
    eventHandle: null,
    isCollapsed: null,
    eventSectionLoaded: false,
    initialize: function (options) {
        var self = this;
        this.setOptions(options);

        this.eventSection = document.getElementById('tl_navigation').getElements('.eventeasy_toggle')[0];
        this.container = document.getElementById('eventeasy');

        // get state
        this.isCollapsed = (this.eventSection.hasClass('eventeasy_collapsed')) ? true : false;

        // check if the event/calendar section content is loaded or if it is going to be loaded via ajax on the next click
        this.eventSectionLoaded = Boolean($$('#tl_navigation a.calendar').length);

        // initialize eventeasy again when someone toggles the section
        this.eventSection.addEvent('click', function () {
            // update state
            self.isCollapsed = !self.isCollapsed;
            self.init();
        });

        window.addEvent('ajax_change', function () {
            self.eventSectionLoaded = true;
            self.init();
        });

        this.init();
    },
    init: function () {
        var self = this;

        // only launch eventeasy if expanded and the data doesn't need to be loaded via ajax first
        if (!this.isCollapsed && this.eventSectionLoaded) {
            this.eventHandle = $$('#tl_navigation a.calendar')[0].getParent('li');
            this.container.inject(this.eventHandle);
            this.container.removeClass('eventeasy_doNotLaunch');
        } else {
            this.container.addClass('eventeasy_doNotLaunch');
            return;
        }

        // Set item to display: block when everything is done
        this.container.addClass('ready');
    }
});