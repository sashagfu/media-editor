<template>
  <div
    :class="{'is-active': isActive}"
    class="CreateProjectPopUp create-new-project modal"
  >
    <div class="modal-background"/>
    <div class="modal-content box">
      <head-line header="Create new project"/>
      <div class="create-new-project__main">

        <div class="create-new-project__row columns is-marginless">
          <div class="create-new-project__column column is-12">
            <div class="input-box">
              <div class="input-box__form">
                <label
                  class="input-box__label"
                  for="input-box__project-name"
                >
                  Project Name
                </label>
                <input
                  id="input-box__project-name"
                  v-model="form.title"
                  type="text"
                  class="input-box__input"
                >
              </div>
              <button class="input-box__button input-box__button--none"/>
            </div>
          </div>
        </div>
        <!--<div class="create-new-project__row columns is-marginless">-->
        <!--<div class="create-new-project__column column is-12">-->
        <!--<div class="input-box">-->
        <!--<div class="input-box__form">-->
        <!--<label class="input-box__label" for="input-box__project-image">-->
        <!--Project Image-->
        <!--</label>-->
        <!--<input-->
        <!--class="input-box__input"-->
        <!--type="text" id="input-box__project-image"-->
        <!--placeholder="Project Image (770x380px min)"-->
        <!--&gt;</div>-->
        <!--<button class="input-box__button input-box__button&#45;&#45;add-photo"/>-->
        <!--</div>-->
        <!--</div>-->
        <!--</div>-->

        <div class="create-new-project__row columns is-marginless">
          <div class="create-new-project__column column is-12">
            <div class="input-box">
              <div class="create-new-project__form">
                <label
                  class="input-box__label"
                  for="input-box__description"
                >
                  Project Description
                </label>
                <textarea
                  id="input-box__description"
                  v-model="form.description"
                  class="input-box__input input-box__input--textarea"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="create-new-project__row columns is-marginless">
          <div class="create-new-project__column column is-12">
            <button
              class="create-new-project__button
                                       create-new-project__button--create"
              @click="onCreateProjectClick"
            >
              Create Project
            </button>
          </div>
        </div>
      </div>
      <button
        class="modal-close"
        @click="closePopUp"
      />
      <div
        v-loading="true"
        v-if="projectsCreateLoading"
        class="create-new-project__loading"
      />
    </div>
  </div>
</template>

<script>
/* @flow */
import { mapActions, mapGetters } from 'vuex';
import HeadLine from '../HeadLine/HeadLine';

export default {
  components: {
    HeadLine,
  },
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      form: {
        title: 'My Video',
      },
    };
  },
  computed: {
    ...mapGetters('mediaEditorProjects', [
      'projectsCreateLoading',
    ]),
  },
  methods: {
    ...mapActions('mediaEditorProjects', [
      'createProject',
    ]),
    closePopUp() {
      this.$emit('close-popup');
    },
    resetModel() {
      this.form = {
        title: 'My Video',
      };
    },
    onCreateProjectClick() {
      this.createProject(this.form)
        .then((response) => {
          this.closePopUp();
          this.resetModel();
          return response;
        });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .create-new-project
    &__main
      padding 13px
    &__row
      align-items flex-start
      display flex
      padding 10px 0
    &__column
      display flex
      flex-direction column
      padding 0 10px
    &__button
      fnt($text-invert, 12px, $weight-semibold, center)
      border-radius 3px
      cursor pointer
      height 36px
      outline none
      transition all .3s
      &--create
        background-color $grey-dark
        border 1px solid $grey-dark
        &:hover
          background-color transparent
          color $grey-dark
    &__label
      fnt($text-light, 13px, $weight-normal, left)
    &__loading
      cover-all()

  .input-box
    fl-between()
    background-color $background-light
    border: 1px solid $border
    border-radius 3px
    padding-left 18px
    &--is-success
      border-color $success
    &--is-danger
      border-color $danger
    &--column-first
      margin-bottom 10px
    &--column
      margin 10px 0
    &--column-last
      margin-top 10px
    &__form
      display flex
      flex-direction column;
      width 100%
    &__label
      fnt($text-light, 10px, $weight-normal, left)
      align-items flex-start
      display flex
      flex-direction column
      height 20px
      justify-content flex-end
    &__input
      fnt($text-light, 12px, $weight-normal, left)
      background-color $background-light
      border none
      height 32px
      outline none
      width 100%
      &--info
        fnt($info, 12px, $weight-normal, left)
      &--turquoise
        fnt($turquoise, 12px, $weight-normal, left)
      &--textarea
        height 72px
        padding 8px 0
        resize none

    &__button
      border-radius 0 3px 3px 0
      border none
      cursor pointer
      flex 0 0 auto
      height 52px
      outline none
      transition all .3s
      width 48px
      &--none
        cursor auto
        opacity 0
        width 18px
      &--angle
        fl-center()
        background-color $white
      &--account
        fl-center()
        background-color $white
        border-radius 3px 0 0 3px
        border-right 1px solid $border
        margin 0 12px 0 -18px
        width 54px
      &--add-photo
        background url('../../../../img/computer-icon--light.png') center center no-repeat $white
        &:hover
          background-image url('../../../../img/computer-icon--dark.png')
          background-color $background-hover

  .modal-content
    overflow: visible
    width 470px

  .modal-close
    position absolute
    right -48px
    top -48px

</style>
