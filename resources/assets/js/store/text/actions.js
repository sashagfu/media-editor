import _ from 'lodash';
import {
  SET_EDITING_TEXT,
  ADD_TEXT,
  SET_TEXT_PROPERTY,
  SET_EDITING_ID,
} from './mutation-types';

export default {
  setEditingText({ commit }, item) {
    if (_.isEmpty(item)) {
      commit(SET_EDITING_ID, '');
    }
    commit(SET_EDITING_TEXT, item);
  },
  addItem({ commit, getters, rootGetters }) {
    const id = Math.floor(Math.random() * 10000);
    const textItem = Object.assign({}, getters.itemTextDefault);
    const { width, height } = rootGetters['coordinates/previewProjectContainer'];

    textItem.id = id;
    textItem.position.x = Number.parseFloat(width / 2).toFixed(0);
    textItem.position.y = Number.parseFloat(height / 2).toFixed(0);

    const texts = Object.assign({}, getters.itemsText);
    texts.items.push(textItem);

    commit(ADD_TEXT, texts);
    commit(SET_EDITING_ID, id);
  },
  deleteItem({ commit, getters }) {
    const fItems = getters.itemsText.items.filter(i => (
      i.id !== getters.itemEditingId
    ));

    const texts = Object.assign({}, getters.itemsText);
    texts.items = fItems;
    commit(ADD_TEXT, texts);
    commit(SET_EDITING_ID, '');
  },
  setActiveText({ commit }, id) {
    commit(SET_EDITING_ID, id);
  },
  setItemProperty({ commit, getters }, { property, value }) {
    const texts = Object.assign({}, getters.itemsText);
    const findText = texts.items.find(item => item.id === getters.itemEditingId);
    findText[property] = value;
    commit(SET_TEXT_PROPERTY, texts);
  },
  setItemPosition({ commit, getters }, { x, y }) {
    const texts = Object.assign({}, getters.itemsText);
    const findText = texts.items.find(item => item.id === getters.itemEditingId);
    findText.position = { x, y };
    commit(SET_TEXT_PROPERTY, texts);
  },
  setItemSize({ commit, getters }, { h, w }) {
    const texts = Object.assign({}, getters.itemsText);
    const findText = texts.items.find(item => item.id === getters.itemEditingId);
    findText.size = { h, w };
    commit(SET_TEXT_PROPERTY, texts);
  },
  setTextProperty({ commit, getters }, { property, value }) {
    const texts = Object.assign({}, getters.itemsText);
    texts[property] = value;
    commit(SET_TEXT_PROPERTY, texts);
  },
};
