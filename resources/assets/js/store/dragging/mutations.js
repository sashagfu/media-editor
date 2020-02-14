import {
  SET_ALL,
  RESET_DRAGGING,
  SET_DRAGGING_POSITION,
} from './mutation-types';
import initialState from './state';

export default {
  [SET_ALL](state, data) {
    Object.assign(state, data);
    return state;
  },
  [RESET_DRAGGING](state) {
    Object.assign(state, initialState());
    return state;
  },
  [SET_DRAGGING_POSITION](state, position) {
    Object.assign(state.draggingPosition, position);
    return state.draggingPosition;
  },
};
