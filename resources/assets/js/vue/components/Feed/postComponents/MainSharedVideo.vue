<template>
  <div class="MainSharedVideo shared-video">
    <div class="shared-video__box box columns is-gapless is-paddingless">
      <div
        :style="{backgroundImage: `url(${media.thumbnail_path})`}"
        class="shared-video__frame column is-4 ">
        <div class="shared-video__cover">
          <div class="video-box__cover">
            <div class="player-box">
              <div class="player-box__icon"/>
            </div>
          </div>
        </div>
      </div>
      <div class="shared-video__main-text column is-8">
        <div class="shared-video__title">
          {{ media.name }}
        </div>
        <div class="shared-video__text">
          Lorem ipsum dolor sit amet, consectetur ipisicing elit,
          sed do eiusmod tempo incididunt ut labore..
        </div>
        <div class="shared-video__source">
          youtube.com
        </div>
      </div>
    </div>
  </div>

</template>

<script>

export default {
  components: {},
  props: {
    media: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .shared-video {
    padding : 16px 26px;
    &__box {
      display : flex;
    }
    &__frame {
      background    : center center no-repeat $grey-light;
      border-radius : 3px 0 0 3px;
      border-right  : 1px solid $border;
      height        : 178px;
      transition    : all .3s;
    }
    &__cover {
      align-items     : center;
      border-radius   : 3px 0 0 3px;
      display         : flex;
      height          : 178px;
      justify-content : center;
      opacity         : 1;
      position        : relative;
      transition      : all .3s;
      width           : 100%;
    }
    &__main-text {
      display         : flex;
      flex-direction  : column;
      justify-content : center;
    }
    &__title {
      color       : $text;
      font-size   : 20px;
      font-weight : $weight-normal;
      padding     : 0 22px 16px 22px;
      text-align  : left;
    }
    &__text {
      color       : $text-light;
      font-size   : 13px;
      font-weight : $weight-normal;
      padding     : 0 22px;
      text-align  : left;
    }
    &__source {
      color          : $text-light;
      cursor         : pointer;
      font-size      : 10px;
      font-weight    : $weight-semibold;
      padding        : 16px 22px 0 22px;
      text-align     : left;
      text-transform : uppercase;
      &:hover {
        color : $text;
      }
    }
  }

  .player-box {
    align-items     : center;
    display         : flex;
    flex-direction  : column;
    justify-content : center;
    &__icon {
      background    : url('../../../../../img/player/play-icon--white.png')
      center center no-repeat transparent;
      border        : 4px solid $dark-invert;
      border-radius : 100%;
      height        : 64px;
      width         : 65px;
      &:hover {
        background-color : $primary;
      }
      &:active {
        background-image : url('../../../../../img/player/play-icon--primary.png')
        center center no-repeat $grey;
      }
    }
  }
</style>
