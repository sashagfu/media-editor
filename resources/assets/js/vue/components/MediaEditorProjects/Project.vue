<template>
  <div class="Project project">
    <div class="project-box">
      <div class="project__headline">
        <div
          :class="{'project__star--is-fav':isFav}"
          class="project__star"
          @click="isFav = !isFav"
        />
        <div class="project__set"/>
      </div>
      <div class="project__main">
        <div class="project__pill">
          <i
            class="fa fa-pencil fa-2x fa-inverse"
            aria-hidden="true"
          />
        </div>
      </div>
      <div class="project__bottom-line">
        <div class="project__title">
          Rock Garden Festival - Day 1
        </div>
        <div class="project__sub-title">
          12:06
        </div>
      </div>

    </div>
  </div>
</template>

<script>
// import LeftSidebar from './../LeftSidebar/LeftSidebar.vue';

export default {
  components: {},
  data() {
    return {
      isFav: false,
    };
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .project
    background url('http://lorempixel.com/400/200') center center no-repeat $grey
    background-size cover
    border 1px solid $border
    border-radius 3px
    cursor pointer
    padding-top 62%
    position relative

    &__headline
      fl-right()
      padding 6px 12px
    &__main
      fl-center()
    &__bottom-line
      display flex
      flex-direction column
      padding 12px 16px
    &__set
      cursor pointer
      height 26px
      width 26px
      background url('../../../../img/three-dots-icon--white.png') center center no-repeat
      transition all .3s
      &:hover
        background : url('../../../../img/three-dots-icon--light.png') center center no-repeat

    &__star
      cursor pointer
      height 26px
      width 26px
      background url('../../../../img/star-icon--white.png') center center no-repeat
      transition all .3s;
      &--is-fav
        background url('../../../../img/star-icon--primary.png') center center no-repeat

    &__pill
      fl-center()
      background center center no-repeat $grey-dark;
      border 2px solid $white;
      border-radius 50%;
      height 56px;
      transition all .3s;
      opacity 0;
      width 56px;

    &__title
      fnt($text-invert, 12px, $weight-semibold, left)
    &__sub-title
      fnt($text-invert, 10px, $weight-normal, left)
    &:hover &__pill
      opacity 1

  .project-box
    cover-all()
    border-radius 3px
    display flex
    flex-direction column
    justify-content space-between

</style>
