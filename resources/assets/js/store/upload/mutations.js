import {
  SET_ALL,
  ADD_FILE,
  SET_ITEM,
} from '../mutation-types';

export default {
  [SET_ALL](state, data) {
    Object.assign(state, data);
    return state;
  },
  [ADD_FILE](state, { file }) {
    Object.assign(state, {
      files: state.files.concat(file),
    });
    return state.files;
  },
  [SET_ITEM](state, items) {
    Object.assign(state, items);
    return state;
  },
};
