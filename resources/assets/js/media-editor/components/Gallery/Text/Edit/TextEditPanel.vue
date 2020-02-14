<template>
  <div class="TextEditPanel te-panel">
    <div class="te-panel__left">
      <div class="te-panel__group">
        <div
          class="te-panel__btn"
          title="add text"
          @click.stop="addItem"
        >
          <font-awesome-icon
            :icon="['fas', 'plus']"
            class="fa-icon"
          />
        </div>
        <div
          :class="{'te-panel__btn--disabled': !itemEditingId}"
          class="te-panel__btn"
          title="delete text"
          @click.stop="deleteItem"
        >
          <font-awesome-icon
            :icon="['fas', 'trash-alt']"
            class="fa-icon"
          />
        </div>
      </div>
      <div class="te-panel__group">
        <div
          class="te-panel__inp-box"
          @click.stop=""
        >
          <div class="te-panel__inp-itm">
            <multiselect
              :searchable="false"
              :close-on-select="true"
              :value="curentEditingText.fontFamily"
              :options="options"
              :show-labels="false"
              :preselect-first="true"
              :disabled="!itemEditingId"
              title="font face"
              placeholder="Select font"
              @input="changeFontFamily"
            >
              <template
                slot="option"
                slot-scope="props"
              >
                <div
                  :style="{'font-family': props.option}"
                  class="option__desc"
                >
                  {{ props.option }}
                </div>
              </template>
            </multiselect>
          </div>
        </div>
        <div class="te-panel__inp-box">
          <div class="te-panel__inp-itm">
            <input
              :class="{'te-panel__inp--disabled': !itemEditingId}"
              :value="curentEditingText.fontSize"
              class="te-panel__inp te-panel__inp--font-size"
              title="font face"
              readoly
            >
          </div>
          <div class="te-panel__inp-itm">
            <div
              :class="{'te-panel__inp-btn--disabled': !itemEditingId}"
              class="te-panel__inp-btn te-panel__inp-btn--increm"
              title="font size increment"
              @click.stop="changeFontSize(1)"
            >
              <font-awesome-icon
                :icon="['fas', 'angle-up']"
                class="fa-icon fa-icon--small"
              />
            </div>
            <div
              :class="{'te-panel__inp-btn--disabled': !itemEditingId}"
              class="te-panel__inp-btn te-panel__inp-btn--decrem"
              title="font size decrement"
              @click.stop="changeFontSize(-1)"
            >
              <font-awesome-icon
                :icon="['fas', 'angle-down']"
                class="fa-icon fa-icon--small"
              />
            </div>
          </div>
        </div>
        <div
          :class="{'te-panel__btn--disabled': !itemEditingId}"
          class="te-panel__btn"
          title="color picker"
          @click.stop=""
        >
          <div
            v-if="curentEditingText.color"
            :style="{'background-color': curentEditingText.color}"
            class="te-panel__btn-color"
          />
          <font-awesome-icon
            v-else
            :icon="['fas', 'eye-dropper']"
            class="fa-icon fa-icon--eye-dropper"
          />
          <input
            :disable="!itemEditingId"
            :value="curentEditingText.color || '#ffffff'"
            type="color"
            class="te-panel__inp--color"
            @input.stop="changeColor"
          >
        </div>
        <div
          :class="{
            'te-panel__btn--disabled': !itemEditingId
              || curentEditingText.fontWeight === 'bold'
          }"
          class="te-panel__btn"
          title="bold text"
          @click.stop="changeFontStyle('fontWeight', 'bold', $event)"
        >
          <font-awesome-icon
            :icon="['fas', 'bold']"
            class="fa-icon"
          />
        </div>
        <div
          :class="{
            'te-panel__btn--disabled': !itemEditingId
              || curentEditingText.fontStyle === 'italic'
          }"
          class="te-panel__btn"
          title="italic text"
          @click.stop="changeFontStyle('fontStyle', 'italic', $event)"
        >
          <font-awesome-icon
            :icon="['fas', 'italic']"
            class="fa-icon"
          />
        </div>
      </div>
      <div class="te-panel__group">
        <div
          :class="{
            'te-panel__btn--disabled': !itemEditingId
              || curentEditingText.textAlign === 'left',
          }"
          class="te-panel__btn"
          title="align-left"
          @click.stop="changeProperty('textAlign', 'left')"
        >
          <font-awesome-icon
            :icon="['fas', 'align-left']"
            class="fa-icon"
          />
        </div>
        <div
          :class="{
            'te-panel__btn--disabled': !itemEditingId
              || curentEditingText.textAlign === 'center',
          }"
          class="te-panel__btn"
          title="align center"
          @click.stop="changeProperty('textAlign', 'center')"
        >
          <font-awesome-icon
            :icon="['fas', 'align-center']"
            class="fa-icon"
          />
        </div>
        <div
          :class="{
            'te-panel__btn--disabled': !itemEditingId
              || curentEditingText.textAlign === 'right',
          }"
          class="te-panel__btn"
          title="align right"
          @click.stop="changeProperty('textAlign', 'right')"
        >
          <font-awesome-icon
            :icon="['fas', 'align-right']"
            class="fa-icon"
          />
        </div>
      </div>
    </div>
    <div class="te-panel__right"/>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.min.css';

export default {
  name: 'TextEditPanel',
  components: {
    Multiselect,
  },
  data() {
    return {
      selected: null,
      options: [
        'Roboto',
        'Lora',
        'Lato',
        'Advent Pro',
        'Abhaya Libre',
        'Open Sans',
        'Oswald',
        'Raleway',
        'Alice',
      ],
    };
  },
  computed: {
    ...mapGetters('text', [
      'itemEditingId',
      'curentEditingText',
    ]),
  },
  methods: {
    ...mapActions('text', [
      'addItem',
      'deleteItem',
      'setItemProperty',
    ]),
    changeProperty(property, value) {
      if (this.itemEditingId) {
        this.setItemProperty({ property, value });
      }
    },
    changeFontFamily(value) {
      if (this.itemEditingId) {
        this.setItemProperty({
          property: 'fontFamily',
          value,
        });
      }
    },
    changeFontSize(val) {
      if (this.itemEditingId) {
        this.setItemProperty({
          property: 'fontSize',
          value: this.curentEditingText.fontSize + val,
        });
      }
    },
    changeFontStyle(prop, val) {
      if (this.itemEditingId) {
        this.setItemProperty({
          property: prop,
          value: this.curentEditingText[prop] === val ? 'normal' : val,
        });
      }
    },
    changeColor(e) {
      if (this.itemEditingId) {
        this.setItemProperty({
          property: 'color',
          value: e.srcElement.value,
        });
      }
    },
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    font-size: 12px;
    transition: all .3s;
    &--disabled {
      color: $grey-lighter;
      cursor: auto;
      &:hover {
        color: $grey-lighter;
      }
    }
    &--small {
      font-size: 8px;
    }
    &--eye-dropper {
      position: absolute;
      z-index: 1010;
    }
  }
  .te-panel {
    align-items: center;
    background-color: $white;
    border-radius: 3px 3px 0 0 ;
    display: flex;
    height: 26px;
    justify-content: space-between;
    padding: 0 16px;
    position: absolute;
    top: -26px;
    width: 100%;
    &__left {
      align-items: center;
      display: flex;
      margin-left: -4px
    }
    &__right {
      align-items: center;
      display: flex;
      justify-content: flex-end;
      margin-right: -4px;
    }
    &__group {
      display: flex;
      margin: 0 4px;
    }
    &__btn {
      align-items: center;
      border-radius: 2px;
      border: 1px solid $border;
      cursor: pointer;
      display: flex;
      height: 22px;
      justify-content: center;
      margin: 0 2px;
      position: relative;
      transition: all .3s;
      width: 22px;
      &:hover {
        background-color: $white-bis;
        border-color: $border-hover;
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
      &--active {
        background-color: $white-ter;
      }
    }
    &__btn-color {
      border-radius: 2px;
      height: 14px;
      width: 14px;
      position: absolute;
      z-index: 1010;
    }
    &__inp-box {
      display: flex;
      margin: 0 2px;
    }
    &__inp-itm {
      &:first-child {
        display: flex;
        align-items: center;
      }
    }
    &__inp {
      fnt($text-light, 12px, $weight-light, center);
      background-color: $white;
      height: 22px;
      border: 1px solid $border;
      border-right: none;
      border-radius: 2px 0 0 2px;
      outline: none;
      &--font-face {
        width: 64px;
        padding-left: 4px;
        text-align: left;
      }
      &--font-size {
        width: 32px;
      }
      &--disabled {
        color: $text-lighter;
      }
      &--color {
        border-radius: 2px;
        cursor: pointer;
        opacity: 0;
        position: absolute;
        width: 22px;
        z-index: 1011;
      }
    }
    &__inp-btn {
      fl-center();
      background-color: $white;
      border: 1px solid $border;
      height: 11px;
      width: 22px;
      &:hover .fa-icon {
        color: $text-lighter;
      }
      &--font-face {
        border-radius: 0 2px 2px 0;
        height: 22px;
      }
      &--increm {
        border-bottom: none;
        border-radius: 0 2px 0 0;
      }
      &--decrem {
        border-radius: 0 0 2px 0;
        border-top: none;
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

<style lang="stylus">
    @import '../../../../../../sass/front/components/bulma-theme';
    .te-panel {
        & .multiselect {
            min-height: 22px;

            &__tags {
                border-radius: 3px;
                border: 1px solid $border;
                font-size: 12px;
                min-height: 22px;
                padding: 0 32px 0 4px;
            }
            &__select {
                color: $text-light;
                height: 22px;
                padding: 0;
                width: 22px;
            }
            &__single {
                fnt($text-light, 12px, $weight-light, left);
                margin-bottom: 0;
                padding-left: 0;
                width: 72px;
            }
            &__content-wrapper {
                border-radius: 0 0 3px 3px;
                border: 1px solid $border;
                border-top: none;
            }
            &__option {
                fnt($text-light, 12px, $weight-light, left);
                align-items: center;
                display: flex;
                line-height: 1;
                min-height: 22px;
                padding: 2px 8px;

                &--highlight {
                    background-color: $blue;
                    fnt($text-invert, 12px, $weight-semibold, left);
                }
                &--selected {
                    background-color: $grey;
                    fnt($text-invert, 12px, $weight-semibold, left);
                }
            }
        }
    }
</style>

