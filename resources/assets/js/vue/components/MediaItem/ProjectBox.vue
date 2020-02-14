<template>
  <div class="ProjectBox box pr-box">
    <div class="pr-box__header">
      <SaveClip
        v-if="project.assets.length"
        :project="project"
      />
      <!-- <div
        :class="{'pr-box__star--is-fav':isFav}"
        class="pr-box__star"
        @click="isFav = !isFav"
      >
        <font-awesome-icon
          :icon="['far', 'star']"
          :class="{'fa-icon--success': isFav}"
          class="fa-icon fa-icon--invert"
        />
      </div> -->
      <div
        v-if="activeUser.id === project.authorId"
        class="pr-box__actions"
      >
        <ActionsBox
          :project="project"
          :user="user"
          @share-project="shareProject"
        />
      </div>
    </div>
    <div
      class="pr-box__main"
      @click="handleClick"
    >
      <div
        v-if="project.thumbPath && project.progress === 100"
        :style="{'background-image': `url(${project.thumbPath})`}"
        class="pr-box__cover"
      />
      <div
        v-else
        class="pr-box__cover-def"
      />
      <div
        v-if="!project.isProcessing"
        :style="{'background-image': `url(${
          'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kA'+
          'AAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABZJREFUeNpi2r9//'+
          '38gYGAEESAAEGAAasgJOgzOKCoAAAAASUVORK5CYII='
        })`}"
        class="pr-box__pixelization"
      />
      <vue-circle
        v-if="project.isProcessing || project.isFailed"
        ref="progressCircle"
        :progress="progress"
        :size="56"
        :fill="circleFill"
        :animation-start-value="0.0"
        :start-angle="0"
        :thickness="8"
        :show-percent="false"
        insert-mode="append"
        line-cap="round"
        empty-fill="rgba(255, 255, 255, .7)"
        class="pr-box__progress-bar"
      >
        <font-awesome-icon
          :icon="actionIcon"
          :class="{
            'fa-icon--error' : project.isFailed,
            'fa-icon--success': project.isProcessing
          }"
          :spin="project.isProcessing"
          class="fa-icon fa-icon--invert"
        />
      </vue-circle>
      <div
        v-else-if="!project.isDraft"
        class="pr-box__pill"
      >
        <font-awesome-icon
          :icon="['fas', 'play']"
          class="fa-icon fa-icon--invert"
        />
      </div>
      <div
        v-else
        class="pr-box__pill"
      >
        <font-awesome-icon
          :icon="['fas', 'pencil-alt']"
          class="fa-icon fa-icon--invert fa-icon--pointer"
        />
      </div>
    </div>
    <div class="pr-box__footer">
      <div class="pr-box__left">
        <div class="pr-box__title-box">
          <div class="pr-box__title">
            {{ project.title }}
          </div>
          <div class="pr-box__sub-title">
            {{ project.updatedAt }}
          </div>
        </div>
      </div>
      <div class="pr-box__right"/>
    </div>
    <div
      v-if="project.isDraft"
      class="ribbon ribbon-bookmark ribbon-reverse"
    >
      <span class="ribbon-inner">
        {{ trans('projects.draft') }}
      </span>
    </div>
    <div
      v-if="project.isRendered"
      class="pr-box__rendered-message"
    >
      Project is rendered. Click
      <span @click="quickPublish"> here </span>to publish.
    </div>
    <!-- Sharing project -->
    <ChatShareBox
      v-if="shareProjectDialogVisible"
      :project="project"
    />
    <!-- it's simple quick video player in popup window -->
    <!-- <PlayVideoBox
      v-if="isPlayVideoDialogOpen && playVideoDialogIdOpen === project.id"
      :project="project"
      :user="user"
    /> -->
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import VueCircle from 'vue2-circle-progress';

import RENDER_PUBLISH_PROJECT from 'Gql/projects/mutations/renderPublishProject.graphql';
import PUBLISH_PROJECT from 'Gql/projects/mutations/publishProject.graphql';
import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';

import * as constants from 'Helpers/constants';

import ActionsBox from './ActionsBox';
import SaveClip from './SaveClip';
import PlayVideoBox from './PlayVideoBox';
import ChatShareBox from '../../../vue/components/ChatWidget/ChatShareBox';

export default {
  name: 'ProjectBox',
  components: {
    ActionsBox,
    PlayVideoBox,
    SaveClip,
    VueCircle,
    ChatShareBox,
  },
  props: {
    project: {
      type: Object,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isFav: false,
      openSet: false,
      action: '',
      openedVideo: '',
      shareProjectDialogVisible: false,
    };
  },
  computed: {
    ...mapGetters('mediaEditorProjects', [
      'isPlayVideoDialogOpen',
      'playVideoDialogIdOpen',
    ]),
    ...mapGetters('general', ['activeUser']),
    circleFill() {
      return (this.project.isFailed)
        ? { gradient: ['#ff0000', '#c40b0b'] }
        : { gradient: ['#bbff42', '#51b847'] };
    },
    progress() {
      return (this.project.isProcessing || this.project.isFailed) ? this.project.progress : null;
    },
    status() {
      return this.project.status;
    },
    actionIcon() {
      if (this.project.isFailed) {
        return ['fas', 'redo'];
      }
      return ['fas', 'spinner'];
    },
  },
  watch: {
    progress(progress) {
      if ((this.project.isProcessing || this.project.isFailed) && this.$refs.progressCircle) {
        this.$refs.progressCircle.updateProgress(progress);
      }
    },
    status() {
      if ((this.project.isProcessing || this.project.isFailed) && this.$refs.progressCircle) {
        this.$refs.progressCircle.updateFill(this.circleFill);
        this.$refs.progressCircle.updateProgress(this.project.progress);
      }
    },
  },
  destroyed() {
    clearInterval(this.fkTimer);
  },
  methods: {
    ...mapActions('mediaEditorProjects', [
      'openPlayVideoDialog',
      'openPlayVideoDialogId',
    ]),
    openPlayVideo() {
      this.openPlayVideoDialogId(this.project.id);
      this.openPlayVideoDialog();
    },
    shareProject() {
      this.shareProjectDialogVisible = true;
    },
    async publishProject(mutation) {
      await this.$apollo.mutate({
        mutation,
        variables: {
          id: this.project.id,
        },
        update: (store) => {
          const fetchProjects = {
            query: FETCH_PROJECTS,
            variables: {
              userId: this.user.id,
            },
          };
          const data = store.readQuery(fetchProjects);
          const idx = data.fetchProjects.findIndex(pr => pr.id === this.project.id);
          if (mutation === RENDER_PUBLISH_PROJECT) {
            data.fetchProjects[idx].status = constants.PROJECT_STATUS_PROCESSING;
            data.fetchProjects[idx].isFailed = false;
            data.fetchProjects[idx].isProcessing = true;
            data.fetchProjects[idx].progress = 10;
          } else {
            data.fetchProjects[idx].status = constants.PROJECT_STATUS_SUCCESS;
            data.fetchProjects[idx].isFailed = false;
            data.fetchProjects[idx].isPublished = true;
          }
          store.writeQuery({ ...fetchProjects, data });
        },
      });
    },
    async handleClick() {
      if (this.project.isDraft) {
        this.$router.push({
          name: 'EditorPage',
          params: {
            id: this.project.id,
          },
        });
      } else if (this.project.isFailed) {
        await this.publishProject(RENDER_PUBLISH_PROJECT);
      } else {
        this.$router.push({
          name: 'SingleProjectPage',
          params: {
            projectUuid: this.project.uuid,
          },
        });
      }
    },
    async quickPublish() {
      await this.publishProject(PUBLISH_PROJECT);
      this.$notify({
        title: 'Success',
        message: this.trans('projects.created'),
        type: 'success',
      });
    },
  },
};
</script>

<style lang="stylus">
  .circle-percent-text-body
    padding-left 2px
</style>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon
    fnt($text-light, 1rem, $weight-normal, left)
    transition all .2s;
    &--invert
      color $text-invert
    &--disable
      opacity .7
    &--pointer
      cursor pointer
    &--success
      color $primary
    &--error
      color $danger

  .pr-box
    border-radius $radius
    border: 1px solid $border
    display flex
    flex-direction column
    height 272px
    justify-content space-between
    overflow hidden
    position relative

    &__header
      fl-right()
      border-radius $radius $radius 0 0
      flex 0 0 auto
      opacity 0
      padding 12px 24px
      position absolute
      transition all .3s
      width 100%
      z-index 3
    &:hover &__header
      opacity 1
    &__main
      fl-center()
      cursor pointer
      border-radius $radius $radius 0 0
      height 188px
      position relative
    &__cover-def
      cover-all()
      background url('../../../../img/bg/lime-splash.png') -124px 5px/100% no-repeat $white
      z-index 1
    &__cover
      cover-all()
      background center center/cover no-repeat
      z-index 1
    &__pixelization
      cover-all()
      opacity 0
      transition all .3s
      z-index 2
    &:hover &__pixelization
      opacity 1
    &__footer
      fl-between()
      border-top 1px solid $border
      background-color $white
      border-radius 0 0 $radius $radius
      flex 1 1 auto
      padding 12px 22px
    &__left
      fl-left()
    &__right
      fl-right()
    &__title-box
      display flex
      flex-direction column
    &__star
      fl-center()
      size(26, 26)
      cursor pointer
      margin-right 8px
      transition all .3s
    &__actions
      size(26, 26)
    &__pill
      fl-center()
      circle(56)
      background-color $grey-dark
      border 2px solid $white
      cursor pointer
      opacity 0
      padding-left 2px
      transition all .3s
      z-index 3
    &:hover &__pill
      opacity 1
    &__progress-bar
      fl-center()
      height 56px
      z-index 3
    &__title
      fnt($text, 12px, $weight-semibold, left)
    &__sub-title
      fnt($text-light, 10px, $weight-normal, left)
    &__rendered-message
      fnt($text-lighter, 12px, $weight-normal, right)
      position absolute
      bottom 5px
      right 5px

      span
        fnt($primary, 12px, $weight-normal, left)
        cursor pointer


  .ribbon
    size(36, 104)
    position absolute
    top 152px
    left -3px
    text-align center
    background-color transparent
    z-index 3

  .ribbon-inner
    background-color $warning
    color $text
    display inline-block
    height 30px
    left 0
    line-height 30px
    padding-left 20px
    padding-right 20px
    position absolute
    top 0
    white-space nowrap

  .ribbon-reverse
    left auto
    right -3px

  .ribbon-reverse .ribbon-inner
    left auto
    right 0

  .ribbon-primary.ribbon-bookmark.ribbon-reverse .ribbon-inner:before
    border-left-color transparent

  .ribbon-primary.ribbon-bookmark .ribbon-inner:before
    border-right-color transparent

  .ribbon-bookmark.ribbon-reverse .ribbon-inner:before
    border-left 10px solid transparent
    border-right 15px solid #ffd52e // $warning
    left auto
    right 100%

  .ribbon-bookmark .ribbon-inner:before
    border 15px solid #ffd52e //$warning
    border-right 10px solid transparent
    content ''
    display block
    height 0
    left 100%
    position absolute
    top 0
    width 0

</style>
