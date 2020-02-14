<template>
  <div class="LastProject last-project columns is-marginless">
    <div class="last-project__snapshot column is-8">
      <div class="last-project__pill">
        <font-awesome-icon
          :icon="['fas', 'pencil-alt']"
          class="fa-icon fa-icon--invert fa-icon--pointer"
        />
      </div>
      <div class="last-project__title">
        Rock Garden Festival - Day 3
      </div>
      <div class="last-project__sub-title">
        12:06
      </div>
    </div>
    <div class="last-project__main column is-4"/>
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
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color      : $text-light;
    font-size  : 1rem;
    transition : all .2s;
    &--invert {
      color : $text-invert;
    }
    &--pointer {
      cursor : pointer;
    }
  }

  .last-project {
    background-color : $background;
    border           : 1px solid $border;
    border-radius    : 3px;
    display          : flex;
    height           : 464px;
    &__snapshot {
      align-items     : center;
      background      : url(http://lorempixel.com/400/200) center center no-repeat $grey;
      border-radius   : 3px 0 0 3px;
      background-size : cover;
      display         : flex;
      flex-direction  : column;
      height          : 100%;
      justify-content : center;
    }
    &__pill {
      fl-center();
      background      : center center no-repeat transparent;
      border          : 2px solid $white;
      border-radius   : 50%;
      cursor          : pointer;
      height          : 56px;
      transition      : all .3s;
      width           : 56px;
      &:hover {
        background-color : $grey-dark;
      }

    }
    &__title {
      fnt($text-invert, 20px, $weight-semibold, center);
      margin-top : 16px;
    }
    &__sub-title {
      fnt($text-invert, 10px, $weight-normal, center);
    }
  }
</style>
