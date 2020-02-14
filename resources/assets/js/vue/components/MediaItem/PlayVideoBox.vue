<template>
  <el-dialog
    :visible="isPlayVideoDialogOpen"
    :close-on-click-modal="false"
    :lock-scroll="true"
    :append-to-body="true"
    width="80%"
    top="10vh"
    class="PlayVideoBox plv"
    @update:visible="toggleDialog"
  >
    <div class="plv__main">
      <div class="plv__player">
        <video-player
          ref="videoPlayer"
          :options="playerOptions"
          :playsinline="true"
          class="vjs-custom-skin"
          @ready="playerReadied"
        />
      </div>
      <div class="plv__post">
        <div
          v-if="$apollo.queries.fetchProject.loading"
          class="plv__loading"
        >
          <font-awesome-icon
            :icon="['fas', 'spinner']"
            spin
            class="fa-icon"
          />
        </div>
        <div
          v-else
          class="plv__top">
          <HeadLine
            :project="fetchProject"
            :user="user"
            :show-button="false"
          />
          <Tags
            v-if="fetchProject.tags.length"
            :tags="fetchProject.tags"
          />
          <Credits
            v-if="asset.credits && asset.credits.length"
            :credits="asset.credits"
          />
          <PostMain
            :content="fetchProject.description"
          />
          <Divider/>
          <FooterWithMedallions
            v-if="user"
            :project="fetchProject"
            :user="user"
          />
          <Border/>
          <CommentsOfProject
            :project="fetchProject"
            :user="user"
          />
        </div>
        <div class="plv__bottom">
          <CommentAProject
            :project="fetchProject"
            :user="user"
          />
        </div>
      </div>
    </div>
  </el-dialog>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import { videoPlayer } from 'vue-video-player';
import 'video.js/dist/video-js.css';
import * as constants from 'Helpers/constants';

import HeadLine from 'Pages/ProjectsContent/HeadLine';
import PostMain from 'Pages/ProjectsContent/Main';
import Credits from 'Pages/ProjectsContent/Credits';
import FooterWithMedallions from 'Pages/ProjectsContent/FooterWithMedallions';
import Border from 'Pages/ProjectsContent/Border';
import Divider from 'Pages/ProjectsContent/Divider';
import CommentsOfProject from 'Pages/ProjectsContent/CommentsOfProject';
import CommentAProject from 'Pages/ProjectsContent/CommentAProject';
import Tags from 'Pages/ProjectsContent/Tags';


import FETCH_PROJECT from 'Gql/projects/queries/fetchProject.graphql';

export default {
  name: 'PlayVideoBox',
  components: {
    Border,
    CommentAProject,
    CommentsOfProject,
    Credits,
    Divider,
    FooterWithMedallions,
    HeadLine,
    PostMain,
    Tags,
    videoPlayer,
  },
  props: {
    project: {
      type: Object,
      required: true,
    },
    clip: {
      type: Object,
      default: () => null,
    },
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      constants,
    };
  },
  computed: {
    ...mapGetters('mediaEditorProjects', [
      'isPlayVideoDialogOpen',
    ]),
    ...mapGetters('feed', [
      'commentsVisibility',
    ]),
    asset() {
      if (this.clip) {
        return this.clip;
      }
      return this.project.assets.find(a => a.type === constants.FULL);
    },
    playerOptions() {
      return ({
        height: '600',
        autoplay: false,
        muted: true,
        language: 'en',
        playbackRates: [0.7, 1.0, 1.5, 2.0],
        sources: [{
          type: 'video/mp4',
          src: this.asset.filePath,
        }],
        poster: this.asset.thumbUrl,
      });
    },
    player() {
      return this.$refs.videoPlayer.player;
    },
  },
  apollo: {
    fetchProject: {
      query: FETCH_PROJECT,
      variables() {
        return {
          id: this.project.id,
        };
      },
    },
  },
  methods: {
    ...mapActions('mediaEditorProjects', [
      'openPlayVideoDialog',
      'closePlayVideoDialog',
    ]),
    // player
    playerReadied(player) {
      player.currentTime(0);
    },
    // window
    toggleDialog(dialogStatus) {
      if (dialogStatus) {
        this.openPlayVideoDialog();
      } else {
        this.closePlayVideoDialog();
      }
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .plv {
    &__loading {
      fl-center();
      height: 160px;
      width: 100%;
    }
    &__main {
      fl-left();
    }
    &__player {
      background-color: $black-bis;
      border-radius: $radius $radius 0 0;
      height: 600px;
      overflow: hidden;
      width: 66.66%;
    }
    &__post {
      display: flex;
      flex-direction: column;
      height: 600px;
      justify-content: space-between;
      overflow-y: auto;
      width: 33.33%;
    }
    &__top {
      width: 100%;
    }

    &__bottom {
      width: 100%;
    }
  }

</style>

<style lang="stylus">
  @import '../../../../sass/front/components/bulma-theme';

  .plv {
    & .el-dialog {
      &__headerbtn {
        height  : 18px;
        width   : 26px;
        z-index : 4;
      }
      &__header {
        padding : 0;
      }
      &__body {
        padding : 0;
      }
    }
    & .video-js .vjs-tech {
      width : calc(100% - 1px);
    }
  }

</style>
