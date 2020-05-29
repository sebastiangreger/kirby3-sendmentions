/* global panel */

import Section from './components/Section.vue';
import Deprecated from './components/Deprecated.vue';

panel.plugin('sgkirby/sendmentions', {

  sections: {
    sendmentions: Section,
    pings: Deprecated,
  },

  icons: {
    'sendmentions-webmention': '<polygon fill-rule="nonzero" points="9.23775281 14.8085393 7.34377528 9.01793258 7.31235955 9.01793258 5.45002247 14.8085393 2.96193258 14.8085393 0.00413483146 3.62759551 2.4611236 3.62759551 4.2294382 11.2382921 4.26067416 11.2382921 6.20139326 5.44750562 8.50202247 5.44750562 10.411191 11.3318652 10.4425618 11.3318652 11.8070562 5.45137079 10.0297079 5.44750562 13.7139775 2.02314607 16 5.45191011 14.189618 5.45078652 11.6785169 14.8085393"></polygon>',
    'sendmentions-archiveorg': '<path d="M15.5 1.8l-.3.5H.5l-.3-.5L8 0zm-.3 1H.5v1.4h14.7zm-1.8 7.7v1.3l.1 1.6.2.1q.6.2 1.3 0 .3-4.1 0-8.8-.8-.2-1.5 0v1.1l-.1 1v3.7zm-12.5.7v2.3H1.3l.2.1h1v-.1-.6l.1-1.9V9v-.8-.8-.8-.9-1H1v.8l-.1.2V11.2zm4 1.1v.7l.1.2v.3h.2l.1.1h1.1v-.1l.1-.6V11l.1-1V9 5.8l-.1-1H5L5 5v1.2l-.1.7v5.4zm6.2-6.8v-.7l-.4-.2-1 .1-.2 1.4V13l.1.6h.2l.2.1h1v-.1-.4l.1-.3v-2l.1-.8V9 7.8v-1-1.2zM.6 15h14.8V14H.6zM0 16h16v-.7H0z"></path>',
    'sendmentions-run': '<path d="M-.001 2.382V13.34l7.959-5.473v5.473l7.968-5.479-7.968-5.478v5.473z" />',
  },

});
