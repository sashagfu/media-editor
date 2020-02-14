<template>
  <div class="ActionBox actions">
    <el-dropdown
      :placement="placement"
      trigger="hover"
      size="small"
      @command="handleCommand"
    >
      <div class="actions__dropdown-button">
        <font-awesome-icon
          v-if="loading"
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon fa-icon--invert"
        />
        <font-awesome-icon
          v-else
          :icon="['fas', 'ellipsis-h']"
          class="fa-icon fa-icon--invert"
        />
      </div>
      <el-dropdown-menu
        slot="dropdown"
        class="actions__menu"
      >
        <!-- DELETE MESSAGE -->
        <el-dropdown-item
          v-if="showDelete"
          class="actions__item"
          command="delete"
        >
          {{ trans('chat.delete_message') }}
        </el-dropdown-item>

        <!-- EDIT MESSAGE -->
        <el-dropdown-item
          v-if="showEdit"
          class="actions__item"
          command="edit"
        >
          {{ trans('chat.edit_message') }}
        </el-dropdown-item>

        <!-- DELETE PARTICIPANT -->
        <el-dropdown-item
          v-if="showDeleteParticipant"
          class="actions__item"
          command="delete-participant"
        >
          {{ trans('chat.delete_participant') }}
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </div>
</template>

<script>

export default {
  name: 'ActionBox',
  props: {
    message: {
      type: Object,
      default: () => ({}),
    },
    actions: {
      type: Array,
      default: () => [],
    },
    placement: {
      type: String,
      default: 'bottom',
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {

    };
  },
  computed: {
    showDelete() {
      return this.actions.find(i => i === 'delete-message');
    },
    showEdit() {
      return this.actions.find(i => i === 'edit-message');
    },
    showDeleteParticipant() {
      return this.actions.find(i => i === 'delete-participant');
    },
  },
  methods: {
    handleCommand(command) {
      switch (command) {
        case 'delete': {
          this.$bus.$emit('delete-message', this.message);
          break;
        }
        case 'edit': {
          this.$bus.$emit('edit-message', this.message);
          break;
        }
        default: {
          this.$bus.$emit('edit-message', this.message);
        }
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

  .fa-icon {
    fnt($white-shadow, 1rem, $weight-normal, left)
    transition: all .3s
    cursor: pointer
    &:hover {
      color: $text-lighter
    }
    &--spinner {
      color $text-invert
      font-size 16px
      margin: -2px
      &:hover {
        color: $text-invert
      }
    }
  }

  .actions {
    fl-center()
    &__dropdown-button {
      fl-center()
      size(12, 26)
      cursor pointer
    }
    &__item {
      fnt($text-light, 11px, $weight-normal, left)
      line-height 26px
      padding 0 12px
      text-transform capitalize
      transition color .3s
      &:hover {
        color $text
        background-color $grey-lighter
      }
    }
  }

</style>
