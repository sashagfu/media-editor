<template>
  <div class="TextSavingPanel ts-panel">
    <div class="ts-panel__left">
      <input
        :value="itemsText.name"
        class="ts-panel__inp"
        type="text"
        @keyup="setText"
      >
    </div>
    <div class="ts-panel__right">
      <div
        class="ts-panel__btn ts-panel__btn--cancel"
        @click="setEditingText({})"
      >
        Cancel
      </div>
      <div
        class="ts-panel__btn ts-panel__btn--save"
        @click="handleClickSave"
      >
        Save
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import UPDATE_SLIDE from 'Gql/slides/mutations/updateSlide.graphql';

export default {
  name: 'TextSavingPanel',
  computed: {
    ...mapGetters('text', [
      'itemsText',
    ]),
    ...mapGetters('project', [
      'id',
    ]),
  },
  methods: {
    ...mapActions('text', [
      'setEditingText',
      'setActiveText',
      'setTextProperty',
    ]),
    ...mapActions('gallery', [
      'setItemText',
    ]),
    setText(e) {
      this.setTextProperty({
        property: 'name',
        value: e.srcElement.value,
      });
    },
    async handleClickSave() {
      const slide = {
        id: this.itemsText.id,
        projectId: this.id,
        name: this.itemsText.name,
        fileType: this.itemsText.fileType,
        effects: this.itemsText.effects ? this.itemsText.effects : [],
        texts: this.itemsText.items,
      };
      try {
        await this.$apollo.mutate({
          mutation: UPDATE_SLIDE,
          variables: {
            slide,
          },
        });
        this.setItemText(this.itemsText);
        this.setEditingText({});
      } catch (e) {
        console.error(e);
        this.$notify.error({
          title: 'Error',
          message: this.trans('media_editor.texts_hasnt_saved'),
        });
      }
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../../../sass/front/components/bulma-theme';

  .ts-panel {
    fl-between();
    background-color: $white;
    border-radius: 0 0 3px 3px;
    bottom: -26px;
    height: 26px;
    position: absolute;
    width: 100%;
    padding: 0 16px;
      &__left {
        fl-left();
      }
      &__right {
        fl-right();
      }
      &__group {
          display: flex;
          margin: 0 4px;
      }
      &__inp {
        fnt($text-light, 12px, $weight-light, left);
        background-color: $white;
        border-radius: 2px;
        border: 1px solid $border;
        height: 22px;
        outline: none;
      }
      &__btn {
        fnt($text-invert, 12px, $weight-normal, center);
        fl-center();
        border-radius: 2px;
        cursor: pointer;
        height: 22px;
        margin: 0 2px;
        padding: 0 16px;
        position: relative;
        transition: all .3s;
        &--save {
          background-color: $green;
          border: 1px solid $green;
          &:hover {
            background-color: transparent;
            color: $green;
          }
        }
        &--cancel {
          background-color: $grey-light;
          border: 1px solid $grey-light;
          &:hover {
            background-color: transparent;
            color: $grey-light;
          }
        }
        &:hover .fa-icon {
          color: $text-lighter;
        }
        &--disabled {
          cursor: auto;
          & .fa-icon {
            color: $grey-lighter;
            cursor: auto;
            &:hover {
              color: $grey-lighter;
            }
          }
          &:hover .fa-icon {
            color: $grey-lighter;
          }
        }
      }
  }
</style>
