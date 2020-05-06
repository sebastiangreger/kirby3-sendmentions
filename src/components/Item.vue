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
        v-if="true"
        :tooltip="$t('resend')"
        icon="refresh"
        :alt="Resend"
        @click.stop="$emit('action', 'resend', item.pageid, item.target, item.type)"
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
      if (this.item.type == 'archive.org' || this.item.type == 'pingback' || this.item.type == 'webmention')
        return 'k-list-item-sendmentions-sent';
      else
        return 'k-list-item-sendmentions-failed';
    },
    type_formatted() {
      switch (this.item.type) {
        case 'none':
          return '';
        case 'webmention':
          return this.item.type + ': ' + this.item.data.response;
        default:
          return this.item.type;
      }
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
    options() {
      return [
        {
          icon: 'refresh',
          text: 'Resend',
          click: 'resend',
        },
      ]
    },
  },

};
</script>

<style lang="scss">
.k-list-item-sendmentions-sent .k-list-item-image .k-icon svg * { fill:green; }
.k-list-item-sendmentions-failed .k-list-item-image .k-icon svg * { fill:red; }
.k-sendmentions-section .k-list-item-text small { text-transform: capitalize; }
</style>
