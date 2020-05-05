<template>
  <li class="k-list-item">
    <div class="k-list-item-content">
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
    type_formatted() {
      switch (this.item.type) {
        case 'none':
            return '';
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
            return 'download';
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

</style>
