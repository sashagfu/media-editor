import { SET_ALL } from './mutation-types';

export default {
  setElementCoordinates({ commit }, { elem, name }) {
    const box = elem.getBoundingClientRect();
    const coord = {
      top: box.top + window.pageYOffset,
      bottom: box.bottom + window.pageYOffset,
      left: box.left + window.pageXOffset,
      right: box.right + window.pageXOffset,
      width: box.width,
      height: box.height,
    };
    commit(SET_ALL, { [name]: coord });
  },
};
