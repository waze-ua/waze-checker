import Route from '@ember/routing/route';
import { hash } from 'rsvp';

export default Route.extend({
  model() {
    return hash({
      regions: this.store.findAll('region'),  
    });
  },

  setupController(controller, model) {
    controller.set('regions', model.regions);
  },

});
