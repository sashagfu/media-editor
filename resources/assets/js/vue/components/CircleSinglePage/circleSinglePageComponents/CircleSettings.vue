<template>
  <div class="CircleSettings personal-info">
    <head-line :header="`${trans('circles.single')} ${trans('circles.settings')}`"/>
    <form @submit.prevent="updateCircle(circle)">
      <div class="personal-info__main">
        <!-- first row -->
        <div class="personal-info__row columns is-marginless">
          <div class="personal-info__column column is-6">
            <div class="input-box">
              <div class="input-box__form">
                <label
                  class="input-box__label"
                  for="input-box__title"
                >
                  {{ trans('circles.title') }}
                </label>
                <input
                  id="input-box__title"
                  v-model="circle.title"
                  type="text"
                  class="input-box__input"
                >
                <template v-if="errors.title">
                  <p
                    v-for="(error, index) in errors.title"
                    :key="index"
                    class="help is-danger"
                  >
                    {{ error }}
                  </p>
                </template>
              </div>
            </div>
          </div>
          <div class="personal-info__column column is-6">
            <div class="input-box">
              <div class="input-box__form">
                <label
                  class="input-box__label"
                  for="input-box__description"
                >
                  {{ trans('circles.description') }}
                </label>
                <input
                  id="input-box__description"
                  v-model="circle.description"
                  type="text"
                  class="input-box__input"
                >
                <template v-if="errors.description">
                  <p
                    v-for="(error, index) in errors.description"
                    :key="index"
                    class="help is-danger"
                  >
                    {{ error }}
                  </p>
                </template>
              </div>
            </div>
          </div>
        </div>
        <!-- second row -->
        <div class="personal-info__row columns is-marginless">
          <div class="personal-info__column column is-6">
            <div class="input-box">
              <div class=" ">
                <label class="input-box__label">
                  {{ trans('circles.type') }}
                </label>
                <label class="input-box__radio radio">
                  <input
                    v-model="circle.type"
                    :checked="circle.type === 'open'"
                    type="radio"
                    name="type"
                    value="open"
                  >
                  {{ trans('circles.type_open') }}
                </label>
                <label class="input-box__radio radio">
                  <input
                    v-model="circle.type"
                    :checked="circle.type === 'closed'"
                    type="radio"
                    name="type"
                    value="closed"
                  >
                  {{ trans('circles.type_closed') }}
                </label>
                <label class="input-box__radio radio">
                  <input
                    v-model="circle.type"
                    :checked="circle.type === 'secret'"
                    type="radio"
                    name="type"
                    value="secret"
                  >
                  {{ trans('circles.type_secret') }}
                </label>
                <template v-if="errors.type">
                  <p
                    v-for="(error, index) in errors.type"
                    :key="index"
                    class="help is-danger"
                  >
                    {{ error }}
                  </p>
                </template>
              </div>
            </div>
          </div>
          <div class="personal-info__column column is-6">
            <div class="input-box">
              <div class="input-box__form">
                <label
                  class="input-box__label"
                  for="input-box__cover"
                >
                  {{ trans('circles.cover') }}
                </label>
                <label class="input-box__radio radio">
                  <input
                    id="input-box__cover"
                    ref="coverInput"
                    type="file"
                    name="cover"
                    accept="image/*"
                    @change="filesChange($event)"
                  >
                </label>

                <template v-if="errors.cover">
                  <p
                    v-for="(error, index) in errors.cover"
                    :key="index"
                    class="help is-danger"
                  >
                    {{ error }}
                  </p>
                </template>
              </div>
            </div>
          </div>
        </div>
        <!-- third row -->
        <div class="personal-info__row columns is-marginless">
          <div class="personal-info__column column is-6">
            <div class="input-box">
              <div class=" ">
                <label class="input-box__label">
                  {{ trans('circles.post-adding-privacy') }}
                </label>
                <label class="input-box__radio radio">
                  <input
                    v-model="circle.post_adding_privacy"
                    :checked="circle.post_adding_privacy === '1'"
                    type="radio"
                    name="post_adding_privacy"
                    value="1"
                  >
                  {{ trans('circles.post-adding-privacy-admins') }}
                </label>
                <label class="input-box__radio radio">
                  <input
                    v-model="circle.post_adding_privacy"
                    :checked="circle.post_adding_privacy === '2'"
                    type="radio"
                    name="post_adding_privacy"
                    value="2"
                  >
                  {{ trans('circles.post-adding-privacy-members') }}
                </label>
                <template v-if="errors.post_adding_privacy">
                  <p
                    v-for="(error, index) in errors.post_adding_privacy"
                    :key="index"
                    class="help is-danger"
                  >
                    {{ error }}
                  </p>
                </template>
              </div>
            </div>
          </div>
          <div class="personal-info__column column is-6">
            <div class="input-box">
              <div class="">
                <label class="input-box__label">
                  {{ trans('circles.members_block_privacy') }}
                </label>
                <label class="input-box__radio radio">
                  <input
                    v-model="circle.members_block_privacy"
                    :checked="circle.members_block_privacy === '1'"
                    type="radio"
                    name="members_block_privacy"
                    value="1"
                  >
                  {{ trans('circles.members_block_privacy-all') }}
                </label>
                <label class="input-box__radio radio">
                  <input
                    v-model="circle.members_block_privacy"
                    :checked="circle.members_block_privacy === '2'"
                    type="radio"
                    name="members_block_privacy"
                    value="2"
                  >
                  {{ trans('circles.post-adding-privacy-members') }}
                </label>
                <template v-if="errors.post_adding_privacy">
                  <p
                    v-for="(error, index) in errors.post_adding_privacy"
                    :key="index"
                    class="help is-danger"
                  >
                    {{ error }}
                  </p>
                </template>
              </div>
            </div>
          </div>
        </div>

        <!-- eights row -->
        <div
          v-if="circleUpdating"
          class="personal-info__row columns is-marginless"
        >
          <div class="personal-info__column column is-12">
            <i
              class="fa fa-spinner fa-spin fa-3x fa-fw
                                  has-text-centered personal-info__loader"
            />
          </div>
        </div>
        <div class="personal-info__row columns is-marginless">
          <div class="personal-info__column column is-6">
            <button class="personal-info__button personal-info__button--restore">
              Restore all Attributes
            </button>
          </div>
          <div class="personal-info__column column is-6">
            <button
              class="personal-info__button personal-info__button--save"
              type="submit"
            >
              Save all Changes
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script>
/* @flow */
import _ from 'lodash';
import HeadLine from '../../HeadLine/HeadLine';

export default {
  components: {
    HeadLine,
  },
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      errors: [],
      image: null,
      circleUpdating: false,
    };
  },
  methods: {
    filesChange(e) {
      this.image = {
        fieldName: _.get(e, 'target.name', ''),
        file: _.get(e, 'target.files[0]', {}),
      };
    },
    updateCircle(circle) {
      this.circleUpdating = !this.circleUpdating;
      const formData = new FormData();
      if (this.image) {
        formData.append(this.image.fieldName, this.image.file);
      }
      formData.append('circle_slug', circle.slug);
      formData.append('title', circle.title);
      formData.append('description', circle.description);
      formData.append('type', circle.type);
      formData.append('post_adding_privacy', circle.post_adding_privacy);
      formData.append('members_block_privacy', circle.members_block_privacy);
      this.$http.post('/api/circle/settings/update', formData)
        .then((response) => {
          this.circleUpdating = !this.circleUpdating;
          window.location.href = response.data;
        })
        .catch((error) => {
          this.circleUpdating = !this.circleUpdating;
          this.errors = error.response.data;
        });
    },
    setPlace(place) {
      this.latLng = {
        lat: place.geometry.location.lat(),
        lng: place.geometry.location.lng(),
      };
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .personal-info {
    &__main {
      padding : 13px;
    }
    &__row {
      padding     : 10px 0;
      display     : flex;
      align-items : flex-start;
    }
    &__column {
      padding        : 0 10px;
      display        : flex;
      flex-direction : column;
    }
    &__loader {
      width : 100%;
    }
    &__button {
      fnt($text-invert, 12px, $weight-semibold, center);
      border-radius : 3px;
      cursor        : pointer;
      height        : 36px;
      transition    : all .3s;

      &--restore {
        border           : 1px solid $grey;
        background-color : $grey;
        &:hover {

          background-color : transparent;
          color            : $grey;
        }
      }
      &--save {
        border           : 1px solid $primary;
        background-color : $primary;
        &:hover {

          background-color : transparent;
          color            : $primary;
        }
      }
    }

  }

  .input-box {
    align-items      : center;
    background-color : $background-light;
    border-radius    : 3px;
    border           : 1px solid $border;
    display          : flex;
    justify-content  : space-between;
    padding-left     : 18px;
    &--is-success {
      border-color : $success;
    }
    &--is-danger {
      border-color : $danger;
    }
    &--column-first {
      margin-bottom : 10px;
    }
    &--column {
      margin : 10px 0;
    }
    &--column-last {
      margin-top : 10px;
    }
    &__form {
      display        : flex;
      flex-direction : column;
      width          : 100%;
    }
    &__label {
      fnt($text-light, 10px, $weight-normal, left);
      align-items     : flex-start;
      display         : flex;
      flex-direction  : column;
      height          : 20px;
      justify-content : flex-end;
    }
    &__radio {
      height : 32px;
    }
    &__input {
      fnt($text-light, 12px, $weight-normal, left);
      background-color : $background-light;
      border           : none;
      height           : 32px;
      outline          : none;
      width            : 100%;
      &--info {
        fnt($info, 12px, $weight-normal, left);
      }
      &--textarea {
        resize  : none;
        padding : 8px 0;
        height  : 106px;

      }
    }
    &__button {
      border-radius : 0 3px 3px 0;
      border        : none;
      cursor        : pointer;
      flex          : 0 0 auto;
      height        : 52px;
      outline       : none;
      transition    : all .3s;
      width         : 48px;
      &--none {
        opacity : 0;
      }
      &--angle {
        align-items      : center;
        background-color : $white;
        display          : flex;
        justify-content  : center;
      }
      &--account {
        align-items      : center;
        background-color : $white;
        border-right     : 1px solid $border;
        display          : flex;
        justify-content  : center;
        margin           : 0 12px 0 -18px;
        width            : 54px;
      }
      &--date {
        background : url('../../../../../img/month-calendar-icon--light.png')
        center center no-repeat $white;
        &:hover {
          background : url('../../../../../img/month-calendar-icon--dark.png') $background-hover;
        }
      }
      &--add-ava {
        background : url('../../../../../img/computer-icon--light.png')
        center center no-repeat $white;
        &:hover {
          background : url('../../../../../img/computer-icon--dark.png') $background-hover;
        }
        &:active {
          background-image : url('../../../../../img/computer-icon--blue.png');
        }
      }
      &--add-friends {
        background : url('../../../../../img/happy-face-icon--light.png')
        center center no-repeat $white;
        &:hover {
          background : url('../../../../../img/happy-face-icon--dark.png') $background-hover;
        }
        &:active {
          background-image : url('../../../../../img/happy-face-icon--blue.png');
        }
      }

    }
  }

  .fa {
    fnt($grey, 20px, $weight-normal, center);
    &--angle {
      fnt($text, 13px, $weight-bold, center);
    }
    &--snapchat {
      color : $yellow;
    }
    &--google_plus {
      color : $red;
    }
    &--instagram {
      color : rgb(234, 74, 82);
    }
    &--linkedin {
      color : $facebook;
    }
    &--facebook {
      color : $facebook;
    }
    &--location {
      color : $red;
    }
  }
</style>
