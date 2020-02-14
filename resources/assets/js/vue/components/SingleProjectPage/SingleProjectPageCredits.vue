<template>
  <div class="SingleProjectPageCredits box spc">
    <div
      class="spc__left"
    >
      <div class="spc__title">
        Clip Credits:
      </div>
      <div
        class="spc__items"
      >
        <div
          v-for="credit in project.credits"
          :key="credit.details.author.id"
          class="item"
        >
          {{ formatTime(credit.details.from) }} -
          {{ formatTime(credit.details.to) }}
          ({{ formatType(credit.details.type) }})
          <span class="item__project">
            <span
              class="item__project-title"
              @click="onProjectClick(credit.details.project.uuid)"
            >
              "{{ credit.details.project.title }}"
            </span>
          </span>
          <span>( {{ roundPercentages(credit.details.percentages) }}%)</span>
          by
          <span
            class="item__user-title"
            @click="onUserClick(credit.details.author.username)"
          >
            {{ credit.details.author.displayName }}
          </span>
        </div>
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
  name: 'SingleProjectPageCredits',
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
    onProjectClick(uuid, deprecated = false) {
      window.location = `/projects/${uuid}${(deprecated) ? '?deprecated=true' : ''}`;
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

  .spc // Single Project User
    fl-between()
    padding 16px 26px
    &__avatar
      circle(40)
      background center center/cover no-repeat $grey-light
    &__title-box
      display flex
      flex-direction column
      padding-left 8px
    &__title
      fnt($text, 14px, $weight-semibold, left)
      margin-bottom 10px
    &__items
      fnt($text, 13px, $weight-normal, left);
      font-style: italic;
      .item
        margin-bottom: 3px;
        &__project:hover > .item__project-title
          color $primary
        &__project-title
          cursor pointer
        &__user-title
          cursor pointer
        &__user-title:hover
          color $blue
        &__project-version
          background-color #f3464c
          border-radius 5px
          color #fff
          height: 15px
          padding 0 6px
          width 20px
          cursor pointer


    &__sub-title
      fnt($primary, 12px, $weight-normal, left);
    &__btn-box + &__btn-box
      margin-left 8px


</style>
