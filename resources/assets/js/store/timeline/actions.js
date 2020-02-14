import { SET_ALL } from './mutation-types';

export default {
  setZoom({ commit }, zoom) {
    commit(SET_ALL, { zoom });
  },
  setScrollH({ commit }, scrollH) {
    commit(SET_ALL, { scrollH });
  },
  setCursorPosition({ commit, getters }, { cursorPosition, inPx, manual = false }) {
    let newCurPos = cursorPosition;
    if (inPx) {
      const { zoomedPxPerSec, scrollH } = getters;
      newCurPos = (cursorPosition + scrollH) / zoomedPxPerSec;
    }
    commit(SET_ALL, {
      cursorPosition: newCurPos,
      cursorMovedManual: manual,
    });
  },
  // setDraggingMouseEvent({ commit }, { draggingMouseEvent }) {
  //     commit(SET_ALL, { draggingMouseEvent });
  // },
  // setDraggingItemId({ commit }, { draggingItemId }) {
  //     commit(SET_ALL, { draggingItemId });
  // },
  // setDraggingItemPosition({ commit }, draggingItemPosition) {
  //     commit(SET_ALL, { draggingItemPosition });
  // },
  // setDraggingOverLayerId({ commit }, layerId) {
  //     commit(SET_ALL, { draggingOverLayerId: layerId });
  // },
  setSelectedItems({ commit }, items) {
    commit(SET_ALL, { selectedItems: items });
  },
  toggleSelectedItem({ commit, getters }, item) {
    let newSelectedItems = [];
    if (getters.selectedItems.indexOf(item) !== -1) {
      newSelectedItems = getters.selectedItems.filter(element => item !== element);
    } else {
      newSelectedItems = getters.selectedItems.concat(item);
    }
    commit(SET_ALL, { selectedItems: newSelectedItems });
  },
  toggleSplitTool({ commit, getters }) {
    commit(SET_ALL, { isSplitTool: !getters.isSplitTool });
  },
  removeSelectedItems({ dispatch, getters }) {
    dispatch('project/removeItems', getters.selectedItems, { root: true });
    dispatch('setSelectedItems', []);
  },
};
