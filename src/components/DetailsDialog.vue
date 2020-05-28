<template>
  <k-dialog class="k-sendmentions-detailsdialog"
    ref="dialog"
    :button="$t('Done')"
    size="large"
    theme="positive"
    @submit="submit"
  >

    <k-sendmentions-list
      v-if="sendmentions.length > 0"
    >
        <k-sendmentions-item
          v-for="item in sendmentions"
          :key="item.uid"
          :item="item"
          @action="action"
        />
    </k-sendmentions-list>

    <k-empty
      v-else
      layout="list"
      icon="globe"
    >
      {{ empty }}
    </k-empty>

  </k-dialog>
</template>

<script>
import Item from "./Item.vue";
import List from "./List.vue";

export default {
  components: {
    'k-sendmentions-list': List,
    'k-sendmentions-item': Item,
  },

  data() {
    return {
      sendmentions: [],
      empty: null,
    };
  },

  methods: {
    open(sendmentions, empty) {
      this.sendmentions = sendmentions;
      this.empty = empty;
      this.$refs.dialog.open();
    },

    submit() {
      this.$refs.dialog.close();
    },

    action(data, item) {
      if (data === 'triggerPing') {
        this.triggerPing(item.pageid, item.target, item.type);
      }
    },

    async triggerPing(pageid, target, type) {
      console.log(pageid + target + type);
      const endpoint = `sendmentions/` + pageid.replace(/\//s, '+');
      const response = await this.$api.patch(endpoint, {target: target, type: type});
      if (response.type === 'none') {
        this.$store.dispatch("notification/error", "No endpoint found for " + target);
      } else {
        await this.load().then(response => this.sendmentions = response.sendmentions);
        this.$store.dispatch("notification/success", ":)");
      }
    },
  }

};
</script>

<style lang="scss">
.k-sendmentions-detailsdialog .k-dialog-button-cancel {
  opacity: 0;
}
</style>
