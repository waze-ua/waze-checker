import Route from '@ember/routing/route';

export default Route.extend({
  model(params) {
    if (+params.region_id === 0) {
      return this.store.createRecord('region');
    } else {
      return this.store.findRecord('region', params.region_id);
    }
  },

  setupController(controller, model) {
    this._super(...arguments);
    controller.set('region', model);
    controller.set('route', this);
  },

  actions: {
    willTransition() {
      if (this.controller.get('region.isNew')) {
        this.controller.get('region').destroyRecord();
      }
    },

    transition(id) {
      if (id) {
        this.transitionTo('init.regions.region', id);
      } else {
        this.transitionTo('init.regions');
      }
    },
  },
});
