<template>
  <div
    :class="{'me-tl-layer-item__container--cutting' : insertLength !== null}"
    class="ItemThumbWrapper me-tl-layer-item__container"
  >
    <div class="me-tl-layer-item__thumb-wrapper">
      <component
        v-if="file"
        :is="componentIs"
        :file="file"
        :id="id"
        :uuid="uuid"
        v-bind="$attrs"
      />
      <div
        v-if="insertLength !== null"
        :style="{ left: `${insertLength}px` }"
        class="me-tl-layer-item__cut-part"
      />
    </div>
  </div>
</template>

<script>
import { startCase, has } from 'lodash';
import * as constants from 'Helpers/constants';
import * as fileTypes from '../../config/file-types';
// import { mapGetters, mapActions } from 'vuex';
import ItemThumbImage from './ItemThumbImage';
import ItemThumbAudio from './ItemThumbAudio';
import ItemThumbVideo from './ItemThumbVideo';
import ItemThumbSlide from './ItemThumbSlide';
// import {
//     addMultiEventListener,
//     removeMultiEventListener,
// } from '../../utils/helpers';

export default {
  name: 'ItemThumbWrapper',
  components: {
    ItemThumbImage,
    ItemThumbAudio,
    ItemThumbVideo,
    ItemThumbSlide,
  },
  props: {
    file: {
      type: Object,
      default: () => ({}),
    },
    showAs: {
      type: String,
      default: '',
    },
    insertLength: {
      type: Number,
      default: null,
    },
    id: {
      type: [Number, String],
      default: 0,
    },
    uuid: {
      type: String,
      default: '',
    },
  },
  computed: {
    componentIs() {
      let type = this.showAs;
      if (!type && has(this.file, 'type')) {
        const fileType = String(this.file.type).toUpperCase();
        if (fileType === constants.FULL || fileType === constants.VIDEO) {
          type = fileTypes.VIDEO;
        }
        if (fileType === constants.AUDIO) {
          type = fileTypes.AUDIO;
        }
      }
      if (!type && has(this.file, '__typename')) {
        const fileType = String(this.file.__typename).toUpperCase();

        if (fileType === constants.VIDEO) {
          type = fileTypes.VIDEO;
        }
        if (fileType === constants.AUDIO) {
          type = fileTypes.AUDIO;
        }
        if (fileType === constants.IMAGE) {
          type = fileTypes.IMAGE;
        }
      }
      if (!type && this.file.fileType) {
        type = this.file.fileType;
      }
      return `ItemThumb${startCase(type)}`;
    },
  },
};
</script>

