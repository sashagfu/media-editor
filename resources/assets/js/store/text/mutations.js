import {
  SET_EDITING_TEXT,
  ADD_TEXT,
  SET_EDITING_ID,
  SET_TEXT_PROPERTY,
} from './mutation-types';

export default {
  [SET_EDITING_TEXT](state, Item) {
    Object.assign(state, {
      itemsText: Item,
    });
    return state.items;
  },
  [ADD_TEXT](state, texts) {
    Object.assign(state, {
      itemsText: texts,
    });
    return state.itemsText;
  },
  [SET_TEXT_PROPERTY](state, texts) {
    Object.assign(state, {
      itemsText: texts,
    });
    return state.itemsText;
  },
  [SET_EDITING_ID](state, id) {
    Object.assign(state, {
      itemEditingId: id,
    });
    return state.itemEditingId;
  },
};
