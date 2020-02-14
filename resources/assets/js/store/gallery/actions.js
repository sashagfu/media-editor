import { cloneDeep } from 'Editor/utils/helpers';
import {
  SET_ITEMS,
  SET_ITEMS_CLIP,
  SET_FILTERS,
  SET_SHOW,
  SET_SELECTED_ITEMS,
  SET_LOADING,
  ADD_ITEM,
  SET_COMPONENT,
  ADD_ITEM_TEXT,
  SET_ITEM_TEXT,
  SET_RECENT_PROJECTS,
} from './mutation-types';

export default {
  async setProjectMedia({ commit }, projectMedia) {
    commit(SET_LOADING);
    try {
      commit(SET_ITEMS, { items: cloneDeep(projectMedia) });
    } catch (e) {
      console.error(e);
    } finally {
      commit(SET_LOADING, { loading: false });
    }
  },

  addItem({ commit }, { item }) {
    commit(ADD_ITEM, { item });
  },
  deleteItem({ commit, getters }, { item }) {
    const items = getters.items.filter(i => i.uuid !== item.uuid);
    commit(SET_ITEMS, { items });
  },
  setComponent({ commit }, component) {
    commit(SET_COMPONENT, component);
  },
  addItemText({ commit }, item) {
    commit(ADD_ITEM_TEXT, item);
  },
  deleteItemText({ commit, getters }, item) {
    const itemsText = getters.itemsText.filter(i => i.id !== item.id);
    commit(SET_ITEM_TEXT, itemsText);
  },
  setTexts({ commit }, items) {
    let itemsText = [];
    itemsText = itemsText.concat(items);
    commit(SET_ITEM_TEXT, itemsText);
  },
  setItemText({ commit, getters }, item) {
    let itemsText = [];
    itemsText = itemsText.concat(getters.itemsText);
    const findText = itemsText.find(i => i.id === item.id);
    Object.assign(findText, item);
    commit(SET_ITEM_TEXT, itemsText);
  },
  setItemTextProperty({ commit, getters }, { id, property, value }) {
    let itemsText = [];
    itemsText = itemsText.concat(getters.itemsText);
    const findText = itemsText.find(item => item.id === id);
    findText[property] = value;
    commit(SET_ITEM_TEXT, itemsText);
  },
  toggleSelectedItem({ commit, getters }, item) {
    const fIndex = getters.selectedItems.findIndex(i => i.id === item.id);
    let selectedItems = [];
    if (fIndex === -1) {
      selectedItems = getters.selectedItems.concat(item);
    } else {
      selectedItems = getters.selectedItems.filter(i => i.id !== item.id);
    }
    commit(SET_SELECTED_ITEMS, selectedItems);
  },
  toggleFilter({ commit, getters }, item) {
    const filters = Object.assign({}, getters.filters);
    filters[item] = !filters[item];
    commit(SET_FILTERS, filters);
  },
  toggleShow({ commit, getters }) {
    const showList = !getters.showList;
    commit(SET_SHOW, showList);
  },
  setSearch({ commit, getters }, text) {
    const filters = Object.assign({}, getters.filters);
    filters.search = text;
    commit(SET_FILTERS, filters);
  },
  fetchClips({ commit }, clips) {
    commit(SET_ITEMS_CLIP, { itemsClip: clips });
  },
  fetchRecentProjects({ commit }, projects) {
    commit(SET_RECENT_PROJECTS, { projects });
  },
};
