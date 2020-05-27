<template>
  <li class="k-list-item" :class="itemclass">
    <div class="k-list-item-content">

      <a :href="itemlink" target="_blank" class="k-link k-list-item-content">
        <span class="k-list-item-image">
          <k-icon :type="icon"/>
        </span>
        <span class="k-list-item-text">
          <em
            v-html="item.target"
          />
          <small
            v-html="type_formatted"
          />
        </span>
      </a>
    </div>
    <nav class="k-list-item-options">
      <k-button
        :tooltip="$t('send')"
        :icon="buttonicon"
        :alt="Send"
        @click.stop="$emit('action', 'resend', item)"
      />
    </nav>
  </li>
</template>

<script>
export default {

  props: {
    item() {
      return {
        type: Object,
        default: {},
      };
    },
  },

  computed: {
    itemlink() {
      if (this.item.type == 'archive.org')
        return this.item.data.url;
      else
        return this.item.target;
    },
    itemclass() {
      if (this.item.type == 'archive.org') {
        return 'k-list-item-sendmentions-sent k-list-item-sendmentions-archiveorg';
      } else if (this.item.type == 'pingback' || this.item.type == 'webmention') {
        return 'k-list-item-sendmentions-sent';
      } else if (this.item.type == 'notsent') {
        return 'k-list-item-sendmentions-notsent';
      } else {
        return 'k-list-item-sendmentions-failed';
      }
    },
    type_formatted() {
      switch (this.item.type) {
        case 'none':
          return 'no endpoint';
        case 'notsent':
          return 'not pinged';
        case 'webmention':
          return this.item.type + ' (' + this.item.data.response + ')';
        default:
          return this.item.type;
      }
    },
    buttonicon() {
      if (this.item.type === 'archive.org') {
        return '';
      } else if (this.item.type === 'notsent') {
        return 'angle-right';
      }
      return 'refresh';
    },
    icon() {
      switch (this.item.type) {
        case 'webmention':
            return 'sendmentions-webmention';
        case 'pingback':
            return 'globe';
        case 'archive.org':
            return 'sendmentions-archiveorg';
        case 'none':
            return 'cancel';
        default:
            return 'circle-outline';
      }
    },
  },

};
</script>

<style lang="scss">
.k-list-item-sendmentions-sent .k-list-item-image .k-icon svg * { fill:#5d800d; }
.k-list-item-sendmentions-notsent .k-list-item-image .k-icon svg * { fill:#f5871f; }
.k-list-item-sendmentions-failed .k-list-item-image .k-icon svg * { fill:#c82829; }
.k-list-item-sendmentions-archiveorg .k-list-item-text small { margin-right:1rem; }
</style>
