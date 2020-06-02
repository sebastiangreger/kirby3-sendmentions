<template>
  <section class="k-section">

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

  </section>
</template>

<script>
export default {
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
    }
  },

  created() {
    this.load().then((response) => {
      this.headline                 = response.headline;
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
  }
}
</script>
