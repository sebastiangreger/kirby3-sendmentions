/* global panel */

import Section from './components/Section.vue';
import Deprecated from './components/Deprecated.vue';

panel.plugin('sgkirby/sendmentions', {

  sections: {
    sendmentions: Section,
    pings: Deprecated,
  },

  icons: {
    'sendmentions-webmention': '<path d="M6.199 4.982H8.5l1.91 5.983h.031l1.568-5.98-1.98-.003L13.712 1.5 16 4.986h-1.81L11.676 14.5H9.236L7.342 8.612H7.31L5.447 14.5H2.96L0 3.131h2.254l1.972 7.739h.032z"/>',
    'sendmentions-archiveorg': '<path d="M15.5 1.8l-.3.5H.5l-.3-.5L8 0zm-.3 1H.5v1.4h14.7zm-1.8 7.7v1.3l.1 1.6.2.1q.6.2 1.3 0 .3-4.1 0-8.8-.8-.2-1.5 0v1.1l-.1 1v3.7zm-12.5.7v2.3H1.3l.2.1h1v-.1-.6l.1-1.9V9v-.8-.8-.8-.9-1H1v.8l-.1.2V11.2zm4 1.1v.7l.1.2v.3h.2l.1.1h1.1v-.1l.1-.6V11l.1-1V9 5.8l-.1-1H5L5 5v1.2l-.1.7v5.4zm6.2-6.8v-.7l-.4-.2-1 .1-.2 1.4V13l.1.6h.2l.2.1h1v-.1-.4l.1-.3v-2l.1-.8V9 7.8v-1-1.2zM.6 15h14.8V14H.6zM0 16h16v-.7H0z"></path>',
    'sendmentions-forward': '<path d="M-.001 2.382V13.34l7.959-5.473v5.473l7.968-5.479-7.968-5.478v5.473z" />',
    'sendmentions-run': '<path d="M15.816.107a.578.578 0 01.24.572l-2.285 13.714a.574.574 0 01-.562.473.616.616 0 01-.215-.044L8.95 13.17l-2.16 2.634a.555.555 0 01-.438.205.57.57 0 01-.571-.571v-3.116l7.714-9.456-9.545 8.26L.422 9.678A.566.566 0 01.351 8.66L15.209.09a.57.57 0 01.607.018z"/>',
    'sendmentions-ok': '<path d="M6.188 14.5L0 7.514l1.614-1.577 4.574 4.866L14.386 1.5 16 3.077z"/>',
    'sendmentions-failed': '<path d="M1.5 3.111l4.953 4.935-4.861 4.843 1.476 1.47L7.93 9.518l5.002 4.983 1.501-1.496-5.002-4.983L14.5 2.97 13.024 1.5l-5.07 5.05-4.953-4.935z"/>',
  },

});
