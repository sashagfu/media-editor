import actions from './actions';
import getters from './getters';
import mutations from './mutations';
import state from './state';
import undoRedo from './modules/undoRedo';

export default {
  namespaced: true,
  actions,
  getters,
  mutations,
  state,
  modules: {
    undoRedo,
  },
};
