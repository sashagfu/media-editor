<template>
  <div class="TimelineScrollbar me-tl-scrollbar">
    <div
      class="me-scrollbar"
    >
      <vue-scrollbar-only
        :content-size="timelineLayerWidth"
        :container-size="timelineLayersContainer.width"
        :value="scrollH"
        horizontal
        @input="setScrollH"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import VueScrollbarOnly from '../common/vue-scrollbar-only';

export default {
  name: 'TimelineScrollbar',
  components: {
    VueScrollbarOnly,
  },
  computed: {
    ...mapGetters('timeline', [
      'scrollH',
      'timelineLayerWidth',
    ]),
    ...mapGetters('coordinates', [
      'timelineLayersContainer',
    ]),
  },
  methods: {
    ...mapActions('timeline', [
      'setScrollH',
    ]),
  },
};
</script>
