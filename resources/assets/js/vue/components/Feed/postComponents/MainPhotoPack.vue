<template>
  <div class="post-main-photo-pack">
    <div class="post-main-photo is-parent">
      <div class="is-child">
        <lightbox
          :key="media.id"
          :src="src"
          :album="`album-${postId}`"
        >
          <div
            v-if="!media.imageable_id"
            :style="{backgroundImage: `url(/post/images/default/${media.file_name})`}"
            class="post-main-photo__photo box"
          />
          <div
            v-else
            :style="{backgroundImage: style}"
            class="post-main-photo__photo box"
          />
        </lightbox>
      </div>
    </div>
  </div>
</template>
<script>
import Lightbox from 'vue-lightbox';

/* @flow */
export default {
  components: {
    Lightbox,
  },
  props: {
    media: {
      type: Object,
      default: () => ({}),
    },
    postId: {
      type: Number,
      default: 0,
    },
  },
  computed: {
    src() {
      return !this.media.imageable_id
        ? `/post/images/default/${this.media.file_name}`
        : `/post/${this.postId}/images/${this.media.file_name}`;
    },
    style() {
      return `url(/post/${this.postId}/images/${this.media.file_name})`;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .box:not(:last-child)
    margin 0

  .post-main-photo-pack
    /*padding: 0 26px 26px 26px;*/
    padding 0 0 26px 26px

  .post-main-photo
    position relative

    &__photo
      cursor pointer
      background center center no-repeat $grey-light
      border-radius 3px
      padding-top 100%
      background-size cover

    &__cover
      fnt($text-invert, 68px, $weight-light, center)
      fl-center
      background-color $cover-primary
      border-radius 3px
      height 100%
      position absolute
      top 0
      width 100%
      z-index 1

    &.is-child
      padding 0 4px;
      &:first-child
        padding-left 0
      &:last-child
        padding-right 0

    for $i in (1..12)
      &.is-$i
        overflow hidden
        flex none
        width ($i / 12) * 100%

</style>
