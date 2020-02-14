<template>
  <div class="friend-content-box">
    <div class="friend-content-box__item--left">
      <div class="friend-content-box__ava-box">
        <UserAva/>
      </div>
      <div class="friend-content-box__title">
        <div class="friend-content-box__title-user">
          Stagg Clothing
        </div>
        <div class="friend-content-box__title-text">
          4 hours ago
        </div>
      </div>
    </div>
    <div class="friend-content__item--right">
      <div class="friend-content-box-icon__inline">
        <div
          class="friend-content-box-icon
                            friend-content-box-icon--more-options"/>
      </div>
      <div class="friend-content-box-icon__accepted">
        <div
          class="friend-content-box-icon
                            friend-content-box-icon--became-friends"/>
      </div>
      <div class="friend-content-box-icon__request friend-content-box-icon__request--visible">
        <div
          class="friend-content-box-icon__request-box
                            friend-content-box-icon__request-box--add"
        >
          +
        </div>
        <div
          class="friend-content-box-icon__request-box
                            friend-content-box-icon__request-box--remove"
        >
          -
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import UserAva from '../../UserAva/UserAva';

export default {
  components: {
    UserAva,
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .friend-content-box {
    align-items     : flex-start;
    display         : flex;
    justify-content : space-between;
    min-height      : 82px;
    padding-bottom  : 26px;
    width           : 100%;
    &__item {
      &--left {
        align-items : flex-start;
        display     : flex;
      }
      &--right {
        display        : flex;
        align-items    : flex-end;
        flex-direction : column;
      }
    }
    &__ava-box {
      align-items     : flex-start;
      display         : flex;
      justify-content : center;
      padding         : 22px 16px 0px 22px;
      width           : 72px;
    }
    &__title {
      align-items    : flex-start;
      display        : flex;
      flex-direction : column;
      padding        : 22px 44px 0 0;
    }

    &__title-user {
      fnt($text, 14px, $weight-bold, left);
    }
    &__title-text {
      fnt($text-light, 12px, $weight-normal, left);
      display     : inline;
    }

    &-icon {
      background : center center no-repeat;
      &--more-options {
        height: 8px;
        margin: 10px 12px 0 0;
        width: 10px;
      }
      &__inline {
        align-items     : flex-start;
        display         : flex;
        justify-content : flex-end;
      }
      &__accepted {
        display : none;
        height  : 100%;
        padding : 12px 26px 0 0;
        width   : 100%;
        &--visible {
          display : flex;
        }
      }
      &__request {
        display         : none;
        justify-content : space-between;
        padding         : 6px 20px 0 0;
        width           : 98px;
        &--visible {
          display : flex;
        }
      }
      &__request-box {
        align-items: flex-start;
        background: url('../../../../../img/happy-face-icon-is-invert.png') center center no-repeat;
        border-radius: $radius;
        color: $dark-invert;
        display: flex;
        font-size: 12px;
        font-weight: $weight-bold;
        height: 36px;
        justify-content: flex-end;
        padding: 0 3px 0 0;
        width: 36px;
        &--add {
          background-color : $info;
        }
        &--remove {
          background-color : $grey-light;
        }
      }
    }

    &:hover {
      background-color : $white-ter;
    }
  }

  a.friend-content-user {
    color       : $text !important;
    font-size   : 12px;
    font-weight : $weight-semibold;
    :hover {
      color : $text-light !important;
    }
  }

</style>
