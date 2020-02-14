<template>
  <div class="Layout media-editor media-editor__column">
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
import { mapActions } from 'vuex';
import TopBar from 'Editor/components/TopBar/TopBar';
import Gallery from 'Editor/components/Gallery/Gallery';
// import UploadDialog from './Upload/UploadDialog';
import PreviewContainer from 'Editor/components/Preview/PreviewContainer';
import Timeline from 'Editor/components/Timeline/Timeline';
import TimelineFooter from 'Editor/components/Timeline/TimelineFooter';

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
  props: {
    id: {
      type: String,
      default: '',
    },
  },
  created() {
    this.undoRedoInit({ store: this.$store });
    this.fetchProject({ id: this.id });
  },
  methods: {
    ...mapActions('project', [
      'fetchProject',
    ]),
    ...mapActions('project/undoRedo', [
      'undoRedoInit',
    ]),
  },
};
</script>

<style
        lang="stylus"
        scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .media-editor {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    position: relative;

    &__main {
      background-color: $white-bis;
      display: flex;
      flex-grow: 1;
      padding-top: 73px;
      position: relative;
    }

    &__column {
      margin: 0 0 0 72px;
      padding: 19px 19px 0 15px;
      position: absolute;
      left: 0;
      right: 0;
    }
  }
</style>
