<template>
  <div class="PostWithSharedVideo post">
    <PostsHeadLine
      :post="data"
    />
    <PostMain
      :content="data.parsedContent"
    />
    <SharedVideo
      :media="data.media"
    />
    <Divider/>
    <PostFooterWithMedallions
      :post="data"
    />
    <Border/>
  </div>
</template>

<script>

import PostsHeadLine from './postComponents/HeadLine';
import PostMain from './postComponents/Main';
import SharedVideo from './postComponents/MainSharedVideo';
import PostFooterWithMedallions from './postComponents/FooterWithMedallions';
import Border from './postComponents/Border';
import Divider from './postComponents/Divider';

export default {
  components: {
    PostsHeadLine,
    PostMain,
    SharedVideo,
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
