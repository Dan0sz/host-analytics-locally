/* * * * * * * * * * * * * * * * * * * *
 *  ██████╗ █████╗  ██████╗ ███████╗
 * ██╔════╝██╔══██╗██╔═══██╗██╔════╝
 * ██║     ███████║██║   ██║███████╗
 * ██║     ██╔══██║██║   ██║╚════██║
 * ╚██████╗██║  ██║╚██████╔╝███████║
 *  ╚═════╝╚═╝  ╚═╝ ╚═════╝ ╚══════╝
 *
 * @author   : Daan van den Bergh
 * @url      : https://daan.dev/wordpress-plugins/optimize-analytics-wordpress/
 * @copyright: (c) 2019 Daan van den Bergh
 * @license  : GPL2v2 or later
 * * * * * * * * * * * * * * * * * * * */
/**
 * Track outbound links with Google Universal Analytics
 *
 * This code is only compatible with analytics.js.
 *
 * Thanks to Ralph Slooten: https://www.axllent.org/docs/view/track-outbound-links-with-analytics-js/
 *
 * @param event
 * @private
 */
function _caosLt(event)
{
    /* If GA is blocked or not loaded, or not main|middle|touch click then don't track */
    if (!ga.hasOwnProperty('loaded') || ga.loaded !== true || (event.which !== 1 && event.which !== 2)) {
        return;
    }

    var eventLink = event.srcElement || event.target;

    /* Loop up the DOM tree through parent elements if clicked element is not a link (eg: an image inside a link) */
    while(eventLink && (typeof eventLink.tagName == 'undefined' || eventLink.tagName.toLowerCase() !== 'a' || !eventLink.href)) {
        eventLink = eventLink.parentNode;
    }

    /* if a link with valid href has been clicked */
    if (eventLink && eventLink.href) {

        var link = eventLink.href;

        /* Only if it is an external link */
        if (link.indexOf(location.host) === -1 && !link.match(/^javascript\:/i)) {

            /* Is actual target set and not _(self|parent|top)? */
            var target = (eventLink.target && !eventLink.target.match(/^_(self|parent|top)$/i)) ? eventLink.target : false;

            /* Assume a target if Ctrl|shift|meta-click */
            if (event.ctrlKey || event.shiftKey || event.metaKey || event.which === 2) {
                target = '_blank';
            }

            var hbrun = false; // tracker has not yet run

            /* HitCallback to open link in same window after tracker */
            var hitBack = function () {
                /* run once only */
                if (hbrun) return;
                hbrun = true;
                window.location.href = link;
            };

            if (target) { /* If target opens a new window then just track */
                ga('send', 'event', 'outbound-link', link, document.location.pathname + document.location.search);
            } else { /* Prevent standard click, track then open */
                event.preventDefault ? event.preventDefault() : event.returnValue = !1;
                /* send event with callback */
                ga('send', 'event', 'outbound-link', link, document.location.pathname + document.location.search, {
                    'transport': 'beacon',
                    'hitCallback': hitBack
                });

                /* Run hitCallback again if GA takes longer than 1 second */
                setTimeout(hitBack, 1000);
            }
        }
    }
}

var _window = window;

/* Use "click" if touchscreen device, else "mousedown" */
var _caosLtEvent = ('ontouchstart' in _window) ? 'click' : 'mousedown';

/* Attach the event to all clicks in the document after page has loaded */
_window.addEventListener ? _window.addEventListener('load', function () {document.body.addEventListener(_caosLtEvent, _caosLt, !1);}, !1) : _window.attachEvent && _window.attachEvent('onload', function () {
    document.body.attachEvent('on' + _caosLtEvent, _caosLt);
});
