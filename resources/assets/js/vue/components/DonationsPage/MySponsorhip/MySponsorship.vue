<template>
  <div class="MySponsorship">
    <MyNewDonations
      v-bind="$props"
    />
    <MyDonationsHistory
      v-bind="$props"
    />
  </div>
</template>

<script>
import MyNewDonations from './MyNewDonations';
import MyDonationsHistory from './MyDonationsHistory';

export default {
  name: 'MySponsorship',
  components: {
    MyNewDonations,
    MyDonationsHistory,
  },
  props: {
    socials: {
      type: Object,
      default: () => ({}),
    },
    flagReasons: {
      type: Array,
      default: () => [],
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    location: {
      type: Object,
      default: () => ({}),
    },
    gmapKey: {
      type: String,
      default: '',
    },
  },
};
</script>
