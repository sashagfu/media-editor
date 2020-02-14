export default class Project {
    id = 0;
    title = '';
    description = '';
    authorId = 0;
    tags = [];
    isDraft = true;
    userReaction = false;
    isPerformance = false;
    author = null;
    stars = [];
    likes = [];
    comments = [];
    slides = [];
    texts = [];
    pinned = false;
    assets = {
      id: 0,
      projectId: 0,
      type: 'full',
      path: '',
      filePath: '',
      credits: '',
      version: 0,
    };

    createTag(name) {
      this.tags.push({ name });
    }
    addTag({ id, name }) {
      this.tags.push({ id, name });
    }
}
