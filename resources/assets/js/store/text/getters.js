import _ from 'lodash';

export default {
  itemsText: state => state.itemsText,
  itemsTextIsEditing: state => !_.isEmpty(state.itemsText),
  itemTextDefault: state => state.itemTextDefault,
  itemEditingId: state => state.itemEditingId,
  curentEditingText: (state, getters) => { // rootState, rootGetters
    if (!getters.itemEditingId) {
      return getters.itemTextDefault;
    }
    return getters.itemsText.items.find(item => item.id === getters.itemEditingId);
  },
};
