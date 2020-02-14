import * as fileTypes from 'Editor/config/file-types';
import * as constants from 'Helpers/constants';

export default {
  // draggingItemId: state => state.draggingItemId,
  // draggingMouseEvent: state => state.draggingMouseEvent,
  // draggingOverLayerId: state => state.draggingOverLayerId,
  zoomedPxPerSec: (state, getters, rootState, rootGetters) => (
    state.zoom * rootGetters['settings/pxPerSec']
  ),
  zoom: state => state.zoom,
  scrollH: state => state.scrollH,
  scrollHTime: (state, getters) => getters.scrollH / getters.zoomedPxPerSec,
  isSplitTool: state => state.isSplitTool,
  cursorPosition: state => state.cursorPosition,
  cursorPositionPx: (state, getters) => (
    getters.cursorPosition * getters.zoomedPxPerSec
  ),
  cursorMovedManual: state => state.cursorMovedManual,
  selectedItems: state => state.selectedItems,
  itemsUnderCursor: (state, getters, rootState, rootGetters) => {
    const items = rootGetters['project/items'];
    const { cursorPosition } = getters;

    return items.filter((item) => {
      const { position, length = parseInt(item.file.time, 10) } = item;
      return cursorPosition > position && cursorPosition < (position + length);
    });
  },
  timelineLayerWidth: (state, getters, rootState, rootGetters) => {
    const { zoomedPxPerSec } = getters;
    const items = rootGetters['project/items'];
    const timelineLayersContainerWidth = rootGetters['coordinates/timelineLayersContainer'].width;
    let widthTime = rootGetters['settings/timelineDuration']; // or 0
    items.forEach((item) => {
      const edge = item.position + (item.length || parseInt(item.file.time, 10));
      widthTime = edge > widthTime ? edge : widthTime;
    });

    const width = Math.round(widthTime * zoomedPxPerSec);
    return width > timelineLayersContainerWidth ? width : timelineLayersContainerWidth;
  },
  timelineWindowRange: (state, getters, rootState, rootGetters) => {
    const { zoomedPxPerSec, scrollH } = getters;
    const timelineLayersContainerWidth = rootGetters['coordinates/timelineLayersContainer'].width;
    const left = scrollH / zoomedPxPerSec;
    const right = left + (timelineLayersContainerWidth / zoomedPxPerSec);

    return { left, right };
  },
  isShowCursor: (state, getters) => {
    const { cursorPosition, timelineWindowRange } = getters;
    return cursorPosition >= timelineWindowRange.left &&
            cursorPosition < timelineWindowRange.right;
  },
  itemsCanUnlinkInSelectedItems: (state, getters, rootState, rootGetters) => {
    const { selectedItems } = getters;
    const items = rootGetters['project/items'];
    if (getters.selectedItems.length) {
      return items.filter(item => (
        // Item is in selected
        selectedItems.indexOf(item.id) !== -1 &&
                // Is video or asset "FULL"
                (item.file.fileType === fileTypes.VIDEO ||
                  (item.file.fileType === fileTypes.ASSET && item.file.type === constants.FULL)) &&
                // Audio us linked
                !item.unlinked &&
                // and it is not unlinked audio
                !item.showAs
      ));
    }
    return [];
  },
};
