<template>
  <div>
    <ul class="nav nav-tabs" role="tablist">
      <li
        v-for="(tab, i) in tabs"
        :key="i"
        class="nav-item"
        role="presentation"
      >
        <a
          v-html="tab.header"
          :aria-controls="tab.hash"
          :aria-selected="tab.isActive"
          @click="selectTab(tab.hash, $event)"
          :href="tab.hash"
          :class="['nav-link', tab.isActive ? 'active' : '']"
          role="tab"
        ></a>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <slot />
    </div>
  </div>
</template>

<script>
import { onMounted, provide, reactive, toRefs } from "vue";

export default {
  name: "tabs",
  props: {
    preventLink: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const state = reactive({
      activeTabHash: "",
      lastActiveTabHash: "",
      tabs: [],
    });

    provide("tabsProvider", state);

    const findTab = (hash) => state.tabs.find((tab) => tab.hash === hash);

    const selectTab = (selectedTabHash, event) => {
      if (event && props.preventLink) {
        event.preventDefault();
      }

      const selectedTab = findTab(selectedTabHash);

      if (!selectedTab) {
        return;
      }

      state.tabs.forEach((tab) => {
        // eslint-disable-next-line no-param-reassign
        tab.isActive = tab.hash === selectedTab.hash;
      });

      state.activeTabHash = selectedTabHash;
    };

    onMounted(() => {
      if (!state.tabs.length) {
        return;
      }

      selectTab(state.tabs[0].hash);
    });

    return {
      ...toRefs(state),
      selectTab,
    };
  },
};
</script>
