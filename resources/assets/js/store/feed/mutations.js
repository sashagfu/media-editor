import Vue from 'vue';
import {
  SET_ITEM,
  // LIKE_POST,
  // STAR_POST,
  DELETE_POST,
  // SET_STATUS,
  // ADD_COMMENT,
  // SET_COMMENT,
  LIKE_COMMENT,
  // DELETE_COMMENT,
  // SET_REPLIES,
} from './mutation-types';

export default {
  [SET_ITEM](state, items) {
    Object.assign(state, items);
    return state;
  },
  // [SET_STATUS](state, {
  //     item,
  //     key,
  //     value,
  // }) {
  //     Vue.set(item, key, value);
  //     return state;
  // },
  // [ADD_COMMENT](state, {
  //     post,
  //     comments,
  //     commentVisibility,
  //     commentsCount,
  // }) {
  //     Vue.set(post, 'comments', comments);
  //     Vue.set(post, 'commentVisibility', commentVisibility);
  //     Vue.set(post, 'commentsCount', commentsCount);
  //     return state;
  // },
  // [SET_COMMENT](state, {
  //     post,
  //     comments,
  //     commentsNextUrl,
  // }) {
  //     Vue.set(post, 'comments', comments);
  //     Vue.set(post, 'commentsNextUrl', commentsNextUrl);
  //     return state;
  // },
  // [SET_REPLIES](state, {
  //     comment,
  //     replies,
  //     repliesNextUrl,
  // }) {
  //     Vue.set(comment, 'replies', replies);
  //     Vue.set(comment, 'repliesNextUrl', repliesNextUrl);
  //     return state;
  // },
  // [DELETE_COMMENT](state, {
  //     post,
  //     comments,
  //     commentsCount,
  // }) {
  //     Vue.set(post, 'comments', comments);
  //     Vue.set(post, 'commentsCount', commentsCount);
  //     return state;
  // },
  [LIKE_COMMENT](state, {
    comment,
    likes,
    deleted,
    like,
    isLiked,
  }) {
    if (deleted) {
      const fLikes = likes.filter(f => f.id !== like.id);
      Vue.set(comment, 'likes', fLikes);
    } else {
      Vue.set(comment, 'likes', likes.concat(like));
    }
    Vue.set(comment, 'isLiked', isLiked);
    return state;
  },
  // [LIKE_POST](state, {
  //     post,
  //     like,
  //     likes,
  //     deleted,
  //     userReaction,
  // }) {
  //     if (deleted) {
  //         const fLikes = likes.filter(f => f.id !== like.id);
  //         Vue.set(post, 'likes', fLikes);
  //     } else {
  //         Vue.set(post, 'likes', likes.concat(like));
  //     }
  //     Vue.set(post, 'userReaction', userReaction);
  //     return state;
  // },
  // [STAR_POST](state, {
  //     post,
  //     star,
  //     stars,
  //     deleted,
  //     userReaction,
  // }) {
  //     if (deleted) {
  //         const fStars = stars.filter(f => f.id !== star.id);
  //         Vue.set(post, 'stars', fStars);
  //     } else {
  //         Vue.set(post, 'stars', stars.concat(star));
  //     }
  //     Vue.set(post, 'userReaction', userReaction);
  //     return state;
  // },
  [DELETE_POST](state, {
    feed,
    fPost,
  }) {
    Vue.set(feed, 'data', fPost);
    return state;
  },
};
