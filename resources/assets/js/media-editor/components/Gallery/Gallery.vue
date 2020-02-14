<template>
  <div class="Gallery me-gallery">
    <gallery-control-bar/>
    <div class="me-gallery__list">
      <gallery-items/>
    </div>
    <!--<GalleryDragLayerContainer />-->
  </div>
</template>

<script>
import GalleryControlBar from './GalleryControlBar';
import GalleryItems from './GalleryItems';

export default {
  name: 'Gallery',
  components: {
    GalleryControlBar,
    GalleryItems,
  },
};
</script>
