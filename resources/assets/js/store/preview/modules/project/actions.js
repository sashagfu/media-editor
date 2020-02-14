import { SET_ALL } from './mutation-types';

export default {
  setCanPlay({ commit }, { canPlay }) {
    commit(SET_ALL, { canPlay });
  },
};
