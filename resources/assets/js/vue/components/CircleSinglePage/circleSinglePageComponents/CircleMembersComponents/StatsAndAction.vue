<template>
  <div class="StatsAndAction stats-and-action">
    <div class="stats level">
      <div class="level-item">
        <div class="stats__title">
          {{ member.totalFollowers ? member.totalFollowers : 0 }}
        </div>
        <div class="stats__sub-title">
          {{ trans('profiles.followers') }}
        </div>
      </div>
      <div class="level-item">
        <div class="stats__title">
          {{ member.totalPhotos ? member.totalPhotos : 0 }}
        </div>
        <div class="stats__sub-title">
          {{ trans('profiles.photos') }}
        </div>
      </div>
      <div class="level-item">
        <div class="stats__title">
          {{ member.totalVideos ? member.totalVideos : 0 }}
        </div>
        <div class="stats__sub-title">
          {{ trans('profiles.videos') }}
        </div>
      </div>
    </div>
    <div class="action">
      <div class="action-item">
        <div
          v-if="member.canBeFollowed"
          :class="{
            'action-btn--friend-request': !member.isFollowing,
            'action-btn--remove-friend': member.isFollowing,
            'action-btn--loading': followingLoading
          }"
          class="action-btn"
          @click="followUser(member)"
        >
          <i
            v-if="followingLoading"
            class="fa fa-spinner fa-spin fa-fw action-btn--loading-spinner"
          />
          <span v-else>
            {{ member.isFollowing ? '-' : '+' }}
          </span>
        </div>
      </div>
      <div class="action-item">
        <div
          :class="{
            'action-btn--loading': messagingLoading
          }"
          class="action-btn action-btn--message"
          @click="startMessaging(member)"
        >
          <i
            v-if="messagingLoading"
            class="fa
                              fa-spinner
                              fa-spin
                              fa-fw
                              action-btn--loading-spinner"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import { mapActions } from 'vuex';

const StatsAndAction = {
  name: 'stats-and-action',
  props: {
    member: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  data() {
    return {
      followingLoading: false,
      messagingLoading: false,
    };
  },
  methods: {
    ...mapActions('chat', [
      'setActiveThread',
      'setWidgetVisibility',
    ]),
    followUser(member) {
      this.followingLoading = true;
      this.$http.patch('/api/profile/follow', {
        _token: window.Laravel.csrfToken,
        follower_id: member.id,
      })
        .then(() => {
          this.followingLoading = false;
          this.$set(member, 'isFollowing', !member.isFollowing);
        })
        .catch((error) => {
          this.followingLoading = false;
          this.errors = error.response.data;
        });
    },
    startMessaging(member) {
      this.messagingLoading = true;
      this.$http.post('/api/chat/startProfileChat/', {
        _token: window.Laravel.csrfToken,
        user_id: member.id,
      })
        .then(({ data }) => {
          this.messagingLoading = false;
          this.setActiveThread(data); // $store.dispatch
          this.setWidgetVisibility(true); // $store.dispatch
        })
        .catch((error) => {
          this.messagingLoading = false;
          this.errors = error.response.data;
        });
    },
  },
};

export default StatsAndAction;
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../../sass/front/components/bulma-theme';

  .level-item {
    display        : flex;
    flex-direction : column;
    align-items    : center;
  }

  .stats {
    padding : 8px 0;
    width   : 176px;
    &__title {
      font-size   : 16px;
      font-weight : $weight-semibold;
      color       : $text;
    }
    &__sub-title {
      font-size   : 12px;
      font-weight : $weight-normal;
      color       : $text-lighter;
    }
  }

  .action {
    align-items     : center;
    display         : flex;
    justify-content : center;
  }

  .action-btn {
    background    : center center no-repeat $blue;
    border-radius : 50%;
    cursor        : pointer;
    height        : 48px;
    margin        : 8px;
    width         : 48px;
    // transition: all .3s;

    &--friend-request {
      fnt($text-invert, 12px, $weight-bold, right);
      background : url('../../../../../../img/happyface-icon--white.png') $info;
    }
    &--remove-friend {
      fnt($text-invert, 12px, $weight-bold, right);
      padding-right : 12px;
      transition    : ease-out .5s;
      background    : url('../../../../../../img/happyface-icon--white.png') $grey-light;
    }
    &--loading-spinner {
      font-size : 25px;
      // margin-top: 10px;
      // margin-left: 9px;
    }
    &--loading {
      background-image : none !important;
      display          : flex;
      align-items      : center;
      justify-content  : center;
      padding          : 0;
    }
    &--message {
      background : url('../../../../../../img/chat-messages-icon--white.png') $accented;
    }
    &--settings {
      background : url('../../../../../../img/settings-icon--white.png') $primary;
    }
  }
</style>
