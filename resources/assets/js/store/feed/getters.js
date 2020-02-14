export default {
  activeComment: state => state.activeComment,
  activePost: state => state.activePost,
  // commentLoading: state => state.commentLoading,
  deleteLoading: state => state.deleteLoading,
  errors: state => state.errors,
  // feedItems: state => state.feedItems,
  mentionUsers: state => state.mentionUsers,
  // toggleCommentDeleteModal: state => state.toggleCommentDeleteModal,
  toggleFlagModal: state => state.toggleFlagModal,
  // togglePostDeleteModal: state => state.togglePostDeleteModal,
  videoModalActive: state => state.videoModalActive,
  videos: state => state.videos,
  videosLoading: state => state.videosLoading,
  error: state => state.error,
  // clickedComment: state => state.clickedComment,
  // clickedPost: state => state.clickedPost,
  // graphQl
  commentsVisibility: state => state.commentsVisibility,
  repliesVisibility: state => state.repliesVisibility,
};

