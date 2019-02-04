import Component from '@ember/component';
import { get } from '@ember/object';

export default Component.extend({
  actions: {
    save() {
      let isNew = get(this, 'region.isNew');
      get(this, 'region')
        .save()
        .then(region => {
          if (isNew) {
            get(this, 'route').send('transition', region.id);
          }
        });
    },

    delete() {
      get(this, 'region')
        .destroyRecord()
        .then(() => {
          get(this, 'route').send('transition');
        });
    },
  },
});
