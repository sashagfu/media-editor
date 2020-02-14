import {
  SET_ALL,
  RESET_DRAGGING,
  SET_DRAGGING_POSITION,
} from './mutation-types';

export default {
  resetDragging({ commit }) {
    commit(RESET_DRAGGING);
  },
  setDraggingTimelineItem({ commit }, draggingTimelineItem = {}) {
    commit(SET_ALL, { draggingTimelineItem });
  },
  setDraggingPosition({ commit }, draggingPosition = { top: null, left: null }) {
    commit(SET_DRAGGING_POSITION, draggingPosition);
  },
  setDraggingOverLayerId({ commit }, layerId) {
    commit(SET_ALL, { draggingOverLayerId: layerId });
  },
  setIsMouseOverTimelineLayersContainer({ commit }, isMouseOverTimelineContainer) {
    commit(SET_ALL, { isMouseOverTimelineContainer });
  },
};
