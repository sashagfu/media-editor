<template>
  <div class="TextEditDialog editor">
    <div class="editor__back"/>
    <div
      :style="{
        'left': `${prevPosition.left}px`,
        'top': `${prevPosition.top}px`,
        'height': `${prevPosition.height}px`,
        'width': `${prevPosition.width}px`,
      }"
      class="editor__prev"
    >
      <text-edit-panel/>
      <text-saving-panel/>
      <div
        v-if="itemEditingId === editText"
        ref="textBoxHidden"
        :style="{
          'color': curentEditingText.color,
          'font-face': curentEditingText.fontFace,
          'font-size': `${curentEditingText.fontSize}px`,
          'font-weight': curentEditingText.fontWeight,
          'font-style': curentEditingText.fontStyle,
          'text-align': curentEditingText.textAlign,
        }"
        class="editor__inp editor__inp--hidden"
      >
        {{ curentEditingText.text }}
      </div>
      <template v-for="(text, index) in itemsText.items">
        <vue-draggable-resizable
          :class="{
            'editor__text-box--active': text.id === itemEditingId,
            'editor__text-box--edited': text.id === editText ,
          }"
          :key="text.id"
          :draggable="text.id === itemEditingId"
          :resizable="false"
          :handles="[]"
          :parent="true"
          :x="Number(text.position.x)"
          :y="Number(text.position.y)"
          :h="Number(text.size.h)"
          :w="Number(text.size.w + 12)"
          class="editor__text-box"
          @dragging="onDrag"
        >
          <div
            @click.stop="setActiveTextItem(text.id, index)"
            @dblclick.stop="setEditText(text.id, index)"
          >
            <input
              v-if="text.id === editText"
              :style="{
                'color': text.color,
                'font-family': text.fontFamily,
                'font-size': `${text.fontSize}px`,
                'font-weight': text.fontWeight,
                'font-style': text.fontStyle,
                'text-align': text.textAlign,
                'height': `${text.size.h}px`,
                'width': `${text.size.w + 12}px`,
              }"
              :value="curentEditingText.text"
              class="editor__inp"
              autofocus
              @keyup="setText"
              @keyup.enter="closeEdit"
              @keyup.esc="escEdit"
              @click.stop=""
            >
            <div
              v-else
              ref="textBox"
              :style="{
                'color': text.color,
                'font-family': text.fontFamily,
                'font-size': `${text.fontSize}px`,
                'font-weight': text.fontWeight,
                'font-style': text.fontStyle,
                'text-align': text.textAlign,
                'height': `${text.size.h}px`,
                'width': `${text.size.w}px`,
              }"
              class="editor__text-item"
            >
              {{ text.text }}
            </div>
          </div>
        </vue-draggable-resizable>
      </template>

    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import VueDraggableResizable from '../../../common/vue-draggable';
import TextEditPanel from './TextEditPanel';
import TextSavingPanel from './TextSavingPanel';

export default {
  name: 'TextEditDialog',
  components: {
    TextEditPanel,
    TextSavingPanel,
    VueDraggableResizable,
  },
  data() {
    return {
      prevPosition: {},
      editText: null,
      undoText: '',
    };
  },
  computed: {
    ...mapGetters('coordinates', [
      'previewProjectContainer',
    ]),
    ...mapGetters('text', [
      'itemsText',
      'itemEditingId',
      'curentEditingText',
    ]),
  },
  watch: {
    previewProjectContainer() {
      this.setCoord(this.previewProjectContainer);
    },
  },
  mounted() {
    this.setCoord(this.previewProjectContainer);
    window.addEventListener('click', this.closeEdit, false);
  },
  destroyed() {
    window.removeEventListener('click', this.closeEdit, false);
  },
  methods: {
    ...mapActions('text', [
      'setActiveText',
      'setItemProperty',
      'setItemPosition',
      'setItemSize',
    ]),
    onDrag(x, y) {
      this.setItemPosition({ x, y });
    },
    setCoord({
      top,
      left,
      height,
      width,
    }) {
      this.prevPosition = Object.assign({}, {
        top,
        left,
        height,
        width,
      });
    },
    setActiveTextItem(id, index) {
      this.setActiveText(id);
      this.$nextTick(() => {
        if (!this.$refs.textBox[index]) {
          return;
        }
        const { width, height } = this.$refs.textBox[index].getBoundingClientRect();
        this.setItemSize({
          h: parseInt(height, 10),
          w: parseInt(width, 10),
        });
      });
    },
    setEditText(id, index) {
      if (id === this.itemEditingId) {
        this.undoText = this.curentEditingText.text;
        this.editText = id;
        this.$nextTick(() => {
          if (!this.$refs.textBox[index]) {
            return;
          }
          const { width, height } = this.$refs.textBox[index].getBoundingClientRect();
          this.setItemSize({
            h: parseInt(height, 10),
            w: parseInt(width, 10),
          });
        });
      }
    },
    setText(e) {
      if (this.itemEditingId) {
        this.setItemProperty({
          property: 'text',
          value: e.srcElement.value,
        });
        this.$nextTick(() => {
          if (!this.$refs.textBoxHidden) {
            return;
          }
          const { width, height } = this.$refs.textBoxHidden.getBoundingClientRect();
          this.setItemSize({
            h: parseInt(height, 10),
            w: parseInt(width, 10),
          });
        });
      }
    },
    escEdit() {
      if (this.itemEditingId) {
        this.setItemProperty({
          property: 'text',
          value: this.undoText,
        });
      }
      this.closeEdit();
    },
    closeEdit() {
      if (this.editText !== null) {
        this.editText = null;
      } else if (this.itemEditingId && this.editText === null) {
        this.setActiveText('');
      }
      this.undoText = '';
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../../../sass/front/components/bulma-theme';

  .editor {
    height: 100vh;
    left: 0;
    position: fixed;
    top: 0;
    width: 100vw;
    z-index: 1002;
    &__back {
      height: 100vh;
      left: 0;
      position: absolute;
      top: 0;
      width: 100vw;
      z-index: 1003;
    }
    &__prev {
      box-shadow: 0 0 0 1000px $cover-info;
      position: fixed;
      z-index: 1004;
    }
    &__text-box {
      display: flex;
      position: absolute;
      transform: translate(-50%, -50%);
      cursor: pointer;
      padding: 2px 4px;
      &--active {
        border: 1px solid $yellow;
      }
      &--edited {
        border: 1px solid $white;
        border-radius: 3px;
      }
    }
    &__text-item {
      color: $white;
      white-space: nowrap;
    }
    &__inp {
      color: $white;
      background: transparent;
      outline: none;
      border: none;
      &--hidden {
        position: fixed;
        top: 0;
        left: 0;
        border: none;
        opacity: 0;
      }
    }
  }

</style>
