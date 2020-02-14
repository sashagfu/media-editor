import {
  ADD_ITEM,
  SET_ITEM,
} from '../mutation-types';

export default {
  [SET_ITEM](state, items) {
    Object.assign(state, items);
    return state;
  },
  [ADD_ITEM](state, item) {
    const key = Object.keys(item)[0];
    Object.assign(state, {
      [key]: state[key].concat(item),
    });
    return state;
  },
};
