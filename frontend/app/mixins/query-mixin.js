import Mixin from '@ember/object/mixin';
import { get, set, computed } from '@ember/object';

export default Mixin.create({
  orderBy: computed('queryParams.order', function() {
    let order = get(this, 'queryParams.order').split(' ');
    if (order.length > 0) {
      return order[0];
    }
    return '';
  }),

  orderType: computed('queryParams.order', function() {
    let order = get(this, 'queryParams.order').split(' ');
    if (order.length > 1) {
      return order[1];
    }

    return '';
  }),

  actions: {
    clearInput(property) {
      set(this, property, '');
      get(this, 'updateQueryParameter')(property, '');
    },

    search(property, value) {
      get(this, 'updateQueryParameter')(property, value);
    },

    changeDropdown(property, value) {
      get(this, 'updateQueryParameter')(property, value);
    },

    setOrder(order) {
      let newOrderType = 'ASC';
      let lastOrderBy = get(this, 'orderBy');

      if (lastOrderBy === order) {
        let lastOrderType = get(this, 'orderType');

        if (lastOrderType === 'ASC') {
          newOrderType = 'DESC';
        }
      }

      get(this, 'updateQueryParameter')('order', `${order} ${newOrderType}`);
    },
  },
});
