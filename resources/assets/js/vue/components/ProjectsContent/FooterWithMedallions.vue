<template>
  <div class="FooterWithMedallions project-footer is-marginless">
    <div class="project-footer__left">
      <div class="project-footer__item">
        <Likes v-bind="$props"/>
      </div>
      <div class="project-footer__item">
        <UserAvaMedallions
          v-if="project.isPerformance"
          :project="project"
          :actions="project.stars"
        />
        <UserAvaMedallions
          v-else
          :project="project"
          :actions="project.likes"
        />
      </div>
    </div>
    <div class="project-footer__right">
      <div class="project-footer__item">
        <ShowComments :project="project"/>
      </div>
      <div
        v-if="project.assets && project.assets.length"
        class="project-footer__item">
        <Clip :project="project"/>
      </div>
      <div class="project-footer__item">
        <PostShares :project="project"/>
      </div>
    </div>
  </div>
</template>

<script>
import Likes from 'Pages/ProjectsContent/footerComponents/Likes';
import PostShares from 'Pages/ProjectsContent/footerComponents/PostShares';
import Clip from 'Pages/ProjectsContent/footerComponents/Clip';
import ShowComments from 'Pages/ProjectsContent/footerComponents/ShowComments';
import UserAvaMedallions from 'Pages/ProjectsContent/footerComponents/UserAvaMedallions';

export default {
  name: 'FooterWithMedallions',
  components: {
    Likes,
    PostShares,
    Clip,
    ShowComments,
    UserAvaMedallions,
  },
  props: {
    project: {
      type: Object,
      default() {
        return {};
      },
    },
    user: {
      type: Object,
      default() {
        return {};
      },
    },
  },
};

</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .project-footer {
    fl-between()
    height 44px
    padding 0px 24px 2px
    width 100%
    +tablet() {
      flex-direction column
      align-items flex-start
    }

    &__left {
      fl-left()
      height 100%
      +tablet() {
        justify-content flex-start
      }
    }
    &__right {
      fl-right()
      height 100%
      +tablet() {
        justify-content flex-start
        align-items flex-start
      }
    }
    &__item {
      fl-left()
      height 100%
    }
    &__item + &__item {
      margin-left 4px
    }
  }

</style>
