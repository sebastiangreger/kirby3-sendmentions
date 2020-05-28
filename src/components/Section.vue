<template>
  <section class="k-sendmentions-section k-section">

    <header class="k-section-header">
      <k-headline>{{ headline }}</k-headline>
      <!--
      <k-button-group>
        <k-button icon="chat" @click="details()">Open</k-button>
      </k-button-group>
      //-->
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

    <ul class="k-list">

      <li class="k-list-item" @click="details()">
        <span class="k-list-item-text k-sendmentions-section-count">
          <em>
            <svg viewBox="0 0 16 16" xmlns:xlink='http://www.w3.org/1999/xlink'>
              <use xlink:href="#icon-sendmentions-webmention"></use>
            </svg>
            {{ counter.mention }}
          </em>
          <em>
            <svg viewBox="0 0 16 16" xmlns:xlink='http://www.w3.org/1999/xlink'>
              <use xlink:href="#icon-globe"></use>
            </svg>
            {{ counter.ping }}
          </em>
          <em>
            <svg viewBox="0 0 16 16" xmlns:xlink='http://www.w3.org/1999/xlink'>
              <use xlink:href="#icon-cancel"></use>
            </svg>
            {{ counter.none }}
          </em>
          <em>
            <svg viewBox="0 0 16 16" xmlns:xlink='http://www.w3.org/1999/xlink'>
              <use xlink:href="#icon-sendmentions-archiveorg"></use>
            </svg>
            {{ counter.archive }}
          </em>
        </span>
        <nav class="k-list-item-options">
          <button type="button" class="k-button">
            <span aria-hidden="true" class="k-button-icon k-icon k-icon-cog">
              <svg viewBox="0 0 16 16" xmlns:xlink='http://www.w3.org/1999/xlink'>
                <use xlink:href="#icon-cog"></use>
              </svg>
            </span>
          </button>
        </nav>
      </li>

      <li class="k-list-item" v-if="queued" @click="triggerQueue">
        <span class="k-list-item-text k-sendmentions-section-queued">
          <svg viewBox="0 0 16 16" xmlns:xlink='http://www.w3.org/1999/xlink'>
            <use xlink:href="#icon-clock"></use>
          </svg>
          <em>Enqueued for processing</em>
        </span>
        <nav class="k-list-item-options">
          <button type="button" class="k-button">
            <span aria-hidden="true" class="k-button-icon k-icon k-icon-sendmentions-run">
              <svg viewBox="0 0 16 16" xmlns:xlink='http://www.w3.org/1999/xlink'>
                <use xlink:href="#icon-sendmentions-run"></use>
              </svg>
            </span>
          </button>
        </nav>
      </li>

    </ul>

    <k-sendmentions-pagesettings>
      <k-sendmentions-pagesettingstoggle
        v-for="setting in settings"
        :key="setting.id"
        :setting="setting"
        @change="changePageSetting"
      />
    </k-sendmentions-pagesettings>

    <k-sendmentions-details-dialog
      ref="details"
    />

  </section>
</template>

<script>
import DetailsDialog from "./DetailsDialog.vue";
import PageSettings from "./PageSettings.vue";
import PageSettingsToggle from "./PageSettingsToggle.vue";

export default {
  extends: "k-info-section",

  components: {
    'k-sendmentions-details-dialog': DetailsDialog,
    'k-sendmentions-pagesettings': PageSettings,
    'k-sendmentions-pagesettingstoggle': PageSettingsToggle,
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
      settings: [],
      counter: [],
      queued: null,
    }
  },

  created() {
    this.load().then((response) => {
      this.headline                 = response.headline;
      this.sendmentions             = response.sendmentions;
      this.empty                    = response.empty;
      this.sendmentionsSystemErrors = response.sendmentionsSystemErrors;
      this.settings                 = response.pageSettings;
      this.pageid                   = response.pageId;
      this.counter                  = this.output(response.sendmentions);
      this.queued                   = response.queued;
    });
    this.$events.$on("page.changeStatus", this.statuschange);
    this.$events.$on("model.update", this.pagesave);
  },

  destroyed() {
    this.$events.$off("page.changeStatus", this.statuschange);
    this.$events.$off("model.update", this.pagesave);
  },

  methods: {
    // re-defining load method from Kirby’s section mixin, because
    // it’s not included otherwhise when hot-reloading the module,
    // which leads to an error during development.
    load() {
      return this.$api.get(this.parent + '/sections/' + this.name);
    },

    statuschange() {
      this.load().then((response) => {
        this.sendmentions             = response.sendmentions;
        this.settings                 = response.pageSettings;
        this.queued                   = response.queued;
        this.counter                  = this.output(response.sendmentions);
      });
      console.log('Page status changed');
    },

    pagesave() {
      this.load().then((response) => {
        this.sendmentions             = response.sendmentions;
        this.settings                 = response.pageSettings;
        this.queued                   = response.queued;
        this.counter                  = this.output(response.sendmentions);
      });
      console.log('Page saved');
    },

    output(sendmentions) {
      var counter = {mention: 0, ping: 0, none: 0, archive: 0};
      Object.keys(sendmentions).forEach(function(key) {
        if(sendmentions[key].type === 'webmention') {
          counter.mention++;
        } else if(sendmentions[key].type === 'pingback') {
          counter.ping++;
        } else if(sendmentions[key].type === 'none') {
          counter.none++;
        } else if(sendmentions[key].type === 'archive.org') {
          counter.archive++;
        }
      });
      return counter;
    },

    details() {
      this.$refs.details.open(this.sendmentions, this.empty);
    },

    async changePageSetting(key, value) {
      const endpoint = `sendmentions/pagesettings/` + this.pageid.replace(/\//s, '+');
      const response = await this.$api.patch(endpoint, {key: key, value: value});
    },

    async triggerQueue() {
      const endpoint = `sendmentions/` + this.pageid.replace(/\//s, '+');
      const response = await this.$api.patch(endpoint);
      if (response.status === 'ok') {
        await this.load().then((response) => {
          this.sendmentions             = response.sendmentions;
          this.queued                   = response.queued;
          this.counter                  = this.output(response.sendmentions);
        });
        this.$store.dispatch("notification/success", ":)");
      } else {
        this.$store.dispatch("notification/error", "Something went wrong.");
      }
    }
  },

}
</script>

<style lang="scss">
.k-sendmentions-section > .k-box {
  margin-bottom: 1.5rem;
}
.k-sendmentions-section-queued svg,
.k-sendmentions-section-count svg {
  width: 1rem;
  height: 1rem;
  -moz-transform: scale(1);
  opacity:.33;
  margin-right:.2rem;
}
</style>
