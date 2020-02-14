import _ from 'lodash';

export default {
  draggingTimelineItem: state => state.draggingTimelineItem,
  draggingOverLayerId: state => state.draggingOverLayerId,
  isDraggingTimelineItem: state => !_.isNil(state.draggingTimelineItem.id),
  isDraggingOverDroppable: state => state.draggingOverLayerId !== null,
  draggingDroppableTolerance: (state, getters) => (
    getters.isDraggingTimelineItem && !state.isMouseOverTimelineContainer ? 'touch' : 'pointer'
  ),
  draggingPosition: state => state.draggingPosition,
  /**
     * Current position dragging item on timeline in milliseconds
     * @param state
     * @param getters
     * @param rootState
     * @param rootGetters
     * @returns {null|number}
     */
  draggingPositionOnTimeline: (state, getters, rootState, rootGetters) => {
    if (!getters.isDraggingOverDroppable) return null;
    const zoomedPxPerSec = rootGetters['timeline/zoomedPxPerSec'];
    let leftPosition = getters.draggingPosition.left;
    // If dragging item is not from timeline (means it's from gallery)
    // it's not relative to timeline container position but to body
    // then subtract timeline container left position
    // and add timeline horizontal scroll value
    if (!getters.draggingTimelineItem.id) {
      const { left: containerLeft } = rootGetters['coordinates/timelineLayersContainer'];
      const scrollH = rootGetters['timeline/scrollH'];
      const { left: absDragItemPos } = getters.draggingPosition;
      leftPosition = (absDragItemPos - containerLeft) + scrollH;
    }
    // Divide to zoomedPxPerSec to get position in milliseconds
    return leftPosition / zoomedPxPerSec;
  },
  /**
     * Is dragging item left position is over another item on timeline
     * @param state
     * @param getters
     * @param rootState
     * @param rootGetters
     * @returns {boolean}
     */
  isDraggingPositionOverTimelineItem: (state, getters, rootState, rootGetters) => {
    if (!getters.isDraggingOverDroppable) return false;
    const dragTimelineItem = getters.draggingTimelineItem;
    const dragItemPos = getters.draggingPositionOnTimeline;
    const dragOverLayerId = getters.draggingOverLayerId;
    const items = rootGetters['project/items'];
    // Check each timeline items, and dragging overlap if:
    //  item is not dragging (or it's from gallery)
    //  and dragging and item on different layers
    //  and dragging position is bigger or equal item position
    //  and dragging position is less then end of item
    return items.some(item => (
      dragTimelineItem.id !== item.id &&
            dragOverLayerId === parseInt(item.layerId, 10) &&
            dragItemPos >= item.position &&
            dragItemPos < (item.position + item.length)
    ));
  },
  /**
     * Max length for dragging item
     * Limited by next item on timeline layer
     * @param state
     * @param getters
     * @param rootState
     * @param rootGetters
     * @returns {null|number}
     */
  maxDraggingInsertLength: (state, getters, rootState, rootGetters) => {
    // Do not calculate when dragging is over timeline item or not over droppable
    if (
      getters.isDraggingPositionOverTimelineItem ||
            !getters.isDraggingOverDroppable
    ) {
      return null;
    }
    const dragTimelineItem = getters.draggingTimelineItem;
    const dragItemPos = getters.draggingPositionOnTimeline;
    const dragOverLayerId = getters.draggingOverLayerId;
    const items = rootGetters['project/items'];
    // First, create initial next item with the biggest position ever
    let nextItem = { position: Number.MAX_SAFE_INTEGER };
    // Find nextItem
    items.forEach((item) => {
      // Item is closer if
      // it's not dragging item
      // and it's on the same timeline layer then dragging
      // and it's before then current next item
      // and it's after dragging
      if (
        dragTimelineItem.id !== item.id &&
                parseInt(item.layerId, 10) === dragOverLayerId &&
                item.position < nextItem.position &&
                (item.position - dragItemPos) > 0
      ) {
        nextItem = item;
      }
    });
    return nextItem.position - dragItemPos;
  },
  isCanDrop(state, getters) {
    const {
      isDraggingOverDroppable,
      isDraggingPositionOverTimelineItem,
      maxDraggingInsertLength,
      isDraggingTimelineItem,
      draggingTimelineItem,
    } = getters;
    // Can't drop
    // if dragging is not over droppable
    // or dragging position (left) over any timeline item
    // and if dragging item is timeline item
    // or it overlap another timeline item
    return (
      isDraggingOverDroppable &&
            !isDraggingPositionOverTimelineItem &&
            (!isDraggingTimelineItem ||
            draggingTimelineItem.length < maxDraggingInsertLength)
    );
  },
};
