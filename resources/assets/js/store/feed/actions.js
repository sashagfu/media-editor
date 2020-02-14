// import _ from 'lodash';
import axios from 'axios';
import {
  SET_ITEM,
  // SET_STATUS,
  // LIKE_POST,
  // STAR_POST,
  DELETE_POST,
  // ADD_COMMENT,
  // SET_COMMENT,
  LIKE_COMMENT,
  // DELETE_COMMENT,
  // SET_REPLIES,
} from './mutation-types';

export default {
  // setFeedItems({ commit }, data) {
  //     commit(SET_ITEM, { feedItems: data });
  // },
  // setActivePost({ commit }, post) {
  //     commit(SET_ITEM, { activePost: post });
  // },
  setMentionUsers({ commit }) {
    axios.get('/api/get/mentions/users').then((response) => {
      commit(SET_ITEM, { mentionUsers: response.data });
    });
  },
  setTogglePostDeleteModal({ commit }, data) {
    commit(SET_ITEM, { togglePostDeleteModal: data });
  },
  setActiveComment({ commit }, comment) {
    // console.log('setActiveComment ', comment);
    commit(SET_ITEM, { activeComment: comment });
  },
  setToggleCommentDeleteModal({ commit }, data) {
    commit(SET_ITEM, { toggleCommentDeleteModal: data });
  },
  // setToggleFlagModal({ commit }, data) {
  //     commit(SET_ITEM, { toggleFlagModal: data });
  // },
  // addNewFeedItem({ commit, getters }, item) {
  //     const items = Object.assign({}, getters.feedItems);
  //     items.data.unshift(item);
  //     commit(SET_ITEM, { feedItems: items });
  // },
  // addFeedItems({ commit, getters }, item) {
  //     const items = Object.assign({}, getters.feedItems);
  //     items.data.concat(item);
  //     commit(SET_ITEM, { feedItems: items });
  // },
  toggleVideoModal({ commit, getters }) {
    commit(SET_ITEM, { videoModalActive: !getters.videoModalActive });
    if (!this.videos.length) {
      commit(SET_ITEM, { videosLoading: !getters.videosLoading });
      this.$http.get('/api/profile/videos/get')
        .then(({ data }) => {
          commit(SET_ITEM, { videos: data });
          commit(SET_ITEM, { videosLoading: false });
        })
        .catch(() => {
          // .catch((error) => {
          //     console.log('toggleVideoModal error ', error);
          commit(SET_ITEM, { videosLoading: false });
        });
    }
  },
  // addComment({ commit }, { post, commentText }) {
  //     commit(SET_ITEM, { commentLoading: true });
  //     axios.patch('/api/comment/add', {
  //         _token: window.Laravel.csrfToken,
  //         post_id: post.id,
  //         comment_text: commentText,
  //     })
  //         .then(({ data: { comment, commentsCount } }) => {
  //             commit(SET_ITEM, { commentLoading: false });
  //             const comments = [];
  //             commit(ADD_COMMENT, {
  //                 post,
  //                 comments: comments.concat(comment, post.comments),
  //                 commentVisibility: true,
  //                 commentsCount,
  //             });
  //         })
  //         .catch((error) => {
  //         //     console.log('addComment error ', error);
  //             commit(SET_ITEM, { commentLoading: false });
  //             commit(SET_ITEM, { errors: error.response.data });
  //         });
  // },
  skipError({ commit, getters }, { key, index }) {
    const errors = Object.assign({}, getters.errors);
    commit(SET_ITEM, { errors: errors[key].splice(index, 1) });
  },
  // deleteComment({ commit, getters }) {
  //     commit(SET_ITEM, { deleteLoading: true });
  //     axios.post('/api/comment/delete', {
  //         _token: window.Laravel.csrfToken,
  //         comment_id: getters.activeComment.id,
  //     })
  //         .then(() => {
  //             commit(SET_ITEM, { deleteLoading: false });
  //             const findIndex = getters.feedItems.data.findIndex(i => (
  //                 i.id === getters.activePost.id
  //             ));
  //             if (findIndex !== -1) {
  //                 const post =  getters.feedItems.data[findIndex];
  //                 if (getters.activeComment.parent_id) {
  //                     const fComments = post.comments.concat([]);
  //                     const findIndexComment = fComments.findIndex(i => (
  //                         i.id === Number(getters.activeComment.parent_id)
  //                     ));
  //                     if (findIndexComment !== -1) {
  //                         fComments[findIndexComment].replies = fComments[findIndexComment]
  //                             .replies.filter(i => (
  //                                 i.id !== getters.activeComment.id
  //                             ));
  //                         fComments[findIndexComment]
  //                             .repliesLength = fComments[findIndexComment]
  //                             .replies.length;
  //                         commit(DELETE_COMMENT, {
  //                             post,
  //                             comments: fComments,
  //                             commentsCount: fComments.length,
  //                         });
  //                     }
  //                 } else {
  //                     const fComments = post.comments.filter(i => (
  //                         i.id !== getters.activeComment.id
  //                     ));
  //                     commit(DELETE_COMMENT, {
  //                         post,
  //                         comments: fComments,
  //                         commentsCount: fComments.length,
  //                     });
  //                 }
  //                 commit(SET_ITEM, { toggleCommentDeleteModal: false });
  //                 commit(SET_ITEM, { activeComment: {} });
  //             }
  //         })
  //         .catch((error) => {
  //         //     console.log('deleteComment error ', error);
  //             commit(SET_ITEM, { deleteLoading: false });
  //             commit(SET_ITEM, { errors: error.response.data });
  //         });
  // },
  toggleCommentVisibility({ commit, getters }, post) {
    if (getters.commentsVisibility.find(i => i === post.id)) {
      const commentsVisibility = getters.commentsVisibility.filter(i => i !== post.id);
      commit(SET_ITEM, { commentsVisibility });
    } else {
      const commentsVisibility = getters.commentsVisibility.concat(post.id);
      commit(SET_ITEM, { commentsVisibility });
    }
  },
  toggleRepliesVisibility({ commit, getters }, comment) {
    if (getters.repliesVisibility.find(i => i === comment.id)) {
      const repliesVisibility = getters.repliesVisibility.filter(i => i !== comment.id);
      commit(SET_ITEM, { repliesVisibility });
    } else {
      const repliesVisibility = getters.repliesVisibility.concat(comment.id);
      commit(SET_ITEM, { repliesVisibility });
    }
  },
  // setComment({ commit }, post) {
  //     commit(SET_STATUS, {
  //         item: post,
  //         key: 'commentLoading',
  //         value: true,
  //     });
  //     axios.post('/api/post/comments/get', {
  //         _token: window.Laravel.csrfToken,
  //         post_id: post.id,
  //     })
  //         .then(({ data }) => {
  //             commit(SET_STATUS, {
  //                 item: post,
  //                 key: 'commentLoading',
  //                 value: false,
  //             });
  //             commit(SET_COMMENT, {
  //                 post,
  //                 comments: data.data,
  //                 commentsNextUrl: data.next_page_url,
  //             });
  //         })
  //         .catch(() => {
  //         // .catch((error) => {
  //             commit(SET_STATUS, {
  //                 item: post,
  //                 key: 'commentLoading',
  //                 value: false,
  //             });
  //             // console.log('addComment error ', error);
  //         });
  // },
  // loadReplies({ commit }, comment) {
  //     commit(SET_STATUS, {
  //         item: comment,
  //         key: 'repliesVisibility',
  //         value: !comment.repliesVisibility,
  //     });
  //     if (!_.has(comment, 'replies')) {
  //         commit(SET_STATUS, {
  //             item: comment,
  //             key: 'repliesLoading',
  //             value: true,
  //         });
  //         axios.post('/api/post/comments/replies/get', {
  //             _token: window.Laravel.csrfToken,
  //             comment_id: comment.id,
  //         })
  //             .then(({ data }) => {
  //                 commit(SET_STATUS, {
  //                     item: comment,
  //                     key: 'repliesLoading',
  //                     value: false,
  //                 });
  //                 commit(SET_REPLIES, {
  //                     comment,
  //                     replies: data.data.reverse(),
  //                     repliesNextUrl: data.next_page_url,
  //                 });
  //             })
  //             .catch(() => {
  //             // .catch((error) => {
  //                 commit(SET_STATUS, {
  //                     item: comment,
  //                     key: 'repliesLoading',
  //                     value: false,
  //                 });
  //                 commit(SET_ITEM, { activeComment: {} });
  //                 // console.log('loadReplies error ', error);
  //             });
  //     }
  // },
  likeComment({ commit }, comment) {
    commit(SET_ITEM, { clickedComment: comment });
    axios.patch('/api/comment/like', {
      _token: window.Laravel.csrfToken,
      comment_id: comment.id,
    })
      .then(({ data: { like, deleted } }) => {
        commit(LIKE_COMMENT, {
          comment,
          likes: comment.likes,
          deleted,
          like,
          isLiked: !comment.isLiked,
        });
        commit(SET_ITEM, { clickedComment: {} });
      });
  },
  // likePost({ commit }, post) {
  //     commit(SET_ITEM, { clickedPost: post });
  //     axios.post('/api/post/like', {
  //         _token: window.Laravel.csrfToken,
  //         post_id: post.id,
  //     })
  //         .then(({ data: { like, deleted } }) => {
  //             commit(LIKE_POST, {
  //                 post,
  //                 like,
  //                 likes: post.likes,
  //                 deleted,
  //                 userReaction: !post.userReaction,
  //             });
  //             commit(SET_ITEM, { clickedPost: {} });
  //         });
  // },
  // starPost({ commit }, post) {
  //     commit(SET_ITEM, { clickedPost: post });
  //     axios.post('/api/post/star', {
  //         _token: window.Laravel.csrfToken,
  //         post_id: post.id,
  //     })
  //         .then(({ data: { star, deleted } }) => {
  //             commit(STAR_POST, {
  //                 post,
  //                 star,
  //                 stars: post.stars,
  //                 deleted,
  //                 userReaction: !post.userReaction,
  //             });
  //             commit(SET_ITEM, { clickedPost: {} });
  //         });
  // },
  deletePost({ commit, getters }) {
    commit(SET_ITEM, { deleteLoading: true });
    // this.deleteLoading = !this.deleteLoading;
    axios.post('/api/post/delete', {
      _token: window.Laravel.csrfToken,
      post_id: getters.activePost.id,
    })
      .then(() => {
        commit(SET_ITEM, { deleteLoading: false });
        const fPost = getters.feedItems.data.filter(i => (
          i.id !== getters.activePost.id
        ));
        commit(DELETE_POST, {
          feed: getters.feedItems,
          fPost,
        });
        commit(SET_ITEM, { togglePostDeleteModal: false });
      })
      .catch(() => {
        // .catch((error) => {
        //     console.log('loadReplies error ', error);
        commit(SET_ITEM, { deleteLoading: false });
      });
  },
};
