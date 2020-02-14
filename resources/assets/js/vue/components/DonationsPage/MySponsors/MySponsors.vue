<template>
  <div class="MySponsors">
    <NewDonations
      v-bind="$props"
    />
    <DonationsHistory
      v-bind="$props"
    />
  </div>
</template>

<script>
import NewDonations from './NewDonations';
import DonationsHistory from './DonationsHistory';

export default {
  name: 'MySponsors',
  components: {
    NewDonations,
    DonationsHistory,
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
