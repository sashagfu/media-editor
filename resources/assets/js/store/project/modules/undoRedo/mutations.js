import _ from 'lodash';
import { objectAssignDeep } from 'Editor/utils/helpers';
import {
  SET_ALL,
  RESET_STATE,
  ADD_DONE_MUTATION,
  MOVE_FROM_DONE_TO_UNDONE,
  MOVE_FROM_UNDONE_TO_DONE,
} from './mutation-types';
import initialState from './state';

export default {
  [SET_ALL](state, data) {
    Object.assign(state, data);
    return state;
  },
  [RESET_STATE](state) {
    Object.assign(state, initialState());
    return state;
  },
  [ADD_DONE_MUTATION](state, mutation) {
    clearTimeout(state.justAddedMutationTypeTimeoutId);

    if (state.justAddedMutationType === mutation.type) {
      objectAssignDeep(_.last(state.done), mutation);
    } else {
      state.done.push(mutation);
    }

    const timeoutId = setTimeout(() => {
      Object.assign(state, { justAddedMutationType: '' });
    }, 200);
    Object.assign(state, {
      justAddedMutationTypeTimeoutId: timeoutId,
      justAddedMutationType: mutation.type,
    });
    return state.done;
  },
  [MOVE_FROM_DONE_TO_UNDONE](state) {
    state.undone.push(state.done.pop());
    return state;
  },
  [MOVE_FROM_UNDONE_TO_DONE](state) {
    state.done.push(state.undone.pop());
    return state;
  },
};
