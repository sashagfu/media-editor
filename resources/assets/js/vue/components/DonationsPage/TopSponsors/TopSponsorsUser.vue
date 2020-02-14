<template>
  <div class="TopSponsorsUser ts-user">
    <div
      class="ts-user__left"
      @click="goToUser"
    >
      <Avatar
        v-if="sponsor.avatar.includes('/profile/default/avatar')"
        :username="sponsor.displayName"
        :size="56"
        custom-class="ts-user__avatar"
      />
      <div
        v-else
        :style="{'background-image': `url(${sponsor.avatar})`}"
        class="ts-user__avatar"
      />
      <div class="ts-user__title-box">
        <div class="ts-user__title">
          {{ sponsor.displayName }}
        </div>
        <div class="ts-user__sub-title">
          {{ trans('donate.on_al_from') }}: {{ formatDate(sponsor.createdAt) }}
        </div>
      </div>
      <div class="ts-user__btn-box">
        <FollowButton
          :following="sponsor"
          :active-user="activeUser"
        />
        <MessageButton
          :user="sponsor"
          class="ts-user__btn"
        />
      </div>
    </div>
    <div class="ts-user__center">
      <div class="ts-user__donate-box">
        <div class="ts-user__donate-title">
          {{ trans('donate.donated') }}:
        </div>
        <div class="ts-user__donate-amount">
          $ {{ Number.parseInt(sponsor.donationsSum, 10) }}
        </div>
      </div>
    </div>
    <div class="ts-user__right"/>
  </div>
</template>

<script>
import moment from 'moment';
import * as constants from 'Helpers/constants';

import Avatar from 'Pages/ProfilePage/Avatar';
import FollowButton from 'Pages/Common/FollowButton';
import MessageButton from 'Pages/Common/MessageButton';

export default {
  name: 'TopSponsorsUser',
  components: {
    Avatar,
    FollowButton,
    MessageButton,
  },
  props: {
    sponsor: {
      type: Object,
      default: () => ({}),
    },
    activeUser: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
    };
  },
  methods: {
    formatDate(date) {
      return moment(date).format('MM/DD/YYYY');
    },
    goToUser() {
      this.$router.push({
        name: 'ProfilePage',
        params: {
          username: this.sponsor.username,
        },
      });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($text-light, 14px, $weight-light, left);
    transition: all .3s;
    &--user {
      color: $grey;
    }
    &--follow {
      color: $grey;
      font-size: 11px;
    }
    &--envelope {
      color: $blue;
      font-size: 11px;
    }
    &--chevron {
      color: $grey-light;
      font-size: 28px;
    }
  }

  .ts-user {
    fl-between();
    background: $white;
    padding: 16px 26px;
    width: 100%;
    &:not(:last-child) {
      border-bottom: 1px solid $border;
    }
    &__left {
      fl-left();
      width: 40%;
    }
    &__center {
      fl-center();
      width: 20%;
    }
    &__right {
      fl-right();
      width: 30%;
    }
    &__avatar {
      fl-center();
      background: center center/cover no-repeat $grey-light;
      border-radius: 50%;
      cursor: pointer;
      flex: 0 0 auto;
      height: 56px;
      width: 56px;
    }
    &__title-box {
      cursor: pointer;
      display: flex;
      flex-direction: column;
      padding-left: 16px;
      width: 240px;
    }
    &__title {
      fnt($text, 18px, $weight-semibold, left);
      line-height: 1;
    }
    &__sub-title {
      fnt($primary, 12px, $weight-light, left);
      line-height: 1;
      &--declined {
        color: $text-light;
      }
    }

    &__btn-box {
      fl-left();
      padding: 0 16px;
    }

    &__donate-box {
      fl-left();
    }
    &__donate-title {
      fnt($text-lighter, 16px, $weight-normal, left);
      line-height: 1;
    }
    &__donate-amount {
      fnt($primary, 22px, $weight-normal, left);
      padding-left: 8px;
      line-height: 1;
      &--declined {
        color: $text-light;
      }
    }
    &__btn {
      margin-left: 8px;
    }
  }
</style>
