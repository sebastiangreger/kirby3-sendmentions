<template>
  <section class="k-sendmentions-section k-section">

    <header class="k-section-header">
      <k-headline>{{ headline }}</k-headline>
    </header>

    <k-box
      v-for="error in sendmentionsSystemErrors"
      :key="error.id"
      :theme="error.theme"
    >
      <k-text
        v-html="error.message"
        size="small"
      />
    </k-box>

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

  </section>
</template>

<script>
import Item from "./Item.vue";
import List from "./List.vue";

export default {
  extends: "k-info-section",

  components: {
    'k-sendmentions-list': List,
    'k-sendmentions-item': Item,
  },

  props: {
    // Re-defined from Kirby’s section mixing, because otherwise
    // the section would throw an error, when hot-reloaded during
    // development.
    name: String,
    parent: String
  },

  data() {
    return {
      headline: null,
      sendmentions: [],
      empty: null,
      sendmentionsSystemErrors: [],
    }
  },

  created() {
    this.load().then((response) => {
      this.headline                 = response.headline;
      this.sendmentions             = response.sendmentions;
      this.empty                    = response.empty;
      this.sendmentionsSystemErrors = response.sendmentionsSystemErrors;
    });
  },

  methods: {
    // re-defining load method from Kirby’s section mixin, because
    // it’s not included otherwhise when hot-reloading the module,
    // which leads to an error during development.
    load() {
      return this.$api.get(this.parent + '/sections/' + this.name);
    },

    action(data, pageid, target, type) {
      if (data === 'resend') {
        this.resend(pageid, target, type);
      }
    },

    async resend(pageid, target, type) {
      pageid = pageid.replace(/\//s, '+');
      const endpoint = `sendmentions/${pageid}`;
      const response = await this.$api.patch(endpoint, {target: target, type: type});

      await this.load().then(response => this.sendmentions = response.sendmentions);
      this.$store.dispatch("notification/success", ":)");
    },

  }

}
</script>

<style lang="scss">
.k-sendmentions-section > .k-box {
  margin-bottom: 1.5rem;
}
</style>
