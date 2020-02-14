<template>
  <el-dialog
    :visible.sync="isChangingThumb"
    :append-to-body="true"
    :title="trans('common.change_thumbnail')"
    width="55%"
    class="ChangeThumbnailBox ctb action-dialog"
    @close="$emit('close')"
  >
    <div
      v-if="thumbsCount"
      class="ctb__row"
    >
      <div
        v-for="(number, idx) in thumbsCount + fakeThumbs"
        :key="idx"
        :style="{
          'background-image': `url('${project.spritePath}')`,
          'background-position-y': `-${idx * 135}px`,
          'visibility': (idx > thumbsCount - 1) ? 'hidden' : 'unset',
        }"
        class="ctb__item"
      >
        <div @click="selectThumb(idx)"/>
        <template v-if="selectedThumb === idx">
          <div class="ctb__overlay">
            <el-button
              type="primary"
              size="mini"
              class="ctb__button"
              @click="changeThumbnail(idx)"
            >
              <font-awesome-icon
                v-if="loading"
                :icon="['fas', 'spinner']"
                spin
                class="fa-icon fa-icon--el fa-icon--invert"
              />
              Apply
            </el-button>
            <el-button
              size="mini"
              class="ctb__button"
              plain
              @click="selectThumb(null)"
            >
              Cancel
            </el-button>
          </div>
        </template>
      </div>
    </div>
  </el-dialog>
</template>

<script>
import moment from 'moment';

import CHANGE_THUMBNAIL from 'Gql/projects/mutations/changeThumbnail.graphql';

export default {
  name: 'ChangeThumbnailBox',
  props: {
    project: {
      type: Object,
      default: () => {},
    },
    user: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      loading: false,
      isChangingThumb: true,
      fakeThumbs: 0,
      thumbsCount: 0,
      selectedThumb: null,
    };
  },
  created() {
    this.calcThumbsCount();
  },
  methods: {
    selectThumb(idx) {
      if (this.loading) return;
      this.selectedThumb = idx;
    },
    changeThumbnail(idx) {
      if (this.loading) return;
      this.loading = true;
      const thumbTime = moment(idx, 's').add(500, 'ms').format('HH:mm:ss.SSS');
      this.$apollo.mutate({
        mutation: CHANGE_THUMBNAIL,
        variables: {
          projectId: this.project.id,
          thumbTime,
        },
        update: () => {
          this.$emit('close');
        },
      });
    },
    async calcThumbsCount() {
      const sprite = await this.loadSprite();
      const { height } = sprite;
      this.fakeThumbs = (4 - ((height / 270) % 4));
      this.thumbsCount = (height / 270);
    },
    loadSprite() {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.src = this.project.spritePath;
        img.onload = () => {
          resolve(img);
        };

        img.onerror = () => {
          reject();
        };
      });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  .ctb
    &__row
      display flex
      justify-content space-around
      flex-wrap wrap

    &__item
      flex-grow 1
      height 135px
      min-width 240px
      max-width 240px
      margin-top 3px
      background-repeat no-repeat
      position relative
      z-index 1
      display flex
      align-items stretch
      justify-content center
      cursor pointer
      background-size 100%
      div
        flex-grow 1

    &__overlay
      position absolute
      top 0
      left 0
      right 0
      bottom 0
      background-color #00000070
      display flex
      justify-content center
      align-items center

    &__button
      position relative
      z-index 2
</style>
