<template>
  <div class="PostWithPhotoPack post-with-picture">
    <PostsHeadLine :post="data"/>
    <PostMain :content="data.parsedContent"/>
    <PostMainPhotoPack :media="data.media"/>
    <Divider/>
    <PostFooterWithMedallions :post="data"/>
    <Border/>
  </div>
</template>

<script>

import PostsHeadLine from './postComponents/HeadLine';
import PostMain from './postComponents/Main';
import PostMainPhotoPack from './postComponents/MainPhotoPack';
import PostFooterWithMedallions from './postComponents/FooterWithMedallions';
import Border from './postComponents/Border';
import Divider from './postComponents/Divider';

export default {
  components: {
    PostsHeadLine,
    PostMain,
    PostMainPhotoPack,
    PostFooterWithMedallions,
    Border,
    Divider,
  },
  props: {
    data: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
