export default {
  loading: state => state.loading,
  items: state => state.items,
  itemsText: state => state.itemsText,
  activeComponent: state => state.activeComponent,
  selectedItems: state => state.selectedItems,
  itemsClip: state => state.itemsClip,
  itemsRecentProjects: state => state.itemsRecentProjects,

  filters: state => state.filters,
  searchText: state => state.filters.search,
  filtersFull: state => state.filters.full,
  filtersVideos: state => state.filters.videos,
  filtersAudios: state => state.filters.audios,
  filtersImages: state => state.filters.images,
  showList: state => state.showList,
};
