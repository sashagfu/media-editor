<template>
  <div class="Layout media-editor">
    <div class="me-bl-main__row columns is-marginless">
      <div class="me-bl-main__column column is-12">
        <top-bar/>
      </div>
    </div>
    <div class="media-editor__gallery-playback me-bl-main__row columns is-marginless">
      <div class="me-bl-main__column media-editor__gallery ">
        <gallery/>
        <!--<upload-dialog/>-->
      </div>
      <div class="me-bl-main__column media-editor__playback ">
        <preview-container/>
      </div>
    </div>

    <div class="me-bl-main__row columns is-marginless">
      <div class="media-editor__timeline me-bl-main__column column is-12">
        <!--<TimelineContainer />-->
        <timeline/>
      </div>
    </div>
    <div class="media-editor__footer me-bl-main__row columns is-marginless">
      <div class="me-bl-main__column column is-12">
        <timeline-footer/>
      </div>
    </div>
    <div class="media-editor__item-editor"/>
  </div>
</template>

<script>
import TopBar from './TopBar/TopBar';
import Gallery from './Gallery/Gallery';
// import UploadDialog from './Upload/UploadDialog';
import PreviewContainer from './Preview/PreviewContainer';
import Timeline from './Timeline/Timeline';
import TimelineFooter from './Timeline/TimelineFooter';

export default {
  name: 'Layout',
  components: {
    TopBar,
    Gallery,
    // UploadDialog,
    PreviewContainer,
    Timeline,
    TimelineFooter,
  },
//        created() {
//            this.$store.dispatch('project/createProject');
//        }
};
</script>
