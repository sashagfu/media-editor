<template>
  <div class="friends-main">
    <div class="columns tile is-ancestor is-marginless is-multiline">
      <div
        v-for="(member, key) in members"
        :key="key"
        class="column tile is-3 is-parent"
      >
        <div class="tile is-child box">
          <CircleMemberTile :member="member"/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import CircleMemberTile from './CircleMemberTile';

export default {
  components: {
    CircleMemberTile,
  },
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
    members: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../../sass/front/components/bulma-theme';

    .friends-main {
        width: 100%;
    }
</style>
