<template>
  <div class="play-list-item level">
    <div class="level-left">
      <div class="play-list-item__frame level-item">
        <div class="play-list-item__cover"/>
      </div>
      <div class="play-list-item__title-box">
        <div class="play-list-item__title">
          Praeludium & Fuge in C-Dur
        </div>
        <div class="play-list-item__sub-title">
          Keith Jarrett
        </div>
      </div>
    </div>
    <div class="level-right">
      <div class="level-item">
        <div class="play-list-item__duration">
          4:16
        </div>
        <div class="play-list-item__settings-box">
          <div class="play-list-item__del"/>
          <div class="play-list-item__like"/>
          <div class="play-list-item__share"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  components: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .play-list-item {
    height     : 60px;
    padding    : 8px 26px;
    transition : all .3s;
    width      : 100%;
    &:hover {
      background-color : $white-bis;
    }
    &__frame {
      background    : center center no-repeat $grey-light;
      border-radius : 3px;
      height        : 36px;
      width         : 36px;
    }
    &__cover {
      background    : center center/40% no-repeat $cover-grey;
      border-radius : 3px;
      cursor        : pointer;
      height        : 36px;
      opacity       : 0;
      transition    : all .3s;
      width         : 36px;
    }
    &__title-box {
      padding : 0 12px;
    }
    &__title {
      fnt($text, 12px, $weight-semibold, left)
    }
    &__sub-title {
      fnt($text-light, 11px, $weight-light, left)
    }
    &__setting-box {
      align-items     : flex-end;
      display         : flex;
      flex-direction  : column;
      justify-content : center;
    }
    &__setting-head-line {
      display         : flex;
      align-items     : flex-start;
      justify-content : flex-end;
      height          : 12px;
    }
    &__duration {
      fnt($text-lighter, 11px, $weight-semibold, left);
      position    : relative;
      opacity     : 1;
      transition  : all .1s;
    }
    &__settings-box {
      align-items     : center;
      cursor          : pointer;
      display         : flex;
      height          : 26px;
      justify-content : flex-end;
      opacity         : 0;
      position        : absolute;
      right           : 0;
      transition      : all .3s;

    }
    &__del {
      background : center center/45% no-repeat;
      height     : 100%;
      width      : 26px;
    }
    &__like {
      background      : center center/51% no-repeat;
      height          : 100%;
      width           : 26px;
    }
    &__share {
      height          : 100%;
      width           : 28px;
      background      : center center/65% no-repeat;
    }
  }

  .level-item {
    position : relative;
  }

  .play-list-item:hover .play-list-item__cover {
    opacity : 1;
  }

  .play-list-item:hover .play-list-item__duration {
    opacity : 0;
  }

  .play-list-item:hover .play-list-item__settings-box {
    opacity : 1;
  }
</style>
