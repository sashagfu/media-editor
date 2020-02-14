<template>
  <div class="HeadLineFeatured posts-headline">
    <div class="header__headline level is-marginless">
      <div class="level-left">
        <div class="header__ava level-item"/>
        <div class="level-item header__title">
          <div class="header__text">
            <span class="header__text--strong">
              James Spiegel
            </span>
            wrote a
            <span class="header__text--em">
              blog post
            </span>
          </div>
          <div class="header__date">
            7 hours ago
          </div>
        </div>
      </div>
      <div class="level-right">
        <div class="level-item level-item--last">
          <a class="header__settings"/>
        </div>
      </div>
    </div>
    <div class="header__buttons">
      <div class="header__button header__button--feature"/>
      <div class="header__button header__button--likes"/>
      <div class="header__button header__button--comments"/>
      <div class="header__button header__button--shares"/>
    </div>
  </div>
</template>

<script>
/* @flow */
export default {
  components: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .posts-headline {
    position : relative;
  }

  .header {
    &__headline {
      height  : 88px;
      padding : 26px;
    }
    &__ava {
      background    : url(http://lorempixel.com/40/40/) center center no-repeat $grey-light;
      border-radius : 100%;
      flex          : 0 0 auto;
      height        : 40px;
      margin-right  : 16px;
      width         : 40px;
    }
    &__title {
      align-items     : flex-start;
      display         : flex;
      flex-direction  : column;
      justify-content : center;
    }
    &__text {
      fnt($text-light, 14px, $weight-semibold, left);
      &--strong {
        color       : $text;
        font-weight : $weight-semibold+100;
      }
      &--em {
        color : $primary;
      }
    }
    &__date {
      color      : $text-light;
      font       : $weight-normal 12px;
      text-align : left;
    }
    &__settings {
      background : url('../../../../../img/three-dots-icon--light.png') center center no-repeat;
      height     : 40px;
      width      : 44px;
      &:hover {
        background-image : url('../../../../../img/three-dots-icon--dark.png');
      }
    }
    &__buttons {
      position : absolute;
      right    : -20px;
      top      : 18px;
    }
    &__button {
      background-color : $grey-light;
      border-radius    : 100%;
      cursor           : pointer;
      height           : 32px;
      margin           : 4px;
      transition       : all .3s;
      width            : 32px;
      &:hover {
        background-color : $grey;
      }
      &--feature {
        background      : url('../../../../../img/trophy-icon--white.png')
        center center no-repeat $yellow;
        background-size : 52%;
        cursor          : auto;
      }
      &--likes {
        background      : url('../../../../../img/heart-icon--white.png')
        center center no-repeat $grey-light;
        background-size : 52%;
        &:active {
          background-color : $primary;
        }
      }
      &--comments {
        background      : url('../../../../../img/speech-balloon-icon--white.png')
        center center no-repeat $grey-light;
        background-size : 55%;
        &:active {
          background-color : $blue;
        }
      }
      &--shares {
        background      : url('../../../../../img/share-icon--white.png')
        center center no-repeat $grey-light;
        background-size : 70%;
        &:active {
          background-color : $turquoise;
        }
      }
    }
  }

</style>
