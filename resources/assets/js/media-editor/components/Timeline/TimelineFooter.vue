<template>
  <div class="TimelineFooter me-footer">
    <div class="me-footer__scrollbar">
      <timeline-scrollbar/>
    </div>
    <div class="me-footer__zoom">
      <timeline-zoom/>
    </div>
  </div>
</template>

<script>
import TimelineZoom from './TimelineZoom';
import TimelineScrollbar from './TimelineScrollbar';

export default {
  name: 'TimelineFooter',
  components: {
    TimelineZoom,
    TimelineScrollbar,
  },
};
</script>
