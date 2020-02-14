<template>
  <div class="MediaEditorProjectsBox media-editor-main">
    <div class="top-header">
      <div class="top-header__box">
        <div class="top-header__title">
          Media Editor
        </div>
        <div class="top-header__sub-title">
          where you can work with your media files
        </div>
      </div>
    </div>
    <div
      v-loading="projectsLoading"
      class="media-editor-main__main"
    >
      <!--<div class="media-editor-main__row columns is-marginless">-->
      <!--<div class="media-editor-main__column column is-12">-->
      <!--<div class="media-editor-main__top-tale box is-marginless">-->
      <!--<div class="media-editor-main__left">-->
      <!--<div class="media-editor-main__title">-->
      <!--Your last project-->
      <!--</div>-->
      <!--</div>-->
      <!--<div class="media-editor-main__right">-->
      <!--<div class="media-editor-main__set-box"></div>-->
      <!--</div>-->
      <!--</div>-->
      <!--</div>-->
      <!--</div>-->
      <div class="media-editor-main__row columns is-marginless">
        <div class="media-editor-main__column column is-3">
          <div class="create-project">
            <div
              class="create-project__pill"
              @click="isActive = true"
            />
            <div class="create-project__title">
              Create new project
            </div>
            <div class="create-project__sub-title">
              It only takes a few minutes!
            </div>
          </div>
        </div>
        <div class="media-editor-main__column column is-9">
          <last-project/>
        </div>
      </div>
      <div class="media-editor-main__row columns is-marginless">
        <div class="media-editor-main__column column is-12">
          <div class="media-editor-main__top-tale box is-marginless">
            <div class="media-editor-main__left">
              <div class="media-editor-main__title">
                Your other project
              </div>
            </div>
            <div class="media-editor-main__right">
              <form class="media-projects-order">
                <label class="media-projects-order__label">
                  Order By:
                </label>
                <select class="media-projects-order__select">
                  <option value="date">
                    Date (Descending)
                  </option>
                  <option value="date">
                    Date (Descending)
                  </option>
                  <option value="date">
                    Date (Descending)
                  </option>
                </select>
              </form>
              <form class="media-projects-search">
                <input
                  class="media-projects-search__input"
                  type="search"
                  placeholder="Search Media Project..."
                >
                <button class="media-projects-search__button"/>
              </form>
              <div class="media-projects-search__set-box"/>
            </div>
          </div>
        </div>
      </div>
      <div class="media-editor-main__row media-editor-main__row--wrap columns is-marginless">
        <div
          v-for="(project, key) in projects"
          :key="key"
          class="media-editor-main__column media-editor-main__column--wrap column is-3"
        >
          <media-editor-projects-item
            v-bind="project"
          />
        </div>

      </div>
      <!--<div class="media-editor-main__row-->
      <!--media-editor-main__row&#45;&#45;wrap columns is-marginless">-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project></project>-->
      <!--</div>-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project></project>-->
      <!--</div>-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project></project>-->
      <!--</div>-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project></project>-->
      <!--</div>-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project></project>-->
      <!--</div>-->
      <!--</div>-->
      <!--<div class="media-editor-main__row columns is-marginless">-->
      <!--<div class="media-editor-main__column column is-12">-->
      <!--<div class="media-editor-main__top-tale box is-marginless">-->
      <!--<div class="media-editor-main__left">-->
      <!--<div class="media-editor-main__title">-->
      <!--Your last project-->
      <!--</div>-->
      <!--</div>-->
      <!--<div class="media-editor-main__right">-->
      <!--<div class="media-editor-main__button"-->
      <!--@click="isActive=true"-->
      <!--&gt;Create</div>-->
      <!--<div class="media-editor-main__set-box"></div>-->
      <!--</div>-->
      <!--</div>-->
      <!--</div>-->
      <!--</div>-->
      <!--<div class="media-editor-main__row-->
      <!--media-editor-main__row&#45;&#45;wrap columns is-marginless">-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project_v2></project_v2>-->
      <!--</div>-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project_v2></project_v2>-->
      <!--</div>-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project_v2></project_v2>-->
      <!--</div>-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project_v2></project_v2>-->
      <!--</div>-->
      <!--<div class="media-editor-main__column-->
      <!--media-editor-main__column&#45;&#45;wrap column is-3">-->
      <!--<project_v2></project_v2>-->
      <!--</div>-->
      <!--</div>-->
    </div>
    <create-project-pop-up
      :is-active="isActive"
      @close-popup="closePopup"
    />
  </div>
</template>

<script>
/* @flow */
import { mapGetters, mapActions } from 'vuex';
import LastProject from './LastProject';
import Project from './Project';
import MediaEditorProjectsItem from './MediaEditorProjectsItem';
import CreateProjectPopUp from './CreateProjectPopUp';

export default {
  name: 'MediaEditorProjectsBox',
  components: {
    LastProject,
    Project,
    MediaEditorProjectsItem,
    CreateProjectPopUp,
  },
  data() {
    return {
      isActive: false,
    };
  },
  computed: {
    ...mapGetters('mediaEditorProjects', [
      'projects',
      'projectsLoading',
    ]),
  },
  created() {
    this.fetchProjects();
  },
  methods: {
    ...mapActions('mediaEditorProjects', [
      'fetchProjects',
    ]),
    closePopup() {
      this.isActive = false;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .top-header {
    // padding: 40px 32px 0 32px;
    width : 100%;
    &__box {
      align-items      : center;
      background-color : $grey-dark;
      border           : 1px solid $border;
      border-radius    : 3px;
      display          : flex;
      flex-direction   : column;
      height           : 400px;
      justify-content  : center;
    }
    &__title {
      fnt($text-invert, 80px, $weight-light, center);
      text-transform : uppercase;
    }
    &__sub-title {
      fnt($text-invert, 40px, $weight-light, center);
    }
  }

  .media-editor-main {
    width : 100%;
    &__main {
      padding : 13px 0;
    }
    &__row {
      display : flex;

      padding : 13px 0;
      &--wrap {
        flex-wrap : wrap;
        padding   : 0;
      }
    }
    &__column {
      padding : 0 13px;
      &--wrap {
        padding : 13px;
      }
      &:first-child {
        padding-left : 0;
      }
      &:last-child {
        padding-right : 0;
      }
    }
    &__top-tale {
      align-items     : center;
      display         : flex;
      height          : 72px;
      justify-content : space-between;
      padding         : 0 26px;
    }
    &__left {
      align-items : center;
      display     : flex;
    }
    &__right {
      align-items     : center;
      display         : flex;
      justify-content : flex-end;
    }
    &__title {
      fnt($text, 14px, $weight-semibold, left);
      cursor : pointer;
      &--no-active {
        color : $text-lighter
      }
    }
    &__button {
      fnt($text-invert, 12px, $weight-semibold, center);
      align-items      : center;
      background-color : $grey-dark;
      border           : 1px solid $grey-dark;
      border-radius    : 3px;
      cursor           : pointer;
      display          : flex;
      height           : 40px;
      justify-content  : center;
      margin-right     : 26px;
      min-width        : 72px;
      padding          : 0 22px;
      transition       : all .3s;
      &:hover {
        background-color : transparent;
        color            : $grey-dark;
      }
    }
    &__set-box {
      background : url('../../../../img/three-dots-icon--light.png') center center no-repeat;
      cursor     : pointer;
      height     : 72px;
      width      : 26px;
    }
  }

  .create-project {
    align-items     : center;
    border          : 2px dashed $grey;
    border-radius   : 6px;
    display         : flex;
    flex-direction  : column;
    height          : 464px;
    justify-content : center;
    &__pill {
      background    : url('../../../../img/plus-icon--white.png')
      center center no-repeat $grey-dark;
      border-radius : 50%;
      box-shadow    : 0 2px 2px 0 rgba($black-bis, .16), 0 0 0 1px rgba($black-bis, .08);
      cursor        : pointer;
      height        : 56px;
      transition    : all .3s;
      width         : 56px;
      &:hover {
        box-shadow : 0 3px 8px 0 rgba($black-bis, .2), 0 0 0 1px rgba($black-bis, .08);
      }
    }
    &__title {
      fnt($text, 14px, $weight-semibold, center);
      margin-top : 26px;
    }
    &__sub-title {
      fnt($text-light, 11px, $weight-normal, center);
    }
  }

  .media-projects-search {
    align-items : center;
    display     : inline-flex;
    &__input {
      fnt($text, 12px, $weight-normal, center);
      border             : 1px solid $border;
      border-radius      : 3px 0 0 3px;
      height             : 40px;
      margin-right       : -1px;
      outline            : none;
      padding-left       : 16px;
      -webkit-appearance : none;
      width              : 304px;
    }
    &__input:focus {
      box-shadow : inset 0 1px 2px rgba(10, 10, 10, 0.1);
    }
    &__button, &__button:focus {
      background         : url('../../../../img/magnifying-glass-icon--small.png')
      center center no-repeat $border;
      border-color       : transparent;
      border-radius      : 0 3px 3px 0;
      height             : 40px;
      margin-right       : -1px;
      outline            : none;
      -webkit-appearance : none;
      width              : 32px;
    }
  }

  .media-projects-order {
    padding : 0 20px;

    &__label {
      fnt($text-lighter, 14px, $weight-normal, left);
      padding : 0 16px;
    }
    &__select {
      fnt($text-lighter, 12px, $weight-normal, left);
      border             : 1px solid $border;
      border-radius      : 3px;
      height             : 40px;
      outline            : none;
      padding-left       : 16px;
      -webkit-appearance : none;
      width              : 304px;
    }
  }

</style>
