<template>
  <div class="MediaEditorProjectsPage media-editor-projects">
    <top-bar/>
    <div class="media-editor-projects__main">
      <left-sidebar/>
      <div class="column media-editor-projects__column">
        <media-editor-projects-box/>
      </div>
    </div>
  </div>
</template>

<script>

import LeftSidebar from '../LeftSidebar/LeftSidebar';
import TopBar from '../TopBar/TopBar';
import MediaEditorProjectsBox from './MediaEditorProjectsBox';

export default {
  name: 'MediaEditorProjects',
  components: {
    LeftSidebar,
    MediaEditorProjectsBox,
    TopBar,
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .media-editor-projects
    display flex
    flex-direction column
    min-height 100vh
    &__main
      background-color $white-bis
      display flex
      flex-grow 1
      position relative
    &__column
      margin-left 72px
      padding 32px 32px 0 28px

</style>
