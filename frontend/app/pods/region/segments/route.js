import Route from '@ember/routing/route';
import { hash } from 'rsvp';
export default Route.extend({
  model() {
    return hash({
      roadTypes: this.store.findAll('road-type'),
      region: this.modelFor('region'),
    });
  },
  setupController(controller, model) {
    this._super(...arguments);

    controller.set('region', model.region);
    controller.set('roadTypes', model.roadTypes);
  },
});
