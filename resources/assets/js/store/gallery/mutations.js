import {
  SET_LOADING,
  SET_ITEMS,
  SET_ITEMS_CLIP,
  SET_SHOW,
  SET_FILTERS,
  SET_SELECTED_ITEMS,
  ADD_ITEM,
  SET_COMPONENT,
  ADD_ITEM_TEXT,
  SET_ITEM_TEXT,
  SET_RECENT_PROJECTS,
} from './mutation-types';

export default {
  [SET_LOADING](state, { loading = true } = {}) {
    Object.assign(state, {
      loading,
    });
    return state.loading;
  },
  [SET_ITEMS](state, { items }) {
    Object.assign(state, {
      items,
    });
    return state.items;
  },
  [SET_ITEMS_CLIP](state, { itemsClip }) {
    Object.assign(state, {
      itemsClip,
    });
    return state.items;
  },
  [SET_RECENT_PROJECTS](state, { projects }) {
    Object.assign(state, {
      itemsRecentProjects: projects,
    });
    return state.items;
  },
  [ADD_ITEM](state, { item }) {
    Object.assign(state, {
      items: state.items.concat(item),
    });
    return state.items;
  },
  [SET_COMPONENT](state, component) {
    Object.assign(state, {
      activeComponent: component,
    });
    return state.activeComponent;
  },
  [ADD_ITEM_TEXT](state, item) {
    Object.assign(state, {
      itemsText: state.itemsText.concat(item),
    });
    return state.itemsText;
  },
  [SET_ITEM_TEXT](state, itemsText) {
    Object.assign(state, {
      itemsText,
    });
    return state.itemsText;
  },
  [SET_SELECTED_ITEMS](state, selectedItems) {
    Object.assign(state, {
      selectedItems,
    });
    return state.selectedItems;
  },
  [SET_FILTERS](state, filters) {
    Object.assign(state, {
      filters,
    });
    return state.filters;
  },
  [SET_SHOW](state, showList) {
    Object.assign(state, {
      showList,
    });
    return state.showList;
  },
};
