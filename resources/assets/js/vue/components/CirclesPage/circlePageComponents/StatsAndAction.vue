<template>
  <div class="StatsAndAction3 stats-and-action">
    <div class="stats level">
      <div class="level-item">
        <div class="stats__title">
          {{ circle.totalMembers ? circle.totalMembers : 0 }}
        </div>
        <div class="stats__sub-title">
          {{ trans('circles.members') }}
        </div>
      </div>
      <div class="level-item">
        <div class="stats__title">
          {{ circle.totalPosts ? circle.totalPosts : 0 }}
        </div>
        <div class="stats__sub-title">
          {{ trans('posts.page_title') }}
        </div>
      </div>
    </div>
    <p class="stats__description">
      {{ circle.description }}
    </p>
  </div>
</template>

<script>
/* @flow */
export default {
  components: {},
  props: {
    circle: {
      type: Object,
      default() {
        return {};
      },
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .level-item {
    display        : flex;
    flex-direction : column;
    align-items    : center;
  }

  .stats {
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
    &__description {
      padding : 0 10px;
    }
  }

  .action {
    display         : flex;
    align-items     : center;
    justify-content : center;
  }

  .action-btn {
    cursor        : pointer;
    margin        : 8px;
    height        : 48px;
    width         : 48px;
    border-radius : 50%;
    background    : center center no-repeat $blue;
    &--friend-request {
      text-align    : end;
      font-size     : 12px;
      font-weight   : 900;
      color         : $white;
      padding-right : 12px;
      background    : url('../../../../../img/happyface-icon--white.png') $info;
    }
    &--remove-friend {
      text-align    : end;
      font-size     : 12px;
      font-weight   : 900;
      color         : $white;
      padding-right : 12px;
      background    : url('../../../../../img/happyface-icon--white.png') $grey-light;
    }
    &--add-fav-page {
      text-align    : end;
      font-size     : 12px;
      font-weight   : 900;
      color         : $white;
      padding-right : 12px;
      background    : url('../../../../../img/star-icon--white.png') $primary;
    }
    &--message {
      background : url('../../../../../img/chat-messages-icon--white.png') $accented;
    }
    &--settings {
      background : url('../../../../../img/settings-icon--white.png') $primary;
    }
  }
</style>
