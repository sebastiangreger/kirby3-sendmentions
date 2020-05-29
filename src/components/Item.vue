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
    <nav
      class="k-list-item-options"
      v-if="item.type != 'archive.org'"
    >
      <k-button
        :tooltip="$t('send')"
        :icon="buttonicon"
        :alt=" Send"
        @click.stop="$emit('action', 'triggerPing', item)"
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
      if (this.item.type == 'triggered') {
        return 'k-list-item-sendmentions-triggered';
      } else if (this.item.type == 'archive.org') {
        return 'k-list-item-sendmentions-sent k-list-item-sendmentions-archiveorg';
      } else if (this.item.type == 'none' || this.item.data.response === null) {
        return 'k-list-item-sendmentions-failed';
      } else {
        return 'k-list-item-sendmentions-sent';
      }
    },
    type_formatted() {
      switch (this.item.type) {
        case 'triggered':
          return '';
        case 'none':
          return 'no endpoint';
        case 'pingback':
        case 'archive.org':
          return this.item.type;
        default:
          return this.item.type + ' (' + this.item.data.response + ')';
      }
    },
    buttonicon() {
      if (this.item.type === 'archive.org') {
        return '';
      }
      return 'refresh';
    },
    icon() {
      switch (this.item.type) {
        case 'pingback':
            return 'globe';
        case 'archive.org':
            return 'sendmentions-archiveorg';
        case 'none':
            return 'cancel';
        case 'triggered':
            return '';
        default:
            return 'sendmentions-webmention';
      }
    },
  },

};
</script>

<style lang="scss">
.k-list-item-sendmentions-sent .k-list-item-image .k-icon svg * { fill:#5d800d; }
.k-list-item-sendmentions-failed .k-list-item-image .k-icon svg * { fill:#c82829; }
.k-list-item-sendmentions-archiveorg .k-list-item-text small { margin-right:2.5rem; }
.k-list-item-sendmentions-triggered { background:#fffecd; }
.k-list-item-sendmentions-triggered .k-button-icon svg { animation: rotation 4s infinite linear; }
@keyframes rotation {
  from {
    transform: rotate(359deg);
  }
  to {
    transform: rotate(0deg);
  }
}
</style>
