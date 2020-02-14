<template>
  <div class="SingleProjectPageCredits box spm">
    <div
      class="spm__left"
    >
      <div class="spm__message">
        <font-awesome-icon
          :icon="['fas', 'info-circle']"
          class="fa-icon"
        />
        {{ message }}
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import Avatar from 'Pages/ProfilePage/Avatar';
import FollowButton from 'Pages/Common/FollowButton';
import MessageButton from 'Pages/Common/MessageButton';// resources/assets/js/vue/components/Common/MessageButton.vue
import { FULL, AUDIO, VIDEO } from '../../../helpers/constants';

export default {
  name: 'SingleProjectPageMessage',
  components: {
    Avatar,
    FollowButton,
    MessageButton,
  },
  props: {
    project: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    message: {
      type: String,
      default: '',
    },
  },
  methods: {
    formatTime(time) {
      return moment.utc(moment.duration(time).asMilliseconds()).format('HH:mm:ss');
    },
    formatType(type) {
      if (type === FULL) return 'Audio/Video';
      if (type === AUDIO) return 'Audio';
      if (type === VIDEO) return 'VIDEO';
      return '';
    },
    onUserClick(username) {
      window.location = `/profile/${username}`;
    },
    onProjectClick(uuid) {
      window.location = `/projects/${uuid}`;
    },
    roundPercentages(percentages) {
      return Math.round(percentages);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .spm // Single Project User
    padding 16px 26px
    .fa-icon
      color: $primary
    &__message
      fnt($text, 14px, $weight-normal, left);
</style>
