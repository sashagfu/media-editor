<template>
  <div class="TimeLine3 timeline columns is-gpless is-marginless">
    <div class="timeline__column timeline__column--left column is-3">
      <div class="timeline__box">
        <div class="box">
          <circle-info :circle="circle"/>
        </div>
      </div>
      <div
        v-if="canSeeMembers"
        class="timeline__box"
      >
        <div class="box">
          <timeline-members :members="members"/>
        </div>
      </div>
    </div>
    <div class="timeline__column timeline__column--main column is-6">
      <feed
        v-if="feedItems"
        :items="feedItems"
        :user="user"
        :form-type="formType"
        :show-form="showForm"
        :circle="circle"
      />
    </div>
  </div>
</template>

<script>
/* @flow */
import TimelineMembers from './TimelineMembers';
import CircleInfo from './CircleInfo';
import Feed from '../../Feed/Feed';

export default {
  components: {
    Feed,
    TimelineMembers,
    CircleInfo,
  },
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
    feedItems: {
      type: Object,
      default: () => ({}),
    },
    members: {
      type: Array,
      default: () => [],
    },
    canSeeMembers: {
      type: Boolean,
      default: true,
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    showForm: {
      type: Boolean,
      default: false,
    },
    formType: {
      type: String,
      default: '',
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .timeline {
    &__column {
      display        : flex;
      flex-direction : column;
      //align-items: flex-start;
      &--left {
        padding : 26px 13px 26px 32px;
      }
      &--main {
        padding : 13px;
      }
      &--right {
        padding : 26px 32px 26px 13px;
      }
    }
    &__box {
      padding : 13px 0;
      &:first-child {
        padding-top : 0;
      }
      &:last-child {
        padding-bottom : 0;
      }
    }
  }

  .column {
    justify-content : flex-start;
  }
</style>
