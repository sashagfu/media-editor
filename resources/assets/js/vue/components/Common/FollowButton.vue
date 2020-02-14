<template>
  <div class="FollowButton">
    <div
      v-if="isFollowing"
      :class="[
        'fl-btn',
        {'fl-btn--round': round}
      ]"
      @click="toggleFollow()"
    >
      <font-awesome-icon
        v-if="uploading"
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon fa-icon--follow"
      />
      <font-awesome-icon
        v-else
        :icon="['fas', 'user-times']"
        class="fa-icon fa-icon--follow"
      />
      <div
        v-if="!round"
        class="fl-btn__btn-text">
        {{ trans('profile_page.unfollow') }}
      </div>
    </div>
    <div
      v-else
      :class="[
        'fl-btn',
        {'fl-btn--round': round}
      ]"
      @click="toggleFollow()"
    >
      <font-awesome-icon
        v-if="uploading"
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon fa-icon--follow"
      />
      <font-awesome-icon
        v-else
        :icon="['fas', 'user-plus']"
        class="fa-icon fa-icon--follow"
      />
      <div
        v-if="!round"
        class="fl-btn__btn-text">
        {{ trans('profile_page.follow') }}
      </div>
    </div>
  </div>
</template>

<script>
import DELETE_FOLLOWER from 'Gql/users/mutations/deleteFollower.graphql';
import CREATE_FOLLOWER from 'Gql/users/mutations/createFollower.graphql';

export default {
  name: 'FollowButton',
  props: {
    following: {
      type: Object,
      default: () => ({}),
    },
    activeUser: {
      type: Object,
      default: () => ({}),
    },
    round: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      uploading: false,
      isFollowing: false,
    };
  },
  created() {
    this.isFollowing = this.following.isFollowing;
  },
  methods: {
    toggleFollow() {
      this.uploading = true;
      this.$apollo.mutate({
        mutation: this.isFollowing ? DELETE_FOLLOWER : CREATE_FOLLOWER,
        variables: {
          userId: this.following.id,
        },
      })
        .then(() => {
          this.isFollowing = !this.isFollowing;
          this.uploading = false;
        })
        .catch((error) => {
          console.error(error);
          this.uploading = false;
        });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon
    fnt($text-light, 14px, $weight-light, left)
    transition all .3s
    &--follow
      color $grey
      font-size 11px
      margin-right -2px

  .fl-btn
    fl-center()
    border-radius 9px
    border 1px solid $grey-light
    cursor pointer
    height 18px
    transition all .3s
    width 120px
    &--round
      border-radius 50%
      height 26px
      width 26px
    &:hover
      opacity 0.5
    &--followed
      width 108px
    &__btn-text
      fnt($grey-light, 12px, $weight-normal, center)
      padding-left 8px
      transition all .3s

</style>
