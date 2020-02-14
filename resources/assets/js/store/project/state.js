export default {
  loading: false,
  saving: false,
  saved: true,
  fetchedProjectData: '',
  projectData: {
    value: [],
    id: null,
    title: '',
    thumbTime: null, // hh:mm:ss:ms
    layers: [
      {
        id: 0,
        volume: 0.8,
        height: 1,
      }, {
        id: 1,
        volume: 0.8,
        height: 1,
      }, {
        id: 2,
        volume: 0.8,
        height: 1,
      },
    ],
    isPublished: false,
    isProcessing: false,
    isDraft: false,
    isFailed: false,
  },
  pendingProjectValue: [],
};
