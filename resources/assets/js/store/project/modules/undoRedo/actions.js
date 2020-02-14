import _ from 'lodash';
import {
  SET_ALL,
  ADD_DONE_MUTATION,
  MOVE_FROM_DONE_TO_UNDONE,
  MOVE_FROM_UNDONE_TO_DONE,
  RESET_STATE,
} from './mutation-types';
import {
  RESTORE_FETCHED_PROJECT_DATA,
  SET_FETCHED_PROJECT_DATA,
  ADD_ITEM,
  CHANGE_ITEM,
  REMOVE_ITEMS,
  SET_LAYER_PROPS,
  UPDATE_ITEMS,
} from '../../mutation-types';

const SUBSCRIBED_MUTATIONS = [
  `project/${ADD_ITEM}`,
  `project/${CHANGE_ITEM}`,
  `project/${REMOVE_ITEMS}`,
  `project/${SET_LAYER_PROPS}`,
  `project/${UPDATE_ITEMS}`,
];

export default {
  undoRedoInit({ dispatch, commit }, { store }) {
    store.subscribe((mutation) => {
      if (SUBSCRIBED_MUTATIONS.indexOf(mutation.type) !== -1) {
        dispatch('onSubscribedMutation', { mutation });
      } else if (mutation.type === `project/${SET_FETCHED_PROJECT_DATA}`) {
        commit(RESET_STATE);
      }
    });
  },
  onSubscribedMutation({ commit, state }, { mutation }) {
    if (state.newMutation) {
      commit(ADD_DONE_MUTATION, mutation);
      commit(SET_ALL, { undone: [] });
    }
  },
  undo({ commit, state, getters }) {
    if (!getters.canUndo) return;
    commit(MOVE_FROM_DONE_TO_UNDONE);
    commit(SET_ALL, { newMutation: false });
    commit(`project/${RESTORE_FETCHED_PROJECT_DATA}`, null, { root: true });
    state.done.forEach((mutation) => {
      commit(
        `${mutation.type}`,
        JSON.parse(JSON.stringify(mutation.payload)),
        { root: true },
      );
    });
    commit(SET_ALL, { newMutation: true });
  },
  redo({ commit, state, getters }) {
    if (!getters.canRedo) return;
    const mutation = _.last(state.undone);
    commit(SET_ALL, { newMutation: false });
    commit(
      `${mutation.type}`,
      JSON.parse(JSON.stringify(mutation.payload)),
      { root: true },
    );
    commit(SET_ALL, { newMutation: true });
    commit(MOVE_FROM_UNDONE_TO_DONE);
  },
};
