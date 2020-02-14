import axios from 'axios';
import { SET_ITEM } from '../mutation-types';

export default {
  setActiveThread({ commit }, data) {
    commit(SET_ITEM, { activeThread: data });
  },
  setWidgetVisibility({ commit }, visibility) {
    commit(SET_ITEM, { widgetVisibility: visibility });
  },
  setUnreadMessagesCount({ commit }, count) {
    if (count) {
      commit(SET_ITEM, { unreadMessagesCount: count });
    } else {
      axios.get('/api/chat/threads/getUnreadCount').then((response) => {
        commit(SET_ITEM, { unreadMessagesCount: response.data });
      });
    }
  },
  setChatPage({ commit }, data) {
    commit(SET_ITEM, { chatPage: data });
  },
  setThreadRead({ commit }, data) {
    // console.log('setThreadRead');
    axios.get(`/api/chat/threads/${data.id}/markAsRead`).then((response) => {
      // this.setActiveThreadRead(false);
      // const thread = Object.assign({}, getters.activeThread);
      // thread.isUnread = false;
      // commit(SET_ITEM, { activeThread: thread });
      commit(SET_ITEM, { unreadMessagesCount: response.data });
    });
  },
  setActiveThreadRead({ commit, getters }, data) {
    // console.log('setActiveThreadRead');
    const thread = Object.assign({}, getters.activeThread);
    thread.isUnread = data;
    commit(SET_ITEM, { activeThread: thread });
  },
};
