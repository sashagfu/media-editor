<template>
  <div class="spp__main">
    <div class="spp__column spp__column--main">
      <SingleProjectPageMessage
        v-if="fetchProject && fetchProject.isRendered && fetchProject.authorId !== user.id"
        :message="trans('projects.shared_project_preview_message')"
      />
      <SingleProjectPageMessage
        v-if="deprecated"
        :message="trans('projects.version_deprecated')"
      />
      <template v-if="$apollo.queries.fetchProject.loading">
        <div class="spp__loading">
          <font-awesome-icon
            :icon="['fas', 'spinner']"
            spin
            class="fa-icon"
          />
        </div>
      </template>
      <template v-else>
        <SingleProjectPageVideoPlayer
          :project="fetchProject"
        />
        <SingleProjectPageTitle
          :project="fetchProject"
          :user="activeUser"
        />
        <SingleProjectPageUser
          :project="fetchProject"
          :user="activeUser"
        />
        <SingleProjectPageCredits
          v-if="fetchProject.credits.length"
          :project="fetchProject"
        />
        <SingleProjectPageComments
          :project="fetchProject"
          :user="activeUser"
        />
      </template>
    </div>
    <div class="spp__column spp__column--right">
      <SingleProjectPageRecommended />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import FETCH_PROJECT from 'Gql/projects/queries/fetchProject.graphql';

import SingleProjectPageRecommended from './SingleProjectPageRecommended';
import SingleProjectPageVideoPlayer from './SingleProjectPageVideoPlayer';
import SingleProjectPageTitle from './SingleProjectPageTitle';
import SingleProjectPageUser from './SingleProjectPageUser';
import SingleProjectPageComments from './SingleProjectPageComments';
import SingleProjectPageCredits from './SingleProjectPageCredits';
import SingleProjectPageMessage from './SingleProjectPageMessage';

export default {
  name: 'SingleProjectPage',
  components: {
    SingleProjectPageRecommended,
    SingleProjectPageVideoPlayer,
    SingleProjectPageTitle,
    SingleProjectPageUser,
    SingleProjectPageComments,
    SingleProjectPageCredits,
    SingleProjectPageMessage,
  },
  props: {
    deprecated: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  apollo: {
    fetchProject: {
      query: FETCH_PROJECT,
      variables() {
        return {
          uuid: this.$route.params.projectUuid,
        };
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

  .fa-icon
    fnt($grey-light, 28px, $weight-semibold, center)

  .spp
    display flex
    flex-direction column
    min-height 100vh
    position relative
    &__body
      background-color $white-bis
      display flex
      flex-grow 1
      padding-top 73px
      position  relative
    &__main
      display flex
      margin-left 72px
      padding 32px 32px 0 28px
      margin-bottom 1.5rem
      width 100%
    &__column
      display flex
      flex-direction column
      min-height 100px
      width 25%
      padding 0 13px
      &:first-child
        padding-left 0
      &:last-child
        padding-right 0
      &--main
        width 75%
        border none
    &__loading
      fl-center()
      height 120px

</style>
