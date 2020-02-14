<template>
  <div class="MediaEditorProjectsItem project">
    <div class="project-box">
      <div class="project__header">
        <div
          :class="{'project__star--is-fav':isFav}"
          class="project__star"
          @click="isFav = !isFav"
        >
          <font-awesome-icon
            :icon="['far', 'star']"
            :class="{'fa-icon--fav': isFav}"
            class="fa-icon"
          />
        </div>
        <div class="project__actions">
          <media-editor-projects-item-actions
            v-bind="$props"
          />
        </div>
      </div>
      <a
        :href="editRef"
        class="project__main"
      >
        <div class="project__pill">
          <font-awesome-icon
            :icon="['fas', 'pencil-alt']"
            class="fa-icon fa-icon--invert fa-icon--pointer"
          />
        </div>
      </a>
      <div class="project__footer">
        <div class="project__left">
          <div class="project__title-box">
            <div class="project__title">
              {{ title }}
            </div>
            <div class="project__sub-title">
              {{ updatedAt }}
            </div>
          </div>
        </div>
        <div class="project__right"/>
      </div>
    </div>
  </div>
</template>
<script>
/* @flow */
import MediaEditorProjectsItemActions from './MediaEditorProjectsItemActions';

export default {
  name: 'MediaEditorProjectsItem',
  components: {
    MediaEditorProjectsItemActions,
  },
  props: {
    id: {
      type: [Number, String],
      required: true,
      validator: val => val > 0,
    },
    title: {
      type: String,
      default: '',
    },
    updatedAt: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      isFav: false,
      openSet: false,
      action: '',
    };
  },
  computed: {
    editRef() {
      return `/media-editor#edit/${this.id}`;
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
    fnt($text-light, 1rem, $weight-normal, left);
    transition : all .2s;
    &--invert {
      color : $text-invert;
    }
    &--pointer {
      cursor : pointer;
    }
    &--fav {
      color : $primary;
    }
  }

  .project {
    border-radius : 3px;
    border        : 1px solid $border;
    padding-top   : 90%;
    position      : relative;

    &__header {
      fl-left();
      background-color : $white;
      border-bottom    : 1px solid $border;
      border-radius    : 3px 3px 0 0;
      flex             : 0 0 auto;
      padding          : 12px 24px;
    }
    &__main {
      fl-center();
      background      : url('http://lorempixel.com/400/200') center center/cover no-repeat $grey;
      cursor          : pointer;
      flex            : 1 1 auto;
    }
    &__footer {
      fl-between()
      background-color : $white;
      border-radius    : 0 0 3px 3px;
      border-top       : 1px solid $border;
      flex             : 0 0 auto;
      padding          : 12px 22px;
    }
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__title-box {
      display        : flex;
      flex-direction : column;
    }
    &__star {
      fl-center();
      cursor       : pointer;
      height       : 26px;
      margin-right : 8px;
      transition   : all .3s;
      width        : 26px;
    }
    &__actions {
      height : 26px;
      width  : 26px;
    }
    &__share {
      fl-center();

      cursor       : pointer;
      height       : 26px;
      margin-right : 8px;
      transition   : all .3s;
      width        : 26px;
    }
    &__pill {
      fl-center();
      background      : center center no-repeat $grey-dark;
      border-radius   : 50%;
      border          : 2px solid $white;
      cursor          : pointer;
      height          : 56px;
      opacity         : 0;
      transition      : all .3s;
      width           : 56px;
    }
    &__title {
      fnt($text, 12px, $weight-semibold, left);
    }
    &__sub-title {
      fnt($text-light, 10px, $weight-normal, left);
    }
    &__main:hover &__pill {
      opacity : 1;
    }
  }

  .project-box {
    border-radius   : 3px;
    bottom          : 0;
    display         : flex;
    flex-direction  : column;
    justify-content : space-between;
    left            : 0;
    position        : absolute;
    right           : 0;
    top             : 0;
  }
</style>
