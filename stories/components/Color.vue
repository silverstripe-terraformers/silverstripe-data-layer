<template>
  <div>
    <div :style="{ 'background-color': hex, color: textColor }" class="color">
      <span class="color__name">{{ name }}</span>
      <span class="color__hex">{{ getHex() }}</span>
    </div>
    <div class="opacity" v-if="showOpacity">
      <div
        :style="{ 'background-color': opacity(80), color: textColor }"
        class="opacity__color"
      >
        <span>80%</span>
      </div>
      <div
        :style="{ 'background-color': opacity(60), color: textColor }"
        class="opacity__color"
      >
        <span>60%</span>
      </div>
      <div
        :style="{ 'background-color': opacity(40), color: textColor }"
        class="opacity__color"
      >
        <span>40%</span>
      </div>
      <div
        :style="{ 'background-color': opacity(20), color: textColor }"
        class="opacity__color"
      >
        <span>20%</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "colour",
  props: {
    name: {
      type: String,
    },
    hex: {
      type: String,
    },
    cssvariable: {
      type: String,
    },
    textColor: {
      type: String,
      default: "#FFFFFF",
    },
    showOpacity: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    opacity(percent) {
      const hex = this.getHex();

      const r = parseInt(hex.substr(1, 2), 16);
      const g = parseInt(hex.substr(3, 2), 16);
      const b = parseInt(hex.substr(5, 2), 16);

      const alpha = percent / 100;

      return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    },
    getHex() {
      if (this.cssvariable) {
        const hex = getComputedStyle(
          document.getElementsByTagName("body")[0]
        ).getPropertyValue(this.cssvariable);
        return hex;
      }
      return this.hex ?? "mediumaquamarine";
    },
  },
};
</script>

<style lang="scss">
.color {
  display: flex;
  height: 180px;
  width: 180px;
  flex-direction: column;
  padding: 10px;
  justify-content: flex-end;
  margin-top: 40px;

  &__name {
    display: block;
    height: 100%;
    font-weight: bold;
  }

  &__hex {
    display: block;
  }
}

.opacity {
  width: 180px;
  margin: 30px 0 0;

  &__color {
    padding: 10px;
  }
}
</style>
