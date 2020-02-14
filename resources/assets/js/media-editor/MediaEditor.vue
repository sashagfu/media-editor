<template>
  <div
    id="MediaEditor media-editor"
    class="MediaEditor media-editor"
  >
    <TopBar />
    <div class="media-editor__main">
      <LeftSidebar />
      <div class="media-editor__column">
        <Layout />
      </div>
      <RightSidebar />
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import TopBar from 'Pages/TopBar/TopBar';
import LeftSidebar from 'Pages/LeftSidebar/LeftSidebar';
import RightSidebar from 'Pages/RightSidebar/RightSidebar';
import Layout from './components/Layout';

export default {
  name: 'MediaEditor',
  components: {
    Layout,
    TopBar,
    LeftSidebar,
    RightSidebar,
  },
  created() {
    this.undoRedoInit({ store: this.$store });
    this.fetchProject();
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
@import '../../sass/front/components/bulma-theme';

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

