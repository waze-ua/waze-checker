import Component from '@ember/component';
import { get, computed } from '@ember/object';

export default Component.extend({
  isShow: computed('pages', function() {
    return this.get('pages.length') > 1;
  }),

  pages: computed('model.meta.{currentPage,totalItems}', function() {
    const length =
      parseInt(
        get(this, 'model.meta.totalItems') /
          get(this, 'model.meta.itemsPerPage')
      ) + 1;
    const page = get(this, 'model.meta.currentPage');
    const pages = [];

    if (length <= 20) {
      for (let i = 1; i <= length; i++) {
        pages.push({
          page: i,
        });
      }
    } else {
      if (page <= 4) {
        for (let i = 1; i <= 5; i++) {
          pages.push({
            page: i,
          });
        }
        pages.push({
          page: '...',
        });

        pages.push({
          page: length,
        });
      } else if (page >= length - 4) {
        pages.push({
          page: 1,
        });

        pages.push({
          page: '...',
        });

        for (let i = length - 5; i <= length; i++) {
          pages.push({
            page: i,
          });
        }
      } else {
        pages.push({
          page: 1,
        });

        pages.push({
          page: '...',
        });

        for (let i = page - 2; i <= page + 2; i++) {
          pages.push({
            page: i,
          });
        }

        pages.push({
          page: '...',
        });

        pages.push({
          page: length,
        });
      }
    }

    return pages;
  }),

  activePage: computed('model.meta.currentPage', function() {
    return get(this, 'model.meta.currentPage');
  }),

  actions: {
    changePage(page) {
      get(this, 'updateQueryParameter')('page', page);
    },
  },
});
