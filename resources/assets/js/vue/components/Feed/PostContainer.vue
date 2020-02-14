<template>
  <div class="PostContainer post">
    <posts-head-line
      :post="post"
      :user="user"
    />
    <post-main :content="post.parsedContent"/>
    <div
      v-if="post.images.length"
      class="post-media columns is-multiline is-gapless is-marginless"
    >
      <div
        v-for="(item, key) in post.images"
        :key="`img-${key}`"
        :class="{
          'is-12': post.images.length === 1,
          'is-3': post.images.length !== 1
        }"
        class="post-media-p_postsitems column"
      >
        <main-photo-pack
          :media="item"
          :post-id="post.id"
        />
      </div>
    </div>
    <div
      v-if="post.videos.length"
      class="post-media columns is-multiline is-gapless is-marginless"
    >
      <!--<div class="post-media-p_postsitems column"-->
      <div
        v-for="(item, key) in post.videos"
        :key="`vid-${key}`"
        :class="{
          'is-12': post.videos.length === 1,
          'is-3': post.videos.length !== 1
        }"
        class="column"
      >
        <main-video
          v-if="post.videos.length === 1"
          :media="item"
        />
        <main-small-video
          v-else
          :media="item"
        />
      </div>
    </div>
    <divider/>
    <post-footer-with-medallions
      v-if="user"
      :post="post"
      :user="user"
    />
    <border v-if="!user"/>
  </div>
</template>

<script>

import PostsHeadLine from './postComponents/HeadLine';
import PostMain from './postComponents/Main';
import MainVideo from './postComponents/MainVideo';
import MainSmallVideo from './postComponents/MainSmallVideo';
import MainPhotoPack from './postComponents/MainPhotoPack';
import PostFooterWithMedallions from './postComponents/FooterWithMedallions';
import Border from './postComponents/Border';
import Divider from './postComponents/Divider';

export default {
  name: 'PostContainer',
  components: {
    PostsHeadLine,
    PostMain,
    MainVideo,
    MainSmallVideo,
    MainPhotoPack,
    PostFooterWithMedallions,
    Border,
    Divider,
  },
  props: {
    post: {
      type: Object,
      default: () => ({}),
      required: true,
    },
    user: {
      type: Object,
      default: () => ({}),
      required: true,
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>

  .post-media
    padding-right 26px

</style>
