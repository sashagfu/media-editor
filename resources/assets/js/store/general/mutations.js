import { SET_ITEM } from '../mutation-types';

export default {
  [SET_ITEM](state, items) {
    Object.assign(state, items);
    return state;
  },
};
