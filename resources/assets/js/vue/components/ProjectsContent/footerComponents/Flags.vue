<template>
  <div
    @click="openFlagDialog"
  >
    <div class="report-project__icon">
      <font-awesome-icon
        :icon="['far', 'flag']"
        :class="{'fa-icon--flagged': flagged}"
        class="fa-icon fa-icon--pointer"
      />
    </div>
    <FlagProjectBox
      v-if="flagDialogIsOpen"
      :project="project"
      @close-dialog="closeFlagDialog"
    />
  </div>
</template>

<script>
import FlagProjectBox from './FlagProjectBox';

export default {
  name: 'Flags',
  components: {
    FlagProjectBox,
  },
  props: {
    project: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      flagged: false,
      positions: '',
      flagDialogIsOpen: false,
    };
  },
  methods: {
    openFlagDialog() {
      if (this.flagged) return;
      this.flagDialogIsOpen = true;
    },
    closeFlagDialog(flagged) {
      if (flagged) {
        this.flagged = true;
      }
      this.flagDialogIsOpen = false;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color $text-light
    transition all .3s
    font-size 1rem

    &:hover {
      color $text-lighter
    }

    &--invert {
      color $white

      &:hover {
        color $white-bis
      }
    }

    &--pointer {
      cursor pointer
    }

    &--flagged {
      color $red

      &:hover {
        color $red-hover
      }
    }

    &--sm {
      font-size 12px
    }

    &__box {
      padding 0 4px 0
    }
  }

  .report-project {
    position relative
    height 100%

    &__icon {
      padding-left 4px
      cursor pointer
      display flex
      align-items center
      height 100%
    }
  }

</style>
