<template>
  <div class="k-toggle-field k-field k-sendmentions-pagesettings">
    <div data-theme="field" data-type="toggle" class="k-input">
      <span class="k-input-element">
        <label class="k-toggle-input">
          <!--input
            ref="input"
            :checked="setting.value"
            :id="setting.id"
            :disabled="setting.disabled"
            class="k-toggle-input-native"
            type="checkbox"
            @change="change($event.target.checked)"
          -->
          <input
            ref="input"
            :checked="value"
            id="autoping"
            class="k-toggle-input-native"
            type="checkbox"
            @change="change($event.target.checked)"
          >
          <span class="k-toggle-input-label" v-html="label"></span>
        </label>
      </span>
    </div>
  </div>
</template>

<script>
export default {

  props: {
    autoping: Object,
  },

  computed: {
    value() {
      ;
    },
    label() {
      return this.value ? "No pings on publish" : "Send pings on publish";
    },
  },

  methods: {
    change(checked) {
      this.value = checked;
      this.$emit('change', 'autoping', checked);
    }
  }

}
</script>

<style lang="scss">
.k-sendmentions-pagesettings {
  margin-top: .5rem;
}
.k-sendmentions-pagesettings .k-input[data-theme="field"] {
  border: none;
  background: transparent;
}
.k-sendmentions-pagesettings .k-toggle-input-label {
  font-size: .875rem;
}
</style>
